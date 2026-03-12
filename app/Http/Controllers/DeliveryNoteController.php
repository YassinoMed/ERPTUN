<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DeliveryNoteController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage delivery note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $deliveryNotes = DeliveryNote::where('created_by', \Auth::user()->creatorId())
            ->with(['invoice', 'customer'])
            ->latest('id')
            ->get();

        return view('delivery_note.index', compact('deliveryNotes'));
    }

    public function create(Request $request)
    {
        if (!\Auth::user()->can('create delivery note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $invoices = Invoice::where('created_by', \Auth::user()->creatorId())
            ->with(['customer', 'items.product'])
            ->get();

        $selectedInvoice = null;
        if (!empty($request->invoice_id)) {
            $selectedInvoice = $invoices->firstWhere('id', (int) $request->invoice_id);
            if ($selectedInvoice) {
                $selectedInvoice->load(['deliveryNotes.items']);
            }
        }

        return view('delivery_note.create', [
            'invoices' => $invoices,
            'selectedInvoice' => $selectedInvoice,
            'statuses' => DeliveryNote::$statuses,
        ]);
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create delivery note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'invoice_id' => 'required|integer',
            'delivery_date' => 'required|date',
            'status' => 'required',
            'items' => 'required|array',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $invoice = $this->invoiceForCurrentCompany($request->invoice_id);
        if (!$invoice) {
            return redirect()->back()->with('error', __('Invoice not found.'));
        }

        $preparedItems = $this->validatedItems($request->items, $invoice);
        if (empty($preparedItems)) {
            return redirect()->back()->with('error', __('No deliverable quantity available.'));
        }

        $deliveryNote = DeliveryNote::create([
            'delivery_note_id' => $this->deliveryNoteNumber(),
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'delivery_date' => $request->delivery_date,
            'status' => $request->status,
            'reference' => $request->reference,
            'tracking_number' => $request->tracking_number,
            'driver_name' => $request->driver_name,
            'vehicle_number' => $request->vehicle_number,
            'shipping_address' => $request->shipping_address ?: optional($invoice->customer)->shipping_address,
            'notes' => $request->notes,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        foreach ($preparedItems as $preparedItem) {
            DeliveryNoteItem::create([
                'delivery_note_id' => $deliveryNote->id,
                'invoice_product_id' => $preparedItem['invoice_product_id'],
                'product_id' => $preparedItem['product_id'],
                'quantity' => $preparedItem['quantity'],
                'description' => $preparedItem['description'],
            ]);
        }

        return redirect()->route('delivery-note.show', $deliveryNote->id)->with('success', __('Delivery note successfully created.'));
    }

    public function show(DeliveryNote $deliveryNote)
    {
        if (!$this->owns($deliveryNote) || !\Auth::user()->can('show delivery note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $deliveryNote->load(['invoice', 'customer', 'items.product']);

        return view('delivery_note.show', compact('deliveryNote'));
    }

    public function edit(DeliveryNote $deliveryNote)
    {
        if (!$this->owns($deliveryNote) || !\Auth::user()->can('edit delivery note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $deliveryNote->load(['invoice.customer', 'invoice.items.product', 'invoice.deliveryNotes.items', 'items.invoiceProduct']);

        return view('delivery_note.edit', [
            'deliveryNote' => $deliveryNote,
            'statuses' => DeliveryNote::$statuses,
        ]);
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        if (!$this->owns($deliveryNote) || !\Auth::user()->can('edit delivery note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'delivery_date' => 'required|date',
            'status' => 'required',
            'items' => 'required|array',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $deliveryNote->load(['invoice.items.product', 'invoice.deliveryNotes.items']);
        $preparedItems = $this->validatedItems($request->items, $deliveryNote->invoice, $deliveryNote->id);
        if (empty($preparedItems)) {
            return redirect()->back()->with('error', __('No deliverable quantity available.'));
        }

        $deliveryNote->update([
            'delivery_date' => $request->delivery_date,
            'status' => $request->status,
            'reference' => $request->reference,
            'tracking_number' => $request->tracking_number,
            'driver_name' => $request->driver_name,
            'vehicle_number' => $request->vehicle_number,
            'shipping_address' => $request->shipping_address,
            'notes' => $request->notes,
        ]);

        $deliveryNote->items()->delete();
        foreach ($preparedItems as $preparedItem) {
            DeliveryNoteItem::create([
                'delivery_note_id' => $deliveryNote->id,
                'invoice_product_id' => $preparedItem['invoice_product_id'],
                'product_id' => $preparedItem['product_id'],
                'quantity' => $preparedItem['quantity'],
                'description' => $preparedItem['description'],
            ]);
        }

        return redirect()->route('delivery-note.show', $deliveryNote->id)->with('success', __('Delivery note successfully updated.'));
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        if (!$this->owns($deliveryNote) || !\Auth::user()->can('delete delivery note')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $deliveryNote->items()->delete();
        $deliveryNote->delete();

        return redirect()->route('delivery-note.index')->with('success', __('Delivery note successfully deleted.'));
    }

    protected function invoiceForCurrentCompany($invoiceId)
    {
        return Invoice::where('id', $invoiceId)
            ->where('created_by', \Auth::user()->creatorId())
            ->with(['customer', 'items.product', 'deliveryNotes.items'])
            ->first();
    }

    protected function validatedItems(array $items, Invoice $invoice, $excludeDeliveryNoteId = null)
    {
        $preparedItems = [];
        $deliveredByInvoiceProduct = [];

        $otherDeliveryNotes = $invoice->deliveryNotes()
            ->when($excludeDeliveryNoteId, function ($query) use ($excludeDeliveryNoteId) {
                $query->where('id', '!=', $excludeDeliveryNoteId);
            })
            ->where('status', '!=', 'cancelled')
            ->with('items')
            ->get();

        foreach ($otherDeliveryNotes as $existingDeliveryNote) {
            foreach ($existingDeliveryNote->items as $existingItem) {
                $deliveredByInvoiceProduct[$existingItem->invoice_product_id] = ($deliveredByInvoiceProduct[$existingItem->invoice_product_id] ?? 0) + (float) $existingItem->quantity;
            }
        }

        foreach ($invoice->items as $invoiceItem) {
            $requestedQuantity = (float) ($items[$invoiceItem->id]['quantity'] ?? 0);
            if ($requestedQuantity <= 0) {
                continue;
            }

            $alreadyDelivered = (float) ($deliveredByInvoiceProduct[$invoiceItem->id] ?? 0);
            $remaining = max(0, (float) $invoiceItem->quantity - $alreadyDelivered);
            if ($requestedQuantity > $remaining) {
                continue;
            }

            $preparedItems[] = [
                'invoice_product_id' => $invoiceItem->id,
                'product_id' => $invoiceItem->product_id,
                'quantity' => $requestedQuantity,
                'description' => $items[$invoiceItem->id]['description'] ?? $invoiceItem->description,
            ];
        }

        return $preparedItems;
    }

    protected function owns(DeliveryNote $deliveryNote)
    {
        return (int) $deliveryNote->created_by === (int) \Auth::user()->creatorId();
    }

    protected function deliveryNoteNumber()
    {
        $latest = DeliveryNote::where('created_by', \Auth::user()->creatorId())->max('delivery_note_id');

        return $latest ? $latest + 1 : 1;
    }
}

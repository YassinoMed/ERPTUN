<?php

namespace App\Http\Controllers;

use App\Models\BtpPriceItem;
use App\Models\BtpPriceQuote;
use App\Models\BtpPriceQuoteItem;
use App\Models\Project;
use Illuminate\Http\Request;

class BtpPriceBreakdownController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage btp price breakdowns')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $projects = Project::where('created_by', $creatorId)->orderBy('id', 'desc')->get();
        $items = BtpPriceItem::where('created_by', $creatorId)->latest('id')->get();
        $quotes = BtpPriceQuote::where('created_by', $creatorId)
            ->withCount('items')
            ->latest('id')
            ->get();
        $quoteItems = BtpPriceQuoteItem::whereIn('quote_id', $quotes->pluck('id'))->latest('id')->get();

        $totals = [
            'items' => $items->count(),
            'quotes' => $quotes->count(),
            'quote_total' => $quotes->sum('total'),
        ];

        return view('btp/price-breakdowns', compact('projects', 'items', 'quotes', 'quoteItems', 'totals'));
    }

    public function storeItem(Request $request)
    {
        if (!\Auth::user()->can('manage btp price breakdowns')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'code' => 'nullable|string|max:50',
            'name' => 'required|string|max:191',
            'unit' => 'nullable|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        BtpPriceItem::create($data);

        return redirect()->route('btp.price-breakdowns.index')->with('success', __('Price item saved.'));
    }

    public function storeQuote(Request $request)
    {
        if (!\Auth::user()->can('manage btp price breakdowns')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'project_id' => 'required|integer',
            'reference' => 'nullable|string|max:191',
            'vat_rate' => 'nullable|numeric|min:0',
        ]);

        $creatorId = \Auth::user()->creatorId();
        Project::where('created_by', $creatorId)->findOrFail($data['project_id']);

        BtpPriceQuote::create([
            'project_id' => $data['project_id'],
            'reference' => $data['reference'] ?? null,
            'vat_rate' => $data['vat_rate'] ?? 19,
            'subtotal' => 0,
            'vat_amount' => 0,
            'total' => 0,
            'created_by' => $creatorId,
        ]);

        return redirect()->route('btp.price-breakdowns.index')->with('success', __('Quote created.'));
    }

    public function storeQuoteItem(Request $request)
    {
        if (!\Auth::user()->can('manage btp price breakdowns')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'quote_id' => 'required|integer',
            'price_item_id' => 'nullable|integer',
            'description' => 'nullable|string|max:191',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        $creatorId = \Auth::user()->creatorId();
        $quote = BtpPriceQuote::where('created_by', $creatorId)->findOrFail($data['quote_id']);

        $priceItem = null;
        if (!empty($data['price_item_id'])) {
            $priceItem = BtpPriceItem::where('created_by', $creatorId)->findOrFail($data['price_item_id']);
        }

        $unitPrice = $data['unit_price'] ?? ($priceItem ? $priceItem->unit_price : 0);
        $description = $data['description'] ?? ($priceItem ? $priceItem->name : null);
        $lineTotal = (float) $data['quantity'] * (float) $unitPrice;

        BtpPriceQuoteItem::create([
            'quote_id' => $quote->id,
            'price_item_id' => $priceItem ? $priceItem->id : null,
            'description' => $description,
            'quantity' => $data['quantity'],
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
        ]);

        $this->recalculateQuoteTotals($quote);

        return redirect()->route('btp.price-breakdowns.index')->with('success', __('Quote item saved.'));
    }

    private function recalculateQuoteTotals(BtpPriceQuote $quote)
    {
        $subtotal = BtpPriceQuoteItem::where('quote_id', $quote->id)->sum('line_total');
        $vatAmount = ($subtotal * (float) $quote->vat_rate) / 100;
        $quote->subtotal = $subtotal;
        $quote->vat_amount = $vatAmount;
        $quote->total = $subtotal + $vatAmount;
        $quote->save();
    }
}

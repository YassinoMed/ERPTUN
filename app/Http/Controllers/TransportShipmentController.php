<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\TransportShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportShipmentController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage transport shipment') && ! Auth::user()->can('show transport shipment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $transportShipments = TransportShipment::where('created_by', Auth::user()->creatorId())
            ->with('customer')
            ->latest('id')
            ->get();

        return view('transport_shipments.index', compact('transportShipments'));
    }

    public function create()
    {
        if (! Auth::user()->can('create transport shipment')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('transport_shipments.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create transport shipment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'reference' => 'required|max:255',
            'origin' => 'required|max:255',
            'destination' => 'required|max:255',
            'status' => 'required|max:100',
            'freight_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        TransportShipment::create($request->only([
            'reference', 'customer_id', 'origin', 'destination', 'vehicle_number', 'driver_name',
            'departure_date', 'arrival_date', 'status', 'freight_amount', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('transport-shipments.index')->with('success', __('Transport shipment successfully created.'));
    }

    public function show(TransportShipment $transportShipment)
    {
        if (! $this->canAccess($transportShipment)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $transportShipment->load('customer');

        return view('transport_shipments.show', compact('transportShipment'));
    }

    public function edit(TransportShipment $transportShipment)
    {
        if (! Auth::user()->can('edit transport shipment') || ! $this->owns($transportShipment)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('transport_shipments.edit', $this->formData() + compact('transportShipment'));
    }

    public function update(Request $request, TransportShipment $transportShipment)
    {
        if (! Auth::user()->can('edit transport shipment') || ! $this->owns($transportShipment)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'reference' => 'required|max:255',
            'origin' => 'required|max:255',
            'destination' => 'required|max:255',
            'status' => 'required|max:100',
            'freight_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $transportShipment->update($request->only([
            'reference', 'customer_id', 'origin', 'destination', 'vehicle_number', 'driver_name',
            'departure_date', 'arrival_date', 'status', 'freight_amount', 'notes',
        ]));

        return redirect()->route('transport-shipments.show', $transportShipment)->with('success', __('Transport shipment successfully updated.'));
    }

    public function destroy(TransportShipment $transportShipment)
    {
        if (! Auth::user()->can('delete transport shipment') || ! $this->owns($transportShipment)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $transportShipment->delete();

        return redirect()->route('transport-shipments.index')->with('success', __('Transport shipment successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'statuses' => TransportShipment::$statuses,
            'customers' => Customer::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function owns(TransportShipment $transportShipment): bool
    {
        return (int) $transportShipment->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(TransportShipment $transportShipment): bool
    {
        return $this->owns($transportShipment) && (Auth::user()->can('manage transport shipment') || Auth::user()->can('show transport shipment'));
    }
}

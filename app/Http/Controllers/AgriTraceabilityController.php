<?php

namespace App\Http\Controllers;

use App\Models\AgriCertificate;
use App\Models\AgriLot;
use App\Models\AgriTraceEvent;
use App\Services\AgriTraceabilityService;
use Illuminate\Http\Request;

class AgriTraceabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index(Request $request, AgriTraceabilityService $service)
    {
        if (!\Auth::user()->can('manage agri traceability')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $lots = AgriLot::query()
            ->where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $events = AgriTraceEvent::query()
            ->where('created_by', $creatorId)
            ->latest('occurred_at')
            ->limit(15)
            ->get();

        $certificates = AgriCertificate::query()
            ->where('created_by', $creatorId)
            ->latest('issued_at')
            ->limit(10)
            ->get();

        $selectedLotId = $request->get('lot_id');
        $selectedLot = $selectedLotId ? $lots->firstWhere('id', (int) $selectedLotId) : $lots->first();
        $traceChain = [];

        if ($selectedLot) {
            $traceChain = AgriTraceEvent::query()
                ->where('lot_id', $selectedLot->id)
                ->orderBy('occurred_at')
                ->get();
        }

        return view('agri/traceability', compact('lots', 'events', 'certificates', 'selectedLot', 'traceChain'));
    }

    public function storeLot(Request $request)
    {
        if (!\Auth::user()->can('manage agri traceability')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'code' => 'required|string|max:100',
            'name' => 'required|string|max:191',
            'crop_type' => 'required|string|max:191',
            'harvest_date' => 'nullable|date',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string|max:32',
            'status' => 'nullable|string|max:32',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['unit'] = $data['unit'] ?? 'kg';
        $data['status'] = $data['status'] ?? 'active';

        AgriLot::updateOrCreate(
            [
                'code' => $data['code'],
                'created_by' => $data['created_by'],
            ],
            $data
        );

        return redirect()->route('agri.traceability.index')->with('success', __('Lot saved.'));
    }

    public function storeEvent(Request $request, AgriTraceabilityService $service)
    {
        if (!\Auth::user()->can('manage agri traceability')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'lot_id' => 'required|integer',
            'step' => 'required|string|max:191',
            'location' => 'nullable|string|max:191',
            'actor' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
            'occurred_at' => 'nullable|date',
        ]);

        $lot = AgriLot::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($data['lot_id']);

        $service->createTraceEvent($lot, $data);

        return redirect()->route('agri.traceability.index')->with('success', __('Trace event recorded.'));
    }

    public function issueCertificate(Request $request, AgriTraceabilityService $service)
    {
        if (!\Auth::user()->can('manage agri traceability')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'lot_id' => 'required|integer',
        ]);

        $lot = AgriLot::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->findOrFail($data['lot_id']);

        $service->issueCertificate($lot);

        return redirect()->route('agri.traceability.index')->with('success', __('Certificate issued.'));
    }
}

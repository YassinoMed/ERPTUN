<?php

namespace App\Http\Controllers;

use App\Models\AgriCertificate;
use App\Models\AgriComplianceCheck;
use App\Models\AgriExportShipment;
use App\Models\AgriLot;
use App\Models\AgriTraceEvent;
use App\Models\AgriTransformationBatch;
use Illuminate\Support\Carbon;
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
        $upstreamBatches = collect();
        $downstreamBatches = collect();
        $complianceChecks = collect();
        $shipments = collect();
        $coldAlerts = collect();

        if ($selectedLot) {
            $traceChain = AgriTraceEvent::query()
                ->where('lot_id', $selectedLot->id)
                ->orderBy('occurred_at')
                ->get();

            $upstreamBatches = AgriTransformationBatch::query()
                ->with(['inputLot', 'outputLot'])
                ->where('output_lot_id', $selectedLot->id)
                ->orderByDesc('processed_at')
                ->get();

            $downstreamBatches = AgriTransformationBatch::query()
                ->with(['inputLot', 'outputLot'])
                ->where('input_lot_id', $selectedLot->id)
                ->orderByDesc('processed_at')
                ->get();

            $complianceChecks = AgriComplianceCheck::query()
                ->where('lot_id', $selectedLot->id)
                ->orderByDesc('checked_at')
                ->get();

            $shipments = AgriExportShipment::query()
                ->where('lot_id', $selectedLot->id)
                ->orderByDesc('departure_date')
                ->get();

            $coldAlerts = collect();
            if ($selectedLot->expiry_date && $selectedLot->expiry_date->lte(Carbon::today()->addDays(14))) {
                $coldAlerts->push([
                    'message' => __('Lot expiry date is approaching.'),
                    'expiry_date' => $selectedLot->expiry_date,
                ]);
            }
        }

        return view('agri/traceability', compact(
            'lots',
            'events',
            'certificates',
            'selectedLot',
            'traceChain',
            'upstreamBatches',
            'downstreamBatches',
            'complianceChecks',
            'shipments',
            'coldAlerts'
        ));
    }

    public function network(Request $request)
    {
        if (!\Auth::user()->can('manage agri traceability')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $lots = AgriLot::where('created_by', $creatorId)->latest('id')->get();
        $selectedLotId = $request->get('lot_id');
        $selectedLot = $selectedLotId ? $lots->firstWhere('id', (int) $selectedLotId) : $lots->first();

        $upstreamBatches = collect();
        $downstreamBatches = collect();
        $shipments = collect();
        $checks = collect();
        $events = collect();

        if ($selectedLot) {
            $upstreamBatches = AgriTransformationBatch::with(['inputLot', 'outputLot'])
                ->where('created_by', $creatorId)
                ->where(function ($query) use ($selectedLot) {
                    $query->where('output_lot_id', $selectedLot->id)
                        ->orWhere('input_lot_id', $selectedLot->id);
                })
                ->orderByDesc('processed_at')
                ->get();

            $downstreamBatches = AgriTransformationBatch::with(['inputLot', 'outputLot'])
                ->where('created_by', $creatorId)
                ->where('input_lot_id', $selectedLot->id)
                ->orderByDesc('processed_at')
                ->get();

            $shipments = AgriExportShipment::where('created_by', $creatorId)
                ->where('lot_id', $selectedLot->id)
                ->latest('departure_date')
                ->get();

            $checks = AgriComplianceCheck::where('created_by', $creatorId)
                ->where('lot_id', $selectedLot->id)
                ->latest('checked_at')
                ->get();

            $events = AgriTraceEvent::where('created_by', $creatorId)
                ->where('lot_id', $selectedLot->id)
                ->orderBy('occurred_at')
                ->get();
        }

        return view('agri.network', compact(
            'lots',
            'selectedLot',
            'upstreamBatches',
            'downstreamBatches',
            'shipments',
            'checks',
            'events'
        ));
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
            'source_reference' => 'nullable|string|max:191',
            'parcel_origin' => 'nullable|string|max:191',
            'harvest_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'quantity' => 'nullable|numeric',
            'unit' => 'nullable|string|max:32',
            'status' => 'nullable|string|max:32',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['unit'] = $data['unit'] ?? 'kg';
        $data['status'] = $data['status'] ?? 'active';
        $data['quality_status'] = 'pending';

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

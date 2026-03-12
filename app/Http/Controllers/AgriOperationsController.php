<?php

namespace App\Http\Controllers;

use App\Models\AgriComplianceCheck;
use App\Models\AgriColdStorageRecord;
use App\Models\AgriCooperative;
use App\Models\AgriExportShipment;
use App\Models\AgriHarvestDelivery;
use App\Models\AgriLot;
use App\Models\AgriTransformationBatch;
use App\Models\AgriWeighing;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgriOperationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage agri operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $lots = AgriLot::where('created_by', $creatorId)->latest('id')->get();
        $cooperatives = AgriCooperative::where('created_by', $creatorId)->latest('id')->get();
        $weighings = AgriWeighing::with(['lot', 'cooperative'])->where('created_by', $creatorId)->latest('weighing_date')->limit(15)->get();
        $coldStorages = AgriColdStorageRecord::with('lot')->where('created_by', $creatorId)->latest('entry_date')->limit(15)->get();
        $exportShipments = AgriExportShipment::with('lot')->where('created_by', $creatorId)->latest('departure_date')->limit(15)->get();
        $transformationBatches = AgriTransformationBatch::with(['inputLot', 'outputLot'])->where('created_by', $creatorId)->latest('processed_at')->limit(15)->get();
        $complianceChecks = AgriComplianceCheck::with('lot')->where('created_by', $creatorId)->latest('checked_at')->limit(15)->get();
        $fefoAlerts = AgriColdStorageRecord::with('lot')
            ->where('created_by', $creatorId)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', Carbon::today()->addDays(14))
            ->orderBy('expiry_date')
            ->limit(10)
            ->get();

        return view('agri.operations', compact(
            'lots',
            'cooperatives',
            'weighings',
            'coldStorages',
            'exportShipments',
            'transformationBatches',
            'complianceChecks',
            'fefoAlerts'
        ));
    }

    public function storeWeighing(Request $request)
    {
        if (!\Auth::user()->can('create agri operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'lot_id' => 'nullable|integer',
            'cooperative_id' => 'nullable|integer',
            'producer_name' => 'nullable|string|max:191',
            'gross_weight' => 'required|numeric|min:0',
            'tare_weight' => 'nullable|numeric|min:0',
            'moisture_percent' => 'nullable|numeric|min:0|max:100',
            'quality_grade' => 'nullable|string|max:64',
            'weighing_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['lot_id'])) {
            AgriLot::where('created_by', $creatorId)->findOrFail($data['lot_id']);
        }
        if (!empty($data['cooperative_id'])) {
            AgriCooperative::where('created_by', $creatorId)->findOrFail($data['cooperative_id']);
        }

        $data['tare_weight'] = $data['tare_weight'] ?? 0;
        $data['moisture_percent'] = $data['moisture_percent'] ?? 0;
        $data['net_weight'] = max(0, (float) $data['gross_weight'] - (float) $data['tare_weight']);
        $data['created_by'] = $creatorId;

        AgriWeighing::create($data);

        return redirect()->route('agri.operations.index')->with('success', __('Weighing recorded.'));
    }

    public function storeColdStorage(Request $request)
    {
        if (!\Auth::user()->can('create agri operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'lot_id' => 'nullable|integer',
            'facility_name' => 'required|string|max:191',
            'chamber_name' => 'nullable|string|max:191',
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric',
            'quantity' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:entry_date',
            'status' => 'nullable|string|in:stored,released,blocked',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['lot_id'])) {
            AgriLot::where('created_by', $creatorId)->findOrFail($data['lot_id']);
        }

        $data['status'] = $data['status'] ?? 'stored';
        $data['created_by'] = $creatorId;

        AgriColdStorageRecord::create($data);

        return redirect()->route('agri.operations.index')->with('success', __('Cold storage record saved.'));
    }

    public function storeExportShipment(Request $request)
    {
        if (!\Auth::user()->can('create agri operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'lot_id' => 'nullable|integer',
            'shipment_ref' => 'required|string|max:100',
            'customer_name' => 'required|string|max:191',
            'destination_country' => 'required|string|max:191',
            'container_no' => 'nullable|string|max:100',
            'incoterm' => 'nullable|string|max:32',
            'shipped_quantity' => 'required|numeric|min:0',
            'departure_date' => 'required|date',
            'status' => 'nullable|string|in:draft,ready,shipped,delivered',
            'document_ref' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['lot_id'])) {
            AgriLot::where('created_by', $creatorId)->findOrFail($data['lot_id']);
        }

        $data['status'] = $data['status'] ?? 'draft';
        $data['created_by'] = $creatorId;

        AgriExportShipment::updateOrCreate(
            [
                'shipment_ref' => $data['shipment_ref'],
                'created_by' => $creatorId,
            ],
            $data
        );

        return redirect()->route('agri.operations.index')->with('success', __('Export shipment saved.'));
    }

    public function storeTransformationBatch(Request $request)
    {
        if (!\Auth::user()->can('manage agri operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'input_lot_id' => 'required|integer',
            'output_lot_code' => 'required|string|max:100',
            'output_lot_name' => 'required|string|max:191',
            'process_step' => 'required|string|max:191',
            'facility_name' => 'nullable|string|max:191',
            'input_quantity' => 'required|numeric|min:0',
            'output_quantity' => 'required|numeric|min:0',
            'waste_quantity' => 'nullable|numeric|min:0',
            'processed_at' => 'required|date',
            'expiry_date' => 'nullable|date',
            'status' => 'nullable|string|max:32',
            'notes' => 'nullable|string',
        ]);

        $inputLot = AgriLot::where('created_by', $creatorId)->findOrFail($data['input_lot_id']);

        $outputLot = AgriLot::updateOrCreate(
            [
                'code' => $data['output_lot_code'],
                'created_by' => $creatorId,
            ],
            [
                'name' => $data['output_lot_name'],
                'crop_type' => $inputLot->crop_type,
                'source_reference' => $inputLot->code,
                'parcel_origin' => $inputLot->parcel_origin,
                'harvest_date' => $inputLot->harvest_date,
                'expiry_date' => $data['expiry_date'] ?? $inputLot->expiry_date,
                'quantity' => $data['output_quantity'],
                'unit' => $inputLot->unit,
                'status' => 'active',
                'quality_status' => $inputLot->quality_status,
            ]
        );

        AgriTransformationBatch::updateOrCreate(
            [
                'batch_number' => sprintf('%s-%s', $inputLot->code, date('YmdHis')),
                'created_by' => $creatorId,
            ],
            [
                'input_lot_id' => $inputLot->id,
                'output_lot_id' => $outputLot->id,
                'process_step' => $data['process_step'],
                'facility_name' => $data['facility_name'] ?? null,
                'input_quantity' => $data['input_quantity'],
                'output_quantity' => $data['output_quantity'],
                'waste_quantity' => $data['waste_quantity'] ?? 0,
                'processed_at' => $data['processed_at'],
                'status' => $data['status'] ?? 'completed',
                'notes' => $data['notes'] ?? null,
            ]
        );

        return redirect()->route('agri.operations.index')->with('success', __('Transformation batch recorded.'));
    }

    public function storeComplianceCheck(Request $request)
    {
        if (!\Auth::user()->can('manage agri operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'lot_id' => 'required|integer',
            'control_type' => 'required|string|max:191',
            'result' => 'required|string|in:pass,warning,fail',
            'certificate_ref' => 'nullable|string|max:100',
            'measured_value' => 'nullable|string|max:100',
            'threshold_value' => 'nullable|string|max:100',
            'checked_at' => 'required|date',
            'corrective_action' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $lot = AgriLot::where('created_by', $creatorId)->findOrFail($data['lot_id']);

        AgriComplianceCheck::create($data + ['created_by' => $creatorId]);

        $lot->update([
            'quality_status' => $data['result'] === 'fail' ? 'blocked' : ($data['result'] === 'warning' ? 'review' : 'released'),
        ]);

        return redirect()->route('agri.operations.index')->with('success', __('Compliance check recorded.'));
    }

    public function reports()
    {
        if (!\Auth::user()->can('manage agri operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $qualitySummary = AgriComplianceCheck::where('created_by', $creatorId)
            ->select('result', DB::raw('count(*) as total'))
            ->groupBy('result')
            ->pluck('total', 'result');

        $destinationSummary = AgriExportShipment::where('created_by', $creatorId)
            ->select('destination_country', DB::raw('sum(shipped_quantity) as total_quantity'))
            ->groupBy('destination_country')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $transformationYield = AgriTransformationBatch::where('created_by', $creatorId)
            ->select(
                'process_step',
                DB::raw('sum(input_quantity) as total_input'),
                DB::raw('sum(output_quantity) as total_output'),
                DB::raw('sum(waste_quantity) as total_waste')
            )
            ->groupBy('process_step')
            ->get();

        $fefoQueue = AgriColdStorageRecord::with('lot')
            ->where('created_by', $creatorId)
            ->where('status', 'stored')
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date')
            ->limit(20)
            ->get();

        $sourceSummary = AgriLot::where('created_by', $creatorId)
            ->select('parcel_origin', DB::raw('count(*) as total_lots'), DB::raw('sum(quantity) as total_quantity'))
            ->groupBy('parcel_origin')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $coldChainSummary = AgriColdStorageRecord::where('created_by', $creatorId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $cooperativeSummary = AgriHarvestDelivery::where('created_by', $creatorId)
            ->select('cooperative_id', DB::raw('sum(quantity) as total_net_weight'))
            ->groupBy('cooperative_id')
            ->orderByDesc('total_net_weight')
            ->limit(10)
            ->get();

        return view('agri.reports', compact(
            'qualitySummary',
            'destinationSummary',
            'transformationYield',
            'fefoQueue',
            'sourceSummary',
            'coldChainSummary',
            'cooperativeSummary'
        ));
    }

    public function fefoBoard()
    {
        if (!\Auth::user()->can('manage agri operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();

        $records = AgriColdStorageRecord::with('lot')
            ->where('created_by', $creatorId)
            ->where('status', 'stored')
            ->whereNotNull('expiry_date')
            ->orderBy('expiry_date')
            ->get()
            ->map(function ($record) {
                $daysToExpiry = optional($record->expiry_date)->diffInDays(Carbon::today(), false);

                return [
                    'record' => $record,
                    'days_to_expiry' => $daysToExpiry,
                    'risk' => $daysToExpiry <= 3 ? 'critical' : ($daysToExpiry <= 10 ? 'warning' : 'normal'),
                ];
            });

        return view('agri.fefo', compact('records'));
    }
}

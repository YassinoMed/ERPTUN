<?php

namespace App\Http\Controllers;

use App\Models\BtpEquipment;
use App\Models\BtpEquipmentMaintenance;
use App\Models\BtpEquipmentUsage;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BtpEquipmentControlController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage btp equipment control')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $projects = Project::where('created_by', $creatorId)->orderBy('id', 'desc')->get();
        $equipments = BtpEquipment::where('created_by', $creatorId)->latest('id')->get();
        $usages = BtpEquipmentUsage::where('created_by', $creatorId)->latest('start_date')->limit(15)->get();
        $maintenances = BtpEquipmentMaintenance::where('created_by', $creatorId)->latest('scheduled_at')->limit(15)->get();

        $totalEquipment = $equipments->count();
        $availableEquipment = $equipments->where('status', 'available')->count();
        $fuelConsumed = BtpEquipmentUsage::where('created_by', $creatorId)->sum('fuel_consumed');
        $pendingMaintenances = BtpEquipmentMaintenance::where('created_by', $creatorId)
            ->whereNull('completed_at')
            ->whereDate('scheduled_at', '>=', Carbon::today())
            ->count();

        return view('btp/equipment-control', compact('projects', 'equipments', 'usages', 'maintenances', 'totalEquipment', 'availableEquipment', 'fuelConsumed', 'pendingMaintenances'));
    }

    public function storeEquipment(Request $request)
    {
        if (!\Auth::user()->can('manage btp equipment control')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'rental_rate' => 'nullable|numeric|min:0',
            'fuel_type' => 'nullable|string|max:100',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        $data['status'] = $data['status'] ?? 'available';

        BtpEquipment::create($data);

        return redirect()->route('btp.equipment-control.index')->with('success', __('Equipment saved.'));
    }

    public function storeUsage(Request $request)
    {
        if (!\Auth::user()->can('manage btp equipment control')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'equipment_id' => 'required|integer',
            'project_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'hours_used' => 'nullable|numeric|min:0',
            'fuel_consumed' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $creatorId = \Auth::user()->creatorId();
        BtpEquipment::where('created_by', $creatorId)->findOrFail($data['equipment_id']);
        Project::where('created_by', $creatorId)->findOrFail($data['project_id']);

        BtpEquipmentUsage::create([
            'equipment_id' => $data['equipment_id'],
            'project_id' => $data['project_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'hours_used' => $data['hours_used'] ?? 0,
            'fuel_consumed' => $data['fuel_consumed'] ?? 0,
            'note' => $data['note'] ?? null,
            'created_by' => $creatorId,
        ]);

        return redirect()->route('btp.equipment-control.index')->with('success', __('Usage saved.'));
    }

    public function storeMaintenance(Request $request)
    {
        if (!\Auth::user()->can('manage btp equipment control')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'equipment_id' => 'required|integer',
            'maintenance_type' => 'nullable|string|max:50',
            'scheduled_at' => 'required|date',
            'completed_at' => 'nullable|date',
            'cost' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $creatorId = \Auth::user()->creatorId();
        BtpEquipment::where('created_by', $creatorId)->findOrFail($data['equipment_id']);

        BtpEquipmentMaintenance::create([
            'equipment_id' => $data['equipment_id'],
            'maintenance_type' => $data['maintenance_type'] ?? 'preventive',
            'scheduled_at' => $data['scheduled_at'],
            'completed_at' => $data['completed_at'] ?? null,
            'cost' => $data['cost'] ?? 0,
            'note' => $data['note'] ?? null,
            'created_by' => $creatorId,
        ]);

        return redirect()->route('btp.equipment-control.index')->with('success', __('Maintenance saved.'));
    }
}

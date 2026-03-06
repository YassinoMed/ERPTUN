<?php

namespace App\Http\Controllers;

use App\Models\HotelHousekeepingTask;
use App\Models\HotelHousekeepingTaskItem;
use App\Models\HotelInventoryItem;
use App\Models\HotelInventoryMovement;
use App\Models\HotelMaintenanceIssue;
use App\Models\HotelRoom;
use App\Models\HotelHousekeepingChecklistItem;
use Illuminate\Http\Request;

class HotelHousekeepingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage hotel housekeeping')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $rooms = HotelRoom::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->get();

        $tasks = HotelHousekeepingTask::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->with('room')
            ->latest('id')
            ->limit(15)
            ->get();

        $issues = HotelMaintenanceIssue::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->with('room')
            ->latest('id')
            ->limit(15)
            ->get();

        $inventory = HotelInventoryItem::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->latest('id')
            ->get();

        $checklistItems = HotelHousekeepingChecklistItem::query()
            ->latest('id')
            ->limit(20)
            ->get();

        return view('hotel/housekeeping', compact('rooms', 'tasks', 'issues', 'inventory', 'checklistItems'));
    }

    public function storeTask(Request $request)
    {
        if (!\Auth::user()->can('manage hotel housekeeping')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'room_id' => 'required|integer',
            'priority' => 'nullable|string|max:32',
            'assigned_to' => 'nullable|integer',
            'scheduled_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'checklist_items' => 'nullable|array',
            'checklist_items.*' => 'integer',
        ]);

        $task = HotelHousekeepingTask::create([
            'room_id' => $data['room_id'],
            'status' => 'pending',
            'priority' => $data['priority'] ?? 'normal',
            'assigned_to' => $data['assigned_to'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        if (!empty($data['checklist_items'])) {
            foreach ($data['checklist_items'] as $itemId) {
                HotelHousekeepingTaskItem::create([
                    'task_id' => $task->id,
                    'checklist_item_id' => $itemId,
                    'is_done' => false,
                ]);
            }
        }

        return redirect()->route('hotel.housekeeping.index')->with('success', __('Task created.'));
    }

    public function updateTaskStatus(Request $request, HotelHousekeepingTask $task)
    {
        if (!\Auth::user()->can('manage hotel housekeeping')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'status' => 'required|string|max:32',
            'room_status' => 'nullable|string|max:32',
        ]);

        $task->update([
            'status' => $data['status'],
            'started_at' => $data['status'] === 'in_progress' ? now() : $task->started_at,
            'completed_at' => $data['status'] === 'completed' ? now() : $task->completed_at,
        ]);

        if (!empty($data['room_status'])) {
            $task->room?->update([
                'status' => $data['room_status'],
            ]);
        }

        return redirect()->route('hotel.housekeeping.index')->with('success', __('Task updated.'));
    }

    public function reportIssue(Request $request)
    {
        if (!\Auth::user()->can('manage hotel housekeeping')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'room_id' => 'required|integer',
            'title' => 'required|string|max:191',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|max:32',
        ]);

        HotelMaintenanceIssue::create([
            'room_id' => $data['room_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'open',
            'priority' => $data['priority'] ?? 'normal',
            'reported_by' => \Auth::user()->id,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('hotel.housekeeping.index')->with('success', __('Issue reported.'));
    }

    public function storeInventoryMovement(Request $request)
    {
        if (!\Auth::user()->can('manage hotel housekeeping')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'inventory_item_id' => 'required|integer',
            'quantity' => 'required|numeric',
            'type' => 'required|string|max:32',
            'reason' => 'nullable|string|max:191',
        ]);

        $item = HotelInventoryItem::where('id', $data['inventory_item_id'])
            ->where('created_by', \Auth::user()->creatorId())
            ->firstOrFail();

        $adjustment = $data['type'] === 'receive' ? $data['quantity'] : -$data['quantity'];
        $item->update([
            'quantity_on_hand' => max(0, $item->quantity_on_hand + $adjustment),
        ]);

        HotelInventoryMovement::create([
            'inventory_item_id' => $item->id,
            'quantity' => $data['quantity'],
            'type' => $data['type'],
            'reason' => $data['reason'] ?? null,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('hotel.housekeeping.index')->with('success', __('Inventory updated.'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ConfigurationItem;
use App\Models\Support;
use App\Models\SupportCategory;
use App\Models\SupportReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternalItsmController extends Controller
{
    public static array $types = [
        'incident' => 'Incident',
        'service_request' => 'Service Request',
        'problem' => 'Problem',
        'change' => 'Change',
    ];

    public static array $levels = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public function index()
    {
        if (! Auth::user()->can('manage internal itsm') && ! Auth::user()->can('show internal itsm')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $tickets = Support::query()
            ->where('created_by', Auth::user()->creatorId())
            ->where('is_internal', true)
            ->with(['createdBy', 'assignUser', 'category', 'configurationItem'])
            ->latest('id')
            ->get();

        $stats = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status', 'Open')->count(),
            'on_hold' => $tickets->where('status', 'On Hold')->count(),
            'close' => $tickets->where('status', 'Close')->count(),
        ];

        return view('internal_itsm.index', compact('tickets', 'stats'));
    }

    public function create()
    {
        if (! Auth::user()->can('create internal itsm')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('internal_itsm.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create internal itsm')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'subject' => 'required|max:255',
            'user' => 'nullable|integer|exists:users,id',
            'support_category_id' => 'nullable|integer|exists:support_categories,id',
            'configuration_item_id' => 'nullable|integer|exists:configuration_items,id',
            'priority' => 'required|max:50',
            'status' => 'required|max:50',
            'ticket_type' => 'required|max:50',
            'impact_level' => 'nullable|max:50',
            'urgency_level' => 'nullable|max:50',
            'end_date' => 'nullable|date',
            'resolution_due_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        Support::create([
            'subject' => $request->subject,
            'user' => $request->user ?: Auth::id(),
            'support_category_id' => $request->support_category_id,
            'configuration_item_id' => $request->configuration_item_id,
            'priority' => $request->priority,
            'end_date' => $request->end_date ?: now()->toDateString(),
            'resolution_due_at' => $request->resolution_due_at,
            'ticket_code' => 'ITSM-'.date('His'),
            'ticket_created' => Auth::id(),
            'status' => $request->status,
            'is_internal' => true,
            'ticket_type' => $request->ticket_type,
            'impact_level' => $request->impact_level,
            'urgency_level' => $request->urgency_level,
            'created_by' => Auth::user()->creatorId(),
            'description' => $request->description,
        ]);

        return redirect()->route('internal-itsm.index')->with('success', __('ITSM ticket successfully created.'));
    }

    public function show(Support $ticket)
    {
        if (! $this->canAccess($ticket)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ticket->load(['createdBy', 'assignUser', 'category', 'configurationItem']);
        $replies = SupportReply::where('support_id', $ticket->id)->with('users')->latest('id')->get();

        return view('internal_itsm.show', compact('ticket', 'replies'));
    }

    public function edit(Support $ticket)
    {
        if (! Auth::user()->can('edit internal itsm') || ! $this->owns($ticket)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('internal_itsm.edit', array_merge($this->formData(), compact('ticket')));
    }

    public function update(Request $request, Support $ticket)
    {
        if (! Auth::user()->can('edit internal itsm') || ! $this->owns($ticket)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'subject' => 'required|max:255',
            'user' => 'nullable|integer|exists:users,id',
            'support_category_id' => 'nullable|integer|exists:support_categories,id',
            'configuration_item_id' => 'nullable|integer|exists:configuration_items,id',
            'priority' => 'required|max:50',
            'status' => 'required|max:50',
            'ticket_type' => 'required|max:50',
            'impact_level' => 'nullable|max:50',
            'urgency_level' => 'nullable|max:50',
            'end_date' => 'nullable|date',
            'resolution_due_at' => 'nullable|date',
            'resolved_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $ticket->update([
            'subject' => $request->subject,
            'user' => $request->user ?: $ticket->user,
            'support_category_id' => $request->support_category_id,
            'configuration_item_id' => $request->configuration_item_id,
            'priority' => $request->priority,
            'status' => $request->status,
            'ticket_type' => $request->ticket_type,
            'impact_level' => $request->impact_level,
            'urgency_level' => $request->urgency_level,
            'end_date' => $request->end_date,
            'resolution_due_at' => $request->resolution_due_at,
            'resolved_at' => $request->resolved_at,
            'description' => $request->description,
        ]);

        return redirect()->route('internal-itsm.index')->with('success', __('ITSM ticket successfully updated.'));
    }

    public function destroy(Support $ticket)
    {
        if (! Auth::user()->can('delete internal itsm') || ! $this->owns($ticket)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ticket->delete();

        return redirect()->route('internal-itsm.index')->with('success', __('ITSM ticket successfully deleted.'));
    }

    public function replyAnswer(Request $request, Support $ticket)
    {
        if (! $this->canAccess($ticket)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'description' => 'required|string',
        ]);

        SupportReply::create([
            'support_id' => $ticket->id,
            'user' => Auth::id(),
            'description' => $request->description,
            'created_by' => Auth::user()->creatorId(),
            'is_read' => 0,
        ]);

        return redirect()->route('internal-itsm.show', $ticket->id)->with('success', __('Reply successfully posted.'));
    }

    protected function formData(): array
    {
        $creatorId = Auth::user()->creatorId();

        return [
            'users' => User::where('created_by', $creatorId)->where('type', '!=', 'client')->pluck('name', 'id'),
            'categories' => SupportCategory::where('created_by', $creatorId)->pluck('name', 'id'),
            'configurationItems' => ConfigurationItem::where('created_by', $creatorId)->pluck('name', 'id'),
            'priorities' => array_combine(Support::$priority, Support::$priority),
            'statuses' => Support::status(),
            'ticketTypes' => self::$types,
            'impactLevels' => self::$levels,
            'urgencyLevels' => self::$levels,
        ];
    }

    protected function owns(Support $ticket): bool
    {
        return (int) $ticket->created_by === (int) Auth::user()->creatorId() && (bool) $ticket->is_internal;
    }

    protected function canAccess(Support $ticket): bool
    {
        return $this->owns($ticket) && (Auth::user()->can('manage internal itsm') || Auth::user()->can('show internal itsm'));
    }
}

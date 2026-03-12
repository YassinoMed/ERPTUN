<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventTicketController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage event ticket') && ! Auth::user()->can('show event ticket')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $eventTickets = EventTicket::where('created_by', Auth::user()->creatorId())
            ->with('event')
            ->latest('id')
            ->get();

        return view('event_tickets.index', compact('eventTickets'));
    }

    public function create()
    {
        if (! Auth::user()->can('create event ticket')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('event_tickets.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create event ticket')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'ticket_code' => 'required|max:255',
            'attendee_name' => 'required|max:255',
            'attendee_email' => 'nullable|email',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        EventTicket::create($request->only([
            'event_id', 'ticket_code', 'attendee_name', 'attendee_email', 'price',
            'status', 'checked_in_at', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('event-tickets.index')->with('success', __('Event ticket successfully created.'));
    }

    public function show(EventTicket $eventTicket)
    {
        if (! $this->canAccess($eventTicket)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $eventTicket->load('event');

        return view('event_tickets.show', compact('eventTicket'));
    }

    public function edit(EventTicket $eventTicket)
    {
        if (! Auth::user()->can('edit event ticket') || ! $this->owns($eventTicket)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('event_tickets.edit', $this->formData() + compact('eventTicket'));
    }

    public function update(Request $request, EventTicket $eventTicket)
    {
        if (! Auth::user()->can('edit event ticket') || ! $this->owns($eventTicket)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'ticket_code' => 'required|max:255',
            'attendee_name' => 'required|max:255',
            'attendee_email' => 'nullable|email',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $eventTicket->update($request->only([
            'event_id', 'ticket_code', 'attendee_name', 'attendee_email', 'price',
            'status', 'checked_in_at', 'notes',
        ]));

        return redirect()->route('event-tickets.show', $eventTicket)->with('success', __('Event ticket successfully updated.'));
    }

    public function destroy(EventTicket $eventTicket)
    {
        if (! Auth::user()->can('delete event ticket') || ! $this->owns($eventTicket)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $eventTicket->delete();

        return redirect()->route('event-tickets.index')->with('success', __('Event ticket successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'statuses' => EventTicket::$statuses,
            'events' => Event::where('created_by', Auth::user()->creatorId())->pluck('title', 'id'),
        ];
    }

    protected function owns(EventTicket $eventTicket): bool
    {
        return (int) $eventTicket->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(EventTicket $eventTicket): bool
    {
        return $this->owns($eventTicket) && (Auth::user()->can('manage event ticket') || Auth::user()->can('show event ticket'));
    }
}

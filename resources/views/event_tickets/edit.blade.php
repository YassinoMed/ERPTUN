<form method="POST" action="{{ route('event-tickets.update', $eventTicket) }}">@csrf @method('PUT')
    <div class="row">
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Event') }}</label><select name="event_id" class="form-control">@foreach($events as $id => $label)<option value="{{ $id }}" @selected((int) $eventTicket->event_id === (int) $id)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Ticket Code') }}</label><input type="text" name="ticket_code" value="{{ $eventTicket->ticket_code }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Attendee Name') }}</label><input type="text" name="attendee_name" value="{{ $eventTicket->attendee_name }}" class="form-control" required></div>
        <div class="col-md-6 form-group"><label class="form-label">{{ __('Attendee Email') }}</label><input type="email" name="attendee_email" value="{{ $eventTicket->attendee_email }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Price') }}</label><input type="number" step="0.01" name="price" value="{{ $eventTicket->price }}" class="form-control"></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Status') }}</label><select name="status" class="form-control">@foreach($statuses as $key => $label)<option value="{{ $key }}" @selected($eventTicket->status === $key)>{{ __($label) }}</option>@endforeach</select></div>
        <div class="col-md-4 form-group"><label class="form-label">{{ __('Checked In At') }}</label><input type="datetime-local" name="checked_in_at" value="{{ $eventTicket->checked_in_at ? \Carbon\Carbon::parse($eventTicket->checked_in_at)->format('Y-m-d\\TH:i') : '' }}" class="form-control"></div>
        <div class="col-12 form-group"><label class="form-label">{{ __('Notes') }}</label><textarea name="notes" class="form-control" rows="3">{{ $eventTicket->notes }}</textarea></div>
    </div><div class="text-end"><button type="submit" class="btn btn-primary">{{ __('Update') }}</button></div>
</form>

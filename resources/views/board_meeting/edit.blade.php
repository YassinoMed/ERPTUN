{{ Form::model($boardMeeting, ['route' => ['board-meeting.update', $boardMeeting->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{ Form::label('branch_id', __('Branch'), ['class' => 'form-label']) }}<x-required></x-required>
                <select class="form-control select" name="branch_id" required>
                    <option value="">{{ __('Select Branch') }}</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $boardMeeting->branch_id == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{ Form::label('title', __('Meeting Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{ Form::label('meeting_date', __('Meeting Date'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::date('meeting_date', $boardMeeting->meeting_date, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{ Form::label('meeting_time', __('Meeting Time'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::time('meeting_time', $boardMeeting->meeting_time, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('status', $statuses, $boardMeeting->status, ['class' => 'form-control select', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                {{ Form::label('location', __('Location'), ['class' => 'form-label']) }}
                {{ Form::text('location', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-12 col-12">
            <div class="form-group">
                {{ Form::label('meeting_link', __('Meeting Link'), ['class' => 'form-label']) }}
                {{ Form::text('meeting_link', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-12 col-12">
            <div class="form-group">
                {{ Form::label('attendee_ids', __('Board Members'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('attendee_ids[]', $employees, $selectedEmployees, ['class' => 'form-control select', 'multiple' => 'multiple', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('agenda', __('Agenda'), ['class' => 'form-label']) }}
                {{ Form::textarea('agenda', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('minutes', __('Minutes'), ['class' => 'form-label']) }}
                {{ Form::textarea('minutes', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('resolution_summary', __('Decision Summary'), ['class' => 'form-label']) }}
                {{ Form::textarea('resolution_summary', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('external_guests', __('External Guests'), ['class' => 'form-label']) }}
                {{ Form::textarea('external_guests', null, ['class' => 'form-control', 'placeholder' => __('One guest per line')]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

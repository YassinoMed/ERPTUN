{{ Form::open(['url' => 'internal-itsm', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('subject', __('Subject'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('subject', null, ['class' => 'form-control', 'required']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('ticket_type', __('Type'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('ticket_type', $ticketTypes, null, ['class' => 'form-control select', 'required']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, 'Open', ['class' => 'form-control select', 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('user', __('Assignee'), ['class' => 'form-label']) }}{{ Form::select('user', $users, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('support_category_id', __('Category'), ['class' => 'form-label']) }}{{ Form::select('support_category_id', $categories, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('configuration_item_id', __('Configuration Item'), ['class' => 'form-label']) }}{{ Form::select('configuration_item_id', $configurationItems, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}{{ Form::select('priority', $priorities, 'Medium', ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('impact_level', __('Impact'), ['class' => 'form-label']) }}{{ Form::select('impact_level', $impactLevels, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('urgency_level', __('Urgency'), ['class' => 'form-label']) }}{{ Form::select('urgency_level', $urgencyLevels, null, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('end_date', __('Target Date'), ['class' => 'form-label']) }}{{ Form::date('end_date', now()->format('Y-m-d'), ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('resolution_due_at', __('Resolution Due At'), ['class' => 'form-label']) }}{{ Form::datetimeLocal('resolution_due_at', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('description', __('Description'), ['class' => 'form-label']) }}{{ Form::textarea('description', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}

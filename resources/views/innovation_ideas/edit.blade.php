{{ Form::model($innovationIdea, ['route' => ['innovation-ideas.update', $innovationIdea->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6"><div class="form-group">{{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('category', __('Category'), ['class' => 'form-label']) }}{{ Form::text('category', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('submitted_by', __('Submitted By'), ['class' => 'form-label']) }}{{ Form::select('submitted_by', $employees, $innovationIdea->submitted_by, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('priority', __('Priority'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('priority', $priorities, $innovationIdea->priority, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-3"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, $innovationIdea->status, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-md-6"><div class="form-group">{{ Form::label('expected_value', __('Expected Value'), ['class' => 'form-label']) }}{{ Form::number('expected_value', null, ['class' => 'form-control', 'step' => '0.0001']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('description', __('Description'), ['class' => 'form-label']) }}{{ Form::textarea('description', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('business_case', __('Business Case'), ['class' => 'form-label']) }}{{ Form::textarea('business_case', null, ['class' => 'form-control']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('implementation_notes', __('Implementation Notes'), ['class' => 'form-label']) }}{{ Form::textarea('implementation_notes', null, ['class' => 'form-control']) }}</div></div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

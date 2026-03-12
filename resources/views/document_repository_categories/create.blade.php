{{ Form::open(['url' => 'document-repository-categories', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="form-group">
        {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
        {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Name')]) }}
    </div>
    <div class="form-check form-switch mt-3">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

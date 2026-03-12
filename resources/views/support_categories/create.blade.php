{{ Form::open(['route' => 'support-categories.store', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Category Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'maxlength' => 100]) }}
        </div>
        <div class="form-group col-md-8">
            {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3, 'maxlength' => 500]) }}
        </div>
        <div class="form-group col-md-4">
            {{ Form::label('color', __('Color'), ['class' => 'form-label']) }}
            {{ Form::color('color', '#3B82F6', ['class' => 'form-control form-control-color w-100']) }}
            <div class="form-check mt-3">
                {{ Form::checkbox('is_active', 1, true, ['class' => 'form-check-input', 'id' => 'support-category-active']) }}
                {{ Form::label('support-category-active', __('Active'), ['class' => 'form-check-label']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

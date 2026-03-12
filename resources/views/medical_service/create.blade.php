{{ Form::open(['route' => 'medical-services.store', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
    <div class="col-md-6">{{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}</div>
    <div class="col-md-6">{{ Form::label('code', __('Code'), ['class' => 'form-label']) }}{{ Form::text('code', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('service_type', __('Type'), ['class' => 'form-label']) }}{{ Form::text('service_type', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('product_service_id', __('Linked Product/Service'), ['class' => 'form-label']) }}{{ Form::select('product_service_id', $products, null, ['class' => 'form-control', 'placeholder' => __('Select')]) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('price', __('Price'), ['class' => 'form-label']) }}{{ Form::number('price', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('default_coverage_rate', __('Default Coverage %'), ['class' => 'form-label']) }}{{ Form::number('default_coverage_rate', 0, ['class' => 'form-control', 'step' => '0.01', 'min' => '0', 'max' => '100']) }}</div>
    <div class="col-12 mt-2">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}

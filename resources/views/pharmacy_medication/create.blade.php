{{ Form::open(['route' => 'pharmacy-medications.store', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
    <div class="col-md-6">{{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}</div>
    <div class="col-md-6">{{ Form::label('product_service_id', __('Linked Product/Service'), ['class' => 'form-label']) }}{{ Form::select('product_service_id', $products, null, ['class' => 'form-control', 'placeholder' => __('Select')]) }}</div>
    <div class="col-md-4 mt-2">{{ Form::label('sku', __('SKU'), ['class' => 'form-label']) }}{{ Form::text('sku', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-4 mt-2">{{ Form::label('dosage_form', __('Dosage Form'), ['class' => 'form-label']) }}{{ Form::text('dosage_form', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-4 mt-2">{{ Form::label('strength', __('Strength'), ['class' => 'form-label']) }}{{ Form::text('strength', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-4 mt-2">{{ Form::label('lot_number', __('Lot Number'), ['class' => 'form-label']) }}{{ Form::text('lot_number', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-4 mt-2">{{ Form::label('expiry_date', __('Expiry Date'), ['class' => 'form-label']) }}{{ Form::date('expiry_date', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-4 mt-2">{{ Form::label('unit_price', __('Unit Price'), ['class' => 'form-label']) }}{{ Form::number('unit_price', 0, ['class' => 'form-control', 'step' => '0.01']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('stock_quantity', __('Stock Quantity'), ['class' => 'form-label']) }}{{ Form::number('stock_quantity', 0, ['class' => 'form-control', 'step' => '0.01']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('reorder_level', __('Reorder Level'), ['class' => 'form-label']) }}{{ Form::number('reorder_level', 0, ['class' => 'form-control', 'step' => '0.01']) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}

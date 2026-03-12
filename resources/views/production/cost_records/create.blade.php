{{ Form::open(['url' => route('production.cost-records.store'), 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body"><div class="row">
    <div class="form-group col-md-6">{{ Form::label('production_order_id', __('Production Order'), ['class' => 'form-label']) }}{{ Form::select('production_order_id', $orders, null, ['class' => 'form-control', 'placeholder' => __('Select Production Order')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}{{ Form::select('product_id', $products, null, ['class' => 'form-control', 'placeholder' => __('Select Product')]) }}</div>
    <div class="form-group col-md-4">{{ Form::label('cost_type', __('Cost Type'), ['class' => 'form-label']) }}{{ Form::select('cost_type', ['material'=>__('Material'),'machine'=>__('Machine'),'labor'=>__('Labor'),'overhead'=>__('Overhead'),'subcontract'=>__('Subcontract')], 'material', ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-4">{{ Form::label('amount', __('Amount'), ['class' => 'form-label']) }}{{ Form::number('amount', 0, ['class' => 'form-control','step'=>'0.01','min'=>0]) }}</div>
    <div class="form-group col-md-4">{{ Form::label('quantity_basis', __('Quantity Basis'), ['class' => 'form-label']) }}{{ Form::number('quantity_basis', 0, ['class' => 'form-control','step'=>'0.0001','min'=>0]) }}</div>
    <div class="form-group col-md-12">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control','rows'=>3]) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}

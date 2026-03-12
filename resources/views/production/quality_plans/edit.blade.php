{{ Form::model($qualityPlan, ['route' => ['production.quality-plans.update', $qualityPlan->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body"><div class="row">
    <div class="form-group col-md-6">{{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}{{ Form::select('product_id', $products, null, ['class' => 'form-control', 'placeholder' => __('Select Product')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('production_routing_id', __('Routing'), ['class' => 'form-label']) }}{{ Form::select('production_routing_id', $routings, null, ['class' => 'form-control', 'placeholder' => __('Select Routing')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('name', __('Name'), ['class' => 'form-label']) }}{{ Form::text('name', null, ['class' => 'form-control','required']) }}</div>
    <div class="form-group col-md-3">{{ Form::label('check_stage', __('Stage'), ['class' => 'form-label']) }}{{ Form::select('check_stage', ['incoming'=>__('Incoming'),'in_process'=>__('In Process'),'final'=>__('Final')], null, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-3">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}{{ Form::select('status', ['active'=>__('Active'),'inactive'=>__('Inactive')], null, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-12">{{ Form::label('sampling_rule', __('Sampling Rule'), ['class' => 'form-label']) }}{{ Form::text('sampling_rule', null, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-12">{{ Form::label('acceptance_criteria', __('Acceptance Criteria'), ['class' => 'form-label']) }}{{ Form::textarea('acceptance_criteria', null, ['class' => 'form-control','rows'=>2]) }}</div>
    <div class="form-group col-md-12">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control','rows'=>2]) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Update') }}" class="btn btn-primary"></div>
{{ Form::close() }}

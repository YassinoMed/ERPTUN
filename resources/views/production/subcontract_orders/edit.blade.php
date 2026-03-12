{{ Form::model($subcontractOrder, ['route' => ['production.subcontract-orders.update', $subcontractOrder->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body"><div class="row">
    <div class="form-group col-md-6">{{ Form::label('production_order_id', __('Production Order'), ['class' => 'form-label']) }}{{ Form::select('production_order_id', $orders, null, ['class' => 'form-control', 'placeholder' => __('Select Production Order')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('production_routing_step_id', __('Routing Step'), ['class' => 'form-label']) }}{{ Form::select('production_routing_step_id', $steps, null, ['class' => 'form-control', 'placeholder' => __('Select Routing Step')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('vender_id', __('Vendor'), ['class' => 'form-label']) }}{{ Form::select('vender_id', $vendors, null, ['class' => 'form-control', 'placeholder' => __('Select Vendor')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}{{ Form::text('reference', null, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-4">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}{{ Form::select('status', ['draft'=>__('Draft'),'sent'=>__('Sent'),'in_progress'=>__('In Progress'),'received'=>__('Received'),'closed'=>__('Closed'),'cancelled'=>__('Cancelled')], null, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-4">{{ Form::label('quantity', __('Quantity'), ['class' => 'form-label']) }}{{ Form::number('quantity', null, ['class' => 'form-control','step'=>'0.0001','min'=>0.0001]) }}</div>
    <div class="form-group col-md-4">{{ Form::label('unit_cost', __('Unit Cost'), ['class' => 'form-label']) }}{{ Form::number('unit_cost', null, ['class' => 'form-control','step'=>'0.01','min'=>0]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('planned_send_date', __('Planned Send Date'), ['class' => 'form-label']) }}{{ Form::date('planned_send_date', $subcontractOrder->planned_send_date, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-6">{{ Form::label('planned_receive_date', __('Planned Receive Date'), ['class' => 'form-label']) }}{{ Form::date('planned_receive_date', $subcontractOrder->planned_receive_date, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-12">{{ Form::label('quality_notes', __('Quality Notes'), ['class' => 'form-label']) }}{{ Form::textarea('quality_notes', null, ['class' => 'form-control','rows'=>2]) }}</div>
    <div class="form-group col-md-12">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control','rows'=>2]) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Update') }}" class="btn btn-primary"></div>
{{ Form::close() }}

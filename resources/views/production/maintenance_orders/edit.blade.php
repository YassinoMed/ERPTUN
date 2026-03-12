{{ Form::model($maintenanceOrder, ['route' => ['production.maintenance-orders.update', $maintenanceOrder->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body"><div class="row">
    <div class="form-group col-md-6">{{ Form::label('work_center_id', __('Work Center'), ['class' => 'form-label']) }}{{ Form::select('work_center_id', $workCenters, null, ['class' => 'form-control', 'placeholder' => __('Select Work Center')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('industrial_resource_id', __('Resource'), ['class' => 'form-label']) }}{{ Form::select('industrial_resource_id', $resources, null, ['class' => 'form-control', 'placeholder' => __('Select Resource')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('assigned_to', __('Assigned To'), ['class' => 'form-label']) }}{{ Form::select('assigned_to', $employees, null, ['class' => 'form-control', 'placeholder' => __('Select Employee')]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}{{ Form::text('reference', null, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-4">{{ Form::label('type', __('Type'), ['class' => 'form-label']) }}{{ Form::select('type', ['preventive'=>__('Preventive'),'corrective'=>__('Corrective'),'predictive'=>__('Predictive')], null, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-4">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}{{ Form::select('status', ['open'=>__('Open'),'in_progress'=>__('In Progress'),'completed'=>__('Completed'),'cancelled'=>__('Cancelled')], null, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-4">{{ Form::label('cost', __('Cost'), ['class' => 'form-label']) }}{{ Form::number('cost', null, ['class' => 'form-control','step'=>'0.01','min'=>0]) }}</div>
    <div class="form-group col-md-6">{{ Form::label('planned_date', __('Planned Date'), ['class' => 'form-label']) }}{{ Form::date('planned_date', $maintenanceOrder->planned_date, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-6">{{ Form::label('completed_date', __('Completed Date'), ['class' => 'form-label']) }}{{ Form::date('completed_date', $maintenanceOrder->completed_date, ['class' => 'form-control']) }}</div>
    <div class="form-group col-md-6">{{ Form::label('downtime_minutes', __('Downtime Minutes'), ['class' => 'form-label']) }}{{ Form::number('downtime_minutes', null, ['class' => 'form-control','min'=>0]) }}</div>
    <div class="form-group col-md-12">{{ Form::label('checklist', __('Checklist'), ['class' => 'form-label']) }}{{ Form::textarea('checklist', null, ['class' => 'form-control','rows'=>2]) }}</div>
    <div class="form-group col-md-12">{{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}{{ Form::textarea('notes', null, ['class' => 'form-control','rows'=>2]) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Update') }}" class="btn btn-primary"></div>
{{ Form::close() }}

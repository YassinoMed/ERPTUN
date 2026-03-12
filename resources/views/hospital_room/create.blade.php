{{ Form::open(['route' => 'hospital-rooms.store', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
    <div class="col-md-6">{{ Form::label('name', __('Name'), ['class' => 'form-label']) }}{{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) }}</div>
    <div class="col-md-6">{{ Form::label('department', __('Department'), ['class' => 'form-label']) }}{{ Form::text('department', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('room_type', __('Room Type'), ['class' => 'form-label']) }}{{ Form::text('room_type', null, ['class' => 'form-control']) }}</div>
    <div class="col-md-6 mt-2">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}{{ Form::select('status', ['available' => __('Available'), 'maintenance' => __('Maintenance')], 'available', ['class' => 'form-control']) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}

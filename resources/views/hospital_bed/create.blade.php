{{ Form::open(['route' => 'hospital-beds.store', 'method' => 'post']) }}
<div class="modal-body"><div class="row">
    <div class="col-md-6">{{ Form::label('hospital_room_id', __('Room'), ['class' => 'form-label']) }}{{ Form::select('hospital_room_id', $rooms, null, ['class' => 'form-control', 'required' => 'required']) }}</div>
    <div class="col-md-6">{{ Form::label('bed_number', __('Bed Number'), ['class' => 'form-label']) }}{{ Form::text('bed_number', null, ['class' => 'form-control', 'required' => 'required']) }}</div>
    <div class="col-md-12 mt-2">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}{{ Form::select('status', ['available' => __('Available'), 'occupied' => __('Occupied'), 'maintenance' => __('Maintenance')], 'available', ['class' => 'form-control']) }}</div>
</div></div>
<div class="modal-footer"><input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal"><input type="submit" value="{{ __('Create') }}" class="btn btn-primary"></div>
{{ Form::close() }}

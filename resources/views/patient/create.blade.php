{{ Form::open(['route' => 'patients.store', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('first_name', __('First Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('first_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter first name')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('last_name', __('Last Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('last_name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter last name')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('customer_id', __('Linked Customer'), ['class' => 'form-label']) }}
                {{ Form::select('customer_id', $customers, null, ['class' => 'form-control', 'data-placeholder' => __('Select customer'), 'placeholder' => __('Select customer')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('gender', __('Gender'), ['class' => 'form-label']) }}
                {{ Form::select('gender', ['male' => __('Male'), 'female' => __('Female'), 'other' => __('Other')], null, ['class' => 'form-control', 'placeholder' => __('Select gender')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('cin', __('CIN'), ['class' => 'form-label']) }}
                {{ Form::text('cin', null, ['class' => 'form-control', 'placeholder' => __('Enter CIN')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('cnam_number', __('CNAM Number'), ['class' => 'form-label']) }}
                {{ Form::text('cnam_number', null, ['class' => 'form-control', 'placeholder' => __('Enter CNAM number')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('blood_group', __('Blood Group'), ['class' => 'form-label']) }}
                {{ Form::text('blood_group', null, ['class' => 'form-control', 'placeholder' => __('Enter blood group')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('birth_date', __('Birth Date'), ['class' => 'form-label']) }}
                {{ Form::date('birth_date', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('phone', __('Phone'), ['class' => 'form-label']) }}
                {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => __('Enter phone')]) }}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Enter email')]) }}
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
                {{ Form::textarea('address', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter address')]) }}
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('allergies', __('Allergies'), ['class' => 'form-label']) }}
                {{ Form::textarea('allergies', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter allergies')]) }}
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('photo', __('Photo'), ['class' => 'form-label']) }}
                {{ Form::file('photo', ['class' => 'form-control']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn btn-primary">
</div>
{{ Form::close() }}

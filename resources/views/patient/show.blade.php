@extends('layouts.admin')

@section('page-title')
    {{ $patient->first_name }} {{ $patient->last_name }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item"><a href="{{route('patients.index')}}">{{__('Patients')}}</a></li>
    <li class="breadcrumb-item">{{ $patient->first_name }} {{ $patient->last_name }}</li>
@endsection

@section('action-btn')
    <div class="float-end d-flex">
        @can('edit patient')
            <a href="#" class="btn btn-sm btn-info me-2" data-url="{{ route('patients.edit', $patient->id) }}" data-ajax-popup="true" data-size="lg" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-title="{{__('Edit Patient')}}">
                <i class="ti ti-pencil"></i>
            </a>
        @endcan
        @can('delete patient')
            {!! Form::open(['method' => 'DELETE', 'route' => ['patients.destroy', $patient->id],'id'=>'delete-form-'.$patient->id]) !!}
            <a href="#" class="btn btn-sm btn-danger bs-pass-para" data-bs-toggle="tooltip" title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$patient->id}}').submit();">
                <i class="ti ti-trash"></i>
            </a>
            {!! Form::close() !!}
        @endcan
    </div>
@endsection

@section('content')
    @php
        $avatarPath = \App\Models\Utility::get_file('uploads/avatar/');
        $photoUrl = $patient->photo_path ? asset(\Storage::url($patient->photo_path)) : $avatarPath.'avatar.png';
    @endphp
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $photoUrl }}" class="rounded border" width="120" height="120">
                        <h5 class="mt-3">{{ $patient->first_name }} {{ $patient->last_name }}</h5>
                        <p class="text-muted mb-0">{{ $patient->gender ? ucfirst($patient->gender) : __('Not specified') }}</p>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2 text-muted">{{__('CIN')}}</div>
                        <div class="col-6 mb-2">{{ $patient->cin ?? '-' }}</div>
                        <div class="col-6 mb-2 text-muted">{{__('CNAM')}}</div>
                        <div class="col-6 mb-2">{{ $patient->cnam_number ?? '-' }}</div>
                        <div class="col-6 mb-2 text-muted">{{__('Blood Group')}}</div>
                        <div class="col-6 mb-2">{{ $patient->blood_group ?? '-' }}</div>
                        <div class="col-6 mb-2 text-muted">{{__('Birth Date')}}</div>
                        <div class="col-6 mb-2">{{ $patient->birth_date ? \Auth::user()->dateFormat($patient->birth_date) : '-' }}</div>
                        <div class="col-6 mb-2 text-muted">{{__('Phone')}}</div>
                        <div class="col-6 mb-2">{{ $patient->phone ?? '-' }}</div>
                        <div class="col-6 mb-2 text-muted">{{__('Email')}}</div>
                        <div class="col-6 mb-2">{{ $patient->email ?? '-' }}</div>
                        <div class="col-12 mb-2 text-muted">{{__('Address')}}</div>
                        <div class="col-12 mb-2">{{ $patient->address ?? '-' }}</div>
                        <div class="col-12 mb-2 text-muted">{{__('Allergies')}}</div>
                        <div class="col-12 mb-2">{{ $patient->allergies ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{__('Consultations')}}</h5>
                    @can('create patient consultation')
                        <a href="#" data-size="lg" data-url="{{ route('patients.consultations.create', $patient->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Create')}}" data-title="{{__('Add Consultation')}}" class="btn btn-sm btn-primary">
                            <i class="ti ti-plus"></i>
                        </a>
                    @endcan
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{__('Date')}}</th>
                                <th>{{__('Doctor')}}</th>
                                <th>{{__('Title')}}</th>
                                <th>{{__('Diagnosis')}}</th>
                                <th>{{__('Next Visit')}}</th>
                                <th>{{__('Notes')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($consultations as $consultation)
                                <tr>
                                    <td>{{ \Auth::user()->dateFormat($consultation->consultation_date) }}</td>
                                    <td>{{ $consultation->doctor_name ?? '-' }}</td>
                                    <td>{{ $consultation->title ?? '-' }}</td>
                                    <td>{{ $consultation->diagnosis ?? '-' }}</td>
                                    <td>{{ $consultation->next_visit_date ? \Auth::user()->dateFormat($consultation->next_visit_date) : '-' }}</td>
                                    <td>{{ $consultation->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{__('No consultations available')}}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{__('Add Prescription')}}</h5>
                </div>
                <div class="card-body">
                    @can('create patient prescription')
                        {{ Form::open(['route' => ['consultations.prescriptions.store', 0], 'method' => 'post', 'id' => 'prescription-form']) }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('consultation_id', __('Consultation'), ['class' => 'form-label']) }}<x-required></x-required>
                                    <select name="consultation_id" class="form-control" id="prescription-consultation" required>
                                        <option value="">{{ __('Select consultation') }}</option>
                                        @foreach ($consultations as $consultation)
                                            <option value="{{ $consultation->id }}" data-store-url="{{ route('consultations.prescriptions.store', $consultation->id) }}">{{ \Auth::user()->dateFormat($consultation->consultation_date) }} - {{ $consultation->title ?? __('Consultation') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('medication_name', __('Medication Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::text('medication_name', null, ['class' => 'form-control', 'required' => 'required']) }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::label('dosage', __('Dosage'), ['class' => 'form-label']) }}
                                    {{ Form::text('dosage', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::label('frequency', __('Frequency'), ['class' => 'form-label']) }}
                                    {{ Form::text('frequency', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::label('duration', __('Duration'), ['class' => 'form-label']) }}
                                    {{ Form::text('duration', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
                                    {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                        </div>
                        {{ Form::close() }}
                    @else
                        <div class="text-muted">{{__('You do not have permission to add prescriptions.')}}</div>
                    @endcan
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{__('Add Lab Result')}}</h5>
                </div>
                <div class="card-body">
                    @can('create patient lab result')
                        {{ Form::open(['route' => ['patients.lab-results.store', $patient->id], 'method' => 'post']) }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('consultation_id', __('Consultation'), ['class' => 'form-label']) }}
                                    <select name="consultation_id" class="form-control">
                                        <option value="">{{ __('Select consultation') }}</option>
                                        @foreach ($consultations as $consultation)
                                            <option value="{{ $consultation->id }}">{{ \Auth::user()->dateFormat($consultation->consultation_date) }} - {{ $consultation->title ?? __('Consultation') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('test_name', __('Test Name'), ['class' => 'form-label']) }}<x-required></x-required>
                                    {{ Form::text('test_name', null, ['class' => 'form-control', 'required' => 'required']) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('result_value', __('Result'), ['class' => 'form-label']) }}
                                    {{ Form::text('result_value', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('unit', __('Unit'), ['class' => 'form-label']) }}
                                    {{ Form::text('unit', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('reference_range', __('Reference Range'), ['class' => 'form-label']) }}
                                    {{ Form::text('reference_range', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{ Form::label('result_date', __('Result Date'), ['class' => 'form-label']) }}
                                    {{ Form::date('result_date', null, ['class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
                                    {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                        </div>
                        {{ Form::close() }}
                    @else
                        <div class="text-muted">{{__('You do not have permission to add lab results.')}}</div>
                    @endcan
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{__('Lab Results')}}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{__('Result Date')}}</th>
                                <th>{{__('Test Name')}}</th>
                                <th>{{__('Result')}}</th>
                                <th>{{__('Unit')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($labResults as $labResult)
                                <tr>
                                    <td>{{ $labResult->result_date ? \Auth::user()->dateFormat($labResult->result_date) : '-' }}</td>
                                    <td>{{ $labResult->test_name }}</td>
                                    <td>{{ $labResult->result_value ?? '-' }}</td>
                                    <td>{{ $labResult->unit ?? '-' }}</td>
                                    <td>
                                        @can('delete patient lab result')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['patient-lab-results.destroy', $labResult->id],'id'=>'delete-lab-'.$labResult->id]) !!}
                                            <a href="#" class="btn btn-sm btn-danger bs-pass-para" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-lab-{{$labResult->id}}').submit();">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{__('No lab results available')}}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{__('Prescriptions')}}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{__('Consultation')}}</th>
                                <th>{{__('Medication')}}</th>
                                <th>{{__('Dosage')}}</th>
                                <th>{{__('Frequency')}}</th>
                                <th>{{__('Duration')}}</th>
                                <th>{{__('Notes')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $prescriptions = collect();
                                $consultationMap = $consultations->keyBy('id');
                                foreach ($consultations as $consultation) {
                                    $prescriptions = $prescriptions->merge($consultation->prescriptions);
                                }
                            @endphp
                            @forelse ($prescriptions as $prescription)
                                <tr>
                                    <td>
                                        @if($consultationMap->has($prescription->consultation_id))
                                            {{ \Auth::user()->dateFormat($consultationMap[$prescription->consultation_id]->consultation_date) }}
                                        @else
                                            {{ $prescription->consultation_id }}
                                        @endif
                                    </td>
                                    <td>{{ $prescription->medication_name }}</td>
                                    <td>{{ $prescription->dosage ?? '-' }}</td>
                                    <td>{{ $prescription->frequency ?? '-' }}</td>
                                    <td>{{ $prescription->duration ?? '-' }}</td>
                                    <td>{{ $prescription->notes ?? '-' }}</td>
                                    <td>
                                        @can('delete patient prescription')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['patient-prescriptions.destroy', $prescription->id],'id'=>'delete-prescription-'.$prescription->id]) !!}
                                            <a href="#" class="btn btn-sm btn-danger bs-pass-para" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-prescription-{{$prescription->id}}').submit();">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{__('No prescriptions available')}}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @can('create patient prescription')
                <script>
                    (function () {
                        var select = document.getElementById('prescription-consultation');
                        var form = document.getElementById('prescription-form');
                        if (!select || !form) {
                            return;
                        }
                        var updateAction = function () {
                            var option = select.options[select.selectedIndex];
                            if (option && option.dataset.storeUrl) {
                                form.action = option.dataset.storeUrl;
                            }
                        };
                        select.addEventListener('change', updateAction);
                        updateAction();
                    })();
                </script>
            @endcan
        </div>
    </div>
@endsection

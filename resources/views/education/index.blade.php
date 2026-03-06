@extends('layouts.admin')

@section('page-title')
    {{ __('Education') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Education') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('New Course') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.courses.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="code" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Training Type') }}</label>
                            <select name="training_type_id" class="form-control">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($trainingTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Trainer') }}</label>
                            <select name="trainer_id" class="form-control">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->firstname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Delivery Mode') }}</label>
                            <select name="delivery_mode" class="form-control" required>
                                <option value="classroom">{{ __('Classroom') }}</option>
                                <option value="online">{{ __('Online') }}</option>
                                <option value="blended">{{ __('Blended') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('New Module') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.modules.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Course') }}</label>
                            <select name="course_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Content URL') }}</label>
                            <input type="text" name="content_url" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Duration (minutes)') }}</label>
                            <input type="number" name="duration_minutes" class="form-control" min="0">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Order') }}</label>
                            <input type="number" name="sort_order" class="form-control" min="0">
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Enrollment') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.enrollments.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Course') }}</label>
                            <select name="course_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Employee') }}</label>
                            <select name="employee_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-control">
                                <option value="enrolled">{{ __('Enrolled') }}</option>
                                <option value="in_progress">{{ __('In progress') }}</option>
                                <option value="completed">{{ __('Completed') }}</option>
                                <option value="cancelled">{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Enrolled At') }}</label>
                            <input type="date" name="enrolled_at" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Completed At') }}</label>
                            <input type="date" name="completed_at" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Progress (%)') }}</label>
                            <input type="number" name="progress_percent" class="form-control" min="0" max="100">
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Session Planning') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.sessions.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Course') }}</label>
                            <select name="course_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Scheduled At') }}</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Duration (hours)') }}</label>
                            <input type="number" name="duration_hours" class="form-control" step="0.25" min="0">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Location') }}</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Attendance') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.attendances.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Session') }}</label>
                            <select name="session_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->id }} - {{ $session->scheduled_at }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Employee') }}</label>
                            <select name="employee_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-control" required>
                                <option value="present">{{ __('Present') }}</option>
                                <option value="absent">{{ __('Absent') }}</option>
                                <option value="late">{{ __('Late') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Note') }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Grades') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.grades.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Course') }}</label>
                            <select name="course_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Employee') }}</label>
                            <select name="employee_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Score') }}</label>
                            <input type="number" name="score" class="form-control" step="0.1" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Grade') }}</label>
                            <input type="text" name="grade" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Note') }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Certificates') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.certificates.issue') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Enrollment') }}</label>
                            <select name="enrollment_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($enrollments as $enrollment)
                                    <option value="{{ $enrollment->id }}">{{ $enrollment->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Send To Email') }}</label>
                            <input type="email" name="sent_to_email" class="form-control">
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Issue') }}</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Trainer Hours') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.trainer-hours.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Trainer') }}</label>
                            <select name="trainer_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->firstname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Course') }}</label>
                            <select name="course_id" class="form-control">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Session') }}</label>
                            <select name="session_id" class="form-control">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Hours') }}</label>
                            <input type="number" name="hours" class="form-control" step="0.25" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Rate') }}</label>
                            <input type="number" name="rate" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Declared At') }}</label>
                            <input type="date" name="declared_at" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Note') }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Trainer Invoice') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('education.trainer-invoices.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Trainer') }}</label>
                            <select name="trainer_id" class="form-control" required>
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}">{{ $trainer->firstname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Period Start') }}</label>
                            <input type="date" name="period_start" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Period End') }}</label>
                            <input type="date" name="period_end" class="form-control" required>
                        </div>
                        <button class="btn btn-primary w-100">{{ __('Generate') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Courses') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Mode') }}</th>
                                    <th>{{ __('Dates') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($courses as $course)
                                    <tr>
                                        <td>{{ $course->code }}</td>
                                        <td>{{ $course->name }}</td>
                                        <td>{{ ucfirst($course->delivery_mode) }}</td>
                                        <td>{{ $course->start_date }} - {{ $course->end_date }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Modules') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($modules as $module)
                                    <tr>
                                        <td>{{ optional($courses->firstWhere('id', $module->course_id))->name }}</td>
                                        <td>{{ $module->title }}</td>
                                        <td>{{ $module->duration_minutes }} {{ __('min') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Enrollments') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Progress') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($enrollments as $enrollment)
                                    <tr>
                                        <td>{{ optional($courses->firstWhere('id', $enrollment->course_id))->name }}</td>
                                        <td>{{ optional($employees->firstWhere('id', $enrollment->employee_id))->name }}</td>
                                        <td>{{ $enrollment->status }}</td>
                                        <td>{{ $enrollment->progress_percent }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Sessions') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Scheduled At') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sessions as $session)
                                    <tr>
                                        <td>{{ optional($courses->firstWhere('id', $session->course_id))->name }}</td>
                                        <td>{{ $session->scheduled_at }}</td>
                                        <td>{{ $session->duration_hours }} {{ __('h') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Attendances') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Session') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->session_id }}</td>
                                        <td>{{ optional($employees->firstWhere('id', $attendance->employee_id))->name }}</td>
                                        <td>{{ $attendance->status }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Grades') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Course') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Score') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($grades as $grade)
                                    <tr>
                                        <td>{{ optional($courses->firstWhere('id', $grade->course_id))->name }}</td>
                                        <td>{{ optional($employees->firstWhere('id', $grade->employee_id))->name }}</td>
                                        <td>{{ $grade->score }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Certificates') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Certificate') }}</th>
                                    <th>{{ __('Enrollment') }}</th>
                                    <th>{{ __('Issued') }}</th>
                                    <th>{{ __('Verify') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($certificates as $certificate)
                                    <tr>
                                        <td>{{ $certificate->certificate_number }}</td>
                                        <td>{{ $certificate->enrollment_id }}</td>
                                        <td>{{ $certificate->issued_at }}</td>
                                        <td>
                                            <a href="{{ route('education.certificates.verify', $certificate->verification_hash) }}" class="btn btn-sm btn-primary" target="_blank">
                                                {{ __('Open') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5>{{ __('Trainer Hours') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Trainer') }}</th>
                                    <th>{{ __('Hours') }}</th>
                                    <th>{{ __('Rate') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trainerHours as $hour)
                                    <tr>
                                        <td>{{ optional($trainers->firstWhere('id', $hour->trainer_id))->firstname }}</td>
                                        <td>{{ $hour->hours }}</td>
                                        <td>{{ $hour->rate }}</td>
                                        <td>{{ $hour->amount }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Trainer Invoices') }}</h5>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Trainer') }}</th>
                                    <th>{{ __('Period') }}</th>
                                    <th>{{ __('Hours') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trainerInvoices as $invoice)
                                    <tr>
                                        <td>{{ optional($trainers->firstWhere('id', $invoice->trainer_id))->firstname }}</td>
                                        <td>{{ $invoice->period_start }} - {{ $invoice->period_end }}</td>
                                        <td>{{ $invoice->total_hours }}</td>
                                        <td>{{ $invoice->total_amount }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

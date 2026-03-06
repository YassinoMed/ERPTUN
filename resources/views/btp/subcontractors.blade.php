@extends('layouts.admin')

@section('page-title')
    {{ __('BTP Subcontractors') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('BTP Subcontractors') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Outstanding') }}</h6>
                            <h3 class="mb-0">{{ \Auth::user()->priceFormat($totalOutstanding) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Retention') }}</h6>
                            <h3 class="mb-0">{{ \Auth::user()->priceFormat($totalRetention) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('VAT') }}</h6>
                            <h3 class="mb-0">{{ \Auth::user()->priceFormat($totalVat) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Subcontractor') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.subcontractors.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Contact Name') }}</label>
                            <input type="text" name="contact_name" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Email') }}</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Address') }}</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Invoice') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.subcontractors.invoices.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Subcontractor') }}</label>
                            <select name="subcontractor_id" class="form-control" required>
                                @foreach ($subcontractors as $subcontractor)
                                    <option value="{{ $subcontractor->id }}">{{ $subcontractor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Project') }}</label>
                            <select name="project_id" class="form-control" required>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Reference') }}</label>
                            <input type="text" name="reference" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Amount') }}</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Retention Rate (%)') }}</label>
                            <input type="number" step="0.01" name="retention_rate" class="form-control" value="10">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('VAT Rate (%)') }}</label>
                            <input type="number" step="0.01" name="vat_rate" class="form-control" value="19">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Due Date') }}</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Create Invoice') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Record Payment') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.subcontractors.payments.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Invoice') }}</label>
                            <select name="invoice_id" class="form-control" required>
                                @foreach ($invoices as $invoice)
                                    <option value="{{ $invoice->id }}">{{ $invoice->reference ?? $invoice->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Amount') }}</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Payment Date') }}</label>
                            <input type="date" name="payment_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Note') }}</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Payment') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Subcontractors') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Contact') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Email') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subcontractors as $subcontractor)
                                <tr>
                                    <td>{{ $subcontractor->name }}</td>
                                    <td>{{ $subcontractor->contact_name }}</td>
                                    <td>{{ $subcontractor->phone }}</td>
                                    <td>{{ $subcontractor->email }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No subcontractors found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Invoices') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Subcontractor') }}</th>
                                <th>{{ __('Project') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Retention') }}</th>
                                <th>{{ __('VAT') }}</th>
                                <th>{{ __('Total Due') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->reference }}</td>
                                    <td>{{ $invoice->subcontractor?->name }}</td>
                                    <td>{{ $projects->firstWhere('id', $invoice->project_id)?->project_name }}</td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->amount) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->retention_amount) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->vat_amount) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($invoice->total_due) }}</td>
                                    <td>{{ \Auth::user()->priceFormat($paymentTotals[$invoice->id] ?? 0) }}</td>
                                    <td>{{ ucfirst($invoice->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">{{ __('No invoices found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Recent Payments') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Invoice') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Payment Date') }}</th>
                                <th>{{ __('Note') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPayments as $payment)
                                <tr>
                                    <td>{{ $invoices->firstWhere('id', $payment->invoice_id)?->reference }}</td>
                                    <td>{{ \Auth::user()->priceFormat($payment->amount) }}</td>
                                    <td>{{ \Auth::user()->dateFormat($payment->payment_date) }}</td>
                                    <td>{{ $payment->note }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No payments recorded.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

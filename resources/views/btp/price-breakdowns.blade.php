@extends('layouts.admin')

@section('page-title')
    {{ __('BTP Price Breakdown') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('BTP Price Breakdown') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Items') }}</h6>
                            <h3 class="mb-0">{{ $totals['items'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Quotes') }}</h6>
                            <h3 class="mb-0">{{ $totals['quotes'] }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">{{ __('Total Quote Value') }}</h6>
                            <h3 class="mb-0">{{ \Auth::user()->priceFormat($totals['quote_total']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Price Item') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.price-breakdowns.items.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Unit') }}</label>
                            <input type="text" name="unit" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Unit Price') }}</label>
                            <input type="number" step="0.01" name="unit_price" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">{{ __('Save Item') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Quote') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.price-breakdowns.quotes.store') }}" method="post">
                        @csrf
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
                            <label class="form-label">{{ __('VAT Rate (%)') }}</label>
                            <input type="number" step="0.01" name="vat_rate" class="form-control" value="19">
                        </div>
                        <button class="btn btn-primary">{{ __('Create Quote') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Add Quote Item') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('btp.price-breakdowns.quote-items.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Quote') }}</label>
                            <select name="quote_id" class="form-control" required>
                                @foreach ($quotes as $quote)
                                    <option value="{{ $quote->id }}">{{ $quote->reference }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Item') }}</label>
                            <select name="price_item_id" class="form-control">
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Quantity') }}</label>
                            <input type="number" step="0.01" name="quantity" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Unit Price') }}</label>
                            <input type="number" step="0.01" name="unit_price" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                        <button class="btn btn-primary">{{ __('Add Item') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Price Items') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Unit') }}</th>
                                <th>{{ __('Unit Price') }}</th>
                                <th>{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ \Auth::user()->priceFormat($item->unit_price) }}</td>
                                    <td>{{ $item->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('No items found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Quotes') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Project') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th>{{ __('Items') }}</th>
                                <th>{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($quotes as $quote)
                                <tr>
                                    <td>{{ $quote->reference }}</td>
                                    <td>{{ $projects->firstWhere('id', $quote->project_id)?->project_name }}</td>
                                    <td>{{ \Auth::user()->dateFormat($quote->created_at) }}</td>
                                    <td>{{ $quote->items_count }}</td>
                                    <td>{{ \Auth::user()->priceFormat($quote->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('No quotes found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Quote Items') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Quote') }}</th>
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Line Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($quoteItems as $quoteItem)
                                <tr>
                                    <td>{{ $quotes->firstWhere('id', $quoteItem->quote_id)?->reference }}</td>
                                    <td>{{ $items->firstWhere('id', $quoteItem->price_item_id)?->name ?? $quoteItem->description }}</td>
                                    <td>{{ $quoteItem->quantity }}</td>
                                    <td>{{ \Auth::user()->priceFormat($quoteItem->line_total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('No quote items found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

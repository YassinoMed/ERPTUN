@extends('layouts.admin')

@section('page-title')
    {{ __('Housekeeping & Maintenance') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Housekeeping') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('New Task') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.housekeeping.tasks.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Room') }}</label>
                            <select name="room_id" class="form-control" required>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Priority') }}</label>
                            <select name="priority" class="form-control">
                                <option value="normal">{{ __('Normal') }}</option>
                                <option value="high">{{ __('High') }}</option>
                                <option value="urgent">{{ __('Urgent') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Scheduled At') }}</label>
                            <input type="datetime-local" name="scheduled_at" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Checklist Items') }}</label>
                            <select name="checklist_items[]" class="form-control" multiple>
                                @foreach($checklistItems as $item)
                                    <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary">{{ __('Create Task') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Report Issue') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.housekeeping.issues.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Room') }}</label>
                            <select name="room_id" class="form-control" required>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Priority') }}</label>
                            <select name="priority" class="form-control">
                                <option value="normal">{{ __('Normal') }}</option>
                                <option value="high">{{ __('High') }}</option>
                                <option value="urgent">{{ __('Urgent') }}</option>
                            </select>
                        </div>
                        <button class="btn btn-primary">{{ __('Submit') }}</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Inventory Movement') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('hotel.housekeeping.inventory.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Item') }}</label>
                            <select name="inventory_item_id" class="form-control" required>
                                @foreach($inventory as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Type') }}</label>
                            <select name="type" class="form-control">
                                <option value="issue">{{ __('Issue') }}</option>
                                <option value="receive">{{ __('Receive') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Quantity') }}</label>
                            <input type="number" step="0.01" name="quantity" class="form-control" required>
                        </div>
                        <button class="btn btn-primary">{{ __('Update') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Active Tasks') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Room') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Priority') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr>
                                    <td>{{ $task->room?->name }}</td>
                                    <td>{{ $task->status }}</td>
                                    <td>{{ $task->priority }}</td>
                                    <td>
                                        <form action="{{ route('hotel.housekeeping.tasks.update', $task->id) }}" method="post" class="d-flex gap-2">
                                            @csrf
                                            <select name="status" class="form-control">
                                                <option value="pending">{{ __('Pending') }}</option>
                                                <option value="in_progress">{{ __('In Progress') }}</option>
                                                <option value="completed">{{ __('Completed') }}</option>
                                            </select>
                                            <select name="room_status" class="form-control">
                                                <option value="">{{ __('Room Status') }}</option>
                                                <option value="dirty">{{ __('Dirty') }}</option>
                                                <option value="cleaning">{{ __('Cleaning') }}</option>
                                                <option value="clean">{{ __('Clean') }}</option>
                                                <option value="repair">{{ __('Repair') }}</option>
                                                <option value="blocked">{{ __('Blocked') }}</option>
                                            </select>
                                            <button class="btn btn-sm btn-primary">{{ __('Update') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Maintenance Issues') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Room') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Priority') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($issues as $issue)
                                <tr>
                                    <td>{{ $issue->room?->name }}</td>
                                    <td>{{ $issue->title }}</td>
                                    <td>{{ $issue->status }}</td>
                                    <td>{{ $issue->priority }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Inventory Levels') }}</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Item') }}</th>
                                <th>{{ __('On Hand') }}</th>
                                <th>{{ __('Reorder Level') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventory as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->quantity_on_hand }}</td>
                                    <td>{{ $item->reorder_level }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('Name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $ppmPortfolio->name ?? '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Owner') }}</label>
        <select name="owner_id" class="form-control">
            <option value="">{{ __('Select owner') }}</option>
            @foreach($owners as $ownerId => $ownerName)
                <option value="{{ $ownerId }}" @selected(old('owner_id', $ppmPortfolio->owner_id ?? '') == $ownerId)>{{ $ownerName }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Status') }}</label>
        <select name="status" class="form-control" required>
            @foreach($statuses as $statusKey => $statusLabel)
                <option value="{{ $statusKey }}" @selected(old('status', $ppmPortfolio->status ?? 'active') == $statusKey)>{{ __($statusLabel) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Priority') }}</label>
        <input type="text" name="priority" class="form-control" value="{{ old('priority', $ppmPortfolio->priority ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Start date') }}</label>
        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $ppmPortfolio->start_date ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('End date') }}</label>
        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $ppmPortfolio->end_date ?? '') }}">
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description', $ppmPortfolio->description ?? '') }}</textarea>
    </div>
</div>

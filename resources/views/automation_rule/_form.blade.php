@csrf
<div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">{{ __('Name') }}</label><input type="text" name="name" class="form-control" value="{{ old('name', $automationRule->name ?? '') }}" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">{{ __('Event Name') }}</label><input type="text" name="event_name" class="form-control" list="automation-event-catalog" value="{{ old('event_name', $automationRule->event_name ?? '') }}" required></div>
    <div class="col-md-2 mb-3"><label class="form-label">{{ __('Priority') }}</label><input type="number" name="priority" class="form-control" value="{{ old('priority', $automationRule->priority ?? 0) }}"></div>
    <div class="col-md-2 mb-3"><div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $automationRule->is_active ?? true) ? 'checked' : '' }}><label class="form-check-label">{{ __('Active') }}</label></div></div>
    <div class="col-12 mb-3"><label class="form-label">{{ __('Description') }}</label><textarea name="description" class="form-control">{{ old('description', $automationRule->description ?? '') }}</textarea></div>
    <div class="col-12">
        <div class="alert alert-light border">
            <div class="fw-semibold mb-1">{{ __('Available rule building blocks') }}</div>
            <div class="small text-muted mb-2">{{ __('Use existing event names and placeholders to keep rules consistent across modules.') }}</div>
            <div class="mb-2"><strong>{{ __('Events') }}:</strong> {{ implode(', ', $eventCatalog ?? []) }}</div>
            <div><strong>{{ __('Condition fields') }}:</strong> {{ implode(', ', $conditionFields ?? []) }}</div>
        </div>
    </div>
</div>
<datalist id="automation-event-catalog">
    @foreach($eventCatalog ?? [] as $eventName)
        <option value="{{ $eventName }}"></option>
    @endforeach
</datalist>
<div class="card mb-3"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">{{ __('Conditions') }}</h5><button type="button" class="btn btn-sm btn-primary" onclick="addConditionRow()">{{ __('Add Condition') }}</button></div><div class="card-body" id="conditions-wrap">
@php($conditions = old('conditions', $automationRule->conditions ?? [['field' => '', 'operator' => 'equals', 'value' => '']]))
@foreach($conditions as $index => $condition)
<div class="row mb-2 condition-row">
    <div class="col-md-4"><input type="text" name="conditions[{{ $index }}][field]" class="form-control" list="automation-condition-fields" value="{{ $condition['field'] ?? '' }}" placeholder="amount"></div>
    <div class="col-md-3"><select name="conditions[{{ $index }}][operator]" class="form-control">@foreach(['equals','not_equals','contains','greater_than','less_than','in'] as $op)<option value="{{ $op }}" {{ ($condition['operator'] ?? '') === $op ? 'selected' : '' }}>{{ $op }}</option>@endforeach</select></div>
    <div class="col-md-4"><input type="text" name="conditions[{{ $index }}][value]" class="form-control" value="{{ $condition['value'] ?? '' }}" placeholder="1000"></div>
    <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.condition-row').remove()">×</button></div>
</div>
@endforeach
</div></div>
<datalist id="automation-condition-fields">
    @foreach($conditionFields ?? [] as $fieldName)
        <option value="{{ $fieldName }}"></option>
    @endforeach
</datalist>
<div class="card mb-3"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">{{ __('Actions') }}</h5><button type="button" class="btn btn-sm btn-primary" onclick="addActionRow()">{{ __('Add Action') }}</button></div><div class="card-body" id="actions-wrap">
@php($actions = old('actions', $automationRule->actions ?? [['type' => 'notification', 'data' => ['message' => '']]]))
@foreach($actions as $index => $action)
<div class="border rounded p-3 mb-3 action-row">
    <div class="row">
        <div class="col-md-3"><select name="actions[{{ $index }}][type]" class="form-control">@foreach(($actionCatalog ?? ['notification' => 'notification','email' => 'email','task' => 'task','update_field' => 'update_field','webhook' => 'webhook','audit_log' => 'audit_log','approval_request' => 'approval_request','zapier_hook' => 'zapier_hook']) as $type => $label)<option value="{{ $type }}" {{ ($action['type'] ?? '') === $type ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-8"><input type="text" name="actions[{{ $index }}][data][message]" class="form-control" value="{{ data_get($action, 'data.message') ?? '' }}" placeholder="Message / body"></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.action-row').remove()">×</button></div>
        <div class="col-md-3 mt-2"><input type="text" name="actions[{{ $index }}][data][title]" class="form-control" value="{{ data_get($action, 'data.title') ?? '' }}" placeholder="Title"></div>
        <div class="col-md-3 mt-2"><input type="text" name="actions[{{ $index }}][data][field]" class="form-control" value="{{ data_get($action, 'data.field') ?? '' }}" placeholder="Field"></div>
        <div class="col-md-3 mt-2"><input type="text" name="actions[{{ $index }}][data][value]" class="form-control" value="{{ data_get($action, 'data.value') ?? '' }}" placeholder="Value"></div>
        <div class="col-md-3 mt-2"><input type="text" name="actions[{{ $index }}][data][url]" class="form-control" value="{{ data_get($action, 'data.url') ?? '' }}" placeholder="URL"></div>
    </div>
</div>
@endforeach
</div></div>
<div><button class="btn btn-primary">{{ __('Save') }}</button> <a href="{{ route('automation-rules.index') }}" class="btn btn-light">{{ __('Cancel') }}</a></div>
<script>
let conditionIndex = {{ count($conditions ?? []) }};
let actionIndex = {{ count($actions ?? []) }};
function addConditionRow(){document.getElementById('conditions-wrap').insertAdjacentHTML('beforeend', `<div class="row mb-2 condition-row"><div class="col-md-4"><input type="text" name="conditions[${conditionIndex}][field]" class="form-control" list="automation-condition-fields" placeholder="amount"></div><div class="col-md-3"><select name="conditions[${conditionIndex}][operator]" class="form-control"><option value="equals">equals</option><option value="not_equals">not_equals</option><option value="contains">contains</option><option value="greater_than">greater_than</option><option value="less_than">less_than</option><option value="in">in</option></select></div><div class="col-md-4"><input type="text" name="conditions[${conditionIndex}][value]" class="form-control" placeholder="1000"></div><div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.condition-row').remove()">×</button></div></div>`); conditionIndex++;}
function addActionRow(){document.getElementById('actions-wrap').insertAdjacentHTML('beforeend', `<div class="border rounded p-3 mb-3 action-row"><div class="row"><div class="col-md-3"><select name="actions[${actionIndex}][type]" class="form-control">@foreach(($actionCatalog ?? ['notification' => 'notification','email' => 'email','task' => 'task','update_field' => 'update_field','webhook' => 'webhook','audit_log' => 'audit_log','approval_request' => 'approval_request','zapier_hook' => 'zapier_hook']) as $type => $label)<option value="{{ $type }}">{{ $label }}</option>@endforeach</select></div><div class="col-md-8"><input type="text" name="actions[${actionIndex}][data][message]" class="form-control" placeholder="Message / body"></div><div class="col-md-1"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.action-row').remove()">×</button></div><div class="col-md-3 mt-2"><input type="text" name="actions[${actionIndex}][data][title]" class="form-control" placeholder="Title"></div><div class="col-md-3 mt-2"><input type="text" name="actions[${actionIndex}][data][field]" class="form-control" placeholder="Field"></div><div class="col-md-3 mt-2"><input type="text" name="actions[${actionIndex}][data][value]" class="form-control" placeholder="Value"></div><div class="col-md-3 mt-2"><input type="text" name="actions[${actionIndex}][data][url]" class="form-control" placeholder="URL"></div></div></div>`); actionIndex++;}
</script>

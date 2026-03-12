@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('Name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $approvalFlow->name ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">{{ __('Resource Type') }}</label>
        <input type="text" name="resource_type" class="form-control" value="{{ old('resource_type', $approvalFlow->resource_type ?? '') }}" placeholder="App\Models\Invoice">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Min Amount') }}</label>
        <input type="number" step="0.01" name="min_amount" class="form-control" value="{{ old('min_amount', $approvalFlow->min_amount ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Max Amount') }}</label>
        <input type="number" step="0.01" name="max_amount" class="form-control" value="{{ old('max_amount', $approvalFlow->max_amount ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Default SLA (hours)') }}</label>
        <input type="number" name="default_sla_hours" class="form-control" value="{{ old('default_sla_hours', $approvalFlow->default_sla_hours ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">{{ __('Escalation User ID') }}</label>
        <input type="number" name="escalation_user_id" class="form-control" value="{{ old('escalation_user_id', $approvalFlow->escalation_user_id ?? '') }}">
    </div>
    <div class="col-md-3 mb-3">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="allow_delegation" value="1" {{ old('allow_delegation', $approvalFlow->allow_delegation ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">{{ __('Allow Delegation') }}</label>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="form-check mt-4">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $approvalFlow->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label">{{ __('Active') }}</label>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Approval Steps') }}</h5>
        <button type="button" class="btn btn-sm btn-primary" onclick="addApprovalStep()">{{ __('Add Step') }}</button>
    </div>
    <div class="card-body">
        <div id="approval-steps">
            @php($steps = old('steps', isset($approvalFlow) ? $approvalFlow->steps->toArray() : [['name' => '', 'approver_type' => 'user']]))
            @foreach($steps as $index => $step)
                <div class="border rounded p-3 mb-3 approval-step-row">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <input type="text" name="steps[{{ $index }}][name]" class="form-control" value="{{ $step['name'] ?? '' }}" placeholder="{{ __('Step name') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="steps[{{ $index }}][approver_type]" class="form-control">
                                <option value="user" {{ ($step['approver_type'] ?? '') === 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
                                <option value="role" {{ ($step['approver_type'] ?? '') === 'role' ? 'selected' : '' }}>{{ __('Role') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="number" name="steps[{{ $index }}][approver_id]" class="form-control" value="{{ $step['approver_id'] ?? '' }}" placeholder="{{ __('Approver ID') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="number" name="steps[{{ $index }}][sla_hours]" class="form-control" value="{{ $step['sla_hours'] ?? '' }}" placeholder="{{ __('SLA hours') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="number" name="steps[{{ $index }}][escalation_user_id]" class="form-control" value="{{ $step['escalation_user_id'] ?? '' }}" placeholder="{{ __('Escalation user') }}">
                        </div>
                        <div class="col-md-1 mb-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.approval-step-row').remove()">×</button>
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="number" step="0.01" name="steps[{{ $index }}][threshold_min]" class="form-control" value="{{ $step['threshold_min'] ?? '' }}" placeholder="{{ __('Threshold min') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="number" step="0.01" name="steps[{{ $index }}][threshold_max]" class="form-control" value="{{ $step['threshold_max'] ?? '' }}" placeholder="{{ __('Threshold max') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" name="steps[{{ $index }}][rule_note]" class="form-control" value="{{ data_get($step, 'rule.note') ?? ($step['rule_note'] ?? '') }}" placeholder="{{ __('Step note / rule explanation') }}">
                        </div>
                        <div class="col-md-2 mb-2">
                            <div class="form-check pt-2">
                                <input type="checkbox" class="form-check-input" name="steps[{{ $index }}][require_reject_reason]" value="1" {{ !empty($step['require_reject_reason']) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ __('Reject reason') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
    <a href="{{ route('approval-flows.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
</div>

<script>
    let approvalStepIndex = {{ count($steps ?? []) }};
    function addApprovalStep() {
        const container = document.getElementById('approval-steps');
        const html = `
            <div class="border rounded p-3 mb-3 approval-step-row">
                <div class="row">
                    <div class="col-md-3 mb-2"><input type="text" name="steps[${approvalStepIndex}][name]" class="form-control" placeholder="Step name"></div>
                    <div class="col-md-2 mb-2">
                        <select name="steps[${approvalStepIndex}][approver_type]" class="form-control">
                            <option value="user">User</option>
                            <option value="role">Role</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2"><input type="number" name="steps[${approvalStepIndex}][approver_id]" class="form-control" placeholder="Approver ID"></div>
                    <div class="col-md-2 mb-2"><input type="number" name="steps[${approvalStepIndex}][sla_hours]" class="form-control" placeholder="SLA hours"></div>
                    <div class="col-md-2 mb-2"><input type="number" name="steps[${approvalStepIndex}][escalation_user_id]" class="form-control" placeholder="Escalation user"></div>
                    <div class="col-md-1 mb-2 text-end"><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.approval-step-row').remove()">×</button></div>
                    <div class="col-md-2 mb-2"><input type="number" step="0.01" name="steps[${approvalStepIndex}][threshold_min]" class="form-control" placeholder="Threshold min"></div>
                    <div class="col-md-2 mb-2"><input type="number" step="0.01" name="steps[${approvalStepIndex}][threshold_max]" class="form-control" placeholder="Threshold max"></div>
                    <div class="col-md-6 mb-2"><input type="text" name="steps[${approvalStepIndex}][rule_note]" class="form-control" placeholder="Step note / rule explanation"></div>
                    <div class="col-md-2 mb-2"><div class="form-check pt-2"><input type="checkbox" class="form-check-input" name="steps[${approvalStepIndex}][require_reject_reason]" value="1"><label class="form-check-label">Reject reason</label></div></div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        approvalStepIndex++;
    }
</script>

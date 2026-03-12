@extends('layouts.admin')
@section('page-title', __('Security Center'))
@section('content')
<div class="row">
    <div class="col-12 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-1">{{ __('Security Operations') }}</h5>
            <p class="text-muted mb-0">{{ __('Manage access scope, two-factor protection, session hygiene and sensitive activity from one workspace.') }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form method="POST" action="{{ route('core.security.cache.warm') }}">@csrf <button class="btn btn-light">{{ __('Warm Cache') }}</button></form>
            <form method="POST" action="{{ route('core.security.cache.flush') }}">@csrf <button class="btn btn-outline-secondary">{{ __('Flush Cache') }}</button></form>
            <form method="POST" action="{{ route('core.security.sessions.revoke-all') }}">@csrf <button class="btn btn-outline-danger">{{ __('Revoke All Active Sessions') }}</button></form>
            <form method="POST" action="{{ route('core.security.scan') }}">
                @csrf
                <button class="btn btn-primary">{{ __('Scan Data Quality') }}</button>
            </form>
        </div>
    </div>

    <div class="col-12 mb-3">
        <div class="row g-3">
            <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">{{ __('Active sessions') }}</div><div class="h4 mb-0">{{ $sessionSummary['active'] ?? 0 }}</div></div></div></div>
            <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">{{ __('Closed sessions') }}</div><div class="h4 mb-0">{{ $sessionSummary['closed'] ?? 0 }}</div></div></div></div>
            <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">{{ __('Sensitive logs') }}</div><div class="h4 mb-0">{{ $sessionSummary['sensitive_logs'] ?? 0 }}</div></div></div></div>
            <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">{{ __('Cached references') }}</div><div class="h4 mb-0">{{ array_sum($cacheSummary ?? []) }}</div></div></div></div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Active Sessions') }}</h5>
                <span class="badge bg-light text-dark">{{ $sessions->count() }} {{ __('tracked') }}</span>
            </div>
            <div class="card-body table-border-style">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Context') }}</th>
                        <th>{{ __('Last Seen') }}</th>
                        <th class="text-end">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ optional($session->user)->name ?: ('#'.$session->user_id) }}</div>
                                <small class="text-muted">{{ optional($session->user)->email }}</small>
                            </td>
                            <td>
                                <div>{{ $session->ip_address ?: __('Unknown IP') }}</div>
                                <small class="text-muted text-break">{{ \Illuminate\Support\Str::limit($session->user_agent, 60) }}</small>
                            </td>
                            <td>
                                <div>{{ optional($session->last_seen_at)->diffForHumans() }}</div>
                                <small class="text-muted">{{ optional($session->login_at)->format('Y-m-d H:i') }}</small>
                            </td>
                            <td class="text-end">
                                @if($session->is_active)
                                    <div class="d-inline-flex gap-2">
                                    <form method="POST" action="{{ route('core.security.session.revoke', $session) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger">{{ __('Revoke') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('core.security.user-sessions.revoke', $session->user_id) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-light">{{ __('Revoke User Sessions') }}</button>
                                    </form>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted">{{ __('Closed') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">{{ __('No session logs yet.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Sensitive Access Logs') }}</h5></div>
            <div class="card-body table-border-style">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Resource') }}</th>
                        <th>{{ __('When') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($accessLogs as $log)
                        <tr>
                            <td>{{ $log->action }}</td>
                            <td>{{ class_basename($log->resource_type) }}#{{ $log->resource_id }}</td>
                            <td>{{ optional($log->created_at)->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">{{ __('No sensitive access logs yet.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Data Quality Issues') }}</h5></div>
            <div class="card-body table-border-style">
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ __('Module') }}</th>
                        <th>{{ __('Issue') }}</th>
                        <th class="text-end">{{ __('Action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($issues as $issue)
                        <tr>
                            <td>{{ $issue->module }}</td>
                            <td>{{ $issue->issue_type }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <form method="POST" action="{{ route('core.security.issue.archive', $issue) }}">@csrf <button class="btn btn-sm btn-light">{{ __('Archive') }}</button></form>
                                    <form method="POST" action="{{ route('core.security.issue.merge', $issue) }}">@csrf <button class="btn btn-sm btn-warning">{{ __('Merge') }}</button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">{{ __('No data quality issues found.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Cache Snapshot') }}</h5></div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($cacheSummary as $segment => $count)
                        <div class="col-md-6">
                            <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                                <span class="text-capitalize">{{ str_replace('_', ' ', $segment) }}</span>
                                <span class="badge bg-light text-dark">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Access Scopes') }}</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('core.security.scope.store') }}" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label class="form-label">{{ __('User') }}</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">{{ __('Select user') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label">{{ __('Scope type') }}</label>
                            <select name="scope_type" class="form-control js-scope-type" required>
                                <option value="branch">{{ __('Branch') }}</option>
                                <option value="warehouse">{{ __('Warehouse') }}</option>
                                <option value="department">{{ __('Department / Service') }}</option>
                                <option value="service">{{ __('Catalog Service') }}</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label">{{ __('Scope IDs') }}</label>
                            <select name="scope_ids[]" class="form-control js-scope-ids" multiple required size="8">
                                @foreach($accessScopeOptions as $type => $options)
                                    @foreach($options as $id => $label)
                                        <option value="{{ $id }}" data-scope-type="{{ $type }}">{{ ucfirst($type) }}: {{ $label }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('If no scope exists for a type, the user keeps unrestricted access for that type.') }}</small>
                        </div>
                        <div class="col-md-12 mb-2">
                            <input type="text" name="notes" class="form-control" placeholder="{{ __('Notes') }}">
                        </div>
                    </div>
                    <button class="btn btn-primary">{{ __('Save Scope Rules') }}</button>
                </form>
                <div class="accordion" id="scopeAccordion">
                    @forelse($users as $user)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $user->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $user->id }}">
                                    {{ $user->name }} <span class="ms-2 text-muted small">{{ $user->email }}</span>
                                </button>
                            </h2>
                            <div id="collapse{{ $user->id }}" class="accordion-collapse collapse" data-bs-parent="#scopeAccordion">
                                <div class="accordion-body">
                                    @php($userScopes = $accessScopes->get($user->id, collect()))
                                    @forelse($userScopes as $scope)
                                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                                            <div>
                                                <div class="fw-semibold">{{ ucfirst($scope->scope_type) }}</div>
                                                <small class="text-muted">{{ $scopeMeta[$scope->scope_type][$scope->scope_id] ?? ($scope->scope_type.' #'.$scope->scope_id) }}</small>
                                            </div>
                                            <form method="POST" action="{{ route('core.security.scope.destroy', $scope) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">{{ __('Remove') }}</button>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="text-muted">{{ __('No scoped restrictions configured.') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">{{ __('No users available for scope assignment.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('2FA & IP Restrictions') }}</h5></div>
            <div class="card-body">
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ __('Two-factor authentication') }}</div>
                            <small class="text-muted">
                                @if($twoFactor)
                                    {{ __('Enabled via :provider', ['provider' => strtoupper($twoFactor->provider)]) }}
                                    <span class="d-block mt-1">{{ __('Configured on :date', ['date' => optional($twoFactor->enabled_at)->format('Y-m-d H:i')]) }}</span>
                                @else
                                    {{ __('Not configured') }}
                                @endif
                            </small>
                        </div>
                        <span class="badge {{ $twoFactor ? 'bg-success' : 'bg-light text-dark' }}">{{ $twoFactor ? __('Active') : __('Inactive') }}</span>
                    </div>
                    <form method="POST" action="{{ route('core.security.twofactor.store') }}" class="mt-3">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <select name="provider" class="form-control">
                                    <option value="email">{{ __('Email') }}</option>
                                    <option value="totp">{{ __('TOTP') }}</option>
                                    <option value="sms">{{ __('SMS') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="secret" class="form-control" placeholder="{{ __('Secret / code seed') }}">
                            </div>
                        </div>
                        <button class="btn btn-primary">{{ $twoFactor ? __('Update 2FA') : __('Enable 2FA') }}</button>
                    </form>
                    @if($twoFactor)
                        <form method="POST" action="{{ route('core.security.twofactor.verify') }}" class="mt-3">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="code" class="form-control" placeholder="{{ __('Enter verification or backup code') }}">
                                <button class="btn btn-outline-primary">{{ __('Verify') }}</button>
                            </div>
                        </form>
                        @if(!empty($twoFactor->backup_codes))
                            <div class="mt-3">
                                <div class="fw-semibold mb-2">{{ __('Backup codes') }} <span class="text-muted small">({{ count($twoFactor->backup_codes) }} {{ __('remaining') }})</span></div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($twoFactor->backup_codes as $code)
                                        <span class="badge bg-light text-dark">{{ $code }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <form method="POST" action="{{ route('core.security.twofactor.backup') }}">@csrf <button class="btn btn-sm btn-light">{{ __('Regenerate backup codes') }}</button></form>
                            <form method="POST" action="{{ route('core.security.twofactor.disable') }}">@csrf <button class="btn btn-sm btn-outline-danger">{{ __('Disable 2FA') }}</button></form>
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('core.security.ip.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-2"><input type="text" name="ip_address" class="form-control" placeholder="{{ __('IP address') }}"></div>
                        <div class="col-md-6 mb-2"><input type="text" name="description" class="form-control" placeholder="{{ __('Description') }}"></div>
                        <div class="col-md-6 mb-2"><input type="datetime-local" name="expires_at" class="form-control"></div>
                        <div class="col-md-6 mb-2">
                            <div class="form-check pt-2">
                                <input class="form-check-input" type="checkbox" name="is_whitelist" value="1" checked>
                                <label class="form-check-label">{{ __('Whitelist') }}</label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-secondary">{{ __('Add IP Restriction') }}</button>
                </form>

                <hr>
                <ul class="list-group">
                    @forelse($ipRestrictions as $restriction)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $restriction->ip_address }} <span class="text-muted">({{ $restriction->description }})</span></span>
                            <span class="badge {{ $restriction->is_whitelist ? 'bg-success' : 'bg-danger' }}">{{ $restriction->is_whitelist ? __('Allow') : __('Block') }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">{{ __('No IP rules configured.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">{{ __('Archive Registry') }}</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('core.security.archive.store') }}" class="mb-4">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label class="form-label">{{ __('Record type') }}</label>
                            <select name="record_type" class="form-control js-archive-type" required>
                                <option value="customer">{{ __('Customer') }}</option>
                                <option value="vender">{{ __('Vendor') }}</option>
                                <option value="product_service">{{ __('Product / Service') }}</option>
                                <option value="patient">{{ __('Patient') }}</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="form-label">{{ __('Record') }}</label>
                            <select name="record_id" class="form-control js-archive-records" required size="8">
                                @foreach($archiveOptions as $type => $records)
                                    @foreach($records as $id => $label)
                                        <option value="{{ $id }}" data-archive-type="{{ $type }}">{{ $label }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-2">
                            <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('Reason for archival') }}"></textarea>
                        </div>
                    </div>
                    <button class="btn btn-outline-dark">{{ __('Archive Record') }}</button>
                </form>

                <div class="table-border-style">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Record') }}</th>
                            <th>{{ __('Archived') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($archivedRecords as $archivedRecord)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $archivedRecord->display_name ?: (class_basename($archivedRecord->record_type).'#'.$archivedRecord->record_id) }}</div>
                                    <small class="text-muted">{{ class_basename($archivedRecord->record_type) }}#{{ $archivedRecord->record_id }}</small>
                                    @if($archivedRecord->reason)
                                        <div class="small text-muted mt-1">{{ $archivedRecord->reason }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ optional($archivedRecord->archived_at)->diffForHumans() }}</div>
                                    @if($archivedRecord->restored_at)
                                        <small class="text-success">{{ __('Restored :date', ['date' => $archivedRecord->restored_at->format('Y-m-d H:i')]) }}</small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(!$archivedRecord->restored_at)
                                        <form method="POST" action="{{ route('core.security.archive.restore', $archivedRecord) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-primary">{{ __('Restore') }}</button>
                                        </form>
                                    @else
                                        <span class="badge bg-light text-success">{{ __('Restored') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">{{ __('No archived records tracked yet.') }}</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scopeType = document.querySelector('.js-scope-type');
    const scopeIds = document.querySelector('.js-scope-ids');
    const archiveType = document.querySelector('.js-archive-type');
    const archiveRecords = document.querySelector('.js-archive-records');

    if (!scopeType || !scopeIds) {
        return;
    }

    const syncScopeOptions = () => {
        const activeType = scopeType.value;
        Array.from(scopeIds.options).forEach((option) => {
            const visible = option.dataset.scopeType === activeType;
            option.hidden = !visible;
            if (!visible) {
                option.selected = false;
            }
        });
    };

    scopeType.addEventListener('change', syncScopeOptions);
    syncScopeOptions();

    const syncArchiveOptions = () => {
        if (!archiveType || !archiveRecords) {
            return;
        }

        const activeType = archiveType.value;
        Array.from(archiveRecords.options).forEach((option) => {
            const visible = option.dataset.archiveType === activeType;
            option.hidden = !visible;
            if (!visible) {
                option.selected = false;
            }
        });

        const firstVisible = Array.from(archiveRecords.options).find((option) => !option.hidden);
        if (firstVisible) {
            firstVisible.selected = true;
        }
    };

    if (archiveType && archiveRecords) {
        archiveType.addEventListener('change', syncArchiveOptions);
        syncArchiveOptions();
    }
});
</script>
@endsection

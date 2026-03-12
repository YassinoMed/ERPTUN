<?php

namespace App\Http\Controllers;

use App\Models\ArchivedRecord;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\DataQualityIssue;
use App\Models\Department;
use App\Models\Patient;
use App\Models\ProductService;
use App\Models\Security\IpRestriction;
use App\Models\Security\TwoFactorAuth;
use App\Models\SensitiveAccessLog;
use App\Models\User;
use App\Models\UserAccessScope;
use App\Models\UserSessionLog;
use App\Models\Vender;
use App\Models\warehouse;
use App\Services\Core\AccessScopeService;
use App\Services\Core\CoreArchiveService;
use App\Services\Core\CoreCacheService;
use App\Services\Core\DataQualityService;
use App\Services\Core\SecurityAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CoreSecurityController extends Controller
{
    public function __construct(
        private readonly DataQualityService $dataQuality,
        private readonly AccessScopeService $accessScopes,
        private readonly SecurityAccessService $securityAccess,
        private readonly CoreCacheService $coreCache,
        private readonly CoreArchiveService $archiveService
    ) {
    }

    public function index()
    {
        $this->authorizeAccess('manage security center');
        $creatorId = \Auth::user()->creatorId();
        $sessions = UserSessionLog::query()->with('user')->where('created_by', $creatorId)->latest('login_at')->limit(50)->get();
        $accessLogs = SensitiveAccessLog::query()->where('created_by', \Auth::user()->creatorId())->latest('created_at')->limit(50)->get();
        $issues = DataQualityIssue::query()->where('created_by', \Auth::user()->creatorId())->latest('id')->limit(50)->get();
        $twoFactor = TwoFactorAuth::query()->where('user_id', \Auth::id())->first();
        $ipRestrictions = IpRestriction::query()->where('user_id', \Auth::id())->latest('id')->get();
        $sessionSummary = [
            'active' => UserSessionLog::query()->where('created_by', $creatorId)->where('is_active', true)->count(),
            'closed' => UserSessionLog::query()->where('created_by', $creatorId)->where('is_active', false)->count(),
            'sensitive_logs' => SensitiveAccessLog::query()->where('created_by', $creatorId)->count(),
        ];
        $cacheSummary = $this->coreCache->snapshot($creatorId);
        $users = User::query()
            ->where(function ($query) use ($creatorId) {
                $query->where('id', $creatorId)->orWhere('created_by', $creatorId);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'type']);
        $accessScopeOptions = $this->accessScopes->availableScopes($creatorId);
        $accessScopes = $this->accessScopes->groupedScopesForCreator($creatorId);
        $archivedRecords = ArchivedRecord::query()
            ->where('created_by', $creatorId)
            ->latest('archived_at')
            ->limit(25)
            ->get();
        $archiveOptions = $this->archiveService->archiveOptions($creatorId);
        $scopeMeta = [
            'branch' => Branch::query()->where('created_by', $creatorId)->pluck('name', 'id'),
            'warehouse' => warehouse::query()->where('created_by', $creatorId)->pluck('name', 'id'),
            'department' => Department::query()->where('created_by', $creatorId)->pluck('name', 'id'),
            'service' => ProductService::query()->where('created_by', $creatorId)->where('type', 'service')->pluck('name', 'id'),
        ];

        return view('core_security.index', compact('sessions', 'accessLogs', 'issues', 'twoFactor', 'ipRestrictions', 'users', 'accessScopeOptions', 'accessScopes', 'scopeMeta', 'sessionSummary', 'cacheSummary', 'archivedRecords', 'archiveOptions'));
    }

    public function scanDataQuality()
    {
        $this->authorizeAccess('manage data quality issue');
        $count = $this->dataQuality->scanCustomerDuplicates(\Auth::user()->creatorId());
        $count += $this->dataQuality->scanVenderDuplicates(\Auth::user()->creatorId());
        $count += $this->dataQuality->scanProductServiceDuplicates(\Auth::user()->creatorId());
        $count += $this->dataQuality->scanPatientDuplicates(\Auth::user()->creatorId());

        return redirect()->back()->with('success', __('Data quality scan completed. Issues found: ') . $count);
    }

    public function archiveIssue(DataQualityIssue $dataQualityIssue)
    {
        $this->ensureIssueOwner($dataQualityIssue);
        $dataQualityIssue->status = 'archived';
        $dataQualityIssue->resolved_at = now();
        $dataQualityIssue->save();

        return redirect()->back()->with('success', __('Issue archived.'));
    }

    public function mergeIssue(DataQualityIssue $dataQualityIssue)
    {
        $this->ensureIssueOwner($dataQualityIssue);

        if ($dataQualityIssue->record_type === Customer::class && $dataQualityIssue->duplicate_type === Customer::class) {
            \App\Models\Invoice::query()->where('customer_id', $dataQualityIssue->duplicate_id)->update(['customer_id' => $dataQualityIssue->record_id]);
            Customer::query()->where('id', $dataQualityIssue->duplicate_id)->delete();
        }

        if ($dataQualityIssue->record_type === Vender::class && $dataQualityIssue->duplicate_type === Vender::class) {
            \App\Models\Bill::query()->where('vender_id', $dataQualityIssue->duplicate_id)->update(['vender_id' => $dataQualityIssue->record_id]);
            Vender::query()->where('id', $dataQualityIssue->duplicate_id)->delete();
        }

        if ($dataQualityIssue->record_type === ProductService::class && $dataQualityIssue->duplicate_type === ProductService::class) {
            \App\Models\MedicalService::query()->where('product_service_id', $dataQualityIssue->duplicate_id)->update(['product_service_id' => $dataQualityIssue->record_id]);
            \App\Models\PharmacyMedication::query()->where('product_service_id', $dataQualityIssue->duplicate_id)->update(['product_service_id' => $dataQualityIssue->record_id]);
            ProductService::query()->where('id', $dataQualityIssue->duplicate_id)->delete();
        }

        if ($dataQualityIssue->record_type === Patient::class && $dataQualityIssue->duplicate_type === Patient::class) {
            $this->mergePatientDuplicate((int) $dataQualityIssue->record_id, (int) $dataQualityIssue->duplicate_id);
        }

        $dataQualityIssue->status = 'merged';
        $dataQualityIssue->resolved_at = now();
        $dataQualityIssue->save();

        return redirect()->back()->with('success', __('Duplicate records merged.'));
    }

    public function storeIpRestriction(Request $request)
    {
        $this->authorizeAccess('manage security center');
        $request->validate([
            'ip_address' => 'required|string|max:255',
        ]);

        IpRestriction::create([
            'user_id' => \Auth::id(),
            'ip_address' => $request->ip_address,
            'description' => $request->description,
            'is_whitelist' => $request->boolean('is_whitelist', true),
            'is_active' => true,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->back()->with('success', __('IP restriction saved.'));
    }

    public function storeTwoFactor(Request $request)
    {
        $this->authorizeAccess('manage two factor auth');

        $twoFactor = TwoFactorAuth::updateOrCreate([
            'user_id' => \Auth::id(),
        ], [
            'provider' => $request->input('provider', 'email'),
            'secret' => $request->input('secret', strtoupper(bin2hex(random_bytes(4)))),
            'enabled_at' => now(),
        ]);

        $twoFactor->generateBackupCodes();

        return redirect()->back()->with('success', __('Two-factor authentication configured.'));
    }

    public function disableTwoFactor()
    {
        $this->authorizeAccess('manage two factor auth');

        TwoFactorAuth::query()->where('user_id', \Auth::id())->delete();

        return redirect()->back()->with('success', __('Two-factor authentication disabled.'));
    }

    public function regenerateBackupCodes()
    {
        $this->authorizeAccess('manage two factor auth');

        $twoFactor = TwoFactorAuth::query()->where('user_id', \Auth::id())->firstOrFail();
        $twoFactor->generateBackupCodes();

        return redirect()->back()->with('success', __('Backup codes regenerated.'));
    }

    public function storeAccessScope(Request $request)
    {
        $this->authorizeAccess('manage access scope');
        $request->validate([
            'user_id' => 'required|integer',
            'scope_type' => 'required|string|in:branch,warehouse,department,service',
            'scope_ids' => 'required|array|min:1',
            'scope_ids.*' => 'integer',
        ]);

        $user = User::query()
            ->where(function ($query) {
                $query->where('id', \Auth::user()->creatorId())
                    ->orWhere('created_by', \Auth::user()->creatorId());
            })
            ->findOrFail($request->integer('user_id'));
        $scopeType = $request->string('scope_type')->toString();
        $allowedScopeIds = array_map('intval', array_keys($this->accessScopes->availableScopes(\Auth::user()->creatorId())[$scopeType] ?? []));
        $scopeIds = collect($request->input('scope_ids', []))
            ->map(static fn ($scopeId) => (int) $scopeId)
            ->filter(static fn ($scopeId) => in_array($scopeId, $allowedScopeIds, true))
            ->values()
            ->all();

        if (empty($scopeIds)) {
            return redirect()->back()->with('error', __('No valid scope selected.'));
        }

        $this->accessScopes->syncScope(
            \Auth::user(),
            $user->id,
            $scopeType,
            $scopeIds,
            $request->input('notes')
        );

        return redirect()->back()->with('success', __('Access scopes updated.'));
    }

    public function destroyAccessScope(UserAccessScope $userAccessScope)
    {
        $this->authorizeAccess('delete access scope');

        if ((int) $userAccessScope->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }

        $userAccessScope->delete();

        return redirect()->back()->with('success', __('Access scope removed.'));
    }

    public function revokeSession(UserSessionLog $userSessionLog)
    {
        $this->authorizeAccess('revoke user session');

        if ((int) $userSessionLog->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }

        $this->securityAccess->revokeSession($userSessionLog);

        return redirect()->back()->with('success', __('Session revoked.'));
    }

    public function revokeUserSessions(User $user)
    {
        $this->authorizeAccess('revoke user session');

        if ((int) $user->creatorId() !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }

        $revoked = $this->securityAccess->revokeUserSessions($user, \Auth::user()->creatorId());

        return redirect()->back()->with('success', __('Revoked :count active sessions.', ['count' => $revoked]));
    }

    public function revokeAllSessions()
    {
        $this->authorizeAccess('revoke user session');
        $revoked = $this->securityAccess->revokeAllSessionsForOwner(\Auth::user()->creatorId());

        return redirect()->back()->with('success', __('Revoked :count active sessions.', ['count' => $revoked]));
    }

    public function verifyTwoFactor(Request $request)
    {
        $this->authorizeAccess('manage two factor auth');
        $request->validate([
            'code' => 'required|string|max:64',
        ]);

        $twoFactor = TwoFactorAuth::query()->where('user_id', \Auth::id())->firstOrFail();
        $code = strtoupper(trim($request->string('code')->toString()));
        $verified = $twoFactor->verifyCode($code) || $twoFactor->useBackupCode($code);

        return redirect()->back()->with($verified ? 'success' : 'error', $verified ? __('Two-factor code verified.') : __('Invalid two-factor code.'));
    }

    public function storeArchive(Request $request)
    {
        $this->authorizeAccess('manage security center');
        $request->validate([
            'record_type' => 'required|string|in:customer,vender,product_service,patient',
            'record_id' => 'required|integer',
            'reason' => 'nullable|string|max:1000',
        ]);

        $archivedRecord = $this->archiveService->archive(
            \Auth::user(),
            $request->string('record_type')->toString(),
            $request->integer('record_id'),
            $request->input('reason')
        );

        $this->securityAccess->logSensitiveAccess('archive_record', $archivedRecord->record_type, $archivedRecord->record_id, [
            'display_name' => $archivedRecord->display_name,
            'reason' => $archivedRecord->reason,
        ]);

        return redirect()->back()->with('success', __('Record archived successfully.'));
    }

    public function restoreArchive(ArchivedRecord $archivedRecord)
    {
        $this->authorizeAccess('manage security center');
        abort_unless((int) $archivedRecord->created_by === (int) \Auth::user()->creatorId(), 403, 'Permission denied.');

        $this->archiveService->restore(\Auth::user(), $archivedRecord);
        $this->securityAccess->logSensitiveAccess('restore_record', $archivedRecord->record_type, $archivedRecord->record_id, [
            'display_name' => $archivedRecord->display_name,
        ]);

        return redirect()->back()->with('success', __('Record restored successfully.'));
    }

    public function warmCaches()
    {
        $this->authorizeAccess('manage security center');
        $creatorId = \Auth::user()->creatorId();
        $this->coreCache->warmForOwner($creatorId);
        $this->accessScopes->flushCache($creatorId);

        return redirect()->back()->with('success', __('Core caches warmed.'));
    }

    public function flushCaches()
    {
        $this->authorizeAccess('manage security center');
        $creatorId = \Auth::user()->creatorId();
        $this->coreCache->flushForOwner($creatorId);
        $this->accessScopes->flushCache($creatorId);

        return redirect()->back()->with('success', __('Core caches flushed.'));
    }

    private function mergePatientDuplicate(int $primaryId, int $duplicateId): void
    {
        $references = [
            'medical_appointments' => 'patient_id',
            'patient_consultations' => 'patient_id',
            'patient_lab_results' => 'patient_id',
            'patient_documents' => 'patient_id',
            'patient_consents' => 'patient_id',
            'medical_record_access_logs' => 'patient_id',
            'hospital_admissions' => 'patient_id',
            'medical_invoices' => 'patient_id',
            'emergency_visits' => 'patient_id',
            'imaging_orders' => 'patient_id',
            'nursing_cares' => 'patient_id',
            'telemedicine_sessions' => 'patient_id',
            'lab_orders' => 'patient_id',
            'surgical_procedures' => 'patient_id',
            'patient_portal_messages' => 'patient_id',
        ];

        foreach ($references as $table => $column) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                DB::table($table)->where($column, $duplicateId)->update([$column => $primaryId]);
            }
        }

        Patient::query()->where('id', $duplicateId)->delete();
    }

    private function ensureIssueOwner(DataQualityIssue $dataQualityIssue): void
    {
        if (! \Auth::user()->can('edit data quality issue') || (int) $dataQualityIssue->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }
    }

    private function authorizeAccess(string $permission): void
    {
        if (! \Auth::user()->can($permission)) {
            abort(403, 'Permission denied.');
        }
    }
}

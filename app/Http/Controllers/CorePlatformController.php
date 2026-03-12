<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanAddon;
use App\Models\PlanRequest;
use App\Models\SavedView;
use App\Models\TenantOnboardingChecklist;
use App\Models\TenantAddonRequest;
use App\Models\TenantPlanAddon;
use App\Models\TenantUsage;
use App\Models\User;
use App\Services\Core\CoreConsolidationService;
use App\Services\Core\TenantLifecycleService;
use App\Services\Core\TimelineService;
use Illuminate\Http\Request;

class CorePlatformController extends Controller
{
    public function __construct(
        private readonly CoreConsolidationService $consolidation,
        private readonly TenantLifecycleService $tenantLifecycle,
        private readonly TimelineService $timeline
    ) {
    }

    public function onboarding()
    {
        $this->authorizeAccess('manage tenant onboarding');
        $tenantId = \Auth::user()->creatorId();
        $tenant = \Auth::user()->type === 'company' ? \Auth::user() : \Auth::user()->ownerDetails();
        $checklist = TenantOnboardingChecklist::query()->firstOrCreate(
            ['created_by' => $tenantId],
            [
                'checklist' => $this->tenantLifecycle->defaultChecklist(),
                'completed_steps' => [],
                'configured_by' => \Auth::id(),
            ]
        );

        if (empty($checklist->checklist)) {
            $checklist = $this->tenantLifecycle->provisionChecklist($tenantId, \Auth::id());
        }

        $addons = PlanAddon::query()->latest('id')->get();
        $activeAddons = TenantPlanAddon::query()->where('created_by', $tenantId)->with('addon')->get();
        $usages = TenantUsage::query()->where('created_by', $tenantId)->latest('metric_key')->get();
        $progress = $this->tenantLifecycle->progressPercent($checklist);
        $currentPlan = Plan::query()->find(\Auth::user()->plan);
        $plans = Plan::query()
            ->when(\Schema::hasColumn('plans', 'is_disable'), fn ($query) => $query->where('is_disable', 1))
            ->orderBy('price')
            ->get(['id', 'name', 'price', 'duration', 'description']);
        $pendingPlanRequest = PlanRequest::query()
            ->where('user_id', \Auth::id())
            ->where('status', 'pending')
            ->latest('id')
            ->first();
        $planRequests = PlanRequest::query()
            ->when(\Auth::user()->type === 'super admin', fn ($query) => $query, fn ($query) => $query->where('user_id', \Auth::id()))
            ->with(['user', 'plan', 'currentPlan', 'reviewer'])
            ->latest('id')
            ->limit(10)
            ->get();
        $recentOrders = Order::query()
            ->where('user_id', \Auth::id())
            ->latest('id')
            ->limit(5)
            ->get(['id', 'order_id', 'plan_name', 'price', 'price_currency', 'payment_status', 'payment_type', 'created_at']);
        $usageSummary = $usages->map(function ($usage) {
            $limit = $usage->limit_value;
            $percent = ($limit && (float) $limit > 0) ? min(100, (int) round(((float) $usage->usage_value / (float) $limit) * 100)) : null;

            return [
                'metric_key' => $usage->metric_key,
                'usage_value' => $usage->usage_value,
                'limit_value' => $usage->limit_value,
                'percent' => $percent,
                'resets_at' => $usage->resets_at,
            ];
        });
        $quotaAlerts = collect($usageSummary)->filter(fn ($usage) => ! is_null($usage['percent']) && $usage['percent'] >= 80)->values();
        $nextSteps = collect($checklist->checklist ?? [])
            ->reject(fn ($item) => in_array($item['key'], $checklist->completed_steps ?? [], true))
            ->take(3)
            ->values();
        $this->tenantLifecycle->syncPlanQuotas($tenant);

        return view('core_platform.onboarding', compact('checklist', 'addons', 'activeAddons', 'usages', 'progress', 'usageSummary', 'currentPlan', 'plans', 'pendingPlanRequest', 'recentOrders', 'planRequests', 'quotaAlerts', 'nextSteps'));
    }

    public function onboardingUpdate(Request $request)
    {
        $this->authorizeAccess('manage tenant onboarding');
        $steps = array_values(array_filter((array) $request->input('completed_steps', [])));
        $checklist = $this->tenantLifecycle->syncChecklist(\Auth::user()->creatorId(), $steps, \Auth::id());
        $this->timeline->record($checklist, __('Tenant onboarding updated'), __('Checklist progress updated to :progress%.', ['progress' => $this->tenantLifecycle->progressPercent($checklist)]), [], 'system', \Auth::user()->creatorId(), \Auth::id());

        return redirect()->back()->with('success', __('Onboarding checklist updated.'));
    }

    public function onboardingReset()
    {
        $this->authorizeAccess('manage tenant onboarding');
        $checklist = $this->tenantLifecycle->provisionChecklist(\Auth::user()->creatorId(), \Auth::id());
        $this->timeline->record($checklist, __('Tenant onboarding reset'), __('The onboarding assistant has been reprovisioned with the default checklist.'), [], 'system', \Auth::user()->creatorId(), \Auth::id());

        return redirect()->back()->with('success', __('Onboarding checklist reset.'));
    }

    public function addons()
    {
        $this->authorizeAccess('manage plan addon');
        $addons = PlanAddon::query()->latest('id')->get();
        $subscriptions = TenantPlanAddon::query()->where('created_by', \Auth::user()->creatorId())->with('addon')->latest('id')->get();
        $addonRequests = TenantAddonRequest::query()
            ->when(\Auth::user()->type === 'super admin', fn ($query) => $query, fn ($query) => $query->where('created_by', \Auth::user()->creatorId()))
            ->with(['addon', 'requester', 'reviewer'])
            ->latest('id')
            ->get();
        $usageSummary = TenantUsage::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->orderBy('metric_key')
            ->get()
            ->map(function ($usage) {
                $limit = (float) ($usage->limit_value ?? 0);
                $current = (float) $usage->usage_value;

                return [
                    'metric_key' => $usage->metric_key,
                    'usage_value' => $usage->usage_value,
                    'limit_value' => $usage->limit_value,
                    'ratio' => $limit > 0 ? min(100, (int) round(($current / $limit) * 100)) : null,
                ];
            });

        return view('core_platform.addons', compact('addons', 'subscriptions', 'usageSummary', 'addonRequests'));
    }

    public function addonStore(Request $request)
    {
        $this->authorizeAccess('create plan addon');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:plan_addons,code',
            'price' => 'nullable|numeric|min:0',
        ]);

        PlanAddon::create([
            'plan_id' => $request->input('plan_id'),
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $request->input('description'),
            'price' => $validated['price'] ?? 0,
            'billing_cycle' => $request->input('billing_cycle', 'monthly'),
            'limits' => $request->input('limits', []),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->back()->with('success', __('Addon created.'));
    }

    public function addonActivate(PlanAddon $planAddon)
    {
        $this->authorizeAccess('edit plan addon');

        $subscription = $this->tenantLifecycle->activateAddon(\Auth::user()->creatorId(), $planAddon, [
            'activated_at' => now(),
            'renews_at' => now()->addMonth(),
        ]);
        $this->timeline->record($subscription, __('Addon activated'), __('Addon :name activated for tenant.', ['name' => $planAddon->name]), [], 'system', \Auth::user()->creatorId(), \Auth::id());

        return redirect()->back()->with('success', __('Addon activated for tenant.'));
    }

    public function planChangeRequestStore(Request $request)
    {
        $this->authorizeAccess('manage tenant onboarding');

        if (\Auth::user()->type === 'super admin') {
            return redirect()->back()->with('error', __('Super admin users do not submit tenant plan change requests from this cockpit.'));
        }

        $validated = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'request_note' => 'nullable|string|max:1000',
        ]);

        if ((int) \Auth::user()->plan === (int) $validated['plan_id']) {
            return redirect()->back()->with('error', __('This tenant is already assigned to the selected plan.'));
        }

        $pending = PlanRequest::query()->where('user_id', \Auth::id())->exists();
        if ($pending || (int) (\Auth::user()->requested_plan ?? 0) !== 0) {
            return redirect()->back()->with('error', __('A plan change request is already pending for this tenant.'));
        }

        $plan = Plan::query()->findOrFail($validated['plan_id']);
        PlanRequest::create([
            'user_id' => \Auth::id(),
            'plan_id' => $plan->id,
            'current_plan_id' => \Auth::user()->plan,
            'duration' => $plan->duration,
            'status' => 'pending',
            'request_note' => $validated['request_note'] ?? null,
        ]);

        \Auth::user()->forceFill([
            'requested_plan' => $plan->id,
        ])->save();

        $this->timeline->record(
            \Auth::user(),
            __('Plan change requested'),
            __('Requested upgrade/downgrade to :plan.', ['plan' => $plan->name]),
            ['note' => $validated['request_note'] ?? null],
            'system',
            \Auth::user()->creatorId(),
            \Auth::id()
        );

        return redirect()->back()->with('success', __('Plan change request submitted successfully.'));
    }

    public function planRequestApprove(PlanRequest $planRequest)
    {
        $this->authorizeAccess('manage tenant onboarding');
        if (\Auth::user()->type !== 'super admin') {
            abort(403, 'Permission denied.');
        }
        if ($planRequest->status !== 'pending') {
            return redirect()->back()->with('error', __('Only pending plan requests can be approved.'));
        }

        $result = $this->tenantLifecycle->approvePlanRequest($planRequest, \Auth::user());
        if (($result['is_success'] ?? false) !== true) {
            return redirect()->back()->with('error', __('Plan request could not be approved.'));
        }

        $this->timeline->record(
            $planRequest,
            __('Plan request approved'),
            __('Plan request approved for tenant :tenant.', ['tenant' => optional($planRequest->user)->name ?: $planRequest->user_id]),
            ['plan_id' => $planRequest->plan_id],
            'system',
            $planRequest->user?->creatorId() ?? $planRequest->user_id,
            \Auth::id()
        );

        return redirect()->back()->with('success', __('Plan request approved successfully.'));
    }

    public function planRequestReject(Request $request, PlanRequest $planRequest)
    {
        $this->authorizeAccess('manage tenant onboarding');
        if (\Auth::user()->type !== 'super admin') {
            abort(403, 'Permission denied.');
        }
        if ($planRequest->status !== 'pending') {
            return redirect()->back()->with('error', __('Only pending plan requests can be rejected.'));
        }

        $this->tenantLifecycle->rejectPlanRequest($planRequest, \Auth::user(), $request->input('review_note'));
        $this->timeline->record(
            $planRequest,
            __('Plan request rejected'),
            __('Plan request rejected for tenant :tenant.', ['tenant' => optional($planRequest->user)->name ?: $planRequest->user_id]),
            ['review_note' => $request->input('review_note')],
            'system',
            $planRequest->user?->creatorId() ?? $planRequest->user_id,
            \Auth::id()
        );

        return redirect()->back()->with('success', __('Plan request rejected successfully.'));
    }

    public function addonRequestStore(Request $request)
    {
        $this->authorizeAccess('manage plan addon');

        $validated = $request->validate([
            'plan_addon_id' => 'required|integer|exists:plan_addons,id',
            'request_note' => 'nullable|string|max:1000',
        ]);

        $planAddon = PlanAddon::query()->findOrFail($validated['plan_addon_id']);

        $existingRequest = TenantAddonRequest::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('plan_addon_id', $planAddon->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return redirect()->back()->with('error', __('An addon request is already pending for this tenant.'));
        }

        $alreadyActive = TenantPlanAddon::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('plan_addon_id', $planAddon->id)
            ->where('status', 'active')
            ->exists();

        if ($alreadyActive) {
            return redirect()->back()->with('error', __('This addon is already active for the tenant.'));
        }

        $addonRequest = TenantAddonRequest::create([
            'created_by' => \Auth::user()->creatorId(),
            'plan_addon_id' => $planAddon->id,
            'requested_by' => \Auth::id(),
            'status' => 'pending',
            'billing_cycle' => $planAddon->billing_cycle,
            'amount' => $planAddon->price,
            'request_note' => $validated['request_note'] ?? null,
        ]);

        $this->timeline->record(
            $addonRequest,
            __('Addon request submitted'),
            __('Requested addon :addon for tenant activation.', ['addon' => $planAddon->name]),
            [],
            'system',
            \Auth::user()->creatorId(),
            \Auth::id()
        );

        return redirect()->back()->with('success', __('Addon request submitted successfully.'));
    }

    public function addonRequestApprove(TenantAddonRequest $tenantAddonRequest)
    {
        $this->authorizeAccess('edit plan addon');

        if (\Auth::user()->type !== 'super admin') {
            abort(403, 'Permission denied.');
        }

        if ($tenantAddonRequest->status !== 'pending') {
            return redirect()->back()->with('error', __('Only pending addon requests can be approved.'));
        }

        $subscription = $this->tenantLifecycle->activateAddon($tenantAddonRequest->created_by, $tenantAddonRequest->addon, [
            'activated_at' => now(),
            'renews_at' => now()->addMonth(),
            'amount' => $tenantAddonRequest->amount,
            'billing_cycle' => $tenantAddonRequest->billing_cycle,
            'metadata' => ['approved_from_request_id' => $tenantAddonRequest->id],
        ]);

        $tenantAddonRequest->forceFill([
            'status' => 'approved',
            'reviewed_by' => \Auth::id(),
            'reviewed_at' => now(),
        ])->save();

        $this->timeline->record(
            $subscription,
            __('Addon request approved'),
            __('Addon :addon approved and activated.', ['addon' => optional($tenantAddonRequest->addon)->name ?: __('Addon')]),
            ['request_id' => $tenantAddonRequest->id],
            'system',
            $tenantAddonRequest->created_by,
            \Auth::id()
        );

        return redirect()->back()->with('success', __('Addon request approved and activated.'));
    }

    public function addonRequestReject(Request $request, TenantAddonRequest $tenantAddonRequest)
    {
        $this->authorizeAccess('edit plan addon');

        if (\Auth::user()->type !== 'super admin') {
            abort(403, 'Permission denied.');
        }

        if ($tenantAddonRequest->status !== 'pending') {
            return redirect()->back()->with('error', __('Only pending addon requests can be rejected.'));
        }

        $tenantAddonRequest->forceFill([
            'status' => 'rejected',
            'reviewed_by' => \Auth::id(),
            'reviewed_at' => now(),
            'review_note' => $request->input('review_note'),
        ])->save();

        $this->timeline->record(
            $tenantAddonRequest,
            __('Addon request rejected'),
            __('Addon request has been rejected.'),
            ['review_note' => $request->input('review_note')],
            'system',
            $tenantAddonRequest->created_by,
            \Auth::id()
        );

        return redirect()->back()->with('success', __('Addon request rejected.'));
    }

    public function addonDeactivate(TenantPlanAddon $tenantPlanAddon)
    {
        $this->authorizeAccess('edit plan addon');
        if ((int) $tenantPlanAddon->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }

        $this->tenantLifecycle->deactivateAddon($tenantPlanAddon, request('cancel_reason'));
        $this->timeline->record($tenantPlanAddon, __('Addon cancelled'), __('Addon subscription cancelled for this tenant.'), ['reason' => request('cancel_reason')], 'system', \Auth::user()->creatorId(), \Auth::id());

        return redirect()->back()->with('success', __('Addon deactivated.'));
    }

    public function addonRenew(TenantPlanAddon $tenantPlanAddon)
    {
        $this->authorizeAccess('edit plan addon');
        if ((int) $tenantPlanAddon->created_by !== (int) \Auth::user()->creatorId() && \Auth::user()->type !== 'super admin') {
            abort(403, 'Permission denied.');
        }

        $tenantPlanAddon = $this->tenantLifecycle->renewAddon($tenantPlanAddon);
        $tenant = User::query()->find($tenantPlanAddon->created_by);
        if ($tenant) {
            $this->tenantLifecycle->recordBillingEvent($tenant, optional($tenantPlanAddon->addon)->name ?: __('Addon'), (float) $tenantPlanAddon->amount, __('Addon renewal'));
        }

        $this->timeline->record($tenantPlanAddon, __('Addon renewed'), __('Addon subscription renewed.'), [], 'system', $tenantPlanAddon->created_by, \Auth::id());

        return redirect()->back()->with('success', __('Addon renewed successfully.'));
    }

    public function usageStore(Request $request)
    {
        $this->authorizeAccess('manage tenant onboarding');

        $validated = $request->validate([
            'metric_key' => 'required|string|max:255',
            'usage_value' => 'required|numeric|min:0',
            'limit_value' => 'nullable|numeric|min:0',
            'resets_at' => 'nullable|date',
        ]);

        $usage = TenantUsage::updateOrCreate([
            'created_by' => \Auth::user()->creatorId(),
            'metric_key' => $validated['metric_key'],
            'subject_type' => null,
            'subject_id' => null,
        ], [
            'usage_value' => $validated['usage_value'],
            'limit_value' => $validated['limit_value'] ?? null,
            'resets_at' => $validated['resets_at'] ?? null,
        ]);

        $this->timeline->record($usage, __('Tenant quota updated'), __('Usage metric :metric updated.', ['metric' => $usage->metric_key]), [], 'system', \Auth::user()->creatorId(), \Auth::id());

        return redirect()->back()->with('success', __('Tenant usage updated.'));
    }

    public function usageSync()
    {
        $this->authorizeAccess('manage tenant onboarding');
        $tenant = \Auth::user()->type === 'company' ? \Auth::user() : \Auth::user()->ownerDetails();
        $metrics = $this->tenantLifecycle->syncPlanQuotas($tenant);
        $this->timeline->record($tenant, __('Tenant quotas synchronized'), __('Quota snapshot refreshed from current plan and active addons.'), ['metrics' => array_keys($metrics)], 'system', \Auth::user()->creatorId(), \Auth::id());

        return redirect()->back()->with('success', __('Tenant quota snapshot synchronized successfully.'));
    }

    public function savedViews()
    {
        $this->authorizeAccess('manage saved view');
        $views = SavedView::query()->where('user_id', \Auth::id())->latest('id')->get();

        return view('core_platform.saved_views', compact('views'));
    }

    public function savedViewStore(Request $request)
    {
        $this->authorizeAccess('create saved view');

        $request->validate([
            'module' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        $this->timeline->saveView(\Auth::id(), $request->module, $request->name, [
            'filters' => $request->input('filters', []),
            'columns' => $request->input('columns', []),
            'sorts' => $request->input('sorts', []),
        ], $request->boolean('is_default'));

        return redirect()->back()->with('success', __('Saved view created.'));
    }

    public function savedViewDestroy(SavedView $savedView)
    {
        $this->authorizeAccess('delete saved view');
        if ((int) $savedView->user_id !== (int) \Auth::id()) {
            abort(403);
        }
        $savedView->delete();

        return redirect()->back()->with('success', __('Saved view deleted.'));
    }

    public function preferences()
    {
        $this->authorizeAccess('manage user preference');

        return view('core_platform.preferences');
    }

    public function preferencesUpdate(Request $request)
    {
        $this->authorizeAccess('manage user preference');

        $validated = $request->validate([
            'theme_mode' => 'required|string|max:20',
            'pwa_notifications' => 'required|string|max:20',
            'keyboard_shortcuts' => 'nullable|string',
        ]);

        \Auth::user()->update([
            'theme_mode' => $validated['theme_mode'],
            'pwa_notifications' => $validated['pwa_notifications'],
            'keyboard_shortcuts' => $validated['keyboard_shortcuts'] ?? null,
        ]);

        return redirect()->back()->with('success', __('Preferences updated.'));
    }

    public function helpCenter()
    {
        $this->authorizeAccess('show help center');
        $search = trim((string) request('q'));
        $guidedArticles = KnowledgeBaseArticle::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('status', 'published')
            ->where(function ($query) {
                $query->where('is_featured', true)
                    ->orWhere('title', 'like', '%onboarding%')
                    ->orWhere('title', 'like', '%api%')
                    ->orWhere('title', 'like', '%security%');
            })
            ->latest('id')
            ->limit(6)
            ->get();
        $articles = KnowledgeBaseArticle::query()
            ->where('created_by', \Auth::user()->creatorId())
            ->where('status', 'published')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', '%'.$search.'%')
                        ->orWhere('summary', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->limit(20)
            ->get();

        return view('core_platform.help_center', compact('articles', 'search', 'guidedArticles'));
    }

    public function consolidation()
    {
        $this->authorizeAccess('manage security center');
        $snapshot = $this->consolidation->snapshot(\Auth::user()->creatorId());

        return view('core_platform.consolidation', [
            'metrics' => $snapshot['metrics'],
            'moduleHealth' => $snapshot['module_health'],
            'checklist' => $snapshot['checklist'],
        ]);
    }

    private function authorizeAccess(string $permission): void
    {
        if (! \Auth::user()->can($permission)) {
            abort(403, 'Permission denied.');
        }
    }
}

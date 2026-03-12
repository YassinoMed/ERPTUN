<?php

namespace App\Services\Core;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Plan;
use App\Models\TenantOnboardingChecklist;
use App\Models\PlanAddon;
use App\Models\PlanRequest;
use App\Models\TenantPlanAddon;
use App\Models\TenantUsage;
use App\Models\User;
use App\Models\Utility;
use App\Models\Vender;

class TenantLifecycleService
{
    public function defaultChecklist(): array
    {
        return [
            ['key' => 'company_profile', 'label' => 'Complete company profile'],
            ['key' => 'branding', 'label' => 'Upload logo and document branding'],
            ['key' => 'users', 'label' => 'Invite users and assign roles'],
            ['key' => 'modules', 'label' => 'Activate plan modules and defaults'],
            ['key' => 'finance', 'label' => 'Review taxes, numbering and invoicing defaults'],
            ['key' => 'support', 'label' => 'Publish onboarding and help-center articles'],
            ['key' => 'security', 'label' => 'Review sessions, IP restrictions and 2FA policy'],
        ];
    }

    public function provisionChecklist(int $tenantId, ?int $configuredBy = null): TenantOnboardingChecklist
    {
        return TenantOnboardingChecklist::updateOrCreate([
            'created_by' => $tenantId,
        ], [
            'configured_by' => $configuredBy,
            'checklist' => $this->defaultChecklist(),
            'completed_steps' => [],
        ]);
    }

    public function syncChecklist(int $tenantId, array $completedSteps, ?int $configuredBy = null): TenantOnboardingChecklist
    {
        $checklist = TenantOnboardingChecklist::firstOrNew(['created_by' => $tenantId]);
        $checklist->checklist = $checklist->checklist ?: $this->defaultChecklist();
        $checklist->configured_by = $configuredBy;
        $checklist->completed_steps = array_values(array_unique(array_filter($completedSteps)));
        $checklist->completed_at = count($checklist->completed_steps) >= count($checklist->checklist ?? []) ? now() : null;
        $checklist->save();

        return $checklist;
    }

    public function progressPercent(TenantOnboardingChecklist $checklist): int
    {
        $total = count($checklist->checklist ?? []);
        if ($total === 0) {
            return 0;
        }

        return (int) round((count($checklist->completed_steps ?? []) / $total) * 100);
    }

    public function registerUsage(int $tenantId, string $metricKey, float $delta = 1, ?float $limit = null, ?string $subjectType = null, ?int $subjectId = null): TenantUsage
    {
        $usage = TenantUsage::firstOrCreate([
            'created_by' => $tenantId,
            'metric_key' => $metricKey,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
        ], [
            'usage_value' => 0,
            'limit_value' => $limit,
        ]);

        $usage->usage_value = (float) $usage->usage_value + $delta;
        if ($limit !== null) {
            $usage->limit_value = $limit;
        }
        $usage->save();

        return $usage;
    }

    public function activateAddon(int $tenantId, PlanAddon $addon, array $attributes = []): TenantPlanAddon
    {
        return TenantPlanAddon::updateOrCreate([
            'created_by' => $tenantId,
            'plan_addon_id' => $addon->id,
        ], [
            'status' => 'active',
            'amount' => $attributes['amount'] ?? $addon->price,
            'billing_cycle' => $attributes['billing_cycle'] ?? $addon->billing_cycle,
            'activated_at' => $attributes['activated_at'] ?? now(),
            'expires_at' => $attributes['expires_at'] ?? $this->resolveAddonExpiry($addon->billing_cycle),
            'renews_at' => $attributes['renews_at'] ?? $this->resolveAddonExpiry($addon->billing_cycle),
            'cancelled_at' => null,
            'cancel_reason' => null,
            'metadata' => $attributes['metadata'] ?? [],
        ]);
    }

    public function deactivateAddon(TenantPlanAddon $subscription, ?string $reason = null): TenantPlanAddon
    {
        $subscription->status = 'cancelled';
        $subscription->cancelled_at = now();
        $subscription->cancel_reason = $reason;
        $subscription->save();

        return $subscription;
    }

    public function renewAddon(TenantPlanAddon $subscription): TenantPlanAddon
    {
        $subscription->status = 'active';
        $subscription->cancelled_at = null;
        $subscription->cancel_reason = null;
        $subscription->activated_at = $subscription->activated_at ?: now();
        $subscription->renews_at = $this->resolveAddonExpiry($subscription->billing_cycle);
        $subscription->expires_at = $subscription->renews_at;
        $subscription->save();

        return $subscription->fresh();
    }

    public function syncPlanQuotas(User $tenant): array
    {
        $plan = Plan::query()->find($tenant->plan);
        if (! $plan) {
            return [];
        }

        $tenantId = $tenant->creatorId();
        $counts = [
            'users' => User::query()
                ->where('created_by', $tenantId)
                ->whereNotIn('type', ['super admin', 'company', 'client'])
                ->count(),
            'clients' => User::query()->where('created_by', $tenantId)->where('type', 'client')->count(),
            'customers' => Customer::query()->where('created_by', $tenantId)->count(),
            'venders' => Vender::query()->where('created_by', $tenantId)->count(),
        ];

        $metrics = [
            'users' => ['usage' => $counts['users'], 'limit' => $plan->max_users],
            'clients' => ['usage' => $counts['clients'], 'limit' => $plan->max_clients],
            'customers' => ['usage' => $counts['customers'], 'limit' => $plan->max_customers],
            'venders' => ['usage' => $counts['venders'], 'limit' => $plan->max_venders],
            'storage_limit' => ['usage' => (float) ($tenant->storage_limit ?? 0), 'limit' => (float) ($plan->storage_limit ?? 0)],
        ];

        $activeAddons = TenantPlanAddon::query()
            ->where('created_by', $tenantId)
            ->where('status', 'active')
            ->with('addon')
            ->get();

        foreach ($activeAddons as $subscription) {
            foreach ((array) optional($subscription->addon)->limits as $metricKey => $value) {
                if (! isset($metrics[$metricKey])) {
                    $metrics[$metricKey] = ['usage' => 0, 'limit' => 0];
                }

                if (is_numeric($value) && (float) $value > 0) {
                    $baseLimit = (float) ($metrics[$metricKey]['limit'] ?? 0);
                    $metrics[$metricKey]['limit'] = $baseLimit + (float) $value;
                }
            }
        }

        foreach ($metrics as $metricKey => $payload) {
            TenantUsage::updateOrCreate(
                [
                    'created_by' => $tenantId,
                    'metric_key' => $metricKey,
                    'subject_type' => null,
                    'subject_id' => null,
                ],
                [
                    'usage_value' => $payload['usage'],
                    'limit_value' => $payload['limit'] > 0 ? $payload['limit'] : null,
                    'resets_at' => $this->resolveUsageResetAt($plan),
                ]
            );
        }

        return $metrics;
    }

    public function approvePlanRequest(PlanRequest $planRequest, User $reviewer): array
    {
        $tenant = User::query()->findOrFail($planRequest->user_id);
        $assignPlan = $tenant->assignPlan($planRequest->plan_id, $tenant->id);
        if (($assignPlan['is_success'] ?? false) !== true) {
            return $assignPlan;
        }

        $tenant->forceFill([
            'requested_plan' => 0,
        ])->save();

        $planRequest->forceFill([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ])->save();

        $plan = Plan::query()->find($planRequest->plan_id);
        if ($plan) {
            $this->recordBillingEvent($tenant, $plan->name, (float) $plan->price, __('Manual SaaS upgrade approval'));
            $this->syncPlanQuotas($tenant);
        }

        return ['is_success' => true, 'user' => $tenant];
    }

    public function rejectPlanRequest(PlanRequest $planRequest, User $reviewer, ?string $reviewNote = null): void
    {
        $tenant = User::query()->find($planRequest->user_id);
        if ($tenant) {
            $tenant->forceFill(['requested_plan' => 0])->save();
        }

        $planRequest->forceFill([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_note' => $reviewNote,
        ])->save();
    }

    public function recordBillingEvent(User $tenant, string $label, float $amount, string $paymentType): Order
    {
        return Order::create([
            'order_id' => strtoupper(str_replace('.', '', uniqid('ORD', true))),
            'name' => $tenant->name,
            'email' => $tenant->email,
            'plan_name' => $label,
            'plan_id' => $tenant->plan,
            'price' => $amount,
            'price_currency' => Utility::getAdminPaymentSetting()['currency'] ?? 'USD',
            'txn_id' => '',
            'payment_status' => 'success',
            'payment_type' => $paymentType,
            'receipt' => null,
            'user_id' => $tenant->id,
        ]);
    }

    private function resolveAddonExpiry(?string $billingCycle)
    {
        return match ($billingCycle) {
            'monthly', 'month' => now()->addMonth(),
            'yearly', 'year' => now()->addYear(),
            default => null,
        };
    }

    private function resolveUsageResetAt(Plan $plan)
    {
        return match ($plan->duration) {
            'month', 'monthly' => now()->endOfMonth(),
            'year', 'yearly' => now()->endOfYear(),
            default => null,
        };
    }
}

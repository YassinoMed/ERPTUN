<?php

namespace Modules\Platform\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Integrations\Integration as IntegrationModel;
use App\Models\Integrations\Webhook;
use App\Models\Integrations\ZapierHook;
use App\Models\JournalEntry;
use App\Models\AdvancedModuleFeatureState;
use App\Models\ModuleAlert;
use App\Models\ModuleKpiSnapshot;
use App\Models\ModuleRecommendation;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Template;
use App\Services\AdvancedFeatureCatalog;
use App\Services\AnalyticsDashboardProvisioner;
use App\Services\AdvancedInsightEngine;
use App\Services\FeatureStateManager;
use App\Services\RecommendationApplier;
use App\Services\WorkflowTemplateProvisioner;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PlatformApiController extends Controller
{
    use ApiResponser;

    public function __construct(
        private readonly AdvancedFeatureCatalog $featureCatalog,
        private readonly AdvancedInsightEngine $insightEngine,
        private readonly FeatureStateManager $featureStateManager,
        private readonly WorkflowTemplateProvisioner $workflowTemplateProvisioner,
        private readonly AnalyticsDashboardProvisioner $dashboardProvisioner,
        private readonly RecommendationApplier $recommendationApplier
    )
    {
    }

    private function tokenAllows(Request $request, string $ability): bool
    {
        $token = $request->user()?->currentAccessToken();
        if (! $token) {
            return false;
        }

        return $request->user()->tokenCan('*') || $request->user()->tokenCan($ability);
    }

    public function chartAccounts(Request $request)
    {
        if (! $this->tokenAllows($request, 'enterprise_accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        $accounts = ChartOfAccount::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'chart_accounts' => $accounts,
        ], 'Chart accounts fetched successfully.');
    }

    public function journals(Request $request)
    {
        if (! $this->tokenAllows($request, 'enterprise_accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        $journals = JournalEntry::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'journals' => $journals,
        ], 'Journal entries fetched successfully.');
    }

    public function integrations(Request $request)
    {
        if (! $this->tokenAllows($request, 'integrations.read')) {
            return $this->error('Forbidden', 403);
        }

        $integrations = IntegrationModel::query()
            ->where('user_id', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'integrations' => $integrations,
        ], 'Integrations fetched successfully.');
    }

    public function webhooks(Request $request)
    {
        if (! $this->tokenAllows($request, 'integrations.read')) {
            return $this->error('Forbidden', 403);
        }

        $webhooks = Webhook::query()
            ->where('user_id', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'webhooks' => $webhooks,
        ], 'Webhooks fetched successfully.');
    }

    public function zapierHooks(Request $request)
    {
        if (! $this->tokenAllows($request, 'integrations.read')) {
            return $this->error('Forbidden', 403);
        }

        $hooks = ZapierHook::query()
            ->where('user_id', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'zapier_hooks' => $hooks,
        ], 'Zapier hooks fetched successfully.');
    }

    public function chatgptTemplates(Request $request)
    {
        if (! $this->tokenAllows($request, 'chatgpt.read')) {
            return $this->error('Forbidden', 403);
        }

        $templates = Template::query()
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'templates' => $templates,
        ], 'Templates fetched successfully.');
    }

    public function saasPlans(Request $request)
    {
        if (! $this->tokenAllows($request, 'saas.read')) {
            return $this->error('Forbidden', 403);
        }

        $plans = Plan::query()
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'plans' => $plans,
        ], 'Plans fetched successfully.');
    }

    public function saasOrders(Request $request)
    {
        if (! $this->tokenAllows($request, 'saas.read')) {
            return $this->error('Forbidden', 403);
        }

        $orders = Order::query()
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'orders' => $orders,
        ], 'Orders fetched successfully.');
    }

    public function advancedFeatures(Request $request)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        return $this->success([
            'modules' => $this->featureCatalog->prioritizedModules(),
            'roadmap' => $this->featureCatalog->roadmap(),
        ], 'Advanced ERP feature catalog fetched successfully.');
    }

    public function workflowCatalog(Request $request)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        return $this->success($this->featureCatalog->workflowCatalog(), 'Workflow catalog fetched successfully.');
    }

    public function moduleDetail(Request $request, string $module)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        $definition = $this->featureCatalog->module($module);
        if (! $definition) {
            return $this->error('Module not found', 404);
        }

        return $this->success([
            'module' => strtolower($module),
            'definition' => $definition,
        ], 'Module feature detail fetched successfully.');
    }

    public function advancedInsights(Request $request)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        $ownerId = $request->user()->creatorId();

        return $this->success($this->insightEngine->getOwnerDashboard($ownerId), 'Advanced insights fetched successfully.');
    }

    public function moduleInsights(Request $request, string $module)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        $module = strtolower($module);
        if (! in_array($module, $this->featureCatalog->keys(), true)) {
            return $this->error('Module not found', 404);
        }

        $ownerId = $request->user()->creatorId();

        return $this->success([
            'module' => $module,
            'definition' => $this->featureCatalog->module($module),
            'latest_snapshot' => ModuleKpiSnapshot::query()
                ->where('owner_id', $ownerId)
                ->where('module_key', $module)
                ->latest('calculated_at')
                ->first(),
            'open_alerts' => ModuleAlert::query()
                ->where('owner_id', $ownerId)
                ->where('module_key', $module)
                ->where('status', 'open')
                ->latest('detected_at')
                ->get(),
            'pending_recommendations' => ModuleRecommendation::query()
                ->where('owner_id', $ownerId)
                ->where('module_key', $module)
                ->where('status', 'pending')
                ->latest('generated_at')
                ->get(),
        ], 'Module insights fetched successfully.');
    }

    public function refreshAdvancedInsights(Request $request)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        $ownerId = $request->user()->creatorId();

        return $this->success(
            $this->insightEngine->refreshForOwner($ownerId),
            'Advanced insights refreshed successfully.'
        );
    }

    public function moduleFeatureStates(Request $request, string $module)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        return $this->success([
            'module' => strtolower($module),
            'states' => $this->featureStateManager->listModuleStates($request->user()->creatorId(), $module),
        ], 'Module feature states fetched successfully.');
    }

    public function updateModuleFeatureState(Request $request, string $module, string $featureKey)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:planned,in_progress,active,paused,disabled',
            'priority' => 'nullable|string|in:low,medium,high',
            'name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $state = $this->featureStateManager->updateState(
            $request->user()->creatorId(),
            $module,
            $featureKey,
            $validated
        );

        return $this->success([
            'state' => $state,
        ], 'Module feature state updated successfully.');
    }

    public function activateModuleFeatures(Request $request, string $module)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        $count = $this->featureStateManager->activateAllModuleFeatures($request->user()->creatorId(), $module);

        return $this->success([
            'module' => strtolower($module),
            'updated_count' => $count,
        ], 'Module features activated successfully.');
    }

    public function installWorkflowTemplates(Request $request)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        return $this->success(
            $this->workflowTemplateProvisioner->installForOwner($request->user()->creatorId()),
            'Workflow templates installed successfully.'
        );
    }

    public function provisionAdvancedDashboard(Request $request)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        return $this->success(
            $this->dashboardProvisioner->provisionForOwner($request->user()->creatorId()),
            'Advanced dashboard provisioned successfully.'
        );
    }

    public function applyRecommendation(Request $request, int $recommendationId)
    {
        if (! $this->tokenAllows($request, 'platform.read')) {
            return $this->error('Forbidden', 403);
        }

        $recommendation = ModuleRecommendation::query()
            ->where('owner_id', $request->user()->creatorId())
            ->find($recommendationId);

        if (! $recommendation) {
            return $this->error('Recommendation not found', 404);
        }

        return $this->success([
            'recommendation' => $this->recommendationApplier->apply($recommendation),
            'states' => AdvancedModuleFeatureState::query()
                ->where('owner_id', $request->user()->creatorId())
                ->where('module_key', $recommendation->module_key)
                ->get(),
        ], 'Recommendation applied successfully.');
    }
}

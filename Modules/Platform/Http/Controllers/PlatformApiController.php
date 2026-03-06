<?php

namespace Modules\Platform\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Integrations\Integration as IntegrationModel;
use App\Models\Integrations\Webhook;
use App\Models\Integrations\ZapierHook;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Template;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PlatformApiController extends Controller
{
    use ApiResponser;

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
}

<?php

namespace App\Services;

use App\Models\AdvancedModuleFeatureState;
use App\Models\AgriHarvestDelivery;
use App\Models\ApprovalRequest;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\HotelReservation;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\ModuleAlert;
use App\Models\ModuleKpiSnapshot;
use App\Models\ModuleRecommendation;
use App\Models\Order;
use App\Models\ProductionOrder;
use App\Models\ProductionQualityCheck;
use App\Models\Project;
use App\Models\User;
use App\Models\WarehouseProduct;
use Carbon\Carbon;

class AdvancedInsightEngine
{
    public function __construct(private readonly AdvancedFeatureCatalog $catalog)
    {
    }

    public function refreshForOwner(int $ownerId): array
    {
        $this->syncFeatureStates($ownerId);

        $snapshots = [];
        $alerts = [];
        $recommendations = [];

        foreach (array_keys($this->catalog->allModules()) as $moduleKey) {
            $metrics = $this->calculateModuleMetrics($moduleKey, $ownerId);
            $snapshots[$moduleKey] = ModuleKpiSnapshot::create([
                'owner_id' => $ownerId,
                'module_key' => $moduleKey,
                'kpis' => $metrics,
                'calculated_at' => now(),
            ]);

            $this->resolveOpenSignals($ownerId, $moduleKey);

            foreach ($this->buildAlerts($moduleKey, $metrics) as $alert) {
                $alerts[] = ModuleAlert::updateOrCreate(
                    [
                        'owner_id' => $ownerId,
                        'module_key' => $moduleKey,
                        'alert_key' => $alert['alert_key'],
                        'status' => 'open',
                    ],
                    array_merge($alert, ['detected_at' => now(), 'resolved_at' => null])
                );
            }

            foreach ($this->buildRecommendations($moduleKey, $metrics) as $recommendation) {
                $recommendations[] = ModuleRecommendation::updateOrCreate(
                    [
                        'owner_id' => $ownerId,
                        'module_key' => $moduleKey,
                        'recommendation_key' => $recommendation['recommendation_key'],
                        'status' => 'pending',
                    ],
                    array_merge($recommendation, ['generated_at' => now(), 'applied_at' => null])
                );
            }
        }

        return [
            'owner_id' => $ownerId,
            'snapshots_created' => count($snapshots),
            'alerts_opened' => count($alerts),
            'recommendations_created' => count($recommendations),
        ];
    }

    public function syncFeatureStates(int $ownerId): int
    {
        $count = 0;

        foreach ($this->catalog->allModules() as $moduleKey => $definition) {
            foreach (($definition['advanced_features'] ?? []) as $index => $featureName) {
                AdvancedModuleFeatureState::updateOrCreate(
                    [
                        'owner_id' => $ownerId,
                        'module_key' => $moduleKey,
                        'feature_key' => str($featureName)->slug('_')->toString(),
                    ],
                    [
                        'status' => 'planned',
                        'priority' => $definition['priority'] ?? 'medium',
                        'meta' => [
                            'name' => $featureName,
                            'category' => $definition['category'] ?? 'general',
                            'sequence' => $index + 1,
                            'kpis' => $definition['kpis'] ?? [],
                        ],
                    ]
                );

                $count++;
            }
        }

        return $count;
    }

    public function getOwnerDashboard(int $ownerId): array
    {
        $snapshots = ModuleKpiSnapshot::query()
            ->where('owner_id', $ownerId)
            ->latest('calculated_at')
            ->get()
            ->groupBy('module_key')
            ->map(fn ($items) => $items->first());

        return [
            'feature_states' => AdvancedModuleFeatureState::query()
                ->where('owner_id', $ownerId)
                ->get()
                ->groupBy('module_key'),
            'latest_snapshots' => $snapshots,
            'open_alerts' => ModuleAlert::query()
                ->where('owner_id', $ownerId)
                ->where('status', 'open')
                ->latest('detected_at')
                ->get()
                ->groupBy('module_key'),
            'pending_recommendations' => ModuleRecommendation::query()
                ->where('owner_id', $ownerId)
                ->where('status', 'pending')
                ->latest('generated_at')
                ->get()
                ->groupBy('module_key'),
        ];
    }

    private function resolveOpenSignals(int $ownerId, string $moduleKey): void
    {
        ModuleAlert::query()
            ->where('owner_id', $ownerId)
            ->where('module_key', $moduleKey)
            ->where('status', 'open')
            ->update(['status' => 'resolved', 'resolved_at' => now()]);

        ModuleRecommendation::query()
            ->where('owner_id', $ownerId)
            ->where('module_key', $moduleKey)
            ->where('status', 'pending')
            ->update(['status' => 'dismissed', 'applied_at' => now()]);
    }

    private function calculateModuleMetrics(string $moduleKey, int $ownerId): array
    {
        return match ($moduleKey) {
            'crm' => [
                'lead_count' => Lead::query()->where('created_by', $ownerId)->count(),
                'customer_count' => Customer::query()->where('created_by', $ownerId)->count(),
                'inactive_leads' => Lead::query()->where('created_by', $ownerId)->where('is_active', 0)->count(),
            ],
            'sales' => [
                'open_leads' => Lead::query()->where('created_by', $ownerId)->where('is_active', 1)->count(),
                'customer_count' => Customer::query()->where('created_by', $ownerId)->count(),
            ],
            'accounting' => [
                'invoice_count' => Invoice::query()->where('created_by', $ownerId)->count(),
                'invoice_total_due' => round((float) Invoice::query()->where('created_by', $ownerId)->get()->sum(fn ($invoice) => $invoice->getDue()), 2),
                'expense_total' => (float) Expense::query()->where('created_by', $ownerId)->sum('amount'),
            ],
            'billing' => [
                'invoice_count' => Invoice::query()->where('created_by', $ownerId)->count(),
                'overdue_invoices' => Invoice::query()
                    ->where('created_by', $ownerId)
                    ->whereDate('due_date', '<', Carbon::today())
                    ->whereNotIn('status', ['Paid'])
                    ->count(),
            ],
            'saas' => [
                'orders_count' => Order::query()->count(),
                'revenue_total' => (float) Order::query()->sum('price'),
            ],
            'inventory' => [
                'tracked_products' => WarehouseProduct::query()->where('created_by', $ownerId)->count(),
                'low_stock_items' => WarehouseProduct::query()->where('created_by', $ownerId)->where('quantity', '<=', 5)->count(),
                'total_quantity' => (float) WarehouseProduct::query()->where('created_by', $ownerId)->sum('quantity'),
            ],
            'wms' => [
                'warehouse_products' => WarehouseProduct::query()->where('created_by', $ownerId)->count(),
                'low_stock_items' => WarehouseProduct::query()->where('created_by', $ownerId)->where('quantity', '<=', 5)->count(),
            ],
            'mrp', 'production' => [
                'production_orders' => ProductionOrder::query()->where('created_by', $ownerId)->count(),
                'draft_orders' => ProductionOrder::query()->where('created_by', $ownerId)->where('status', 'draft')->count(),
            ],
            'quality' => [
                'quality_checks' => ProductionQualityCheck::query()->where('created_by', $ownerId)->count(),
                'failed_checks' => ProductionQualityCheck::query()->where('created_by', $ownerId)->where('result', 'fail')->count(),
            ],
            'maintenance' => [
                'quality_failures_proxy' => ProductionQualityCheck::query()->where('created_by', $ownerId)->where('result', 'fail')->count(),
            ],
            'projects' => [
                'projects' => Project::query()->where('created_by', $ownerId)->count(),
                'active_projects' => Project::query()->where('created_by', $ownerId)->where('status', '!=', 'complete')->count(),
                'budget_total' => (float) Project::query()->where('created_by', $ownerId)->sum('budget'),
            ],
            'hrm', 'hrops' => [
                'employees' => Employee::query()->where('created_by', $ownerId)->count(),
                'active_users' => User::query()->where('created_by', $ownerId)->where('is_active', 1)->count(),
            ],
            'hotel' => [
                'reservations' => HotelReservation::query()->where('created_by', $ownerId)->count(),
                'upcoming_checkins' => HotelReservation::query()->where('created_by', $ownerId)->whereDate('check_in', '>=', Carbon::today())->count(),
            ],
            'traceability' => [
                'deliveries' => AgriHarvestDelivery::query()->where('created_by', $ownerId)->count(),
            ],
            'cropplanning' => [
                'weather_alert_proxy' => AgriHarvestDelivery::query()->where('created_by', $ownerId)->count(),
            ],
            'cooperative' => [
                'harvest_deliveries' => AgriHarvestDelivery::query()->where('created_by', $ownerId)->count(),
            ],
            'hedging' => [
                'purchase_deliveries_proxy' => AgriHarvestDelivery::query()->where('created_by', $ownerId)->count(),
            ],
            'btp' => [
                'projects_proxy' => Project::query()->where('created_by', $ownerId)->count(),
            ],
            'integrations' => [
                'webhook_templates' => (int) count(config('advanced_features.workflow.templates', [])),
                'workflow_templates' => (int) count(config('advanced_features.workflow.templates', [])),
            ],
            'platform' => [
                'module_count' => count($this->catalog->allModules()),
                'feature_count' => AdvancedModuleFeatureState::query()->where('owner_id', $ownerId)->count(),
                'open_alerts' => ModuleAlert::query()->where('owner_id', $ownerId)->where('status', 'open')->count(),
            ],
            'chatgpt' => [
                'template_count' => (int) count(config('advanced_features.workflow.templates', [])),
            ],
            'approvals' => [
                'pending_requests' => ApprovalRequest::query()->where('created_by', $ownerId)->where('status', 'pending')->count(),
                'approval_requests' => ApprovalRequest::query()->where('created_by', $ownerId)->count(),
            ],
            default => [
                'records' => 0,
            ],
        };
    }

    private function buildAlerts(string $moduleKey, array $metrics): array
    {
        $alerts = [];

        if (($metrics['overdue_invoices'] ?? 0) > 0) {
            $alerts[] = [
                'alert_key' => 'overdue_invoices',
                'severity' => 'high',
                'title' => 'Overdue invoices detected',
                'message' => sprintf('%d overdue invoices require follow-up.', $metrics['overdue_invoices']),
                'payload' => $metrics,
            ];
        }

        if (($metrics['low_stock_items'] ?? 0) > 0) {
            $alerts[] = [
                'alert_key' => 'low_stock_items',
                'severity' => 'high',
                'title' => 'Low stock risk',
                'message' => sprintf('%d stock items are below threshold.', $metrics['low_stock_items']),
                'payload' => $metrics,
            ];
        }

        if (($metrics['failed_checks'] ?? 0) > 0) {
            $alerts[] = [
                'alert_key' => 'failed_quality_checks',
                'severity' => 'high',
                'title' => 'Quality failures detected',
                'message' => sprintf('%d quality checks failed and require CAPA.', $metrics['failed_checks']),
                'payload' => $metrics,
            ];
        }

        if (($metrics['pending_requests'] ?? 0) > 0) {
            $alerts[] = [
                'alert_key' => 'pending_approvals',
                'severity' => 'medium',
                'title' => 'Pending approvals',
                'message' => sprintf('%d approval requests are pending.', $metrics['pending_requests']),
                'payload' => $metrics,
            ];
        }

        return $alerts;
    }

    private function buildRecommendations(string $moduleKey, array $metrics): array
    {
        $recommendations = [];

        if ($moduleKey === 'billing' && ($metrics['overdue_invoices'] ?? 0) > 0) {
            $recommendations[] = [
                'recommendation_key' => 'launch_collections_workflow',
                'priority' => 'high',
                'title' => 'Launch collections workflow',
                'description' => 'Enable automated dunning for overdue invoices and escalate unpaid accounts.',
                'payload' => $metrics,
            ];
        }

        if (in_array($moduleKey, ['inventory', 'wms'], true) && ($metrics['low_stock_items'] ?? 0) > 0) {
            $recommendations[] = [
                'recommendation_key' => 'enable_replenishment_rules',
                'priority' => 'high',
                'title' => 'Enable replenishment rules',
                'description' => 'Configure min/max restocking and supplier lead-time alerts.',
                'payload' => $metrics,
            ];
        }

        if ($moduleKey === 'quality' && ($metrics['failed_checks'] ?? 0) > 0) {
            $recommendations[] = [
                'recommendation_key' => 'activate_quality_gate',
                'priority' => 'high',
                'title' => 'Activate quality gate',
                'description' => 'Use approval workflows to block releases after failed inspections.',
                'payload' => $metrics,
            ];
        }

        if ($moduleKey === 'platform') {
            $recommendations[] = [
                'recommendation_key' => 'publish_wave_1',
                'priority' => 'medium',
                'title' => 'Prioritize wave 1 modules',
                'description' => 'Roll out Approvals, Platform, Integrations, Accounting and Billing first for fastest platform impact.',
                'payload' => ['roadmap' => $this->catalog->roadmap()['wave_1'] ?? []],
            ];
        }

        return $recommendations;
    }
}

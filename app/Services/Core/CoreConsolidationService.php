<?php

namespace App\Services\Core;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CoreConsolidationService
{
    public function snapshot(int $creatorId): array
    {
        $metrics = [
            'pending_approvals' => $this->countTable('approval_requests', $creatorId, ['status' => 'pending']),
            'automation_failures' => $this->countTable('automation_logs', $creatorId, ['status' => 'failed']),
            'api_clients_active' => $this->countBooleanTable('api_clients', $creatorId, 'is_active'),
            'api_logs_today' => $this->countSince('api_logs', $creatorId, 'created_at'),
            'sensitive_access_today' => $this->countSince('sensitive_access_logs', $creatorId, 'created_at'),
            'active_sessions' => $this->countBooleanTable('user_session_logs', $creatorId, 'is_active'),
            'saved_views' => $this->countTable('saved_views', null, ['user_id' => auth()->id()]),
            'timeline_entries' => $this->countTable('timeline_entries', $creatorId),
            'archived_records' => $this->countTable('archived_records', $creatorId),
            'notification_backlog' => $this->countUnreadNotifications($creatorId),
        ];

        $moduleHealth = [
            'crm' => $this->moduleRow($metrics['pending_approvals'] >= 0, $this->countTable('customers', $creatorId)),
            'operations' => $this->moduleRow($metrics['automation_failures'] >= 0, $this->countTable('delivery_notes', $creatorId)),
            'medical' => $this->moduleRow($this->tableExists('patients'), $this->countTable('patients', $creatorId)),
            'industry' => $this->moduleRow($this->tableExists('production_orders'), $this->countTable('production_orders', $creatorId)),
            'agri' => $this->moduleRow($this->tableExists('agri_lots'), $this->countTable('agri_lots', $creatorId)),
            'core' => $this->moduleRow(true, $metrics['api_clients_active'] + $metrics['active_sessions']),
        ];

        $checklist = [
            $this->checkItem('Permissions & scopes', $this->tableExists('user_access_scopes') && $metrics['active_sessions'] >= 0, 'Access scopes and active session controls are available.'),
            $this->checkItem('Security audit', $this->tableExists('sensitive_access_logs') && $metrics['sensitive_access_today'] >= 0, 'Sensitive access is logged across critical modules.'),
            $this->checkItem('Workflow & automation', $this->tableExists('approval_requests') && $this->tableExists('automation_logs'), 'Approvals and automation logs are stored centrally.'),
            $this->checkItem('API governance', $this->tableExists('api_clients') && $this->tableExists('api_logs'), 'API clients and logs are available for tenant integrations.'),
            $this->checkItem('Tenant operations', $this->tableExists('tenant_usages') && $this->tableExists('tenant_plan_addons'), 'Usage and addon lifecycle are available for SaaS operations.'),
            $this->checkItem('Knowledge & help', $this->tableExists('knowledge_base_articles'), 'Knowledge base is available for guided support.'),
            $this->checkItem('Archiving', $this->tableExists('archived_records'), 'Archived record registry is available for safe data retention.'),
        ];

        return [
            'metrics' => $metrics,
            'module_health' => $moduleHealth,
            'checklist' => $checklist,
        ];
    }

    private function moduleRow(bool $enabled, int $volume): array
    {
        return [
            'status' => $enabled ? 'ready' : 'missing',
            'volume' => $volume,
        ];
    }

    private function checkItem(string $name, bool $ok, string $detail): array
    {
        return [
            'name' => $name,
            'status' => $ok ? 'ready' : 'attention',
            'detail' => $detail,
        ];
    }

    private function countTable(string $table, ?int $creatorId = null, array $filters = []): int
    {
        if (! $this->tableExists($table)) {
            return 0;
        }

        $query = DB::table($table);
        if (! is_null($creatorId) && Schema::hasColumn($table, 'created_by')) {
            $query->where('created_by', $creatorId);
        }
        foreach ($filters as $column => $value) {
            if (Schema::hasColumn($table, $column)) {
                $query->where($column, $value);
            }
        }

        return (int) $query->count();
    }

    private function countBooleanTable(string $table, int $creatorId, string $column): int
    {
        if (! $this->tableExists($table) || ! Schema::hasColumn($table, $column)) {
            return 0;
        }

        $query = DB::table($table)->where($column, true);
        if (Schema::hasColumn($table, 'created_by')) {
            $query->where('created_by', $creatorId);
        }

        return (int) $query->count();
    }

    private function countSince(string $table, int $creatorId, string $dateColumn): int
    {
        if (! $this->tableExists($table) || ! Schema::hasColumn($table, $dateColumn)) {
            return 0;
        }

        $query = DB::table($table)->where($dateColumn, '>=', now()->startOfDay());
        if (Schema::hasColumn($table, 'created_by')) {
            $query->where('created_by', $creatorId);
        }

        return (int) $query->count();
    }

    private function countUnreadNotifications(int $creatorId): int
    {
        if (! $this->tableExists('notifications')) {
            return 0;
        }

        $query = DB::table('notifications');
        if (Schema::hasColumn('notifications', 'created_by')) {
            $query->where('created_by', $creatorId);
        }
        if (Schema::hasColumn('notifications', 'is_read')) {
            $query->where('is_read', 0);
        }

        return (int) $query->count();
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }
}

<?php

namespace Database\Seeders\Modules;

/**
 * Approvals Service Seeder — Workflow Approbations
 * Seeds: approval statuses, workflow templates, escalation rules
 */
class ApprovalsSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Approvals';

    protected function seed(): void
    {
        // ── Approval Statuses ─────────────────────────────────────
        if ($this->tableExists('approval_statuses')) {
            $statuses = [
                ['name' => 'Pending', 'slug' => 'pending', 'color' => '#FFA500', 'sort_order' => 1],
                ['name' => 'Under Review', 'slug' => 'under_review', 'color' => '#3498DB', 'sort_order' => 2],
                ['name' => 'Approved', 'slug' => 'approved', 'color' => '#27AE60', 'sort_order' => 3],
                ['name' => 'Rejected', 'slug' => 'rejected', 'color' => '#E74C3C', 'sort_order' => 4],
                ['name' => 'Escalated', 'slug' => 'escalated', 'color' => '#9B59B6', 'sort_order' => 5],
                ['name' => 'Cancelled', 'slug' => 'cancelled', 'color' => '#95A5A6', 'sort_order' => 6],
            ];
            foreach ($statuses as $s) {
                $this->upsert('approval_statuses', ['slug' => $s['slug']], $s);
            }
        }

        // ── Approval Types ────────────────────────────────────────
        if ($this->tableExists('approval_types')) {
            $types = [
                ['name' => 'Purchase Order', 'slug' => 'purchase_order', 'module' => 'billing'],
                ['name' => 'Expense Report', 'slug' => 'expense_report', 'module' => 'accounting'],
                ['name' => 'Leave Request', 'slug' => 'leave_request', 'module' => 'hrm'],
                ['name' => 'Budget Allocation', 'slug' => 'budget_allocation', 'module' => 'accounting'],
                ['name' => 'Invoice Override', 'slug' => 'invoice_override', 'module' => 'billing'],
                ['name' => 'Vendor Registration', 'slug' => 'vendor_registration', 'module' => 'billing'],
            ];
            foreach ($types as $t) {
                $this->upsert('approval_types', ['slug' => $t['slug']], $t);
            }
        }

        // ── Escalation Rules ──────────────────────────────────────
        if ($this->tableExists('escalation_rules')) {
            $rules = [
                ['name' => 'Auto-escalate after 48h', 'hours_threshold' => 48, 'action' => 'escalate_to_manager', 'is_active' => 1],
                ['name' => 'Auto-reject after 7 days', 'hours_threshold' => 168, 'action' => 'auto_reject', 'is_active' => 0],
                ['name' => 'Notify after 24h', 'hours_threshold' => 24, 'action' => 'send_reminder', 'is_active' => 1],
            ];
            foreach ($rules as $r) {
                $this->upsert('escalation_rules', ['name' => $r['name']], $r);
            }
        }
    }
}

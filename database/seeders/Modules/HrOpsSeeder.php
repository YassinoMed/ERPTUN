<?php

namespace Database\Seeders\Modules;

/**
 * HR Operations Seeder
 * Seeds: departments, designations, employment types, leave types, shift schedules
 */
class HrOpsSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'HR Operations';

    protected function seed(): void
    {
        // ── Departments ───────────────────────────────────────────
        if ($this->tableExists('departments')) {
            $departments = [
                ['name' => 'Administration', 'code' => 'ADMIN', 'is_active' => 1],
                ['name' => 'Human Resources', 'code' => 'HR', 'is_active' => 1],
                ['name' => 'Finance', 'code' => 'FIN', 'is_active' => 1],
                ['name' => 'IT / Technology', 'code' => 'IT', 'is_active' => 1],
                ['name' => 'Sales', 'code' => 'SALES', 'is_active' => 1],
                ['name' => 'Marketing', 'code' => 'MKT', 'is_active' => 1],
                ['name' => 'Operations', 'code' => 'OPS', 'is_active' => 1],
                ['name' => 'Customer Support', 'code' => 'CS', 'is_active' => 1],
                ['name' => 'R&D', 'code' => 'RND', 'is_active' => 1],
                ['name' => 'Legal', 'code' => 'LEGAL', 'is_active' => 1],
            ];
            foreach ($departments as $d) {
                $this->upsert('departments', ['code' => $d['code']], $d);
            }
        }

        // ── Designations ──────────────────────────────────────────
        if ($this->tableExists('designations')) {
            $designations = [
                ['name' => 'CEO', 'level' => 1],
                ['name' => 'CTO', 'level' => 2],
                ['name' => 'CFO', 'level' => 2],
                ['name' => 'Director', 'level' => 3],
                ['name' => 'Senior Manager', 'level' => 4],
                ['name' => 'Manager', 'level' => 5],
                ['name' => 'Team Lead', 'level' => 6],
                ['name' => 'Senior Developer', 'level' => 7],
                ['name' => 'Developer', 'level' => 8],
                ['name' => 'Junior Developer', 'level' => 9],
                ['name' => 'Intern', 'level' => 10],
            ];
            foreach ($designations as $d) {
                $this->upsert('designations', ['name' => $d['name']], $d);
            }
        }

        // ── Leave Types ───────────────────────────────────────────
        if ($this->tableExists('leave_types')) {
            $leaveTypes = [
                ['name' => 'Annual Leave', 'days_per_year' => 25, 'is_paid' => 1, 'is_active' => 1],
                ['name' => 'Sick Leave', 'days_per_year' => 10, 'is_paid' => 1, 'is_active' => 1],
                ['name' => 'Maternity Leave', 'days_per_year' => 90, 'is_paid' => 1, 'is_active' => 1],
                ['name' => 'Paternity Leave', 'days_per_year' => 14, 'is_paid' => 1, 'is_active' => 1],
                ['name' => 'Unpaid Leave', 'days_per_year' => 30, 'is_paid' => 0, 'is_active' => 1],
                ['name' => 'Bereavement', 'days_per_year' => 5, 'is_paid' => 1, 'is_active' => 1],
                ['name' => 'Study Leave', 'days_per_year' => 10, 'is_paid' => 0, 'is_active' => 1],
            ];
            foreach ($leaveTypes as $l) {
                $this->upsert('leave_types', ['name' => $l['name']], $l);
            }
        }

        // ── Employment Types ──────────────────────────────────────
        if ($this->tableExists('employment_types')) {
            $types = [
                ['name' => 'Full-Time', 'slug' => 'full_time'],
                ['name' => 'Part-Time', 'slug' => 'part_time'],
                ['name' => 'Contract', 'slug' => 'contract'],
                ['name' => 'Freelance', 'slug' => 'freelance'],
                ['name' => 'Internship', 'slug' => 'internship'],
                ['name' => 'Temporary', 'slug' => 'temporary'],
            ];
            foreach ($types as $t) {
                $this->upsert('employment_types', ['slug' => $t['slug']], $t);
            }
        }
    }
}

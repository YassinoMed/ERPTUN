<?php

namespace Database\Seeders\Modules;

/**
 * Quality Service Seeder
 * Seeds: inspection types, defect categories, quality standards
 */
class QualitySeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Quality';

    protected function seed(): void
    {
        if ($this->tableExists('inspection_types')) {
            $types = [
                ['name' => 'Incoming Inspection', 'slug' => 'incoming', 'description' => 'Quality check on received materials'],
                ['name' => 'In-Process Inspection', 'slug' => 'in_process', 'description' => 'Quality check during production'],
                ['name' => 'Final Inspection', 'slug' => 'final', 'description' => 'Quality check on finished products'],
                ['name' => 'Periodic Audit', 'slug' => 'periodic_audit', 'description' => 'Scheduled quality audits'],
                ['name' => 'Customer Complaint', 'slug' => 'customer_complaint', 'description' => 'Inspection triggered by customer feedback'],
            ];
            foreach ($types as $t) {
                $this->upsert('inspection_types', ['slug' => $t['slug']], $t);
            }
        }

        if ($this->tableExists('defect_categories')) {
            $categories = [
                ['name' => 'Dimensional', 'slug' => 'dimensional', 'severity' => 'high'],
                ['name' => 'Visual/Cosmetic', 'slug' => 'visual', 'severity' => 'low'],
                ['name' => 'Functional', 'slug' => 'functional', 'severity' => 'critical'],
                ['name' => 'Material', 'slug' => 'material', 'severity' => 'high'],
                ['name' => 'Packaging', 'slug' => 'packaging', 'severity' => 'medium'],
                ['name' => 'Documentation', 'slug' => 'documentation', 'severity' => 'low'],
            ];
            foreach ($categories as $c) {
                $this->upsert('defect_categories', ['slug' => $c['slug']], $c);
            }
        }

        if ($this->tableExists('quality_standards')) {
            $standards = [
                ['name' => 'ISO 9001:2015', 'code' => 'ISO-9001', 'is_active' => 1],
                ['name' => 'ISO 14001:2015', 'code' => 'ISO-14001', 'is_active' => 1],
                ['name' => 'ISO 22000', 'code' => 'ISO-22000', 'is_active' => 0],
                ['name' => 'HACCP', 'code' => 'HACCP', 'is_active' => 0],
                ['name' => 'CE Marking', 'code' => 'CE', 'is_active' => 1],
            ];
            foreach ($standards as $s) {
                $this->upsert('quality_standards', ['code' => $s['code']], $s);
            }
        }
    }
}

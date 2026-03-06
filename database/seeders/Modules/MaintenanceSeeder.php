<?php

namespace Database\Seeders\Modules;

/**
 * Maintenance Service Seeder
 * Seeds: maintenance types, priority levels, equipment categories, schedules
 */
class MaintenanceSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Maintenance';

    protected function seed(): void
    {
        if ($this->tableExists('maintenance_types')) {
            $types = [
                ['name' => 'Preventive', 'slug' => 'preventive', 'description' => 'Scheduled maintenance to prevent failures'],
                ['name' => 'Corrective', 'slug' => 'corrective', 'description' => 'Repair after failure'],
                ['name' => 'Predictive', 'slug' => 'predictive', 'description' => 'Data-driven maintenance scheduling'],
                ['name' => 'Condition-Based', 'slug' => 'condition_based', 'description' => 'Based on equipment condition monitoring'],
                ['name' => 'Emergency', 'slug' => 'emergency', 'description' => 'Unplanned urgent repairs'],
            ];
            foreach ($types as $t) {
                $this->upsert('maintenance_types', ['slug' => $t['slug']], $t);
            }
        }

        if ($this->tableExists('equipment_categories')) {
            $categories = [
                ['name' => 'Machinery', 'slug' => 'machinery'],
                ['name' => 'Electrical', 'slug' => 'electrical'],
                ['name' => 'HVAC', 'slug' => 'hvac'],
                ['name' => 'Plumbing', 'slug' => 'plumbing'],
                ['name' => 'IT Infrastructure', 'slug' => 'it_infrastructure'],
                ['name' => 'Vehicles', 'slug' => 'vehicles'],
                ['name' => 'Safety Equipment', 'slug' => 'safety_equipment'],
            ];
            foreach ($categories as $c) {
                $this->upsert('equipment_categories', ['slug' => $c['slug']], $c);
            }
        }

        if ($this->tableExists('maintenance_schedules')) {
            $schedules = [
                ['name' => 'Daily Check', 'frequency_type' => 'daily', 'frequency_value' => 1],
                ['name' => 'Weekly Inspection', 'frequency_type' => 'weekly', 'frequency_value' => 1],
                ['name' => 'Monthly Service', 'frequency_type' => 'monthly', 'frequency_value' => 1],
                ['name' => 'Quarterly Overhaul', 'frequency_type' => 'monthly', 'frequency_value' => 3],
                ['name' => 'Annual Certification', 'frequency_type' => 'yearly', 'frequency_value' => 1],
            ];
            foreach ($schedules as $s) {
                $this->upsert('maintenance_schedules', ['name' => $s['name']], $s);
            }
        }
    }
}

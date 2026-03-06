<?php

namespace Database\Seeders\Modules;

/**
 * SaaS Service Seeder — Multi-tenant Management
 * Seeds: tenant plans, feature toggles, SaaS settings
 */
class SaasSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'SaaS';

    protected function seed(): void
    {
        // ── SaaS Settings ─────────────────────────────────────────
        if ($this->tableExists('saas_settings')) {
            $settings = [
                ['key' => 'enable_registration', 'value' => '1'],
                ['key' => 'enable_trial', 'value' => '1'],
                ['key' => 'trial_days', 'value' => '14'],
                ['key' => 'default_plan', 'value' => 'free'],
                ['key' => 'max_tenants', 'value' => '-1'],
                ['key' => 'enable_subdomain', 'value' => '1'],
                ['key' => 'enable_custom_domain', 'value' => '0'],
                ['key' => 'storage_quota_mb', 'value' => '1024'],
                ['key' => 'enable_api_access', 'value' => '1'],
                ['key' => 'maintenance_mode', 'value' => '0'],
            ];
            foreach ($settings as $s) {
                $this->upsert('saas_settings', ['key' => $s['key']], $s);
            }
        }

        // ── Feature Toggles ───────────────────────────────────────
        if ($this->tableExists('feature_toggles')) {
            $features = [
                ['feature' => 'multi_language', 'enabled' => 1, 'tier' => 'starter'],
                ['feature' => 'api_access', 'enabled' => 1, 'tier' => 'starter'],
                ['feature' => 'custom_branding', 'enabled' => 1, 'tier' => 'business'],
                ['feature' => 'advanced_reports', 'enabled' => 1, 'tier' => 'business'],
                ['feature' => 'sso_integration', 'enabled' => 1, 'tier' => 'enterprise'],
                ['feature' => 'audit_trail', 'enabled' => 1, 'tier' => 'enterprise'],
                ['feature' => 'data_export', 'enabled' => 1, 'tier' => 'starter'],
                ['feature' => 'webhooks', 'enabled' => 1, 'tier' => 'business'],
            ];
            foreach ($features as $f) {
                $this->upsert('feature_toggles', ['feature' => $f['feature']], $f);
            }
        }
    }
}

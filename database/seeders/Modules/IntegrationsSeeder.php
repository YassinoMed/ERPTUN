<?php

namespace Database\Seeders\Modules;

/**
 * Integrations Service Seeder
 * Seeds: integration providers, webhook configurations, API keys settings
 */
class IntegrationsSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Integrations';

    protected function seed(): void
    {
        if ($this->tableExists('integration_providers')) {
            $providers = [
                ['name' => 'Stripe', 'slug' => 'stripe', 'category' => 'payment', 'is_active' => 0],
                ['name' => 'PayPal', 'slug' => 'paypal', 'category' => 'payment', 'is_active' => 0],
                ['name' => 'Mailchimp', 'slug' => 'mailchimp', 'category' => 'marketing', 'is_active' => 0],
                ['name' => 'Twilio', 'slug' => 'twilio', 'category' => 'communication', 'is_active' => 0],
                ['name' => 'Slack', 'slug' => 'slack', 'category' => 'communication', 'is_active' => 0],
                ['name' => 'Google Workspace', 'slug' => 'google_workspace', 'category' => 'productivity', 'is_active' => 0],
                ['name' => 'Microsoft 365', 'slug' => 'microsoft_365', 'category' => 'productivity', 'is_active' => 0],
                ['name' => 'Zapier', 'slug' => 'zapier', 'category' => 'automation', 'is_active' => 0],
                ['name' => 'QuickBooks', 'slug' => 'quickbooks', 'category' => 'accounting', 'is_active' => 0],
                ['name' => 'Xero', 'slug' => 'xero', 'category' => 'accounting', 'is_active' => 0],
            ];
            foreach ($providers as $p) {
                $this->upsert('integration_providers', ['slug' => $p['slug']], $p);
            }
        }

        if ($this->tableExists('integration_settings')) {
            $settings = [
                ['key' => 'webhook_secret_key', 'value' => ''],
                ['key' => 'webhook_retry_attempts', 'value' => '3'],
                ['key' => 'webhook_timeout_seconds', 'value' => '30'],
                ['key' => 'api_rate_limit_per_minute', 'value' => '60'],
                ['key' => 'enable_logging', 'value' => '1'],
            ];
            foreach ($settings as $s) {
                $this->upsert('integration_settings', ['key' => $s['key']], $s);
            }
        }
    }
}

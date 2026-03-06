<?php

namespace Database\Seeders\Modules;

/**
 * Platform Service Seeder
 * Seeds: platform settings, notification channels, system configuration
 */
class PlatformSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Platform';

    protected function seed(): void
    {
        if ($this->tableExists('platform_settings')) {
            $settings = [
                ['key' => 'platform_name', 'value' => 'ERPGo SaaS'],
                ['key' => 'platform_version', 'value' => '8.1'],
                ['key' => 'default_language', 'value' => 'en'],
                ['key' => 'default_timezone', 'value' => 'UTC'],
                ['key' => 'date_format', 'value' => 'Y-m-d'],
                ['key' => 'time_format', 'value' => 'H:i:s'],
                ['key' => 'enable_notifications', 'value' => '1'],
                ['key' => 'enable_audit_log', 'value' => '1'],
                ['key' => 'max_file_upload_mb', 'value' => '50'],
                ['key' => 'session_lifetime_minutes', 'value' => '120'],
                ['key' => 'enable_2fa', 'value' => '0'],
                ['key' => 'maintenance_mode', 'value' => '0'],
            ];
            foreach ($settings as $s) {
                $this->upsert('platform_settings', ['key' => $s['key']], $s);
            }
        }

        if ($this->tableExists('notification_channels')) {
            $channels = [
                ['name' => 'Email', 'driver' => 'mail', 'is_active' => 1],
                ['name' => 'SMS', 'driver' => 'sms', 'is_active' => 0],
                ['name' => 'Push Notification', 'driver' => 'push', 'is_active' => 0],
                ['name' => 'Slack', 'driver' => 'slack', 'is_active' => 0],
                ['name' => 'In-App', 'driver' => 'database', 'is_active' => 1],
                ['name' => 'Webhook', 'driver' => 'webhook', 'is_active' => 0],
            ];
            foreach ($channels as $c) {
                $this->upsert('notification_channels', ['driver' => $c['driver']], $c);
            }
        }
    }
}

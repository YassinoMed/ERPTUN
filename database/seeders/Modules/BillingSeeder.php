<?php

namespace Database\Seeders\Modules;

/**
 * Billing Service Seeder — Facturation
 * Seeds: payment methods, tax rates, invoice templates, billing settings
 */
class BillingSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Billing';

    protected function seed(): void
    {
        // ── Payment Methods ───────────────────────────────────────
        if ($this->tableExists('payment_methods')) {
            $methods = [
                ['name' => 'Cash', 'slug' => 'cash', 'is_active' => 1, 'sort_order' => 1],
                ['name' => 'Bank Transfer', 'slug' => 'bank_transfer', 'is_active' => 1, 'sort_order' => 2],
                ['name' => 'Credit Card', 'slug' => 'credit_card', 'is_active' => 1, 'sort_order' => 3],
                ['name' => 'PayPal', 'slug' => 'paypal', 'is_active' => 1, 'sort_order' => 4],
                ['name' => 'Stripe', 'slug' => 'stripe', 'is_active' => 1, 'sort_order' => 5],
                ['name' => 'Check', 'slug' => 'check', 'is_active' => 1, 'sort_order' => 6],
            ];
            foreach ($methods as $m) {
                $this->upsert('payment_methods', ['slug' => $m['slug']], $m);
            }
        }

        // ── Billing Settings ──────────────────────────────────────
        if ($this->tableExists('billing_settings')) {
            $settings = [
                ['key' => 'invoice_prefix', 'value' => 'INV-'],
                ['key' => 'bill_prefix', 'value' => 'BILL-'],
                ['key' => 'proposal_prefix', 'value' => 'PROP-'],
                ['key' => 'default_currency', 'value' => 'USD'],
                ['key' => 'decimal_places', 'value' => '2'],
                ['key' => 'default_tax_rate', 'value' => '20'],
                ['key' => 'auto_increment_start', 'value' => '1000'],
                ['key' => 'payment_terms_days', 'value' => '30'],
                ['key' => 'late_fee_percentage', 'value' => '2'],
                ['key' => 'enable_reminders', 'value' => '1'],
            ];
            foreach ($settings as $s) {
                $this->upsert('billing_settings', ['key' => $s['key']], $s);
            }
        }

        // ── Tax Rates ─────────────────────────────────────────────
        if ($this->tableExists('tax_rates')) {
            $taxes = [
                ['name' => 'TVA 20%', 'rate' => 20.00, 'type' => 'percentage', 'is_default' => 1],
                ['name' => 'TVA 10%', 'rate' => 10.00, 'type' => 'percentage', 'is_default' => 0],
                ['name' => 'TVA 5.5%', 'rate' => 5.50, 'type' => 'percentage', 'is_default' => 0],
                ['name' => 'Exempt', 'rate' => 0.00, 'type' => 'percentage', 'is_default' => 0],
            ];
            foreach ($taxes as $t) {
                $this->upsert('tax_rates', ['name' => $t['name']], $t);
            }
        }
    }
}

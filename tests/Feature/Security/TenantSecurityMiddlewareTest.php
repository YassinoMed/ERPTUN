<?php

namespace Tests\Feature\Security;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TenantSecurityMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_required_scope_cannot_access_tenant_finance_endpoint(): void
    {
        $user = User::factory()->create([
            'type' => 'company',
        ]);
        $user->created_by = $user->id;
        $user->save();

        Sanctum::actingAs($user, ['erp.crm.read']);

        $this->getJson("/api/tenants/{$user->id}/finance/invoices")
            ->assertForbidden()
            ->assertSeeText('Insufficient scope.');
    }

    public function test_user_cannot_access_another_tenant_context(): void
    {
        $owner = User::factory()->create([
            'type' => 'company',
        ]);
        $owner->created_by = $owner->id;
        $owner->save();

        $otherTenant = User::factory()->create([
            'type' => 'company',
        ]);
        $otherTenant->created_by = $otherTenant->id;
        $otherTenant->save();

        $employee = User::factory()->create([
            'type' => 'staff',
            'created_by' => $owner->id,
        ]);

        Sanctum::actingAs($employee, ['erp.finance.read']);

        $this->getJson("/api/tenants/{$otherTenant->id}/finance/invoices")
            ->assertForbidden()
            ->assertSeeText('You do not belong to this tenant.');
    }

    public function test_user_cannot_fetch_invoice_from_another_tenant_even_with_valid_scope(): void
    {
        $owner = User::factory()->create([
            'type' => 'company',
        ]);
        $owner->created_by = $owner->id;
        $owner->save();

        $employee = User::factory()->create([
            'type' => 'staff',
            'created_by' => $owner->id,
        ]);

        $otherTenant = User::factory()->create([
            'type' => 'company',
        ]);
        $otherTenant->created_by = $otherTenant->id;
        $otherTenant->save();

        $invoice = Invoice::create([
            'invoice_id' => 1,
            'customer_id' => 0,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'category_id' => 0,
            'status' => 'Draft',
            'created_by' => $otherTenant->id,
        ]);

        Sanctum::actingAs($employee, ['erp.finance.read']);

        $this->getJson("/api/tenants/{$owner->id}/finance/invoices/{$invoice->id}")
            ->assertNotFound();
    }

    public function test_user_can_create_invoice_with_valid_scope_and_tenant_context(): void
    {
        $owner = User::factory()->create([
            'type' => 'company',
        ]);
        $owner->created_by = $owner->id;
        $owner->save();

        Sanctum::actingAs($owner, ['erp.finance.write']);

        $this->postJson("/api/tenants/{$owner->id}/finance/invoices", [
            'customer_id' => 0,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDay()->toDateString(),
            'category_id' => 0,
            'status' => 'Draft',
        ])->assertCreated()
            ->assertJsonPath('data.invoice.created_by', $owner->id);
    }
}

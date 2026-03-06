<?php

namespace Tests\Feature\Microservices;

use App\Models\Bill;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_accounting_endpoints_return_success()
    {
        $user = User::factory()->create([
            'type' => 'company',
        ]);
        $user->created_by = $user->id;
        $user->save();

        Invoice::create([
            'invoice_id' => 1,
            'customer_id' => 0,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'category_id' => 0,
            'status' => 0,
            'created_by' => $user->id,
        ]);

        Bill::create([
            'bill_id' => '1',
            'vender_id' => 0,
            'bill_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'category_id' => 0,
            'status' => 0,
            'created_by' => $user->id,
        ]);

        Expense::create([
            'name' => 'Expense',
            'date' => now()->toDateString(),
            'amount' => 10,
            'created_by' => $user->id,
        ]);

        JournalEntry::create([
            'date' => now()->toDateString(),
            'reference' => 'REF',
            'created_by' => $user->id,
        ]);

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/accounting/invoices')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['invoices']]);

        $this->getJson('/api/accounting/bills')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['bills']]);

        $this->getJson('/api/accounting/expenses')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['expenses']]);

        $this->getJson('/api/accounting/journals')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['journals']]);
    }
}

<?php

namespace Tests\Feature\Microservices;

use App\Models\Customer;
use App\Models\Proposal;
use App\Models\Quotation;
use App\Models\User;
use App\Models\warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_endpoints_return_success()
    {
        $user = User::factory()->create([
            'type' => 'company',
        ]);
        $user->created_by = $user->id;
        $user->save();

        $customer = Customer::create([
            'customer_id' => 1,
            'name' => 'Customer A',
            'email' => 'customer@example.com',
            'created_by' => $user->id,
        ]);

        $warehouse = warehouse::create([
            'name' => 'Sales Warehouse',
            'address' => 'Address',
            'city' => 'City',
            'city_zip' => '00000',
            'created_by' => $user->id,
        ]);

        Quotation::create([
            'quotation_id' => 1,
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'quotation_date' => now()->toDateString(),
            'category_id' => 0,
            'status' => 0,
            'created_by' => $user->id,
        ]);

        Proposal::create([
            'proposal_id' => 1,
            'customer_id' => $customer->id,
            'issue_date' => now()->toDateString(),
            'category_id' => 0,
            'status' => 0,
            'created_by' => $user->id,
        ]);

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/sales/quotations')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['quotations']]);

        $this->getJson('/api/sales/proposals')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['proposals']]);
    }
}

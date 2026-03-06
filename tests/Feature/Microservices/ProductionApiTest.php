<?php

namespace Tests\Feature\Microservices;

use App\Models\ProductService;
use App\Models\ProductionBom;
use App\Models\ProductionOrder;
use App\Models\ProductionWorkCenter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_endpoints_return_success()
    {
        $user = User::factory()->create([
            'type' => 'company',
        ]);
        $user->created_by = $user->id;
        $user->save();

        $product = ProductService::create([
            'name' => 'Product A',
            'sku' => 'SKU-001',
            'sale_price' => 10,
            'purchase_price' => 5,
            'quantity' => 1,
            'tax_id' => null,
            'category_id' => 0,
            'unit_id' => 0,
            'type' => 'product',
            'sale_chartaccount_id' => 0,
            'expense_chartaccount_id' => 0,
            'created_by' => $user->id,
        ]);

        $workCenter = ProductionWorkCenter::create([
            'name' => 'Assembly Line 1',
            'type' => 'machine',
            'cost_per_hour' => 10,
            'created_by' => $user->id,
        ]);

        ProductionBom::create([
            'product_id' => $product->id,
            'code' => 'BOM-001',
            'name' => 'Base BOM',
            'created_by' => $user->id,
        ]);

        ProductionOrder::create([
            'order_number' => 1,
            'product_id' => $product->id,
            'work_center_id' => $workCenter->id,
            'quantity_planned' => 10,
            'created_by' => $user->id,
        ]);

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/production/work-centers')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['work_centers']]);

        $this->getJson('/api/production/boms')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['boms']]);

        $this->getJson('/api/production/orders')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['orders']]);
    }
}

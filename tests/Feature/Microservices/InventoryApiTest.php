<?php

namespace Tests\Feature\Microservices;

use App\Models\ProductService;
use App\Models\User;
use App\Models\WarehouseProduct;
use App\Models\warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_endpoints_return_success()
    {
        $user = User::factory()->create([
            'type' => 'company',
        ]);
        $user->created_by = $user->id;
        $user->save();

        $warehouse = warehouse::create([
            'name' => 'Main Warehouse',
            'address' => 'Address',
            'city' => 'City',
            'city_zip' => '00000',
            'created_by' => $user->id,
        ]);

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

        WarehouseProduct::create([
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'created_by' => $user->id,
        ]);

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/inventory/products')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['products']]);

        $this->getJson('/api/inventory/warehouses')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['warehouses']]);

        $this->getJson('/api/inventory/warehouse-products')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['warehouse_products']]);
    }
}

<?php

namespace Tests\Feature\Microservices;

use App\Models\ProductService;
use App\Models\StockReport;
use App\Models\User;
use App\Models\WarehouseProduct;
use App\Models\warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WmsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_wms_endpoints_return_success()
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

        $report = StockReport::create([
            'product_id' => $product->id,
            'quantity' => 5,
            'type' => 'adjustment',
            'type_id' => 1,
            'description' => 'Initial stock',
        ]);
        $report->created_by = $user->id;
        $report->save();

        Sanctum::actingAs($user, ['*']);

        $this->getJson('/api/wms/warehouses')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['warehouses']]);

        $this->getJson('/api/wms/warehouse-products')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['warehouse_products']]);

        $this->getJson('/api/wms/stock-reports')
            ->assertStatus(200)
            ->assertJsonStructure(['is_success', 'message', 'data' => ['stock_reports']]);
    }
}

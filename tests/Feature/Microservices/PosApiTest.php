<?php

namespace Tests\Feature\Microservices;

use App\Models\Pos;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PosApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_list_returns_success()
    {
        $user = User::factory()->create([
            'type' => 'company',
        ]);
        $user->created_by = $user->id;
        $user->save();

        Pos::create([
            'pos_id' => 1,
            'customer_id' => 0,
            'warehouse_id' => 0,
            'pos_date' => now()->toDateString(),
            'category_id' => 0,
            'status' => 0,
            'shipping_display' => 1,
            'created_by' => $user->id,
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/pos');

        $response->assertStatus(200)->assertJsonStructure([
            'is_success',
            'message',
            'data' => [
                'pos',
            ],
        ]);
    }
}

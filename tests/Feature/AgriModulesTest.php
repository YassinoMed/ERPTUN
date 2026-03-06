<?php

namespace Tests\Feature;

use App\Models\AgriCoopMember;
use App\Models\AgriCooperative;
use App\Models\AgriCropPlan;
use App\Models\AgriParcel;
use App\Models\AgriPurchaseContract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AgriModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_traceability_lot_can_be_created(): void
    {
        Permission::query()->firstOrCreate(['name' => 'manage agri traceability', 'guard_name' => 'web']);

        $user = User::factory()->create(['type' => 'super admin']);
        $user->givePermissionTo('manage agri traceability');

        $response = $this->actingAs($user)->post(route('agri.traceability.lots.store'), [
            'code' => 'LOT-001',
            'name' => 'Lot A',
            'crop_type' => 'Wheat',
            'quantity' => 1200,
            'unit' => 'kg',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('agri_lots', [
            'code' => 'LOT-001',
            'name' => 'Lot A',
            'crop_type' => 'Wheat',
            'created_by' => $user->creatorId(),
        ]);
    }

    public function test_crop_plan_can_be_created(): void
    {
        Permission::query()->firstOrCreate(['name' => 'manage agri planning', 'guard_name' => 'web']);

        $user = User::factory()->create(['type' => 'super admin']);
        $user->givePermissionTo('manage agri planning');

        $parcel = AgriParcel::create([
            'name' => 'Parcel A',
            'code' => 'P-A',
            'area' => 2.5,
            'area_unit' => 'ha',
            'created_by' => $user->creatorId(),
        ]);

        $response = $this->actingAs($user)->post(route('agri.planning.plans.store'), [
            'parcel_id' => $parcel->id,
            'crop_name' => 'Tomato',
            'start_date' => '2026-02-01',
            'end_date' => '2026-05-01',
            'expected_yield' => 1500,
            'yield_unit' => 'kg',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('agri_crop_plans', [
            'parcel_id' => $parcel->id,
            'crop_name' => 'Tomato',
            'created_by' => $user->creatorId(),
        ]);
    }

    public function test_cooperative_distribution_can_be_created(): void
    {
        Permission::query()->firstOrCreate(['name' => 'manage agri cooperative', 'guard_name' => 'web']);

        $user = User::factory()->create(['type' => 'super admin']);
        $user->givePermissionTo('manage agri cooperative');

        $coop = AgriCooperative::create([
            'name' => 'Coop A',
            'code' => 'COOP-A',
            'currency' => 'USD',
            'created_by' => $user->creatorId(),
        ]);

        $member = AgriCoopMember::create([
            'cooperative_id' => $coop->id,
            'name' => 'Member A',
            'member_code' => 'M-1',
            'share_percent' => 60,
            'advance_balance' => 50,
            'created_by' => $user->creatorId(),
        ]);

        $this->actingAs($user)->post(route('agri.cooperatives.deliveries.store'), [
            'cooperative_id' => $coop->id,
            'member_id' => $member->id,
            'crop_type' => 'Coffee',
            'quantity' => 100,
            'unit' => 'kg',
            'delivery_date' => '2026-02-10',
            'price_per_unit' => 2,
        ]);

        $response = $this->actingAs($user)->post(route('agri.cooperatives.distributions.create'), [
            'cooperative_id' => $coop->id,
            'period_start' => '2026-02-01',
            'period_end' => '2026-02-28',
            'distribution_method' => 'quantity',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('agri_revenue_distributions', [
            'cooperative_id' => $coop->id,
            'created_by' => $user->creatorId(),
        ]);
    }

    public function test_contract_and_hedge_can_be_created(): void
    {
        Permission::query()->firstOrCreate(['name' => 'manage agri hedging', 'guard_name' => 'web']);

        $user = User::factory()->create(['type' => 'super admin']);
        $user->givePermissionTo('manage agri hedging');

        $response = $this->actingAs($user)->post(route('agri.contracts.store'), [
            'contract_number' => 'C-100',
            'buyer_name' => 'Buyer X',
            'crop_type' => 'Corn',
            'quantity' => 500,
            'unit' => 'kg',
            'fixed_price' => 3.5,
            'price_currency' => 'USD',
            'delivery_start' => '2026-03-01',
            'delivery_end' => '2026-04-01',
            'hedge_ratio' => 50,
        ]);

        $response->assertStatus(302);

        $contract = AgriPurchaseContract::query()->where('contract_number', 'C-100')->firstOrFail();

        $hedgeResponse = $this->actingAs($user)->post(route('agri.contracts.hedges.store'), [
            'contract_id' => $contract->id,
            'instrument' => 'FUT',
            'position_type' => 'future',
            'opened_at' => '2026-03-01',
        ]);

        $hedgeResponse->assertStatus(302);
        $this->assertDatabaseHas('agri_hedge_positions', [
            'contract_id' => $contract->id,
            'created_by' => $user->creatorId(),
        ]);
    }
}

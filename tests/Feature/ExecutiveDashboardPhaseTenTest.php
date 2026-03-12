<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ExecutiveDashboardPhaseTenTest extends TestCase
{
    use RefreshDatabase;

    public function test_executive_dashboard_renders_operational_pulse_for_cross_module_permissions(): void
    {
        foreach ([
            'manage retail operations',
            'manage medical operations',
            'manage industrial resource',
            'manage agri operations',
        ] as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $user = User::factory()->create([
            'type' => 'company',
            'email_verified_at' => now(),
        ]);

        $user->givePermissionTo([
            'manage retail operations',
            'manage medical operations',
            'manage industrial resource',
            'manage agri operations',
        ]);

        $response = $this->actingAs($user)->get(route('executive.dashboard'));

        $response->assertOk();
        $response->assertSee('Executive Overview');
    }
}

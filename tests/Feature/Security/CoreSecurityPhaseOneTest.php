<?php

namespace Tests\Feature\Security;

use App\Models\ProductService;
use App\Models\Customer;
use App\Models\Patient;
use App\Models\Security\TwoFactorAuth;
use App\Models\User;
use App\Models\UserSessionLog;
use App\Services\Core\CoreArchiveService;
use App\Services\Core\SecurityAccessService;
use App\Models\ProductServiceCategory;
use App\Models\ProductServiceUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CoreSecurityPhaseOneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware();
    }

    public function test_security_center_accepts_service_scope_assignments(): void
    {
        $owner = User::factory()->create(['type' => 'super admin', 'created_by' => 1]);
        $owner->created_by = $owner->id;
        $owner->save();

        $staff = User::factory()->create(['created_by' => $owner->id, 'type' => 'employee']);
        Permission::create(['name' => 'manage access scope', 'guard_name' => 'web']);
        $owner->givePermissionTo('manage access scope');

        $category = ProductServiceCategory::create([
            'name' => 'Services',
            'type' => 'product & service',
            'created_by' => $owner->id,
        ]);
        $unit = ProductServiceUnit::create([
            'name' => 'Unit',
            'created_by' => $owner->id,
        ]);

        $service = ProductService::create([
            'name' => 'Installation',
            'sku' => 'SRV-001',
            'sale_price' => 100,
            'purchase_price' => 0,
            'tax_id' => null,
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'type' => 'service',
            'sale_chartaccount_id' => 0,
            'expense_chartaccount_id' => 0,
            'created_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->post(route('core.security.scope.store'), [
            'user_id' => $staff->id,
            'scope_type' => 'service',
            'scope_ids' => [$service->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_access_scopes', [
            'user_id' => $staff->id,
            'scope_type' => 'service',
            'scope_id' => $service->id,
            'created_by' => $owner->id,
        ]);
    }

    public function test_security_center_can_revoke_all_sessions_for_user(): void
    {
        $owner = User::factory()->create(['type' => 'super admin', 'created_by' => 1]);
        $owner->created_by = $owner->id;
        $owner->save();

        $staff = User::factory()->create(['created_by' => $owner->id, 'type' => 'employee']);
        Permission::create(['name' => 'revoke user session', 'guard_name' => 'web']);
        $owner->givePermissionTo('revoke user session');

        UserSessionLog::create([
            'user_id' => $staff->id,
            'created_by' => $owner->id,
            'session_id' => 'sess-a',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'login_at' => now(),
            'last_seen_at' => now(),
            'is_active' => true,
        ]);

        $revoked = app(SecurityAccessService::class)->revokeUserSessions($staff, $owner->creatorId());

        $this->assertSame(1, $revoked);
        $this->assertDatabaseHas('user_session_logs', [
            'user_id' => $staff->id,
            'session_id' => 'sess-a',
            'is_active' => false,
        ]);
    }

    public function test_security_center_verifies_backup_code(): void
    {
        $owner = User::factory()->create(['type' => 'super admin', 'created_by' => 1]);
        $owner->created_by = $owner->id;
        $owner->save();

        Permission::create(['name' => 'manage two factor auth', 'guard_name' => 'web']);
        $owner->givePermissionTo('manage two factor auth');

        $twoFactor = TwoFactorAuth::create([
            'user_id' => $owner->id,
            'provider' => 'email',
            'secret' => 'ABC123',
            'enabled_at' => now(),
            'backup_codes' => ['BACKUP001'],
        ]);

        $response = $this->actingAs($owner)->post(route('core.security.twofactor.verify'), [
            'code' => 'BACKUP001',
        ]);

        $response->assertRedirect();
        $twoFactor->refresh();
        $this->assertSame([], $twoFactor->backup_codes);
    }

    public function test_security_center_can_archive_and_restore_records(): void
    {
        $owner = User::factory()->create(['type' => 'super admin', 'created_by' => 1]);
        $owner->created_by = $owner->id;
        $owner->save();

        Permission::create(['name' => 'manage security center', 'guard_name' => 'web']);
        $owner->givePermissionTo('manage security center');

        $customer = Customer::create([
            'customer_id' => 1,
            'name' => 'Archive Me',
            'email' => 'archive@example.com',
            'contact' => '12345678',
            'created_by' => $owner->id,
        ]);

        $archivedRecord = app(CoreArchiveService::class)->archive($owner, 'customer', $customer->id, 'Merged into master record');
        $this->assertDatabaseHas('archived_records', [
            'created_by' => $owner->id,
            'record_type' => Customer::class,
            'record_id' => $customer->id,
        ]);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'archived_by' => $owner->id,
        ]);

        app(CoreArchiveService::class)->restore($owner, $archivedRecord);
        $this->assertDatabaseHas('archived_records', [
            'id' => $archivedRecord->id,
            'restored_by' => $owner->id,
        ]);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'archived_at' => null,
            'archived_by' => null,
        ]);
    }

    public function test_security_center_scans_patient_duplicates(): void
    {
        $owner = User::factory()->create(['type' => 'super admin', 'created_by' => 1]);
        $owner->created_by = $owner->id;
        $owner->save();

        Permission::create(['name' => 'manage data quality issue', 'guard_name' => 'web']);
        $owner->givePermissionTo('manage data quality issue');

        Patient::create([
            'first_name' => 'Meriem',
            'last_name' => 'Ben Ali',
            'email' => 'patient@example.com',
            'phone' => '20111222',
            'created_by' => $owner->id,
        ]);

        Patient::create([
            'first_name' => 'Meriem',
            'last_name' => 'Benali',
            'email' => 'patient@example.com',
            'phone' => '20111222',
            'created_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)->post(route('core.security.scan'));

        $response->assertRedirect();
        $this->assertDatabaseHas('data_quality_issues', [
            'created_by' => $owner->id,
            'module' => 'patients',
            'record_type' => Patient::class,
            'duplicate_type' => Patient::class,
        ]);
    }
}

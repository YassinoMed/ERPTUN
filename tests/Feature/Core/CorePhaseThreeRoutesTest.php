<?php

namespace Tests\Feature\Core;

use Tests\TestCase;

class CorePhaseThreeRoutesTest extends TestCase
{
    public function test_phase_three_route_names_are_registered(): void
    {
        $this->assertSame('/core/onboarding/plan-requests/1/approve', route('core.onboarding.plan-requests.approve', 1, false));
        $this->assertSame('/core/onboarding/plan-requests/1/reject', route('core.onboarding.plan-requests.reject', 1, false));
        $this->assertSame('/core/tenant-addons/1/renew', route('core.addons.renew', 1, false));
        $this->assertSame('/core/usages/sync', route('core.usages.sync', absolute: false));
    }
}

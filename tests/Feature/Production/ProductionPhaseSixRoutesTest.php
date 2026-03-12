<?php

namespace Tests\Feature\Production;

use Tests\TestCase;

class ProductionPhaseSixRoutesTest extends TestCase
{
    public function test_phase_six_route_names_are_registered(): void
    {
        $this->assertSame('/production/planning', route('production.planning', absolute: false));
        $this->assertSame('/production/planning/realtime', route('production.planning.realtime', absolute: false));
        $this->assertSame('/production/planning/analytics', route('production.planning.analytics', absolute: false));
        $this->assertSame('/production/planning/bi', route('production.planning.bi', absolute: false));
    }
}

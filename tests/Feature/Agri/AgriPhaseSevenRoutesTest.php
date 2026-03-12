<?php

namespace Tests\Feature\Agri;

use Tests\TestCase;

class AgriPhaseSevenRoutesTest extends TestCase
{
    public function test_phase_seven_agri_routes_are_registered(): void
    {
        $this->assertSame('/agri/traceability/network', route('agri.traceability.network', absolute: false));
        $this->assertSame('/agri/planning/dashboard', route('agri.planning.dashboard', absolute: false));
        $this->assertSame('/agri/operations/fefo', route('agri.operations.fefo', absolute: false));
        $this->assertSame('/agri/reports', route('agri.reports.index', absolute: false));
    }
}

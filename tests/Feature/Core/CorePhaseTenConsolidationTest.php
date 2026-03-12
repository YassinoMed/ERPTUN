<?php

namespace Tests\Feature\Core;

use Tests\TestCase;

class CorePhaseTenConsolidationTest extends TestCase
{
    public function test_phase_ten_core_routes_are_registered(): void
    {
        $this->assertSame('/core/consolidation', route('core.consolidation', [], false));
        $this->assertSame('/core/help-center', route('core.help-center', [], false));
        $this->assertSame('/core/security', route('core.security.index', [], false));
    }
}

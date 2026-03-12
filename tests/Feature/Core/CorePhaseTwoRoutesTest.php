<?php

namespace Tests\Feature\Core;

use Tests\TestCase;

class CorePhaseTwoRoutesTest extends TestCase
{
    public function test_phase_two_route_names_are_registered(): void
    {
        $this->assertSame('/approval-requests', route('approval-requests.store', absolute: false));
        $this->assertSame('/approval-requests/escalate-all', route('approval-requests.escalate-all', absolute: false));
        $this->assertSame('/core/exports/dispatch-due', route('core.exports.dispatch-due', absolute: false));
        $this->assertSame('/core/reports/dispatch-due', route('core.reports.schedule.dispatch-due', absolute: false));
    }
}

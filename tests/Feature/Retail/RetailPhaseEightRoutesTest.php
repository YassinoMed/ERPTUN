<?php

namespace Tests\Feature\Retail;

use Tests\TestCase;

class RetailPhaseEightRoutesTest extends TestCase
{
    public function test_phase_eight_retail_routes_are_registered(): void
    {
        $this->assertSame('/retail-operations/bi', route('retail.operations.bi', absolute: false));
        $this->assertSame('/retail-operations/procurement-requests', route('retail.operations.procurement.store', absolute: false));
        $this->assertSame('/retail-operations/replenishments', route('retail.operations.replenishments.store', absolute: false));
        $this->assertSame('/retail/customer-portal', route('retail.customer-portal', absolute: false));
        $this->assertSame('/retail/supplier-portal', route('retail.supplier-portal', absolute: false));
    }
}

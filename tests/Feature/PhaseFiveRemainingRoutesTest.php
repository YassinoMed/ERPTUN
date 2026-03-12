<?php

namespace Tests\Feature;

use Tests\TestCase;

class PhaseFiveRemainingRoutesTest extends TestCase
{
    public function test_phase_five_remaining_route_names_are_registered(): void
    {
        $this->assertSame('/partners', route('partners.index', absolute: false));
        $this->assertSame('/vendor-ratings', route('vendor-ratings.index', absolute: false));
        $this->assertSame('/product-lifecycle-records', route('product-lifecycle-records.index', absolute: false));
        $this->assertSame('/lims-records', route('lims-records.index', absolute: false));
        $this->assertSame('/hse-incidents', route('hse-incidents.index', absolute: false));
        $this->assertSame('/succession-plans', route('succession-plans.index', absolute: false));
        $this->assertSame('/event-tickets', route('event-tickets.index', absolute: false));
        $this->assertSame('/microfinance-loans', route('microfinance-loans.index', absolute: false));
        $this->assertSame('/leasing-contracts', route('leasing-contracts.index', absolute: false));
        $this->assertSame('/transport-shipments', route('transport-shipments.index', absolute: false));
    }
}

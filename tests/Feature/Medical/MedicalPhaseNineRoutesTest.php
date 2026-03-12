<?php

namespace Tests\Feature\Medical;

use Tests\TestCase;

class MedicalPhaseNineRoutesTest extends TestCase
{
    public function test_phase_nine_medical_routes_are_registered(): void
    {
        $this->assertTrue(route('medical.operations.laboratory', [], false) === '/medical-operations/laboratory');
        $this->assertTrue(route('medical.operations.surgery', [], false) === '/medical-operations/surgery');
        $this->assertTrue(route('medical.operations.biomedical', [], false) === '/medical-operations/biomedical');
        $this->assertTrue(route('medical.operations.specialties', [], false) === '/medical-operations/specialties');
        $this->assertTrue(route('medical.patient-portal', [], false) === '/medical/patient-portal');
    }
}

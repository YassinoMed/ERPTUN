<?php

namespace Database\Seeders\Modules;

/**
 * Industry Service Seeder
 * Seeds: industry categories, compliance frameworks, certifications
 */
class IndustrySeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'Industry';

    protected function seed(): void
    {
        if ($this->tableExists('industry_categories')) {
            $categories = [
                ['name' => 'Manufacturing', 'slug' => 'manufacturing', 'code' => 'MFG'],
                ['name' => 'Agriculture', 'slug' => 'agriculture', 'code' => 'AGR'],
                ['name' => 'Construction', 'slug' => 'construction', 'code' => 'CON'],
                ['name' => 'Healthcare', 'slug' => 'healthcare', 'code' => 'HCR'],
                ['name' => 'Hospitality', 'slug' => 'hospitality', 'code' => 'HSP'],
                ['name' => 'Retail', 'slug' => 'retail', 'code' => 'RTL'],
                ['name' => 'Technology', 'slug' => 'technology', 'code' => 'TCH'],
                ['name' => 'Energy', 'slug' => 'energy', 'code' => 'NRG'],
                ['name' => 'Logistics', 'slug' => 'logistics', 'code' => 'LOG'],
                ['name' => 'Education', 'slug' => 'education', 'code' => 'EDU'],
            ];
            foreach ($categories as $c) {
                $this->upsert('industry_categories', ['slug' => $c['slug']], $c);
            }
        }

        if ($this->tableExists('compliance_frameworks')) {
            $frameworks = [
                ['name' => 'ISO 9001', 'code' => 'ISO-9001', 'industry' => 'all'],
                ['name' => 'GDPR', 'code' => 'GDPR', 'industry' => 'technology'],
                ['name' => 'HIPAA', 'code' => 'HIPAA', 'industry' => 'healthcare'],
                ['name' => 'SOC 2', 'code' => 'SOC2', 'industry' => 'technology'],
                ['name' => 'ISO 14001', 'code' => 'ISO-14001', 'industry' => 'manufacturing'],
            ];
            foreach ($frameworks as $f) {
                $this->upsert('compliance_frameworks', ['code' => $f['code']], $f);
            }
        }
    }
}

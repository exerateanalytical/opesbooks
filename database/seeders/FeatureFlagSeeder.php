<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagSeeder extends Seeder
{
    public function run(): void
    {
        $flags = [
            ['key' => 'ai_suggestions',     'name' => 'Suggestions IA',        'enabled_for' => 'all'],
            ['key' => 'mecef_integration',  'name' => 'Intégration MECeF',     'enabled_for' => 'all'],
            ['key' => 'multi_company',      'name' => 'Multi-entreprises',     'enabled_for' => 'plan_starter_plus'],
            ['key' => 'crm_module',         'name' => 'Module CRM',            'enabled_for' => 'all'],
            ['key' => 'projects_module',    'name' => 'Module Projets',        'enabled_for' => 'plan_starter_plus'],
            ['key' => 'api_access',         'name' => 'Accès API',             'enabled_for' => 'plan_starter_plus'],
            ['key' => 'cemac_countries',    'name' => 'Pays CEMAC',            'enabled_for' => 'all'],
            ['key' => 'advanced_reports',   'name' => 'Rapports avancés',      'enabled_for' => 'plan_business_plus'],
            ['key' => 'group_consolidation','name' => 'Consolidation groupe',  'enabled_for' => 'plan_enterprise_only'],
            ['key' => 'white_label',        'name' => 'Marque blanche',        'enabled_for' => 'plan_enterprise_only'],
            ['key' => 'offline_sync',       'name' => 'Sync hors ligne',       'enabled_for' => 'all'],
        ];

        foreach ($flags as $f) {
            FeatureFlag::updateOrCreate(['key' => $f['key']], $f);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\CountryConfig;
use Illuminate\Database\Seeder;

class CountryConfigSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['country_code' => 'CM', 'country_name_fr' => 'Cameroun',            'country_name_en' => 'Cameroon',           'flag' => '🇨🇲', 'vat_standard_rate' => 19.25, 'vat_reduced_rate' => 0,    'regulatory_body_name' => 'DGI Cameroun',             'company_id_label' => 'NIU', 'dsf_equivalent_name' => 'DSF'],
            ['country_code' => 'GA', 'country_name_fr' => 'Gabon',               'country_name_en' => 'Gabon',              'flag' => '🇬🇦', 'vat_standard_rate' => 18.00, 'vat_reduced_rate' => 10,   'regulatory_body_name' => 'DGI Gabon',                'company_id_label' => 'NIF', 'dsf_equivalent_name' => 'Liasse Fiscale'],
            ['country_code' => 'CG', 'country_name_fr' => 'Congo',               'country_name_en' => 'Congo',              'flag' => '🇨🇬', 'vat_standard_rate' => 18.00, 'vat_reduced_rate' => 5,    'regulatory_body_name' => 'DGI Congo',                'company_id_label' => 'NIU', 'dsf_equivalent_name' => 'Liasse Fiscale'],
            ['country_code' => 'TD', 'country_name_fr' => 'Tchad',               'country_name_en' => 'Chad',               'flag' => '🇹🇩', 'vat_standard_rate' => 18.00, 'vat_reduced_rate' => 9,    'regulatory_body_name' => 'DGI Tchad',                'company_id_label' => 'NIF', 'dsf_equivalent_name' => 'Liasse Fiscale'],
            ['country_code' => 'GQ', 'country_name_fr' => 'Guinée Équatoriale',  'country_name_en' => 'Equatorial Guinea',  'flag' => '🇬🇶', 'vat_standard_rate' => 15.00, 'vat_reduced_rate' => 6,    'regulatory_body_name' => 'DGI Guinée Équatoriale',   'company_id_label' => 'NIF', 'dsf_equivalent_name' => 'Liasse Fiscale'],
            ['country_code' => 'CF', 'country_name_fr' => 'Centrafrique',        'country_name_en' => 'Central African Republic', 'flag' => '🇨🇫', 'vat_standard_rate' => 19.00, 'vat_reduced_rate' => null, 'regulatory_body_name' => 'DGI Centrafrique',         'company_id_label' => 'NIF', 'dsf_equivalent_name' => 'Liasse Fiscale'],
        ];

        foreach ($countries as $c) {
            CountryConfig::updateOrCreate(
                ['country_code' => $c['country_code']],
                array_merge($c, ['currency_code' => 'XAF', 'fiscal_year_end_month' => 12, 'active' => true]),
            );
        }
    }
}

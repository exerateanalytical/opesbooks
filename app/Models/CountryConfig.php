<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryConfig extends Model
{
    protected $fillable = [
        'country_code', 'country_name_fr', 'country_name_en', 'flag',
        'currency_code', 'vat_standard_rate', 'vat_reduced_rate',
        'regulatory_body_name', 'company_id_label', 'fiscal_year_end_month',
        'dsf_equivalent_name', 'active',
    ];

    protected $casts = [
        'vat_standard_rate' => 'decimal:2',
        'vat_reduced_rate'  => 'decimal:2',
        'active'            => 'boolean',
    ];

    public function companies()
    {
        return $this->hasMany(Company::class, 'country_code', 'country_code');
    }
}

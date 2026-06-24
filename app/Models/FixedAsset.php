<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FixedAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'asset_code', 'category',
        'syscohada_account_code', 'acquisition_date', 'acquisition_cost',
        'residual_value', 'useful_life_months', 'depreciation_method',
        'accumulated_depreciation', 'acquisition_journal_entry_id',
        'disposal_date', 'disposal_proceeds', 'disposal_journal_entry_id',
        'is_active',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'disposal_date'    => 'date',
        'is_active'        => 'boolean',
    ];

    public function company()            { return $this->belongsTo(Company::class); }
    public function depreciationEntries(){ return $this->hasMany(DepreciationEntry::class); }

    public function monthlyDepreciation(): float
    {
        $depreciable = $this->acquisition_cost - $this->residual_value;
        return round($depreciable / max($this->useful_life_months, 1), 2);
    }

    public function bookValue(): float
    {
        return max(0, $this->acquisition_cost - $this->accumulated_depreciation - $this->residual_value);
    }

    public function isFullyDepreciated(): bool
    {
        return $this->accumulated_depreciation >= ($this->acquisition_cost - $this->residual_value);
    }

    /** Corresponding depreciation expense account (28xxxx) */
    public function depreciationAccountCode(): string
    {
        return match($this->category) {
            'BUILDING'     => '281300',
            'MACHINERY'    => '284100',
            'VEHICLE'      => '284400',
            'FURNITURE'    => '285000',
            'IT_EQUIPMENT' => '285100',
            default        => '285000',
        };
    }

    /** Accumulated depreciation account (28xxxx offset) */
    public function accumulatedDepreciationAccountCode(): string
    {
        return match($this->category) {
            'BUILDING'     => '281310',
            'MACHINERY'    => '284110',
            'VEHICLE'      => '284410',
            'FURNITURE'    => '285010',
            'IT_EQUIPMENT' => '285110',
            default        => '285010',
        };
    }
}

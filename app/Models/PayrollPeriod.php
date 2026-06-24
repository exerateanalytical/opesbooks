<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'company_id', 'period_month', 'period_year', 'status',
        'total_gross', 'total_cnps_employee', 'total_cnps_employer',
        'total_irpp', 'total_cac_irpp', 'total_net', 'journal_entry_id',
    ];

    protected $casts = [
        'total_gross' => 'float', 'total_cnps_employee' => 'float',
        'total_cnps_employer' => 'float', 'total_irpp' => 'float',
        'total_cac_irpp' => 'float', 'total_net' => 'float',
    ];

    public function company()      { return $this->belongsTo(Company::class); }
    public function lines()        { return $this->hasMany(PayrollLine::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLine extends Model
{
    protected $fillable = [
        'payroll_period_id', 'employee_id', 'gross_salary',
        'cnps_employee', 'cnps_employer', 'irpp', 'cac_irpp', 'rav', 'net_salary', 'tsr_employer',
    ];

    protected $casts = [
        'gross_salary' => 'float', 'cnps_employee' => 'float',
        'cnps_employer' => 'float', 'irpp' => 'float',
        'cac_irpp' => 'float', 'rav' => 'float', 'net_salary' => 'float',
    ];

    public function period()   { return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id'); }
    public function employee() { return $this->belongsTo(Employee::class); }
}

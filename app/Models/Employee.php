<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'cnps_number', 'position',
        'gross_salary_xaf', 'hire_date', 'termination_date', 'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date', 'termination_date' => 'date',
        'is_active' => 'boolean', 'gross_salary_xaf' => 'float',
    ];

    public function company()       { return $this->belongsTo(Company::class); }
    public function payrollLines()  { return $this->hasMany(PayrollLine::class); }
}

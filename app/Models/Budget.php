<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['company_id', 'name', 'fiscal_year', 'status'];

    public function company() { return $this->belongsTo(Company::class); }
    public function lines()   { return $this->hasMany(BudgetLine::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetLine extends Model
{
    protected $fillable = ['budget_id', 'account_code', 'period_month', 'budgeted_amount'];

    public function budget() { return $this->belongsTo(Budget::class); }
}

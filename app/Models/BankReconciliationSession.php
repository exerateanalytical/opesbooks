<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankReconciliationSession extends Model
{
    protected $fillable = [
        'company_id', 'bank_account_code', 'statement_date',
        'statement_balance', 'book_balance', 'difference',
        'is_reconciled', 'reconciled_at',
    ];

    protected $casts = [
        'statement_date'  => 'date',
        'reconciled_at'   => 'datetime',
        'is_reconciled'   => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function lines()   { return $this->hasMany(BankStatementLine::class); }
}

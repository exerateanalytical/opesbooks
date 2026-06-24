<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatementLine extends Model
{
    protected $fillable = [
        'bank_reconciliation_session_id', 'transaction_date',
        'description', 'amount', 'is_credit', 'is_matched', 'journal_line_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'is_credit'        => 'boolean',
        'is_matched'       => 'boolean',
    ];

    public function session()     { return $this->belongsTo(BankReconciliationSession::class, 'bank_reconciliation_session_id'); }
    public function journalLine() { return $this->belongsTo(JournalLine::class); }
}

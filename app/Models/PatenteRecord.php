<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatenteRecord extends Model
{
    protected $fillable = [
        'company_id', 'tax_year', 'patente_number', 'amount_due_xaf',
        'amount_paid_xaf', 'due_date', 'paid_date', 'status', 'notes', 'journal_entry_id',
    ];

    protected $casts = ['due_date' => 'date', 'paid_date' => 'date', 'amount_due_xaf' => 'float', 'amount_paid_xaf' => 'float'];

    public function company()      { return $this->belongsTo(Company::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
}

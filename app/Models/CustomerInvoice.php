<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'customer_id', 'journal_entry_id', 'invoice_number',
        'invoice_date', 'due_date', 'amount_ht', 'tva_amount', 'cac_amount',
        'amount_ttc', 'status', 'credit_note_for_id', 'paid_at', 'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date', 'due_date' => 'date', 'paid_at' => 'datetime',
        'amount_ht' => 'float', 'tva_amount' => 'float',
        'cac_amount' => 'float', 'amount_ttc' => 'float',
    ];

    public function company()      { return $this->belongsTo(Company::class); }
    public function customer()     { return $this->belongsTo(Customer::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
    public function creditNoteFor(){ return $this->belongsTo(CustomerInvoice::class, 'credit_note_for_id'); }

    public function isOverdue(): bool
    {
        return $this->status === 'SENT' && $this->due_date->isPast();
    }
}

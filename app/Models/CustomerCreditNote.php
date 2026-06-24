<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCreditNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'customer_id', 'original_invoice_id', 'credit_note_number',
        'credit_note_date', 'reason', 'amount_ht', 'tva_amount', 'cac_amount',
        'amount_ttc', 'status', 'journal_entry_id',
    ];

    protected $casts = ['credit_note_date' => 'date', 'amount_ht' => 'float', 'tva_amount' => 'float', 'cac_amount' => 'float', 'amount_ttc' => 'float'];

    public function company()          { return $this->belongsTo(Company::class); }
    public function customer()         { return $this->belongsTo(Customer::class); }
    public function originalInvoice()  { return $this->belongsTo(CustomerInvoice::class, 'original_invoice_id'); }
    public function journalEntry()     { return $this->belongsTo(JournalEntry::class); }
}

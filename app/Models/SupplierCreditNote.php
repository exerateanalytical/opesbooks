<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierCreditNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'supplier_id', 'original_invoice_id', 'credit_note_number',
        'credit_note_date', 'reason', 'amount_ht', 'tva_amount', 'net_payable',
        'status', 'journal_entry_id',
    ];

    protected $casts = ['credit_note_date' => 'date', 'amount_ht' => 'float', 'tva_amount' => 'float', 'net_payable' => 'float'];

    public function company()          { return $this->belongsTo(Company::class); }
    public function supplier()         { return $this->belongsTo(Supplier::class); }
    public function originalInvoice()  { return $this->belongsTo(SupplierInvoice::class, 'original_invoice_id'); }
    public function journalEntry()     { return $this->belongsTo(JournalEntry::class); }
}

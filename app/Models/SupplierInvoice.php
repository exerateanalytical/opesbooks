<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'supplier_id', 'journal_entry_id',
        'invoice_number', 'supplier_ref', 'invoice_date', 'due_date',
        'amount_ht', 'tva_amount', 'cac_amount', 'amount_ttc',
        'withholding_amount', 'net_payable',
        'status', 'paid_at', 'payment_ref', 'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date'     => 'date',
        'paid_at'      => 'datetime',
    ];

    public function company()     { return $this->belongsTo(Company::class); }
    public function supplier()    { return $this->belongsTo(Supplier::class); }
    public function journalEntry(){ return $this->belongsTo(JournalEntry::class); }

    public function isOverdue(): bool
    {
        return $this->status === 'RECEIVED' && $this->due_date->isPast();
    }
}

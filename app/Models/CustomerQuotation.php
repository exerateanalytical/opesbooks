<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerQuotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'customer_id', 'converted_invoice_id', 'quotation_number',
        'quotation_date', 'valid_until', 'amount_ht', 'tva_amount', 'cac_amount',
        'amount_ttc', 'status', 'notes',
    ];

    protected $casts = ['quotation_date' => 'date', 'valid_until' => 'date', 'amount_ht' => 'float', 'tva_amount' => 'float', 'cac_amount' => 'float', 'amount_ttc' => 'float'];

    public function company()           { return $this->belongsTo(Company::class); }
    public function customer()          { return $this->belongsTo(Customer::class); }
    public function lines()             { return $this->hasMany(CustomerQuotationLine::class); }
    public function convertedInvoice()  { return $this->belongsTo(CustomerInvoice::class, 'converted_invoice_id'); }
}

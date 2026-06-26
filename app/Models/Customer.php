<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'company_id', 'name', 'niu', 'email', 'phone', 'address',
        'payment_terms_days', 'credit_limit_xaf', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean', 'credit_limit_xaf' => 'float'];

    public function company()       { return $this->belongsTo(Company::class); }
    public function invoices()      { return $this->hasMany(CustomerInvoice::class); }
    public function creditNotes()   { return $this->hasMany(CustomerCreditNote::class); }
    public function quotations()    { return $this->hasMany(CustomerQuotation::class); }
    public function deliveryNotes() { return $this->hasMany(DeliveryNote::class); }
}

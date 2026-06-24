<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'niu', 'email', 'phone', 'address',
        'payment_terms_days', 'credit_limit_xaf', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean', 'credit_limit_xaf' => 'float'];

    public function company()    { return $this->belongsTo(Company::class); }
    public function invoices()   { return $this->hasMany(CustomerInvoice::class); }
}

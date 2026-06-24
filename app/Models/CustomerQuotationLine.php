<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerQuotationLine extends Model
{
    protected $fillable = [
        'customer_quotation_id', 'description', 'account_code',
        'quantity', 'unit_price_ht', 'line_total_ht',
    ];

    protected $casts = ['quantity' => 'float', 'unit_price_ht' => 'float', 'line_total_ht' => 'float'];

    public function quotation() { return $this->belongsTo(CustomerQuotation::class, 'customer_quotation_id'); }
}

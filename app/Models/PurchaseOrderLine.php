<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLine extends Model
{
    protected $fillable = [
        'purchase_order_id', 'description', 'account_code',
        'quantity', 'unit_price_ht', 'line_total_ht', 'qty_received',
    ];

    protected $casts = ['quantity' => 'float', 'unit_price_ht' => 'float', 'line_total_ht' => 'float', 'qty_received' => 'float'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
}

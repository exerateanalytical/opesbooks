<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'supplier_id', 'po_number', 'order_date',
        'expected_delivery_date', 'amount_ht', 'tva_amount', 'amount_ttc',
        'status', 'notes',
    ];

    protected $casts = ['order_date' => 'date', 'expected_delivery_date' => 'date', 'amount_ht' => 'float', 'tva_amount' => 'float', 'amount_ttc' => 'float'];

    public function company()  { return $this->belongsTo(Company::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function lines()    { return $this->hasMany(PurchaseOrderLine::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'dn_type', 'customer_id', 'supplier_id',
        'customer_invoice_id', 'purchase_order_id',
        'dn_number', 'delivery_date', 'delivery_address',
        'status', 'notes',
    ];

    public function company()    { return $this->belongsTo(Company::class); }
    public function customer()   { return $this->belongsTo(Customer::class); }
    public function supplier()   { return $this->belongsTo(Supplier::class); }
    public function lines()      { return $this->hasMany(DeliveryNoteLine::class); }
    public function invoice()    { return $this->belongsTo(CustomerInvoice::class, 'customer_invoice_id'); }
    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
}

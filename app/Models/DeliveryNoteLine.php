<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNoteLine extends Model
{
    protected $fillable = ['delivery_note_id', 'description', 'product_code', 'quantity', 'unit'];

    public function deliveryNote() { return $this->belongsTo(DeliveryNote::class); }
}

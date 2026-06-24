<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'company_id', 'name', 'niu', 'email', 'phone', 'address',
        'payment_terms_days', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function company() { return $this->belongsTo(Company::class); }
}

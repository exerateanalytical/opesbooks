<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id', 'admin_user_id', 'event_type', 'from_plan', 'to_plan',
        'amount_xaf', 'notes', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function company() { return $this->belongsTo(Company::class); }
    public function admin()   { return $this->belongsTo(User::class, 'admin_user_id'); }
}

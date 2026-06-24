<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id', 'company_id', 'action', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip_address',
    ];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'created_at'  => 'datetime',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function company() { return $this->belongsTo(Company::class); }
}

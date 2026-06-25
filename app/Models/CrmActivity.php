<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmActivity extends Model
{
    protected $fillable = [
        'lead_id', 'user_id', 'type', 'description', 'scheduled_at', 'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lead() { return $this->belongsTo(CrmLead::class, 'lead_id'); }
    public function user() { return $this->belongsTo(User::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InAppNotification extends Model
{
    protected $fillable = [
        'company_id', 'user_id', 'type', 'title', 'body', 'icon',
        'icon_color', 'action_url', 'action_label', 'read_at',
    ];

    protected $casts = ['read_at' => 'datetime'];

    public function scopeUnread($q) { return $q->whereNull('read_at'); }
}

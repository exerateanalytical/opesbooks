<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminImpersonationLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['admin_user_id', 'company_id', 'started_at', 'ended_at', 'ip_address', 'created_at'];

    protected $casts = ['started_at' => 'datetime', 'ended_at' => 'datetime', 'created_at' => 'datetime'];

    public function admin()   { return $this->belongsTo(User::class, 'admin_user_id'); }
    public function company() { return $this->belongsTo(Company::class); }
}

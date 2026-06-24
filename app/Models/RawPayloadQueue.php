<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawPayloadQueue extends Model
{
    protected $table = 'raw_payload_queue';

    protected $fillable = [
        'operator',
        'transaction_id',
        'company_niu',
        'amount',
        'message',
        'txn_date',
        'status',
        'error_detail',
    ];

    protected $casts = [
        'txn_date' => 'date',
        'amount'   => 'decimal:2',
    ];

    public function scopeQueued($query)
    {
        return $query->where('status', 'QUEUED');
    }
}

<?php

namespace App\Models;

use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'posting_date',
        'posting_type',
        'reference_id',
        'invoice_crypto_hash',
        'transaction_status',
        'source_pipeline',
        'memo',
        'dgi_validation_token',
        'dgi_validated_at',
        'dgi_sync_status',
        'dgi_error_payload',
    ];

    protected $casts = [
        'posting_date'    => 'date',
        'dgi_validated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (self $entry) {
            app(\App\Services\WebhookService::class)->dispatch('journal.entry.posted', $entry->toArray(), $entry->company);
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lines()
    {
        return $this->hasMany(JournalLine::class);
    }

    public function attachments()
    {
        return $this->hasMany(JournalEntryAttachment::class);
    }

    public function isBalanced(): bool
    {
        $totalDebit  = BigDecimal::of((string) $this->lines->sum('debit'));
        $totalCredit = BigDecimal::of((string) $this->lines->sum('credit'));

        return $totalDebit->isEqualTo($totalCredit);
    }

    /** ADJUSTMENT entries are immutable — they may not be deleted or reversed after posting. */
    public function isDeletable(): bool
    {
        return $this->posting_type !== 'ADJUSTMENT';
    }
}

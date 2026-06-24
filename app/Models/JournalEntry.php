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
        'posting_date',
        'reference_id',
        'source_pipeline',
        'memo',
        'status',
    ];

    protected $casts = [
        'posting_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function lines()
    {
        return $this->hasMany(JournalLine::class);
    }

    public function isBalanced(): bool
    {
        $totalDebit  = BigDecimal::of((string) $this->lines->sum('debit'));
        $totalCredit = BigDecimal::of((string) $this->lines->sum('credit'));

        return $totalDebit->isEqualTo($totalCredit);
    }
}

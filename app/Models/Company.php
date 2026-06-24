<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'niu',
        'rccm',
        'tax_regime',
        'tax_center',
        'phone',
        'email',
        'address',
    ];

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function hasValidFiscalProfile(): bool
    {
        return filled($this->niu) && filled($this->rccm) && filled($this->tax_center);
    }
}

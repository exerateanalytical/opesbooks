<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyscohadaAccount extends Model
{
    protected $fillable = ['company_id', 'code', 'label', 'class_digit', 'is_active'];

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class);
    }

    public static function findByCode(string $code): self
    {
        return static::where('code', $code)->where('is_active', true)->firstOrFail();
    }
}

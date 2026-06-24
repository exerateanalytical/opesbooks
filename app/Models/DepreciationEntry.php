<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepreciationEntry extends Model
{
    protected $fillable = ['fixed_asset_id', 'period_month', 'period_year', 'amount', 'journal_entry_id'];

    public function fixedAsset()   { return $this->belongsTo(FixedAsset::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
}

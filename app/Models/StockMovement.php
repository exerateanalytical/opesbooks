<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'company_id', 'product_code', 'product_name', 'account_code',
        'movement_type', 'quantity', 'unit_cost_xaf', 'total_cost_xaf',
        'movement_date', 'reference', 'description', 'journal_entry_id',
    ];

    protected $casts = [
        'quantity'       => 'float',
        'unit_cost_xaf'  => 'float',
        'total_cost_xaf' => 'float',
        'movement_date'  => 'date',
    ];

    public function company()      { return $this->belongsTo(Company::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
}

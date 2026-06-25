<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEntry extends Model
{
    protected $fillable = [
        'project_id', 'company_id', 'entry_type', 'reference_id',
        'reference_type', 'amount', 'description', 'entry_date',
    ];

    protected $casts = [
        'amount'     => 'float',
        'entry_date' => 'date',
    ];

    public function project() { return $this->belongsTo(Project::class); }
}

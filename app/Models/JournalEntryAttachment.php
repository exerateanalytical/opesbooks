<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JournalEntryAttachment extends Model
{
    protected $fillable = [
        'journal_entry_id', 'user_id', 'file_path', 'file_name', 'mime_type', 'file_size_bytes',
    ];

    protected $appends = ['file_url'];

    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
    public function user()         { return $this->belongsTo(User::class); }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}

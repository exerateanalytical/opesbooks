<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'cover_image', 'author_id',
        'published_at', 'is_published', 'tags', 'reading_time_minutes',
        'meta_title', 'meta_description',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($q)
    {
        return $q->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

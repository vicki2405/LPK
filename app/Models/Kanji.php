<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kanji extends Model
{
    use HasFactory;

    protected $fillable = [
        'kanji',
        'hiragana',
        'meaning_id',
        'romaji',
        'onyomi',
        'kunyomi',
        'level',
        'stroke_count',
        'notes',
        'created_by',
    ];

    /**
     * Sensei who created this Kanji entry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected $appends = ['meaning'];

    public function getMeaningAttribute(): string
    {
        return $this->meaning_id ?? '';
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'kanji_topic')->withTimestamps();
    }
}
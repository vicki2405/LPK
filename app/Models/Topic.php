<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'level',
        'title',
        'description',
        'sort_order',
        'created_by',
    ];

    public function vocabularies(): BelongsToMany
    {
        return $this->belongsToMany(Vocabulary::class, 'topic_vocabulary')->withTimestamps();
    }

    public function kanjis(): BelongsToMany
    {
        return $this->belongsToMany(Kanji::class, 'kanji_topic')->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForVocabulary($query)
    {
        return $query->where('type', 'vocabulary');
    }

    public function scopeForKanji($query)
    {
        return $query->where('type', 'kanji');
    }
}
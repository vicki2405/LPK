<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vocabulary extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'category',
        'chapter_id',
        'kanji',
        'hiragana',
        'romaji',
        'meaning_id',
        'word_type',
        'image_file',
        'audio_file',
        'example_sentence_jp',
        'example_sentence_id',
        'order_index',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'topic_vocabulary')->withTimestamps();
    }
}
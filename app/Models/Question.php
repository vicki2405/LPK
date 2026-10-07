<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'question_category_id',
        'chapter_id',
        'level',
        'level_code',
        'section_type',
        'instruction',
        'question_text',
        'reading_passage',
        'image_url',
        'audio_url',
        'audio_play_limit',
        'explanation',
        'score_points',
    ];

    protected $casts = [
        'audio_play_limit' => 'integer',
        'score_points' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($question) {
            if (empty($question->level_code) && !empty($question->level)) {
                $question->level_code = $question->level;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'question_category_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function correctOption()
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_questions')
            ->withPivot('section_type', 'order_index')
            ->withTimestamps();
    }

    public function questionBanks(): BelongsToMany
    {
        return $this->belongsToMany(QuestionBank::class, 'question_bank_questions')
            ->withPivot('section_type', 'order_index')
            ->withTimestamps();
    }
}

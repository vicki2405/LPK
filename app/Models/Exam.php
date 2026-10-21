<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'question_bank_id',
        'subject_id',
        'batch_id',
        'title',
        'code',
        'exam_type',
        'level',
        'description',
        'duration_minutes',
        'passing_score',
        'max_score',
        'max_attempts',
        'start_time',
        'end_time',
        'access_token',
        'is_randomized',
        'allow_review_immediately',
        'is_published',
        'display_mode',
        'allow_student_mode_switch',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'passing_score' => 'integer',
        'max_score' => 'integer',
        'max_attempts' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_randomized' => 'boolean',
        'allow_review_immediately' => 'boolean',
        'is_published' => 'boolean',
        'allow_student_mode_switch' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($exam) {
            if (empty($exam->code)) {
                $exam->code = 'EXAM-' . strtoupper(Str::random(6));
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_questions')
            ->withPivot('section_type', 'order_index')
            ->orderByPivot('order_index')
            ->withTimestamps();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}

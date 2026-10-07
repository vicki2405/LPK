<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'attempt_number',
        'started_at',
        'expires_at',
        'submitted_at',
        'total_score',
        'moji_goi_score',
        'bunpou_dokkai_score',
        'choukai_score',
        'correct_answers_count',
        'wrong_answers_count',
        'unanswered_count',
        'violation_count',
        'violation_logs',
        'is_passed',
        'is_disqualified',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'submitted_at' => 'datetime',
        'total_score' => 'decimal:2',
        'moji_goi_score' => 'decimal:2',
        'bunpou_dokkai_score' => 'decimal:2',
        'choukai_score' => 'decimal:2',
        'correct_answers_count' => 'integer',
        'wrong_answers_count' => 'integer',
        'unanswered_count' => 'integer',
        'violation_count' => 'integer',
        'violation_logs' => 'array',
        'is_passed' => 'boolean',
        'is_disqualified' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }
}

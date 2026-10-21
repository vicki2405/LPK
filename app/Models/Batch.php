<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'program_type',
        'job_sector',
        'target_level',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'batch_user')
            ->withPivot('role_in_batch')
            ->withTimestamps();
    }

    public function senseis(): BelongsToMany
    {
        return $this->users()->wherePivot('role_in_batch', 'sensei');
    }

    public function siswas(): BelongsToMany
    {
        return $this->users()->wherePivot('role_in_batch', 'siswa');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}

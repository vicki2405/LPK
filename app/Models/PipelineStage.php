<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_jp',
        'code',
        'order_step',
        'badge_color',
        'icon',
        'description',
        'is_active',
    ];

    protected $casts = [
        'order_step' => 'integer',
        'is_active' => 'boolean',
    ];
}

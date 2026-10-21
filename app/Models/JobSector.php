<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_jp',
        'code',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(fn() => \Illuminate\Support\Facades\Cache::forget('landing_page_props'));
        static::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('landing_page_props'));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'image_path',
        'description',
        'activity_date',
        'order_index',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order_index' => 'integer',
        'activity_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(fn() => \Illuminate\Support\Facades\Cache::forget('landing_page_props'));
        static::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('landing_page_props'));
    }
}

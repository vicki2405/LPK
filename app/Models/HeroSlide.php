<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_jp',
        'badge_top_label',
        'badge_top',
        'status_label',
        'status_label_jp',
        'image_path',
        'salary_jpy',
        'salary_idr',
        'placement_location',
        'placement_location_jp',
        'facilities',
        'facilities_jp',
        'cta_text',
        'cta_text_jp',
        'cta_url',
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

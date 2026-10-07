<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniTestimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo',
        'work_sector',
        'japan_location',
        'testimony_text',
        'program_type',
        'order_index',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order_index' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(fn() => \Illuminate\Support\Facades\Cache::forget('landing_page_props'));
        static::deleted(fn() => \Illuminate\Support\Facades\Cache::forget('landing_page_props'));
    }
}

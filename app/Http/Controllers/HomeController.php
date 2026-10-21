<?php

namespace App\Http\Controllers;

use App\Models\ActivityGallery;
use App\Models\AlumniTestimonial;
use App\Models\Batch;
use App\Models\Course;
use App\Models\HeroSlide;
use App\Models\JobSector;
use App\Models\PipelineStage;
use App\Models\Program;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the official public landing page of LPK Masayume with High-Speed In-Memory Cache.
     */
    public function index(Request $request): Response
    {
        $data = Cache::remember('landing_page_props', 1800, function () {
            $settings = SiteSetting::getAllSettings();
            $heroSlides = HeroSlide::where('is_active', true)->orderBy('sort_order')->get();
            $programs = Program::where('is_active', true)->orderBy('sort_order')->get();
            $jobSectors = JobSector::where('is_active', true)->orderBy('sort_order')->get();
            $pipelineStages = PipelineStage::where('is_active', true)->orderBy('order_step')->get();
            $testimonials = AlumniTestimonial::where('is_published', true)->orderBy('order_index')->get();
            $galleries = ActivityGallery::where('is_published', true)->orderBy('order_index')->latest()->get();
            $batches = Batch::where('status', 'active')->withCount('siswas')->latest()->take(3)->get();
            
            $totalStudents = rescue(fn() => User::role('siswa')->count(), 0, false);
            $totalSensei = rescue(fn() => User::role('sensei')->count(), 0, false);
            $totalCourses = Course::where('is_published', true)->count();

            return [
                'settings' => $settings,
                'heroSlides' => $heroSlides,
                'programs' => $programs,
                'jobSectors' => $jobSectors,
                'pipelineStages' => $pipelineStages,
                'testimonials' => $testimonials,
                'galleries' => $galleries,
                'batches' => $batches,
                'stats' => [
                    'total_students' => $totalStudents > 0 ? $totalStudents : 120,
                    'total_sensei' => $totalSensei > 0 ? $totalSensei : 8,
                    'total_courses' => $totalCourses,
                ],
            ];
        });

        return Inertia::render('Welcome', array_merge($data, [
            'canLogin' => true,
        ]));
    }
}

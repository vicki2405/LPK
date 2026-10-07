<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityGallery;
use App\Models\AlumniTestimonial;
use App\Models\HeroSlide;
use App\Models\JobSector;
use App\Models\PipelineStage;
use App\Models\Program;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class WebsiteSettingController extends Controller
{
    /**
     * Clear landing page props cache.
     */
    protected function clearCache(): void
    {
        Cache::forget('landing_page_props');
    }
    /**
     * Display the Website Content Management (CMS) settings workspace.
     */
    public function index(Request $request): Response
    {
        $activeTab = $request->query('tab', 'hero');

        $settings = SiteSetting::getAllSettings();
        $testimonials = AlumniTestimonial::orderBy('order_index')->get();
        $galleries = ActivityGallery::orderBy('order_index')->latest()->get();
        $programs = Program::orderBy('sort_order')->get();
        $jobSectors = JobSector::orderBy('sort_order')->get();
        $pipelineStages = PipelineStage::orderBy('order_step')->get();
        $heroSlides = HeroSlide::orderBy('sort_order')->get();

        return Inertia::render('Admin/WebsiteSettings/Index', [
            'initialTab' => $activeTab,
            'settings' => $settings,
            'testimonials' => $testimonials,
            'galleries' => $galleries,
            'programs' => $programs,
            'jobSectors' => $jobSectors,
            'pipelineStages' => $pipelineStages,
            'heroSlides' => $heroSlides,
        ]);
    }

    /**
     * Save all general website settings (Identity, Hero, Stats, About, Contact, Social, Footer).
     */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $input = $request->except(['_token', '_method']);

        foreach ($input as $key => $value) {
            SiteSetting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Pengaturan konten website LPK berhasil disimpan!');
    }

    /**
     * Store new Alumni Testimonial.
     */
    public function storeTestimonial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'work_sector' => 'required|string|max:255',
            'japan_location' => 'required|string|max:255',
            'testimony_text' => 'required|string',
            'program_type' => 'nullable|string|max:100',
            'order_index' => 'nullable|integer',
            'is_published' => 'required|boolean',
        ]);

        AlumniTestimonial::create($validated);

        return redirect()->back()->with('success', 'Testimoni alumni baru berhasil ditambahkan!');
    }

    /**
     * Update existing Alumni Testimonial.
     */
    public function updateTestimonial(Request $request, AlumniTestimonial $testimonial): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'work_sector' => 'required|string|max:255',
            'japan_location' => 'required|string|max:255',
            'testimony_text' => 'required|string',
            'program_type' => 'nullable|string|max:100',
            'order_index' => 'nullable|integer',
            'is_published' => 'required|boolean',
        ]);

        $testimonial->update($validated);

        return redirect()->back()->with('success', 'Data testimoni alumni berhasil diperbarui!');
    }

    /**
     * Delete Alumni Testimonial.
     */
    public function destroyTestimonial(AlumniTestimonial $testimonial): RedirectResponse
    {
        $testimonial->delete();
        return redirect()->back()->with('success', 'Testimoni alumni berhasil dihapus!');
    }

    /**
     * Store new Program.
     */
    public function storeProgram(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'badge_label' => 'nullable|string|max:100',
            'salary_range' => 'nullable|string|max:100',
            'benefits_json' => 'nullable|array',
            'icon' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = \Illuminate\Support\Str::slug($validated['name'], '_');
        }

        Program::create($validated);

        return redirect()->back()->with('success', 'Program pelatihan baru berhasil ditambahkan!');
    }

    /**
     * Update Program details (benefits, salary range, icon, badge).
     */
    public function updateProgram(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'badge_label' => 'nullable|string|max:100',
            'salary_range' => 'nullable|string|max:100',
            'benefits_json' => 'nullable|array',
            'icon' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $program->update($validated);

        return redirect()->back()->with('success', 'Data program pelatihan berhasil diperbarui!');
    }

    /**
     * Delete Program.
     */
    public function destroyProgram(Program $program): RedirectResponse
    {
        $program->delete();
        return redirect()->back()->with('success', 'Program pelatihan berhasil dihapus!');
    }

    /**
     * Store new Job Sector.
     */
    public function storeJobSector(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_jp' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = strtoupper(\Illuminate\Support\Str::slug($validated['name'], '_'));
        }

        JobSector::create($validated);

        return redirect()->back()->with('success', 'Sektor pekerjaan baru berhasil ditambahkan!');
    }

    /**
     * Update Job Sector details (icon, active).
     */
    public function updateJobSector(Request $request, JobSector $jobSector): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_jp' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $jobSector->update($validated);

        return redirect()->back()->with('success', 'Sektor pekerjaan berhasil diperbarui!');
    }

    /**
     * Delete Job Sector.
     */
    public function destroyJobSector(JobSector $jobSector): RedirectResponse
    {
        $jobSector->delete();
        return redirect()->back()->with('success', 'Sektor pekerjaan berhasil dihapus!');
    }

    /**
     * Store new Activity Gallery Photo.
     */
    public function storeGallery(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'activity_date' => 'nullable|date',
            'order_index' => 'nullable|integer',
            'is_published' => 'required|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('website/gallery', 'public');
            $validated['image_path'] = '/storage/' . $path;
        } elseif (!empty($validated['image_url'])) {
            $validated['image_path'] = $validated['image_url'];
        } else {
            $validated['image_path'] = 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80';
        }

        unset($validated['image_file'], $validated['image_url']);

        ActivityGallery::create($validated);

        return redirect()->back()->with('success', 'Foto dokumentasi kegiatan baru berhasil ditambahkan!');
    }

    /**
     * Update existing Activity Gallery Photo.
     */
    public function updateGallery(Request $request, ActivityGallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'activity_date' => 'nullable|date',
            'order_index' => 'nullable|integer',
            'is_published' => 'required|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('website/gallery', 'public');
            $validated['image_path'] = '/storage/' . $path;
        } elseif (!empty($validated['image_url'])) {
            $validated['image_path'] = $validated['image_url'];
        }

        unset($validated['image_file'], $validated['image_url']);

        $gallery->update($validated);

        return redirect()->back()->with('success', 'Foto kegiatan berhasil diperbarui!');
    }

    /**
     * Delete Activity Gallery Photo.
     */
    public function destroyGallery(ActivityGallery $gallery): RedirectResponse
    {
        $gallery->delete();
        return redirect()->back()->with('success', 'Foto kegiatan berhasil dihapus!');
    }

    /**
     * Store new Hero Slider Card.
     */
    public function storeHeroSlide(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_jp' => 'nullable|string|max:255',
            'badge_top_label' => 'nullable|string|max:100',
            'badge_top' => 'nullable|string|max:100',
            'status_label' => 'nullable|string|max:100',
            'status_label_jp' => 'nullable|string|max:100',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|string|max:500',
            'salary_jpy' => 'nullable|string|max:100',
            'salary_idr' => 'nullable|string|max:100',
            'placement_location' => 'nullable|string|max:150',
            'placement_location_jp' => 'nullable|string|max:150',
            'facilities' => 'nullable|string|max:150',
            'facilities_jp' => 'nullable|string|max:150',
            'cta_text' => 'nullable|string|max:100',
            'cta_text_jp' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('website/hero-slides', 'public');
            $validated['image_path'] = '/storage/' . $path;
        } elseif (!empty($validated['image_url'])) {
            $validated['image_path'] = $validated['image_url'];
        } else {
            $validated['image_path'] = '/images/hero-japan.jpg';
        }

        unset($validated['image_file'], $validated['image_url']);

        HeroSlide::create($validated);

        return redirect()->back()->with('success', 'Slide kartu hero baru berhasil ditambahkan!');
    }

    /**
     * Update existing Hero Slider Card.
     */
    public function updateHeroSlide(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_jp' => 'nullable|string|max:255',
            'badge_top_label' => 'nullable|string|max:100',
            'badge_top' => 'nullable|string|max:100',
            'status_label' => 'nullable|string|max:100',
            'status_label_jp' => 'nullable|string|max:100',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|string|max:500',
            'salary_jpy' => 'nullable|string|max:100',
            'salary_idr' => 'nullable|string|max:100',
            'placement_location' => 'nullable|string|max:150',
            'placement_location_jp' => 'nullable|string|max:150',
            'facilities' => 'nullable|string|max:150',
            'facilities_jp' => 'nullable|string|max:150',
            'cta_text' => 'nullable|string|max:100',
            'cta_text_jp' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('website/hero-slides', 'public');
            $validated['image_path'] = '/storage/' . $path;
        } elseif (!empty($validated['image_url'])) {
            $validated['image_path'] = $validated['image_url'];
        }

        unset($validated['image_file'], $validated['image_url']);

        $heroSlide->update($validated);

        return redirect()->back()->with('success', 'Slide kartu hero berhasil diperbarui!');
    }

    /**
     * Delete Hero Slider Card.
     */
    public function destroyHeroSlide(HeroSlide $heroSlide): RedirectResponse
    {
        $heroSlide->delete();
        return redirect()->back()->with('success', 'Slide kartu hero berhasil dihapus!');
    }
}

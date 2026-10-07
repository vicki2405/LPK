<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\JobSectorController;
use App\Http\Controllers\Admin\LearningIndicatorController;
use App\Http\Controllers\Admin\PipelineStageController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SenseiController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sensei\ExamController;
use App\Http\Controllers\Sensei\GradeController;
use App\Http\Controllers\Sensei\LmsController;
use App\Http\Controllers\Sensei\QuestionController;
use App\Http\Controllers\Sensei\QuestionSettingsController;
use App\Http\Controllers\Sensei\VocabularyController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard')->middleware('role:admin');
    Route::get('/siswa/dashboard', [DashboardController::class, 'siswaDashboard'])->name('siswa.dashboard')->middleware('role:siswa|admin|sensei');

    // ================= ADMIN MANAGEMENT ROUTES =================
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        // 1. Master Data & Pengaturan LPK
        Route::prefix('master')->name('master.')->group(function () {
            Route::resource('batches', BatchController::class)->except(['create', 'edit', 'show']);
            Route::resource('programs', ProgramController::class)->except(['create', 'edit', 'show']);
            Route::resource('job-sectors', JobSectorController::class)->except(['create', 'edit', 'show']);
            Route::resource('pipeline-stages', PipelineStageController::class)->except(['create', 'edit', 'show']);
            Route::resource('learning-indicators', LearningIndicatorController::class)->except(['create', 'edit', 'show']);
        });

        // 2. Manajemen Siswa & Sensei
        Route::resource('students', StudentController::class)->except(['create', 'edit', 'show']);
        Route::post('students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');

        Route::resource('senseis', SenseiController::class)->except(['create', 'edit', 'show']);
        Route::post('senseis/{sensei}/reset-password', [SenseiController::class, 'resetPassword'])->name('senseis.reset-password');

        // 3. Akun & Hak Akses (Permissions)
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::resource('roles', RolePermissionController::class)->except(['create', 'edit', 'show']);

        // 4. Pengaturan Website LPK (CMS Landing Page)
        Route::get('website-settings', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'index'])->name('website-settings.index');
        Route::post('website-settings/general', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateGeneral'])->name('website-settings.update-general');
        Route::post('website-settings/testimonials', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeTestimonial'])->name('website-settings.testimonials.store');
        Route::put('website-settings/testimonials/{testimonial}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateTestimonial'])->name('website-settings.testimonials.update');
        Route::delete('website-settings/testimonials/{testimonial}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyTestimonial'])->name('website-settings.testimonials.destroy');
        Route::post('website-settings/galleries', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeGallery'])->name('website-settings.galleries.store');
        Route::post('website-settings/galleries/{gallery}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateGallery'])->name('website-settings.galleries.update');
        Route::delete('website-settings/galleries/{gallery}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyGallery'])->name('website-settings.galleries.destroy');
        Route::post('website-settings/programs', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeProgram'])->name('website-settings.programs.store');
        Route::put('website-settings/programs/{program}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateProgram'])->name('website-settings.programs.update');
        Route::delete('website-settings/programs/{program}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyProgram'])->name('website-settings.programs.destroy');
        Route::post('website-settings/job-sectors', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeJobSector'])->name('website-settings.job-sectors.store');
        Route::put('website-settings/job-sectors/{jobSector}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateJobSector'])->name('website-settings.job-sectors.update');
        Route::delete('website-settings/job-sectors/{jobSector}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyJobSector'])->name('website-settings.job-sectors.destroy');
        Route::post('website-settings/hero-slides', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'storeHeroSlide'])->name('website-settings.hero-slides.store');
        Route::post('website-settings/hero-slides/{heroSlide}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'updateHeroSlide'])->name('website-settings.hero-slides.update');
        Route::delete('website-settings/hero-slides/{heroSlide}', [\App\Http\Controllers\Admin\WebsiteSettingController::class, 'destroyHeroSlide'])->name('website-settings.hero-slides.destroy');
    });

    // ================= SENSEI PORTAL ROUTES =================
    Route::prefix('sensei')->name('sensei.')->middleware(['role:sensei|admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'senseiDashboard'])->name('dashboard');
        
        // 1. LMS (Materi & Kurikulum)
        Route::resource('lms', LmsController::class)->except(['create', 'edit']);
        Route::post('vocabularies/auto-translate', [VocabularyController::class, 'autoTranslate'])->name('vocabularies.auto-translate');
        Route::get('vocabularies/pronunciation-audio', [VocabularyController::class, 'pronunciationAudio'])->name('vocabularies.pronunciation-audio');
        Route::post('vocabularies/generate-native-audio', [VocabularyController::class, 'generateNativeAudio'])->name('vocabularies.generate-native-audio');
        Route::resource('vocabularies', VocabularyController::class)->except(['show']);
        
        // 2. Mata Pelajaran (Mapel) & Paket Soal CBT (Bank Soal)
        Route::post('subjects', [QuestionController::class, 'storeSubject'])->name('subjects.store');
        Route::put('subjects/{subject}', [QuestionController::class, 'updateSubject'])->name('subjects.update');
        Route::delete('subjects/{subject}', [QuestionController::class, 'destroySubject'])->name('subjects.destroy');

        Route::post('questions/packages', [QuestionController::class, 'storePackage'])->name('questions.store-package');
        Route::put('questions/packages/{questionBank}', [QuestionController::class, 'updatePackage'])->name('questions.update-package');
        Route::delete('questions/packages/{questionBank}', [QuestionController::class, 'destroyPackage'])->name('questions.destroy-package');
        Route::post('questions/packages/{questionBank}/toggle-publish', [QuestionController::class, 'togglePublishPackage'])->name('questions.toggle-publish');
        Route::get('questions/packages/{questionBank}/manage', [QuestionController::class, 'managePackage'])->name('questions.manage-package');
        Route::post('questions/packages/{questionBank}/items', [QuestionController::class, 'storeItem'])->name('questions.store-item');
        Route::put('questions/packages/{questionBank}/items/{question}', [QuestionController::class, 'updateItem'])->name('questions.update-item');
        Route::delete('questions/packages/{questionBank}/items/{question}', [QuestionController::class, 'destroyItem'])->name('questions.destroy-item');
        Route::get('questions/packages/{questionBank}/preview', [QuestionController::class, 'previewPackage'])->name('questions.preview-package');
        Route::get('questions/template/download', [QuestionController::class, 'downloadTemplate'])->name('questions.template.download');
        Route::get('questions/packages/{questionBank}/export-docx', [QuestionController::class, 'exportDocx'])->name('questions.export-docx');
        Route::post('questions/packages/{questionBank}/import-docx', [QuestionController::class, 'importDocx'])->name('questions.import-docx');
        Route::post('questions/packages/{questionBank}/import-text', [QuestionController::class, 'importText'])->name('questions.import-text');
        Route::resource('questions', QuestionController::class)->except(['show']);
        
        // 3. CBT (Ujian & Jadwal, Anti-Cheat Live Monitoring)
        Route::resource('exams', ExamController::class)->except(['create', 'edit', 'show']);
        Route::post('exams/{exam}/toggle-publish', [ExamController::class, 'togglePublish'])->name('exams.toggle-publish');
        Route::post('exams/{exam}/toggle-mode', [ExamController::class, 'toggleDisplayMode'])->name('exams.toggle-mode');
        Route::get('exams/{exam}/preview', [ExamController::class, 'preview'])->name('exams.preview');
        Route::get('exams/{exam}/monitoring', [ExamController::class, 'monitoring'])->name('exams.monitoring');
        Route::post('exam-sessions/{session}/disqualify', [ExamController::class, 'disqualifySession'])->name('exams.disqualify-session');
        Route::post('exam-sessions/{session}/reset-violations', [ExamController::class, 'resetViolations'])->name('exams.reset-violations');
        Route::post('exam-sessions/{session}/log-violation', [ExamController::class, 'logViolation'])->name('exams.log-violation');
        
        // 4. Rapor & Kemahiran Siswa
        Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
        Route::get('grades/{user}', [GradeController::class, 'show'])->name('grades.show');
        Route::delete('grades/sessions/{session}', [GradeController::class, 'destroySession'])->name('grades.destroy-session');
        Route::delete('grades/students/{user}/reset', [GradeController::class, 'resetStudentGrades'])->name('grades.reset-student');

        // 5. Pengaturan Bank Soal (Level Kompetensi & Kategori Soal)
        Route::get('settings/questions', [QuestionSettingsController::class, 'index'])->name('settings.questions');
        // Level CRUD
        Route::post('settings/levels', [QuestionSettingsController::class, 'storeLevel'])->name('settings.levels.store');
        Route::put('settings/levels/{level}', [QuestionSettingsController::class, 'updateLevel'])->name('settings.levels.update');
        Route::delete('settings/levels/{level}', [QuestionSettingsController::class, 'destroyLevel'])->name('settings.levels.destroy');
        // Kategori CRUD
        Route::post('settings/categories', [QuestionSettingsController::class, 'storeCategory'])->name('settings.categories.store');
        Route::put('settings/categories/{category}', [QuestionSettingsController::class, 'updateCategory'])->name('settings.categories.update');
        Route::delete('settings/categories/{category}', [QuestionSettingsController::class, 'destroyCategory'])->name('settings.categories.destroy');
    });

    // ================= SISWA PORTAL DEDICATED ROUTES =================
    Route::prefix('siswa')->name('siswa.')->middleware(['role:siswa|admin|sensei'])->group(function () {
        // 1. LMS Materi Bab (1-50)
        Route::get('lms', [\App\Http\Controllers\Siswa\LmsController::class, 'index'])->name('lms.index');
        Route::get('lms/{chapter}', [\App\Http\Controllers\Siswa\LmsController::class, 'show'])->name('lms.show');

        // 2. Kotoba Flashcards & Quiz
        Route::get('flashcards', [\App\Http\Controllers\Siswa\FlashcardController::class, 'index'])->name('flashcards.index');
        Route::get('flashcards/pronunciation-audio', [VocabularyController::class, 'pronunciationAudio'])->name('flashcards.pronunciation-audio');

        // 3. CBT Ujian & Jadwal Siswa
        Route::get('exams', [\App\Http\Controllers\Siswa\ExamController::class, 'index'])->name('exams.index');
        Route::post('exams/{exam}/start', [\App\Http\Controllers\Siswa\CbtController::class, 'start'])->name('cbt.start');
        Route::get('cbt/{session}', [\App\Http\Controllers\Siswa\CbtController::class, 'show'])->name('cbt.show');
        Route::post('cbt/{session}/answer', [\App\Http\Controllers\Siswa\CbtController::class, 'saveAnswer'])
            ->name('cbt.save-answer')
            ->middleware('throttle:120,1');
        Route::post('cbt/{session}/log-violation', [\App\Http\Controllers\Siswa\CbtController::class, 'logViolation'])
            ->name('cbt.log-violation')
            ->middleware('throttle:30,1');
        Route::post('cbt/{session}/submit', [\App\Http\Controllers\Siswa\CbtController::class, 'submit'])
            ->name('cbt.submit')
            ->middleware('throttle:10,1');
        Route::get('cbt/{session}/result', [\App\Http\Controllers\Siswa\CbtController::class, 'result'])->name('cbt.result');

        // 4. Progres Penyaluran & Visa Jepang
        Route::get('journey', [\App\Http\Controllers\Siswa\JourneyController::class, 'index'])->name('journey.index');
    });

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['patch', 'post'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\PlaceholderController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SectionSettingController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SocialLinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| All routes here are prefixed with /admin and named admin.*. Required
| from routes/web.php. Guest-only routes (login) redirect authenticated
| admins to the dashboard; everything else requires the "auth" guard
| (see bootstrap/app.php for the guest redirect target).
|
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('section-settings', [SectionSettingController::class, 'index'])->name('section-settings');
        Route::patch('section-settings/{sectionSetting}/toggle', [SectionSettingController::class, 'toggle'])->name('section-settings.toggle');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('social-links', [SocialLinkController::class, 'index'])->name('social-links');
        Route::get('social-links/create', [SocialLinkController::class, 'create'])->name('social-links.create');
        Route::post('social-links', [SocialLinkController::class, 'store'])->name('social-links.store');
        Route::get('social-links/{socialLink}/edit', [SocialLinkController::class, 'edit'])->name('social-links.edit');
        Route::put('social-links/{socialLink}', [SocialLinkController::class, 'update'])->name('social-links.update');
        Route::delete('social-links/{socialLink}', [SocialLinkController::class, 'destroy'])->name('social-links.destroy');
        Route::patch('social-links/{socialLink}/move', [SocialLinkController::class, 'move'])->name('social-links.move');

        Route::get('about-skills', [SkillController::class, 'index'])->name('about-skills');
        Route::get('about-skills/create', [SkillController::class, 'create'])->name('about-skills.create');
        Route::post('about-skills', [SkillController::class, 'store'])->name('about-skills.store');
        Route::get('about-skills/{skill}/edit', [SkillController::class, 'edit'])->name('about-skills.edit');
        Route::put('about-skills/{skill}', [SkillController::class, 'update'])->name('about-skills.update');
        Route::delete('about-skills/{skill}', [SkillController::class, 'destroy'])->name('about-skills.destroy');
        Route::patch('about-skills/{skill}/move', [SkillController::class, 'move'])->name('about-skills.move');

        Route::get('projects', [ProjectController::class, 'index'])->name('projects');
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::patch('projects/{project}/move', [ProjectController::class, 'move'])->name('projects.move');

        Route::get('experience', [ExperienceController::class, 'index'])->name('experience');
        Route::get('experience/create', [ExperienceController::class, 'create'])->name('experience.create');
        Route::post('experience', [ExperienceController::class, 'store'])->name('experience.store');
        Route::get('experience/{experience}/edit', [ExperienceController::class, 'edit'])->name('experience.edit');
        Route::put('experience/{experience}', [ExperienceController::class, 'update'])->name('experience.update');
        Route::delete('experience/{experience}', [ExperienceController::class, 'destroy'])->name('experience.destroy');
        Route::patch('experience/{experience}/move', [ExperienceController::class, 'move'])->name('experience.move');

        Route::get('blog', [BlogPostController::class, 'index'])->name('blog');
        Route::get('blog/create', [BlogPostController::class, 'create'])->name('blog.create');
        Route::post('blog', [BlogPostController::class, 'store'])->name('blog.store');
        Route::get('blog/{post}/edit', [BlogPostController::class, 'edit'])->name('blog.edit');
        Route::put('blog/{post}', [BlogPostController::class, 'update'])->name('blog.update');
        Route::delete('blog/{post}', [BlogPostController::class, 'destroy'])->name('blog.destroy');
        Route::patch('blog/{post}/move', [BlogPostController::class, 'move'])->name('blog.move');

        // Menus without a real feature yet — placeholder pages so the
        // sidebar never 404s while making the owning iteration explicit.
        // [route name suffix => [menu title, "Segera Hadir" note]]
        $placeholders = [
            'playground' => ['Playground', 'Segera hadir — saklar aktif/nonaktifnya sudah ada di menu Pengaturan Section'],
            'testimonials' => ['Testimonials', 'Segera hadir — bagian dari Iterasi 7'],
            'messages' => ['Pesan Masuk', 'Segera hadir — bagian dari Iterasi 8'],
        ];

        foreach ($placeholders as $slug => [$title, $iterationNote]) {
            Route::get($slug, fn () => app(PlaceholderController::class)->show($title, $iterationNote))
                ->name($slug);
        }
    });
});

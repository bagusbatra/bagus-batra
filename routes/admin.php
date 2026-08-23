<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlaceholderController;
use App\Http\Controllers\Admin\ProfileController;
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

        // Menus without a real feature yet — placeholder pages so the
        // sidebar never 404s while making the owning iteration explicit.
        // [route name suffix => [menu title, "Segera Hadir" note]]
        $placeholders = [
            'projects' => ['Projects', 'Segera hadir — bagian dari Iterasi 4'],
            'playground' => ['Playground', 'Segera hadir — saklar aktif/nonaktifnya sudah ada di menu Pengaturan Section'],
            'experience' => ['Experience', 'Segera hadir — bagian dari Iterasi 5'],
            'blog' => ['Blog', 'Segera hadir — bagian dari Iterasi 6'],
            'testimonials' => ['Testimonials', 'Segera hadir — bagian dari Iterasi 7'],
            'messages' => ['Pesan Masuk', 'Segera hadir — bagian dari Iterasi 8'],
        ];

        foreach ($placeholders as $slug => [$title, $iterationNote]) {
            Route::get($slug, fn () => app(PlaceholderController::class)->show($title, $iterationNote))
                ->name($slug);
        }
    });
});

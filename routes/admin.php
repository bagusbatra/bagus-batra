<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlaceholderController;
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

        // Menus without a real feature yet — placeholder pages so the
        // sidebar never 404s while making the owning iteration explicit.
        // [route name suffix => [menu title, "Segera Hadir" note]]
        $placeholders = [
            'profile' => ['Profil & Hero', 'Segera hadir — bagian dari Iterasi 2'],
            'social-links' => ['Social Links', 'Segera hadir — bagian dari Iterasi 2'],
            'about-skills' => ['About & Skills', 'Segera hadir — bagian dari Iterasi 3'],
            'projects' => ['Projects', 'Segera hadir — bagian dari Iterasi 4'],
            'playground' => ['Playground', 'Segera hadir — saklar aktif/nonaktifnya bagian dari Iterasi 1 (Pengaturan Section)'],
            'experience' => ['Experience', 'Segera hadir — bagian dari Iterasi 5'],
            'blog' => ['Blog', 'Segera hadir — bagian dari Iterasi 6'],
            'testimonials' => ['Testimonials', 'Segera hadir — bagian dari Iterasi 7'],
            'messages' => ['Pesan Masuk', 'Segera hadir — bagian dari Iterasi 8'],
            'section-settings' => ['Pengaturan Section', 'Segera hadir — bagian dari Iterasi 1'],
        ];

        foreach ($placeholders as $slug => [$title, $iterationNote]) {
            Route::get($slug, fn () => app(PlaceholderController::class)->show($title, $iterationNote))
                ->name($slug);
        }
    });
});

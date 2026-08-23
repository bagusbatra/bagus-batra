<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProjectPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');

// Fase 2 (Iterasi 10-11): halaman Projects terpisah dari modal on-page.
// {project:project_key} = route-model-binding pakai kolom project_key
// (bukan id), supaya URL rapi mis. /projects/lumina-saas — lihat
// docs/RENCANA-PENGEMBANGAN.md #10.
Route::get('/projects', [ProjectPageController::class, 'index'])->name('projects.index');
Route::get('/projects/{project:project_key}', [ProjectPageController::class, 'show'])->name('projects.show');

require __DIR__.'/admin.php';

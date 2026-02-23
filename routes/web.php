<?php

use App\Http\Controllers\BriefController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'welcome'])->name('home');
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/locations', [CatalogController::class, 'locations'])->name('locations');
Route::get('/l/{slug}', [CatalogController::class, 'locationShow'])->name('locations.show');
Route::get('/copyright', [CatalogController::class, 'copyright'])->name('copyright');
Route::get('/c/{slug}', [CatalogController::class, 'show'])->name('categories.show');
Route::post('/briefs', [BriefController::class, 'store'])->name('brief.store');
Route::get('/brief/{token}', [BriefController::class, 'show'])->name('brief.show');

Route::get('dashboard', function () {
    return \Inertia\Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';

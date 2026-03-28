<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BriefController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\PhotographersController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/category/{slug}', [CatalogController::class, 'show'])->name('categories.show');

Route::get('/about-us', AboutUsController::class)->name('about');

Route::get('/contact-us', [ContactController::class, 'index'])->name('contact');
Route::post('/contact-us', [ContactController::class, 'store'])->name('contact.store');

Route::get('/locations', [LocationsController::class, 'index'])->name('locations');
Route::get('/location/{slug}', [LocationsController::class, 'show'])->name('locations.show');

Route::get('/photographers', [PhotographersController::class, 'index'])->name('photographers');
Route::get('/photographer/{slug}', [PhotographersController::class, 'show'])->name('photographers.show');

Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blogs.show');

Route::get('/legal/{slug}', [LegalController::class, 'show'])->name('legal.show');
Route::get('/copyright', [LegalController::class, 'copyright'])->name('copyright');

Route::post('/briefs', [BriefController::class, 'store'])->name('brief.store');
Route::get('/brief/{token}', [BriefController::class, 'show'])->name('brief.show');

Route::get('dashboard', function () {
    return \Inertia\Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';

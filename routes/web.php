<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadEnquiryController;
use App\Http\Controllers\SeoLandingController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/services', ServicesController::class)->name('services');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:6,1')->name('contact.store');
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{type}/{slug}', SeoLandingController::class)
    ->whereIn('type', ['makes', 'categories', 'body-types'])
    ->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('cars.landings.show');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');
Route::post('/cars/{car}/enquiries', LeadEnquiryController::class)->middleware('throttle:6,1')->name('cars.enquiries.store');
Route::get('/admin/vehicles/{car:id}/preview', [CarController::class, 'preview'])->middleware(['auth', 'can:view,car'])->name('admin.cars.preview');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

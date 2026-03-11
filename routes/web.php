<?php

use App\Http\Controllers\LocationPagePreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Location Page Preview Routes
Route::get('/preview/{slug}', [LocationPagePreviewController::class, 'show'])
    ->name('location-pages.preview')
    ->where('slug', '[a-z0-9\-]+');

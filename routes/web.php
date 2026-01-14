<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminController;

// Публичные маршруты
Route::get('/', function () 
{
    return Inertia::render('Home');
});


Route::get('/adminenter', [AdminController::class, 'enter'])->name('admin.enter');
Route::post('/adminenter', [AdminController::class, 'login'])->name('admin.login');

// Защищенные маршруты
Route::middleware('auth')->group(function () 
{
    // GET маршруты
    Route::get('/adminenter/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // POST маршруты - Ziggy должен их видеть
    Route::post('/adminenter/logout', [AdminController::class, 'logout'])->name('admin.logout');
});

// Динамические страницы - В САМЫЙ КОНЕЦ!
Route::get('/{slug}', function ($slug) 
{
    return Inertia::render('Page', ['slug' => $slug]);
});
<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;

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

Route::middleware('auth')->prefix('adminenter')->name('admin.')->group(function () 
{
    // Страницы
    Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    
    // Просмотр (публичный)
    Route::get('/pages/{page}', [PageController::class, 'show'])->name('pages.show');
});

// Динамические страницы - В САМЫЙ КОНЕЦ!
Route::get('/{slug}', function ($slug) 
{
    return Inertia::render('Page', ['slug' => $slug]);
});
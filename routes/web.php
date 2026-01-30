<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::resource('platforms', \App\Http\Controllers\API\InventoryItemController::class);
    Route::get('posts/show', [PostController::class, 'listScheduled']);
    Route::get('settings/platforms', [PostController::class, 'setting'])->name('settings.platforms');
    Route::post('settings/platforms/{platform}/toggle', [PostController::class, 'togglePlatform'] )->name('platforms.toggle');
});

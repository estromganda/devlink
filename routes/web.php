<?php

use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\DeveloperProfileController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/developer-profile', [
        DeveloperProfileController::class,
        'edit',
    ])->name('developer-profile.edit');

    Route::patch('/developer-profile', [
        DeveloperProfileController::class,
        'update',
    ])->name('developer-profile.update');
});

Route::get('/developers', [
    DeveloperController::class,
    'index',
])->name('developers.index');

Route::get('/developers/{user}', [
    DeveloperController::class,
    'show',
])->whereNumber('user')->name('developers.show');

require __DIR__ . '/settings.php';

<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\DeveloperProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
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

    // Communauties
    Route::get('/communities/create', [
        CommunityController::class,
        'create',
    ])->name('communities.create');

    Route::post('/communities', [
        CommunityController::class,
        'store',
    ])->name('communities.store');

    Route::post('/communities/{slug}/join', [CommunityController::class, 'join'])
        ->where('slug', '[a-z0-9-]+')
        ->name('communities.join');

    Route::delete('/communities/{slug}/leave', [CommunityController::class, 'leave'])
        ->where('slug', '[a-z0-9-]+')
        ->name('communities.leave');

    // Posts
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
        ->whereNumber('post')
        ->name('posts.comments.store');

    Route::post('/posts/{post}/like', [PostLikeController::class, 'store'])
        ->whereNumber('post')
        ->name('posts.likes.store');

    Route::delete('/posts/{post}/like', [PostLikeController::class, 'destroy'])
        ->whereNumber('post')
        ->name('posts.likes.destroy');
});

Route::get('/developers', [
    DeveloperController::class,
    'index',
])->name('developers.index');

Route::get('/developers/{user}', [
    DeveloperController::class,
    'show',
])->whereNumber('user')->name('developers.show');

// Communuties
Route::get('/communities', [
    CommunityController::class,
    'index',
])->name('communities.index');

Route::get('/communities/{slug}', [
    CommunityController::class,
    'show',
])->where('slug', '[a-z0-9-]+')->name('communities.show');

// Posts
Route::get('/posts', [PostController::class, 'index'])
    ->name('posts.index');

Route::get('/posts/create', [PostController::class, 'create'])
    ->middleware('auth')
    ->name('posts.create');

Route::post('/posts', [PostController::class, 'store'])
    ->middleware('auth')
    ->name('posts.store');

Route::get('/posts/{post}', [PostController::class, 'show'])
    ->whereNumber('post')
    ->name('posts.show');

require __DIR__ . '/settings.php';

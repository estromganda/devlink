<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\AnswerVoteController;
use App\Http\Controllers\CollaborationOfferController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\DeveloperProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Questions
    Route::get('/questions/create', [QuestionController::class, 'create'])
        ->name('questions.create');

    Route::post('/questions', [QuestionController::class, 'store'])
        ->name('questions.store');

    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])
        ->name('questions.edit');

    Route::put('/questions/{question}', [QuestionController::class, 'update'])
        ->name('questions.update');

    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])
        ->name('questions.destroy');

    // Answers
    Route::post(
        '/questions/{question}/answers',
        [AnswerController::class, 'store']
    )->name('questions.answers.store');

    Route::put(
        '/questions/{question}/answers/{answer}',
        [AnswerController::class, 'update']
    )->name('questions.answers.update');

    Route::delete(
        '/questions/{question}/answers/{answer}',
        [AnswerController::class, 'destroy']
    )->name('questions.answers.destroy');

    Route::post(
        '/questions/{question}/answers/{answer}/accept',
        [AnswerController::class, 'accept']
    )->name('questions.answers.accept');

    Route::put(
        '/questions/{question}/answers/{answer}/vote',
        [AnswerVoteController::class, 'store']
    )->name('questions.answers.vote');

    Route::delete(
        '/questions/{question}/answers/{answer}/vote',
        [AnswerVoteController::class, 'destroy']
    )->name('questions.answers.vote.destroy');
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

    // Projets
    Route::get('/my-projects', [ProjectController::class, 'mine'])
        ->name('projects.mine');

    Route::get('/projects/create', [ProjectController::class, 'create'])
        ->name('projects.create');

    Route::post('/projects', [ProjectController::class, 'store'])
        ->name('projects.store');

    Route::get('/projects/{slug}/edit', [ProjectController::class, 'edit'])
        ->where('slug', '[a-z0-9-]+')
        ->name('projects.edit');

    Route::put('/projects/{slug}', [ProjectController::class, 'update'])
        ->where('slug', '[a-z0-9-]+')
        ->name('projects.update');

    Route::delete('/projects/{slug}', [ProjectController::class, 'destroy'])
        ->where('slug', '[a-z0-9-]+')
        ->name('projects.destroy');

    // Demandes envoyées par le développeur connecté
    Route::get('/my-collaboration-requests', [
        CollaborationOfferController::class,
        'mine',
    ])->name('collaborations.mine');

    // Demandes reçues par le propriétaire d'un projet
    Route::get('/projects/{slug}/collaboration-requests', [
        CollaborationOfferController::class,
        'received',
    ])->where('slug', '[a-z0-9-]+')
        ->name('projects.collaboration-requests');

    // Envoyer une demande
    Route::post('/projects/{slug}/collaboration-requests', [
        CollaborationOfferController::class,
        'store',
    ])->where('slug', '[a-z0-9-]+')
        ->name('projects.collaboration-requests.store');

    // Accepter ou refuser une demande
    Route::patch('/collaboration-offers/{offer}', [
        CollaborationOfferController::class,
        'update',
    ])->whereNumber('offer')
        ->name('collaboration-offers.update');

    // Retirer sa propre demande
    Route::delete('/collaboration-offers/{offer}', [
        CollaborationOfferController::class,
        'withdraw',
    ])->whereNumber('offer')
        ->name('collaboration-offers.withdraw');

    // Conversations
    // Liste des conversations
    Route::get('/messages', [
        ConversationController::class,
        'index',
    ])->name('messages.index');

    // Rechercher un développeur pour démarrer une conversation
    // Cette route doit être déclarée avant /messages/{conversation}.
    Route::get('/messages/new', [
        ConversationController::class,
        'create',
    ])->name('messages.create');

    // Créer ou retrouver une conversation privée
    Route::post('/messages/start', [
        ConversationController::class,
        'store',
    ])->name('messages.store');

    // Historique d'une conversation
    Route::get('/messages/{conversation}', [
        ConversationController::class,
        'show',
    ])->whereNumber('conversation')
        ->name('messages.show');

    // Envoyer un message
    Route::post('/messages/{conversation}', [
        ConversationController::class,
        'send',
    ])->whereNumber('conversation')
        ->name('messages.send');
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

// Les projets
Route::get('/projects', [ProjectController::class, 'index'])
    ->name('projects.index');

Route::get('/projects/{slug}', [ProjectController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('projects.show');

// Question
Route::get('/questions', [QuestionController::class, 'index'])
    ->name('questions.index');

Route::get('/questions/{question}', [QuestionController::class, 'show'])
    ->name('questions.show');
require __DIR__ . '/settings.php';

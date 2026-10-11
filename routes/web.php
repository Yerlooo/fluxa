<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\NoteController;

// Landing page publik
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard pribadi
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'user' => request()->user()->only('name', 'email'),
        ]);
    })->name('dashboard');

    // To-do List
    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    // Penghapusan massal
    Route::delete('/tasks/bulk', [TaskController::class, 'destroySelected'])
        ->name('tasks.destroy-selected');

    Route::delete('/tasks/all', [TaskController::class, 'destroyAll'])
        ->name('tasks.destroy-all');

    Route::patch('/tasks/{task}', [TaskController::class, 'update'])
        ->name('tasks.update');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy');

    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])
        ->name('bookmarks.index');

    Route::post('/bookmarks', [BookmarkController::class, 'store'])
        ->name('bookmarks.store');

    Route::patch('/bookmarks/{bookmark}', [BookmarkController::class, 'update'])
        ->name('bookmarks.update');

    Route::patch('/bookmarks/{bookmark}/favorite', [
        BookmarkController::class,
        'toggleFavorite',
    ])->name('bookmarks.favorite');

    Route::delete('/bookmarks/{bookmark}', [
        BookmarkController::class,
        'destroy',
    ])->name('bookmarks.destroy');

    // Profil //
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [
        ProfileController::class,
        'updatePassword',
    ])->name('profile.password.update');

    
    // Personal Notes
    Route::get('/notes', [NoteController::class, 'index'])
        ->name('notes.index');

    Route::post('/notes', [NoteController::class, 'store'])
        ->name('notes.store');

    Route::patch('/notes/{note}', [NoteController::class, 'update'])
        ->name('notes.update');

    Route::patch('/notes/{note}/favorite', [
        NoteController::class,
        'toggleFavorite',
    ])->name('notes.favorite');

    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])
        ->name('notes.destroy');

});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
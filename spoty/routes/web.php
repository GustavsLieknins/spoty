<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpotyController;
use Illuminate\Support\Facades\Auth;



Route::get('/auth/spotify/redirect', [SpotyController::class, 'redirectToSpotify'])->name('spotify.login');
Route::get('/auth/spotify/callback', [SpotyController::class, 'handleSpotifyCallback']);

Route::redirect('/', '/top-songs');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/genres', [SpotyController::class, 'genres'])->name('genres');

    Route::get('/create', [SpotyController::class, 'createPlaylistShow'])->name('create.show');
    Route::post('/createPlaylist', [SpotyController::class, 'createPlaylist'])->name('create.create');

    // Route::get('/artists', [SpotyController::class, 'artists'])->name('artists');
    Route::get('/artists', [SpotyController::class, 'artists'])->name('top.artists');

    Route::get('/wrapped', [SpotyController::class, 'showWrapped'])->name('wrapped.show');

    
});

require __DIR__.'/auth.php';

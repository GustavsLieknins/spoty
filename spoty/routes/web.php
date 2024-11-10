<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpotyController;
use Illuminate\Support\Facades\Auth;



Route::get('/auth/spotify/redirect', [SpotyController::class, 'redirectToSpotify'])->name('spotify.login');
Route::get('/auth/spotify/callback', [SpotyController::class, 'handleSpotifyCallback']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/genres', [SpotyController::class, 'genres'])->name('genres');
});

require __DIR__.'/auth.php';

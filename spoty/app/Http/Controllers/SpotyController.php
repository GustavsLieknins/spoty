<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades;
use App\Models\User;

class SpotyController extends Controller
{
    public function redirectToSpotify()
    {
        return Socialite::driver('spotify')->scopes(['user-read-email'])->redirect();
    }
    
    public function handleSpotifyCallback()
    {
        try {
            $spotifyUser = Socialite::driver('spotify')->user();

            // Check if the user already exists
            $user = User::where('spotify_id', $spotifyUser->id)->first();

            if (!$user) {
                // Create a new user if not found
                $user = User::create([
                    'name' => $spotifyUser->name,
                    'email' => $spotifyUser->email,
                    'spotify_id' => $spotifyUser->id,
                    'avatar' => $spotifyUser->avatar,
                    'access_token' => $spotifyUser->token,
                    'refresh_token' => $spotifyUser->refreshToken,
                ]);
            }

            // Log in the user
            Auth::login($user);

            return redirect()->route('dashboard'); // Redirect to the intended page or dashboard
        } catch (\Exception $e) {
            return redirect()->route('login');
        }
    }
    
}

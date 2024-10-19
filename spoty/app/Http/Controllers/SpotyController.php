<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth; 
use App\Models\User;
use App\Services\SpotifyService;
use Illuminate\Http\Request;

class SpotyController extends Controller
{
    protected $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function index(Request $request)
    {
        // Fetch top songs using the SpotifyService
        $timeRange = $request->input('range', 'short_term');
        $topSongs = $this->spotifyService->getTopSongs($limit = 50, $timeRange);

        if ($topSongs === 401) {
            Auth::logout();
            return redirect()->route('login');
        }

        return view('dashboard', compact('topSongs', 'timeRange'));
    }

    public function redirectToSpotify()
    {
        // Request the user-top-read scope along with email
        return Socialite::driver('spotify')->scopes(['user-top-read', 'user-read-email'])->redirect();

    }
    
    public function handleSpotifyCallback()
    {
        try {
            // Get the authenticated user's Spotify information
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
                    'access_token' => $spotifyUser->accessTokenResponseBody['access_token'],
                    'refresh_token' => $spotifyUser->accessTokenResponseBody['refresh_token'],
                    'token' => $spotifyUser->token,
                ]);
            } else {
                // Update existing user's tokens
                $user->update([
                    'access_token' => $spotifyUser->accessTokenResponseBody['access_token'],
                    'refresh_token' => $spotifyUser->accessTokenResponseBody['refresh_token'],
                ]);
            }

            // Log in the user
            Auth::login($user);

            // return redirect()->route('dashboard'); // Redirect to the intended page or dashboard
            return redirect()->route('top.songs');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors('Failed to login with Spotify: ' . $e->getMessage());
        }
    }
}

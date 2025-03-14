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

    public function artists(Request $request)
    {
        $timeRange = $request->input('range', 'medium_term');
        $limit = $request->input('limit', 20);
        $offset = $request->input('offset', 0);

        try {
            $response = $this->spotifyService->getTopArtists($limit, $timeRange, $offset);

            if ($response === 401) {
                Auth::logout();
                return redirect()->route('login');
            }

            $topArtists = $response['items'];
            $total = $response['total'];

            return view('artists', compact('topArtists', 'timeRange', 'limit', 'offset', 'total', 'timeRange'));
        } catch (\Exception $e) {
            // return redirect()->route('login')->withErrors('Failed to fetch top artists: ' . $e->getMessage());
            return  $e->getMessage();
        }
    }

    
    public function genres()
    {
        $genres = $this->spotifyService->getGenres($limit = 50);

        if ($genres === 401) {
            Auth::logout();
            return redirect()->route('login');
        }
        $genresCount = [];
        foreach ($genres['items'] as $artist) {
            foreach ($artist['genres'] as $genre) {
                $genresCount[$genre] = ($genresCount[$genre] ?? 0) + 1;
            }
        }

        $totalGenres = array_sum($genresCount);
        $otherCount = 0;

        $genresCount = collect($genresCount)
            ->map(function ($count, $genre) use ($totalGenres, &$otherCount) {
                $percentage = ($count / $totalGenres) * 100;
                return [
                    'genre' => $genre,
                    'count' => $count,
                    'percentage' => round($percentage, 2),
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->toArray();

        $totalPercentage = array_sum(array_column($genresCount, 'percentage'));

        if (abs($totalPercentage - 100) > 0.01) {
            $difference = 100 - $totalPercentage;
            $genresCount[0]['percentage'] = round($genresCount[0]['percentage'] + $difference, 2);
        }

        return view('genres', compact('genresCount'));
    }

    public function redirectToSpotify()
    {
        // Request the user-top-read scope along with email
        return Socialite::driver('spotify')->scopes(['user-top-read', 'user-read-email', 'playlist-modify-private'])->redirect();

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

            // return  $e->getMessage();
        }
    }

    public function createPlaylist(Request $request)
    {
        $playlistName = $request->input('playlistName');
        $playlistDescription = $request->input('playlistDescription');

        $playlist = $this->spotifyService->createPlaylist($playlistName, $playlistDescription);

        return redirect()->route('genres')->with('success', 'Playlist created successfully!');
    }

    
    public function createPlaylistShow()
    {
        return view('create');
    }
}

<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SpotifyService
{
    protected $client;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->accessToken = $this->getAccessToken();
    }

    protected function getAccessToken()
    {
        // Retrieve the access token from the database or session
        // You might want to implement token refresh logic here if needed
        if (Auth::check()) {
            return Auth::user()->access_token; // Adjust as necessary
        }
    }

    public function getTopSongs($limit, $timeRange = 'short_term')
    {
        try {
            $response = $this->client->request('GET', 'https://api.spotify.com/v1/me/top/tracks', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'limit' => $limit,
                    'time_range' => $timeRange, 
                ],
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            // Check if it's a 401 Unauthorized error
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
                // Log the user out
                Auth::logout();
                
                // Optionally log the event
                // Log::warning('User logged out due to invalid Spotify access token.');

                // Redirect or handle this case appropriately
                return $response = 401;

            }

            // Handle other potential exceptions
            throw $e;
        }
    }

    
    public function getGenres($limit)
    {
        try {
            $response = $this->client->request('GET', 'https://api.spotify.com/v1/me/top/artists', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'limit' => $limit,
                ],
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            // Handle 401 Unauthorized error
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
                return $response = 401;
            }

            // Handle other potential exceptions
            throw $e;
        }
    }

public function createPlaylist($playlistName, $playlistDescription, $timeRange = 'short_term')
{
    try {
        $topSongs = $this->getTopSongs(50, $timeRange);

        if ($topSongs === 401) {
            return $response = 401;
        }

        $tracks = array_map(function ($song) {
            return $song['id'];
        }, $topSongs['items']);

        $userId = Auth::user()->spotify_id;

        $playlistData = [
            'name' => $playlistName ?: 'Default Playlist Name',
            'description' => $playlistDescription,
            'public' => false,
        ];

        $response = $this->client->request('POST', "https://api.spotify.com/v1/users/$userId/playlists", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $playlistData,
        ]);

        $playlistId = json_decode($response->getBody(), true)['id'];

        $response = $this->client->request('POST', "https://api.spotify.com/v1/playlists/$playlistId/tracks", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'uris' => array_map(function ($track) {
                    return "spotify:track:$track";
                }, $tracks),
            ],
        ]);

        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
            return $response = 401;
        }

        throw $e;
    }
}

public function getTopArtists($limit, $timeRange = 'short_term')
{
    try {
        $response = $this->client->request('GET', 'https://api.spotify.com/v1/me/top/artists', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
            'query' => [
                'limit' => $limit,
                'time_range' => $timeRange, 
            ],
        ]);
        
        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        // Check if it's a 401 Unauthorized error
        if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
            // Log the user out
            Auth::logout();
            
            // Optionally log the event
            // Log::warning('User logged out due to invalid Spotify access token.');

            // Redirect or handle this case appropriately
            return $response = 401;

        }

        // Handle other potential exceptions
        throw $e;
    }
}


// public function createPlaylist(Request $request)
// {
//     $playlistName = $request->input('playlist_name');
//     $playlistDescription = $request->input('playlist_description');

//     $playlist = $this->spotifyService->createPlaylist($playlistName, $playlistDescription);

//     return redirect()->route('dashboard')->with('success', 'Playlist created successfully!');
// }

// curl --request POST \
// --url https://api.spotify.com/v1/users/smedjan/playlists \
// --header 'Authorization: Bearer 1POdFZRZbvb...qqillRxMr2z' \
// --header 'Content-Type: application/json' \
// --data '{
// "name": "New Playlist",
// "description": "New playlist description",
// "public": false
// }'
// /users/{user_id}/playlists

}
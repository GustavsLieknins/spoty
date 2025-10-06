<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::check()) {
            return Auth::user()->access_token;
        }
    }

    public function getTopSongs($limit, $timeRange = 'short_term')
    {
        try {
            $response = $this->client->request('GET', 'https://api.spotify.com/v1/me/top/tracks', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'limit' => $limit,
                    'time_range' => $timeRange,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
                Auth::logout();

                return $response = 401;
            }

            throw $e;
        }
    }

    public function getGenres($limit)
    {
        try {
            $response = $this->client->request('GET', 'https://api.spotify.com/v1/me/top/artists', [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'limit' => $limit,
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
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $playlistData,
            ]);

            $playlistId = json_decode($response->getBody(), true)['id'];

            $response = $this->client->request('POST', "https://api.spotify.com/v1/playlists/$playlistId/tracks", [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->accessToken,
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
                    'Authorization' => 'Bearer '.$this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'limit' => $limit,
                    'time_range' => $timeRange,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 401) {
                Auth::logout();

                return $response = 401;
            }

            throw $e;
        }
    }
}

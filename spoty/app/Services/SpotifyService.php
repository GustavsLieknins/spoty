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
}

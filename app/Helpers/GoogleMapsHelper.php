<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class GoogleMapsHelper
{
    public static function getDistance($origin, $destination)
    {
        $apiKey = config('services.google_maps.key'); // Fetch API key from config/services.php

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json";

        $response = Http::get($url, [
            'origins' => $origin,
            'destinations' => $destination,
            'key' => $apiKey,
            'mode' => 'driving', // Modes: driving, walking, bicycling, transit
            'units' => 'metric'
        ]);

        $data = $response->json();

        if ($data['status'] == 'OK') {
            return [
                'distance' => $data['rows'][0]['elements'][0]['distance']['text'] ?? 'N/A',
                'duration' => $data['rows'][0]['elements'][0]['duration']['text'] ?? 'N/A',
            ];
        }

        return ['error' => $data['status'] ?? 'Unknown error'];
    }
}

<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function index()
    {
        return view('front.distance.map');
    }

    public function calculateDistance(Request $request)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $origin = urlencode($request->input('origin'));
        $destination = urlencode($request->input('destination'));

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$origin}&destinations={$destination}&mode=driving&key={$apiKey}";

        $response = Http::get($url);
        $data = $response->json();

        if (!empty($data['rows'][0]['elements'][0]['distance'])) {
            return response()->json([
                'origin' => $request->input('origin'),
                'destination' => $request->input('destination'),
                'distance' => $data['rows'][0]['elements'][0]['distance']['text'],
                'duration' => $data['rows'][0]['elements'][0]['duration']['text']
            ]);
        }

        return response()->json(['error' => 'ไม่สามารถคำนวณระยะทางได้'], 400);
    }
}


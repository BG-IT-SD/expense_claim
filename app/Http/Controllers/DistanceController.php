<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DistanceController extends Controller
{
    // public function showForm()
    // {
    //     return view('front.distance.distance-form');
    // }

    // public function calculateDistance(Request $request)
    // {
    //     $request->validate([
    //         'google_maps_url' => 'required|url'
    //     ]);

    //     $coordinates = $this->getCoordinatesFromUrl($request->google_maps_url);

    //     if (!$coordinates) {
    //         return back()->withErrors(['error' => 'ลิงก์ Google Maps ไม่ถูกต้อง หรือไม่สามารถดึงพิกัดได้']);
    //     }

    //     return view('front.distance.map-form', [
    //         'origin' => $coordinates['origin'],
    //         'destination' => $coordinates['destination'],
    //         'google_maps_url' => $request->google_maps_url
    //     ]);
    // }

    // private function getCoordinatesFromUrl($url)
    // {
    //     // ดึงค่าพิกัดจาก URL Google Maps ที่มีโครงสร้าง /dir/
    //     preg_match('/dir\/([^\/]+)\/([^\/?]+)/', $url, $matches);

    //     if (count($matches) >= 3) {
    //         return [
    //             'origin' => urldecode($matches[1]),
    //             'destination' => urldecode($matches[2])
    //         ];
    //     }

    //     return null;
    // }




    public function showForm()
    {
        return view('front.distance.distance-form');
    }

    public function calculateDistance(Request $request)
    {
        $request->validate([
            'google_maps_url' => 'required|url'
        ]);

        // ดึงค่าพิกัดจาก URL
        $urlmap = $request->google_maps_url;
        // dd($urlmap);

        $conURL = $this->expandGoogleMapsShortUrl($urlmap);
        $coordinates = $this->getCoordinatesFromUrl($conURL);
        // dd($coordinates);


        if (!$coordinates) {
            return back()->withErrors(['error' => '❌ ไม่สามารถดึงพิกัดจากลิงก์ Google Maps ได้']);
        }

        // คำนวณระยะทาง
        $distanceData = $this->getDistanceUsingRoutesAPI($coordinates['lat'], $coordinates['lng']);
        dd($distanceData);

        if (!$distanceData) {
            return back()->withErrors(['error' => '❌ ไม่สามารถคำนวณระยะทางได้']);
        }

        return view('front.distance.map-form', [
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lng'],
            'distance' => $distanceData['distance'],
            'duration' => $distanceData['duration'],
            'google_maps_url' => $request->google_maps_url
        ]);
    }

    private function getCoordinatesFromUrl($url)
    {
        // ดึงค่าพิกัดจาก URL Google Maps ที่อยู่หลัง @
        preg_match('/@([-0-9.]+),([-0-9.]+)/', $url, $matches);

        if (count($matches) >= 3) {
            return [
                'lat' => $matches[1],
                'lng' => $matches[2]
            ];
        }

        return null;
    }
    private function getDistanceUsingRoutesAPI($latitude, $longitude)
    {
        $apiKey = config('services.google_maps.key');
        $destinationLat = 13.7563;
        $destinationLng = 100.5018;

        $url = "https://routes.googleapis.com/distanceMatrix/v2:computeRouteMatrix";

        $curl = curl_init();
        $data = [
            'origins' => [
                [
                    'waypoint' => [
                        'location' => [
                            'latLng' => [
                                'latitude' => 13.736717,
                                'longitude' => 100.523186
                            ]
                        ]
                    ]
                ]
            ],
            'destinations' => [
                [
                    'waypoint' => [
                        'location' => [
                            'latLng' => [
                                'latitude' => 13.7563,
                                'longitude' => 100.5018
                            ]
                        ]
                    ]
                ]
            ],
            'travelMode' => 'DRIVE'
        ];
        dd(json_encode($data));
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'X-Goog-Api-Key: '.$apiKey,
            'X-Goog-FieldMask: originIndex,destinationIndex,duration,distanceMeters'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if (!empty($response) && isset($response[0]['distanceMeters'])) {
            return [
                'distance' => ($response[0]['distanceMeters'] / 1000) . " km",
                'duration' => gmdate("H:i:s", (int) filter_var($response[0]['duration'], FILTER_SANITIZE_NUMBER_INT))
            ];
        }
        // dd($response);

        // return null;
        // return $response;
    }



    // private function expandGoogleMapsShortUrl($shortUrl)
    // {
    //     // dd($shortUrl);
    //     try {
    //         $response = Http::get($shortUrl);
    //         return $response->effectiveUri(); // คืนค่า URL เต็มที่ Redirect ไป
    //     } catch (\Exception $e) {
    //         return $shortUrl; // คืนค่าเดิมถ้าดึงข้อมูลไม่ได้
    //     }
    // }

    private function expandGoogleMapsShortUrl($shortUrl)
{
    if (!filter_var($shortUrl, FILTER_VALIDATE_URL)) {
        return $shortUrl;
    }

    $headers = @get_headers($shortUrl, 1); // ใส่ "@" เพื่อป้องกัน Error หาก URL ใช้งานไม่ได้

    if (isset($headers['Location'])) {
        return is_array($headers['Location']) ? end($headers['Location']) : $headers['Location'];
    }

    return $shortUrl;
}

}

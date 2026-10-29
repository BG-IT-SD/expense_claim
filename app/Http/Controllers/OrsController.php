<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrsController extends Controller
{
    public function autocomplete(Request $request)
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'min:2', 'max:2048'],
        ]);

        $manualLocation = $this->resolveManualLocation($validated['text']);

        if ($manualLocation) {
            return response()->json([
                'results' => [$manualLocation],
            ]);
        }

        $response = $this->geoapify()
            ->acceptJson()
            ->get($this->geoapifyUrl('/v1/geocode/autocomplete'), [
                'text' => $validated['text'],
                'limit' => 6,
                'filter' => 'countrycode:th',
                'lang' => 'th',
                'apiKey' => config('services.geoapify.key'),
            ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Geoapify autocomplete failed.',
            ], 502);
        }

        $features = collect($response->json('features', []))
            ->map(function ($feature) {
                $properties = $feature['properties'] ?? [];
                $coordinates = $feature['geometry']['coordinates'] ?? null;

                if (!is_array($coordinates) || count($coordinates) < 2) {
                    return null;
                }

                return [
                    'label' => $properties['formatted'] ?? $properties['address_line1'] ?? $properties['name'] ?? '',
                    'name' => $properties['name'] ?? $properties['address_line1'] ?? $properties['formatted'] ?? '',
                    'address' => $properties['formatted'] ?? '',
                    'lat' => (float) $coordinates[1],
                    'lng' => (float) $coordinates[0],
                ];
            })
            ->filter(fn ($item) => !empty($item['label']))
            ->values();

        return response()->json([
            'results' => $features,
        ]);
    }

    public function directions(Request $request)
    {
        $request->validate([
            'origin.lat' => ['required', 'numeric', 'between:-90,90'],
            'origin.lng' => ['required', 'numeric', 'between:-180,180'],
            'destination.lat' => ['required', 'numeric', 'between:-90,90'],
            'destination.lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $response = $this->ors()
            ->accept('application/geo+json')
            ->asJson()
            ->post($this->orsUrl('/openrouteservice/v2/directions/driving-car/geojson'), [
                'coordinates' => [
                    [
                        (float) $request->input('origin.lng'),
                        (float) $request->input('origin.lat'),
                    ],
                    [
                        (float) $request->input('destination.lng'),
                        (float) $request->input('destination.lat'),
                    ],
                ],
                'units' => 'm',
            ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'OpenRouteService directions failed.',
            ], 502);
        }

        $feature = $response->json('features.0');
        $summary = $feature['properties']['summary'] ?? [];
        $distanceMeters = (float) ($summary['distance'] ?? 0);

        return response()->json([
            'distance_meters' => $distanceMeters,
            'duration_seconds' => (float) ($summary['duration'] ?? 0),
            'distance_text' => number_format($distanceMeters / 1000, 2) . ' km',
            'geometry' => $feature['geometry'] ?? null,
        ]);
    }

    public function routeLink(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        $originalUrl = trim($validated['url']);
        $expandedUrl = $this->expandUrl($originalUrl);
        $route = $this->parseGoogleRouteLink($expandedUrl) ?: $this->parseGoogleRouteLink($originalUrl);

        if (!$route) {
            return response()->json([
                'message' => 'อ่านเส้นทางจากลิงก์นี้ไม่ได้ กรุณาวางลิงก์สถานที่ต้นทาง/ปลายทางแยกช่อง',
            ], 422);
        }

        $origin = $this->resolveRouteEndpoint($route['origin']);
        $destination = $this->resolveRouteEndpoint($route['destination']);

        if (!$origin || !$destination) {
            return response()->json([
                'message' => 'อ่านเส้นทางจากลิงก์นี้ไม่ได้ กรุณาวางลิงก์สถานที่ต้นทาง/ปลายทางแยกช่อง',
            ], 422);
        }

        return response()->json([
            'origin' => $origin,
            'destination' => $destination,
            'expanded_url' => $expandedUrl,
        ]);
    }

    private function ors()
    {
        $apiKey = config('services.openrouteservice.key');

        abort_if(empty($apiKey), 500, 'OpenRouteService API key is not configured.');

        return Http::withHeaders([
            'Authorization' => $apiKey,
            'Accept-Language' => 'th,en;q=0.8',
        ])->timeout(15);
    }

    private function orsUrl(string $path): string
    {
        return rtrim(config('services.openrouteservice.base_url'), '/') . $path;
    }

    private function geoapify()
    {
        $apiKey = config('services.geoapify.key');

        abort_if(empty($apiKey), 500, 'Geoapify API key is not configured.');

        return Http::timeout(15);
    }

    private function geoapifyUrl(string $path): string
    {
        return rtrim(config('services.geoapify.base_url'), '/') . $path;
    }

    private function resolveManualLocation(string $text): ?array
    {
        $candidate = trim($text);

        if ($coordinates = $this->extractCoordinates($candidate)) {
            return $this->manualLocationResult($candidate, $coordinates);
        }

        if (!filter_var($candidate, FILTER_VALIDATE_URL)) {
            return null;
        }

        $expandedUrl = $this->expandUrl($candidate);

        if ($coordinates = $this->extractCoordinates($expandedUrl)) {
            return $this->manualLocationResult($candidate, $coordinates);
        }

        return null;
    }

    private function parseGoogleRouteLink(string $url): ?array
    {
        $decodedUrl = urldecode($url);
        $parts = parse_url($decodedUrl);
        $query = [];

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        if (!empty($query['origin']) && !empty($query['destination'])) {
            return [
                'origin' => $query['origin'],
                'destination' => $query['destination'],
            ];
        }

        $path = $parts['path'] ?? '';
        $pathSegments = array_values(array_filter(explode('/', trim($path, '/')), fn ($segment) => $segment !== ''));
        $dirIndex = array_search('dir', $pathSegments, true);

        if ($dirIndex !== false) {
            $routeSegments = array_slice($pathSegments, $dirIndex + 1);
            $routeSegments = array_values(array_filter($routeSegments, function ($segment) {
                return !$this->isGoogleRouteMetadataSegment($segment);
            }));

            if (count($routeSegments) >= 2) {
                return [
                    'origin' => rawurldecode($routeSegments[0]),
                    'destination' => rawurldecode($routeSegments[1]),
                ];
            }
        }

        $coordinates = $this->extractAllCoordinates($decodedUrl);

        if (count($coordinates) >= 2) {
            return [
                'origin' => $coordinates[0],
                'destination' => $coordinates[count($coordinates) - 1],
            ];
        }

        return null;
    }

    private function isGoogleRouteMetadataSegment(string $segment): bool
    {
        return str_starts_with($segment, '@')
            || str_starts_with($segment, 'data=')
            || str_starts_with($segment, 'am=')
            || preg_match('/^\d+(?:\.\d+)?z$/', $segment);
    }

    private function resolveRouteEndpoint($endpoint): ?array
    {
        if (is_array($endpoint)) {
            return $this->manualLocationResult(
                $endpoint['lat'] . ',' . $endpoint['lng'],
                $endpoint
            );
        }

        $label = trim($endpoint);

        if ($label === '') {
            return null;
        }

        if ($coordinates = $this->extractCoordinates($label)) {
            return $this->manualLocationResult($label, $coordinates);
        }

        return $this->geocodeText($label);
    }

    private function geocodeText(string $text): ?array
    {
        try {
            $response = $this->geoapify()
                ->acceptJson()
                ->get($this->geoapifyUrl('/v1/geocode/search'), [
                    'text' => $text,
                    'limit' => 1,
                    'filter' => 'countrycode:th',
                    'lang' => 'th',
                    'apiKey' => config('services.geoapify.key'),
                ]);
        } catch (\Throwable) {
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $feature = $response->json('features.0');
        $properties = $feature['properties'] ?? [];
        $coordinates = $feature['geometry']['coordinates'] ?? null;

        if (!is_array($coordinates) || count($coordinates) < 2) {
            return null;
        }

        return [
            'label' => $text,
            'name' => $text,
            'address' => $properties['formatted'] ?? '',
            'lat' => (float) $coordinates[1],
            'lng' => (float) $coordinates[0],
        ];
    }

    private function extractCoordinates(string $value): ?array
    {
        $decoded = urldecode($value);

        $patterns = [
            '/^\s*(-?\d{1,2}(?:\.\d+)?)\s*,\s*(-?\d{1,3}(?:\.\d+)?)\s*$/',
            '/@(-?\d{1,2}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)/',
            '/!3d(-?\d{1,2}(?:\.\d+)?)!4d(-?\d{1,3}(?:\.\d+)?)/',
            '/(?:[?&](?:q|query|destination|origin|ll)=)(-?\d{1,2}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)/',
            '/\/(-?\d{1,2}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)(?:[\/?&]|$)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $decoded, $matches)) {
                $lat = (float) $matches[1];
                $lng = (float) $matches[2];

                if ($this->validCoordinates($lat, $lng)) {
                    return [
                        'lat' => $lat,
                        'lng' => $lng,
                    ];
                }
            }
        }

        return null;
    }

    private function extractAllCoordinates(string $value): array
    {
        $decoded = urldecode($value);
        $patterns = [
            '/@(-?\d{1,2}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)/',
            '/!3d(-?\d{1,2}(?:\.\d+)?)!4d(-?\d{1,3}(?:\.\d+)?)/',
            '/(?:[?&](?:q|query|destination|origin|ll)=)(-?\d{1,2}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)/',
            '/\/(-?\d{1,2}(?:\.\d+)?),(-?\d{1,3}(?:\.\d+)?)(?:[\/?&]|$)/',
        ];

        $coordinates = [];
        $seen = [];

        foreach ($patterns as $pattern) {
            if (!preg_match_all($pattern, $decoded, $matches, PREG_SET_ORDER)) {
                continue;
            }

            foreach ($matches as $match) {
                $lat = (float) $match[1];
                $lng = (float) $match[2];

                if (!$this->validCoordinates($lat, $lng)) {
                    continue;
                }

                $key = round($lat, 6) . ',' . round($lng, 6);

                if (isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;
                $coordinates[] = [
                    'lat' => $lat,
                    'lng' => $lng,
                ];
            }
        }

        return $coordinates;
    }

    private function expandUrl(string $url): string
    {
        $currentUrl = $url;

        for ($i = 0; $i < 5; $i++) {
            try {
                $response = Http::withoutVerifying()
                    ->withOptions(['allow_redirects' => false])
                    ->timeout(10)
                    ->get($currentUrl);
            } catch (\Throwable) {
                return $currentUrl;
            }

            $location = $response->header('Location');

            if (!$location) {
                return $currentUrl;
            }

            if (str_starts_with($location, '/')) {
                $parts = parse_url($currentUrl);
                $scheme = $parts['scheme'] ?? 'https';
                $host = $parts['host'] ?? '';
                $location = $scheme . '://' . $host . $location;
            }

            $currentUrl = $location;

            if ($this->extractCoordinates($currentUrl)) {
                return $currentUrl;
            }
        }

        return $currentUrl;
    }

    private function validCoordinates(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180;
    }

    private function manualLocationResult(string $label, array $coordinates): array
    {
        return [
            'label' => $label,
            'name' => $label,
            'address' => $label,
            'lat' => $coordinates['lat'],
            'lng' => $coordinates['lng'],
        ];
    }
}

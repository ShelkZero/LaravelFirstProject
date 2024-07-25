<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    protected $weatherUrl = 'https://api.openweathermap.org/data/2.5/weather';
    protected $apiKey;
    protected $ipinfoApiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openweathermap.api_key'); // Load API key from configuration
        $this->ipinfoApiKey = config('services.ipinfo.api_key'); // Load IPinfo API key from configuration
    }

    public function showWeather(Request $request)
    {
        $ip = $this->getClientIp($request);

        if ($ip === '127.0.0.1' || $ip === 'localhost') {
            $latitude = '48.4647'; // Coordinates of Dnipro
            $longitude = '35.0462';
        } else {
            $location = $this->getLocationFromIp($ip);

            if (!isset($location['latitude']) || !isset($location['longitude'])) {
                return view('weather', ['weatherData' => ['error' => $location['error']]]);
            }

            $latitude = $location['latitude'];
            $longitude = $location['longitude'];
        }

        Log::info('Location coordinates obtained', ['latitude' => $latitude, 'longitude' => $longitude]);

        $cacheKey = "weather_{$latitude}_{$longitude}";
        $weatherData = Cache::store('redis')->get($cacheKey);

        if (!$weatherData) {
            try {
                $response = Http::get("{$this->weatherUrl}?lat={$latitude}&lon={$longitude}&appid={$this->apiKey}&units=metric");

                Log::info('Weather API request URL', ['url' => "{$this->weatherUrl}?lat={$latitude}&lon={$longitude}&appid={$this->apiKey}&units=metric"]);
                Log::info('Weather API response', ['response' => $response->body()]);

                if ($response->successful()) {
                    $weatherData = $response->json();
                    Cache::store('redis')->put($cacheKey, $weatherData, now()->addHour());
                } else {
                    Log::error('Weather API error', [
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);
                    $weatherData = ['error' => 'Failed to retrieve weather data'];
                }
            } catch (\Exception $e) {
                Log::error('Exception', ['message' => $e->getMessage()]);
                $weatherData = ['error' => 'An error occurred while retrieving weather data'];
            }
        } else {
            Log::info('Weather data found in cache', ['weatherData' => $weatherData]);
        }

        return view('weather', ['weatherData' => $weatherData]);
    }

    private function getClientIp(Request $request)
    {
        $ip = $request->server('HTTP_X_FORWARDED_FOR')
            ? explode(',', $request->server('HTTP_X_FORWARDED_FOR'))[0]
            : ($request->server('HTTP_X_REAL_IP')
                ? $request->server('HTTP_X_REAL_IP')
                : $request->ip()
            );

        Log::info('Client IP address obtained', ['ip' => trim($ip)]);

        return trim($ip);
    }

    private function getLocationFromIp($ip)
    {
        Log::info('IP address received for geolocation', ['ip' => $ip]);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->ipinfoApiKey}",
            ])->get("https://ipinfo.io/{$ip}/json");

            Log::info('IPinfo API request URL', ['url' => "https://ipinfo.io/{$ip}/json"]);
            Log::info('Response from IPinfo', ['response' => $response->body()]);

            if ($response->successful()) {
                $location = $response->json();

                if (isset($location['loc']) && !empty($location['loc'])) {
                    $coordinates = explode(',', $location['loc']);
                    return [
                        'latitude' => $coordinates[0],
                        'longitude' => $coordinates[1],
                    ];
                } else {
                    Log::error('IPinfo response does not contain loc field or loc field is empty', ['response' => $response->body()]);
                    return [
                        'latitude' => null,
                        'longitude' => null,
                        'error' => 'Failed to retrieve location coordinates',
                    ];
                }
            } else {
                Log::error('IPinfo API error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return [
                    'latitude' => null,
                    'longitude' => null,
                    'error' => 'Failed to retrieve location coordinates',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception during IPinfo request', ['message' => $e->getMessage()]);
            return [
                'latitude' => null,
                'longitude' => null,
                'error' => 'An error occurred while retrieving location data',
            ];
        }
    }
}

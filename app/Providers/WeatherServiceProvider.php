<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class WeatherServiceProvider extends ServiceProvider
{
    protected $weatherUrl = 'https://api.openweathermap.org/data/2.5/weather';
    protected $apiKey = 'f848ea6ef59ea770c725824305f12cf7'; 
    protected $ipinfoApiKey = 'bbebc5d44349e5'; 

    public function register()
    {
        
    }

    public function boot(Request $request)
    {
        View::composer('*', function ($view) use ($request) {
            $ip = $this->getClientIp($request);

            if ($ip === '127.0.0.1' || $ip === 'localhost') {
                $latitude = '48.4647'; 
                $longitude = '35.0462';
            } else {
                $location = $this->getLocationFromIp($ip);

                if (!isset($location['latitude']) || !isset($location['longitude'])) {
                    $view->with('weatherData', ['error' => $location['error']]);
                    return;
                }

                $latitude = $location['latitude'];
                $longitude = $location['longitude'];
            }

            $cacheKey = "weather_{$latitude}_{$longitude}";
            $weatherData = Cache::store('redis')->get($cacheKey);

            if (!$weatherData) {
                try {
                    $response = Http::get("{$this->weatherUrl}?lat={$latitude}&lon={$longitude}&appid={$this->apiKey}&units=metric");

                    if ($response->successful()) {
                        $weatherData = $response->json();
                        Cache::store('redis')->put($cacheKey, $weatherData, now()->addHour());
                    } else {
                        $weatherData = ['error' => 'Failed to retrieve weather data'];
                    }
                } catch (\Exception $e) {
                    $weatherData = ['error' => 'An error occurred while retrieving weather data'];
                }
            }

            $view->with('weatherData', $weatherData);
        });
    }

    private function getClientIp(Request $request)
    {
        $ip = $request->server('HTTP_X_FORWARDED_FOR')
            ? explode(',', $request->server('HTTP_X_FORWARDED_FOR'))[0]
            : ($request->server('HTTP_X_REAL_IP')
                ? $request->server('HTTP_X_REAL_IP')
                : $request->ip()
            );

        return trim($ip);
    }

    private function getLocationFromIp($ip)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->ipinfoApiKey}",
            ])->get("https://ipinfo.io/{$ip}/json");

            if ($response->successful()) {
                $location = $response->json();

                if (isset($location['loc']) && !empty($location['loc'])) {
                    $coordinates = explode(',', $location['loc']);
                    return [
                        'latitude' => $coordinates[0],
                        'longitude' => $coordinates[1],
                    ];
                } else {
                    return [
                        'latitude' => null,
                        'longitude' => null,
                        'error' => 'Failed to retrieve location coordinates',
                    ];
                }
            } else {
                return [
                    'latitude' => null,
                    'longitude' => null,
                    'error' => 'Failed to retrieve location coordinates',
                ];
            }
        } catch (\Exception $e) {
            return [
                'latitude' => null,
                'longitude' => null,
                'error' => 'An error occurred while retrieving location data',
            ];
        }
    }
}

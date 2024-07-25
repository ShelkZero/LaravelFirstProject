<!DOCTYPE html>
<html>
<head>
    <title>Weather Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
            margin: 0;
        }
        .weather-info {
            margin-top: 20px;
            display: flex;
            align-items: center;
        }
        .weather-icon {
            width: 50px;
            height: 50px;
            margin-right: 20px;
        }
        .weather-details {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .weather-details p {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="weather-info">
        @if (isset($weatherData['error']))
            <p>{{ $weatherData['error'] }}</p>
        @else
            @if(isset($weatherData['weather']) && isset($weatherData['main']))
                <img src="http://openweathermap.org/img/wn/{{ $weatherData['weather'][0]['icon'] }}@2x.png" alt="Weather Icon" class="weather-icon">
                <div class="weather-details">
                    <p><strong>Weather Information:</strong></p>
                    <p>Location: {{ $weatherData['name'] }}</p>
                    <p>Temperature: {{ $weatherData['main']['temp'] }}°C</p>
                    <p>Weather: {{ $weatherData['weather'][0]['description'] }}</p>
                    <p>Humidity: {{ $weatherData['main']['humidity'] }}%</p>
                    <p>Pressure {{ $weatherData['main']['pressure'] }} hPa</p>
                    <p>Wind Speed: {{ $weatherData['wind']['speed'] }} m/s</p>
                    <p>Visibility: {{ $weatherData['visibility'] / 1000 }} km</p>
                </div>
            @else
                <p>Не удалось получить данные о погоде</p>
            @endif
        @endif
    </div>
</body>
</html>

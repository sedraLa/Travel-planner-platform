<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>5-Day Weather Forecast</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f8ff;
            color: #333;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #007acc;
        }

        table {
            margin: 30px auto;
            border-collapse: collapse;
            width: 90%;
            max-width: 800px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #007acc;
            color: white;
        }

        tr:hover {
            background-color: #f1f9ff;
        }

        img.weather-icon {
            width: 40px;
            height: 40px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    @if ($errorMessage)
    <div class="bg-red-100 text-red-700 p-3 rounded">
        {{ $errorMessage }}
    </div>
@else
    <h1>5-Day Weather Forecast for {{ $weather['city']['name'] }}</h1>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Temperature (°C)</th>
                <th>Condition</th>
                <th>Humidity (%)</th>
            </tr>
        </thead>
        <tbody>
            @php $datesShown = []; @endphp <!--store shown dates so that we don't repeat them-->

            @foreach ($weather['list'] as $entry)
                @php $date = explode(' ', $entry['dt_txt'])[0]; @endphp <!--to take dates only without hours-->

                @if (!in_array($date, $datesShown)) <!--check if this date has been shown earlier-->
                    @php $datesShown[] = $date; @endphp
                    <tr>
                        <td>{{ $date }}</td>
                        <td>{{ $entry['main']['temp'] }}</td>
                        <td>
                            <img src="http://openweathermap.org/img/wn/{{ $entry['weather'][0]['icon'] }}@2x.png"
                                 alt="icon"
                                 class="weather-icon">
                            <br>
                            {{ $entry['weather'][0]['description'] }}
                        </td>
                        <td>{{ $entry['main']['humidity'] }}</td>
                    </tr>
                @endif

                @if (count($datesShown) === 5)
                    @break
                @endif
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>

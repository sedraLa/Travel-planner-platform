<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{asset('css/details.css')}}"
    @endpush
    {{--body content--}}
    <div class="main-wrapper">
        <div class="hero-background" style="background-image: url('{{ asset('storage/' . $destination->images->where('is_primary', true)->first()->image_url) }}');">
        <div class="headings">
            <h1>{{$destination->name}}</h1>
            <h3>{{$destination->city}}</h3>
        </div>
        </div>

        <div class="details">
            <header>Explore everything about this city</header>
            <div class="cards">
                @foreach($destination->images as $image)
                    @if(!$image->is_primary) 
                        <div class="card">
                            <img src="{{ asset('storage/' . $image->image_url) }}" alt="Destination Image">
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="container">
                    <div class="location">
                <h1>Location</h1>
                <p>{{$destination->location_details}}</p>
            </div>
            <div class="flight-deals">
                <h1>Flight deals to {{$destination->name}}</h1>
            <p>{{$destination->description}}</p>
            </div>
            <div class="things">
                <h1>Things to do in {{$destination->name}}</h1>
                    <p>{{ $destination->activities }}</p>
            </div>

            <div class="weather">
                <h1>Weather info</h1>
                <p>{{$destination->weather_info}}</p>
                <a href="{{route('weather.forecast',['city'=>$destination->city])}}">View 5-Day Forecast</a>
            </div>
            </div>
        </div>
    </div>
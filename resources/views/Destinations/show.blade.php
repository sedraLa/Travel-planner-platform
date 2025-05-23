@php use App\Enums\UserRole;@endphp
<x-details-layout>
    @push('styles')
        <link rel="stylesheet" href="{{asset('css/details.css')}}">
    @endpush

    {{--body content--}}


    <div class="main-wrapper">
    <div class="hero-background" style="background-image: url('{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}');">
        <div class="headings" style="font-size:32px;">
            <h1>{{$destination->name}}</h1>
            <h3>{{$destination->city}}</h3>
        </div>

        </div>
        </div>

        <div class="details">
            <header>Explore everything about this city</header>
            @if (Auth::user()->role === UserRole::ADMIN->value)
            <!-- Create Destination Button -->
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Edit Destination') }}
                </h2>
                <a href="{{ route('destinations.edit', $destination->id) }}"

                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                    Edit
                </a>

                <form action="{{ route('destination.destroy', $destination->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this destination?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                      Delete
                  </button>
              </form>
            </div>

            @endif
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
                <h1>Things to do</h1>
                    <p>{{ $destination->activities }}</p>
            </div>

            <div class="weather">
                <h1>Weather info</h1>
                <a href="{{route('weather.forecast',['city'=>$destination->name])}}">Click here to view 5-Day Forecast</a>
            </div>
            </div>
        </div>
    </div>

</x-details-layout>

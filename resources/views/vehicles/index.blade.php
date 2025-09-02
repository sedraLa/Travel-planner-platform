<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/cardetails.css') }}">
    @endpush

    <!--Suceess message-->

    <!--details-->

    <div class="main-container">
        <header>
            <a href="{{route('transport.index')}}">
                <button class="back">Back</button>
            </a>
            <div class="head">
                <h1>Available cars</h1>
                <p>Manage Vehicles For This Transport Service</p>
            </div>
        </header>

        <!--for user only-->
        <div class="middle-section">
            <div class="overview">
                <div class="trip-overview">
                    <div class="align">
                        <img class="icon" src="{{asset('images/icons/car-side-solid-full.svg')}}">

                        <h2>Trip overview</h2>
                    </div>

                    <ul class="trip-list">
                        <li>Pickup Location:LosAngelous</li>
                        <li>Destination Location:Newyork</li>
                    </ul>
                </div>

            </div>
            <div class="map">
                <img class="map-img" src="{{asset('images/thomas-kinto-6MsMKWzJWKc-unsplash.jpg')}}">
            </div>
        </div>

        <!--cards-->
        <div class="cards">
            @foreach($vehicles as $vehicle)
                <div class="card">
                    <div class="car-image">
                        <img src="{{asset('storage/' . $vehicle->image)}}" alt="car image" class="car-img">
                    </div>
                    <div class="details">
                        <div class="top-section">
                            <div class="main-info">
                                <h2>{{$vehicle->car_model}}</h2>
                                <div class="align">
                                    <img src="{{asset('images/icons/user-group-solid-full.svg')}}" class="icon">
                                    <p>Up to {{$vehicle->max_passengers}} passengers / {{$vehicle->plate_number}} Plate:
                                        LUX-001</p>
                                </div>

                            </div>
                            <div class="category">
                                <span>{{$vehicle->category}}</span>
                            </div>


                        </div>

                        <div class="driver-section">
                            <h4>Driver Name : <span class="driver-name">{{$vehicle->driver_name}}</span></h4>
                            <div class="align">
                                <img src="{{asset('images/icons/phone-solid-full.svg')}}" class="icon">
                                <p>{{$vehicle->driver_contact}}</p>

                            </div>

                        </div>

                        <div class="price-section">
                            <div class="left">
                                <h4>Pase Price : {{$vehicle->base_price}}$</h4>
                                <p>+ ${{$vehicle->price_per_km}}/km</p>
                            </div>
                            <div class="right">
                                <p>Final price calculated on completion</p>
                            </div>

                        </div>
                        <div class="book-car">
                            <button type="submit">Book This Car</button>
                        </div>

                        <a href="{{ route('vehicle.edit', $vehicle->id) }}" class="edit-btn">Edit</a>

                        {{-- 3. زر الحذف --}}
                        <form action="{{ route('vehicle.destroy', $vehicle->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this vehicle?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </div>


                </div>
            @endforeach

        </div>
    </div>



    </div>
</x-app-layout>
<x-app-layout>
    @push('styles')
    <link rel=stylesheet href="{{asset('css/flights.css')}}">
    @endpush


    <div class="main-wrapper">
        <div class="hero-background flight-page">

          <div class="headings">
            <h1 style='font-size:42px;'>Fly Anywhere</h1>
            <h3 style='font-size:32px;'>Fly Budget and Comfortably</h3>
          </div>
        </div>
        
    
        <div class="main-container">
          <p>Enter your details</p>
          <span style='font-weight:normal;font-size:15px'>*Fill the Return field only if your trip type is round-trip</span>
          <!--flight form-->
          <div class="form-container">
            <form action="{{ route('flights.search') }}" method="POST">
                @csrf

                <!--where to select-->
                <div class="container">
                    <x-input-label for="country" value="Where from?"/>
                <select class="crs-country" name="country"  id="country">
                    <option value="">select country</option>
                    @foreach ($destinations as $destination) 
                        <option value="{{$destination->id}}">{{$destination->name}}</option>
                    
                    @endforeach

                </select>
                </div>

                <!--where from select-->
                <div class="container">
                    <x-input-label for="to-country" value="Where to?"/>
                <select class="crs-country" name="to-country" id="to-country">
                    <option value="">select country</option>
                    @foreach ($destinations as $destination)
                        <option value="{{$destination->id}}">{{$destination->name}}</option>
                    
                    @endforeach
                </select>
                </div>
                <!--departure date-->
                <div class="container">
                    <x-input-label for="departure" value="Departure"/>
                    <input id="departure" type="date" name="departure">
                </div>

                <!--return date-->
                <div class="container">
                    <x-input-label for="return" value="Return"/>
                    <input id="return" type="date" name="return">
                </div>
               
                <!--seats number-->
               <div class="container">
                <x-input-label for="seats" value="seats"/>
                <input id="seats" type="number" min="1" name="seats" value="1">
               </div>

               <!--trip type-->
               <div class="container">
                <x-input-label for="trip-type" value="Trip Type"/>
                <select id="trip-type" name="trip-type">
                    <option value="one-way">One-way-trip</option>
                    <option value="round">Round-trip</option>
                </select>
               </div>
            
               
               <div class="search-button-wrapper">
                <button class="search-button" type="submit">Search</button>
              </div>
              </form>
          </div>
          
        </div>
      </div>
</x-app-layout>

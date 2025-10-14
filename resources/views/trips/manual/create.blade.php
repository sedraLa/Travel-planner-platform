<x-app-layout>
    @push('styles')
    <link rel='stylesheet' href="{{asset('css/manual.css')}}">
    @endpush
    <div class="main-container">
    <header>
        <a href="{{route('trip.view')}}">
      <button class="back">Back</button>
    </a>
      <div class="head">
        <h1>Create New Trip Plan</h1>
        <!--<p>Choose how you'd like to create your perfect itinerary</p>-->
        <p>Manual planning — step: {{ $currentStep }}</p>
      </div>
    </header>
    <div class="plan">
      <h2>Manual Planning</h2>
    </div>

    <div id="steps">
      <!-- Circles -->
      <div id="one" class="circle">1</div>
      <div style="width:75px;border-bottom:2px solid blue;margin-bottom: 35px;"></div>
      <div id="two" class="circle">2</div>
      <div style="width:75px;border-bottom:2px solid blue;margin-bottom: 35px;"></div>
      <div id="three" class="circle">3</div>
      <div style="width:75px;border-bottom:2px solid blue;margin-bottom: 35px;"></div>
      <div id="four" class="circle">4</div>
      <div style="width:75px;border-bottom:2px solid blue;margin-bottom: 35px;"></div>
      <div id="five" class="circle">5</div>
      <div style="width:75px;border-bottom:2px solid blue;margin-bottom: 35px;"></div>
    </div>

    <!-- Form containing all steps -->
    <form id="trip-form" method="POST" action="{{route('manual.step')}}">
      @csrf
      <input type="hidden" name="step" id="step-input" value="1">

      <!-- Step 1 Basic trip info -->
      <div class="bottom-container step">
        <div class="form-header">
          <h2>Basic Trip Information</h2>
          <p>Let's start with essentials</p>
        </div>
        <div class="form-container">
          <div class="first-section">
            <div class="container">
              <label for="trip-name">Trip Name</label>
              <input type="text" name="name" id="name" value="{{old('name',$data['basic']['name'] ?? '')}}" placeholder="e.g., European Adventure 2024">
              @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="container">
              <label for="description">Description</label>
              <input type="text" name="description" id="description" value="{{old('description',$data['basic']['description'] ?? '')}}" placeholder="Brief description of your trip ... ">
              @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="second-section">
            <div class="container">
              <label for="start_date">Start Date</label>
              <input type="date" name="start_date" id="start_date" value="{{old('start_date',$data['basic']['start_date'] ?? '')}}">
              @error('start_date') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="container">
              <label for="end_date">End Date</label>
              <input type="date" name="end_date" id="end_date" value="{{old('end_date',$data['basic']['end_date'] ?? '')}}">
              @error('end_date') <div class="error">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="second-section">
            <div class="container">
              <label for="travelers_number">Number of Travelers</label>
              <input type="number" name="travelers_number" id="travelers_number" min="1" placeholder="Enter number of Travelers" value="{{old('travelers_number',$data['basic']['travelers_number'] ?? '')}}">
              @error('travelers_number') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="container">
              <label for="budget">Estimated Budget $</label>
              <input type="text" name="budget" id="budget" placeholder="Enter your Budget" value="{{old('budget',$data['basic']['budget'] ?? '')}}">
              @error('budget') <div class="error">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
      </div>



 <!-- Step 2 -->
<div class="bottom-container step">
      <div class="form-header">
        <h2>Choose Destination</h2>
        <p>Select the city or country you plan to visit</p>
      </div>

      <div class="destinations-container">
        <h3 class="dest-title">🌍 Popular Destinations</h3>
        <div class="destination-cards">
            @foreach ($popular as $destination)
              <div class="dist-card" data-id="{{ $destination->id }}">
                <div class="content">
                  <img src="{{ asset('storage/' . optional($destination->images->where('is_primary', true)->first())->image_url) }}">
                  <div class="info">
                    <h4>{{ $destination->name }}</h4>
                    <span>{{ $destination->country }}</span>
                  </div>
                  <button type="button" class="add-btn" data-id="{{ $destination->id }}">+</button>
                </div>
              </div>
            @endforeach
        </div>

        <input type="hidden" name="destination_id" id="selected_destination">
      </div>

      <h3 class="dest-title" style="margin-top:20px;">Custom Destination</h3>
      <div class="custom" style="display: flex; gap:20px;">
        <label for="custom-destination">Destination Name:</label>
        <input type="text" id="custom-destination" name="destination_name" placeholder="Enter a custom destination" style="border-color: #052659;">
      </div>

      <button type="submit" class="next-btn">Next Step</button>
    </div>



      <!-- Step 3 -->
      <!-- Step 3 -->
<div class="bottom-container step">
  <div class="form-header">
    <h2>Choose Your Hotels</h2>
    <p>Select accommodations for each day of your trip</p>
  </div>

  <div class="destinations-container">
    <h3 class="dest-title">🏨 Available Hotels</h3>

    <div class="destination-cards">
      <!-- Hotel cards-->
      @foreach ( $hotels as $hotel )
      <div class="hotel-card" data-id="1">
        <img src="{{ asset('storage/' . optional($hotel->images->where('is_primary', true)->first())->image_url) }}">

        <div class="hotel-info">
          <h4>{{$hotel->name}}</h4>

          <div class="rating-price">
            <span class="rating">
              ⭐ {{$hotel->global_rating}}
            </span>
            <span class="price">${{$hotel->price_per_night}} / night</span>
          </div>

          <div class="services">
            <span>🛜 Free Wi-Fi</span>
            <span>🍳 Breakfast included</span>
            <span>🏊 Pool access</span>
          </div>
        </div>
      </div>
      @endforeach
      <div class="hotel-card" data-id="2">
        <img src="./images/hotel2.jpg" alt="Skyline Resort">

        <div class="hotel-info">
          <h4>Skyline Resort</h4>

          <div class="rating-price">
            <span class="rating">⭐ 4.2</span>
            <span class="price">$145 / night</span>
          </div>

          <div class="services">
            <span>🛜 Free Wi-Fi</span>
            <span>🚗 Parking</span>
            <span>🏋 Gym</span>
          </div>
        </div>
      </div>

      <div class="hotel-card" data-id="3">
        <img src="./images/atul-vinayak-NQOA7YlcyF8-unsplash.jpg" alt="Coastal View Inn">

        <div class="hotel-info" style="padding: 12px 14px;">
          <h4 style="  font-size: 19px;
                        font-weight: 600;
                         color: var(--indigo);
                         margin-bottom: 6px;">
                         Coastal View Inn</h4>

          <div class="rating-price">
            <span class="rating" >⭐ 4.8</span>
            <span class="price">$220 / night</span>
          </div>

          <div class="services">
            <span>🛜 Wi-Fi</span>
            <span>🍽 Restaurant</span>
            <span>🌅 Sea view</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="days-container">
    <h3 class="dest-title" style="margin-top:30px;">Assign Hotels to Days</h3>
    <p>Select which hotel you’ll stay at for each day:</p>

    <div class="days-selection">
      <div class="day">
        <label>Day 1</label>
        <select name="day_1_hotel" class="hotel-select">
          <option value="">-- Select Hotel --</option>
          <option value="1">Grand Plaza Hotel</option>
          <option value="2">Skyline Resort</option>
          <option value="3">Coastal View Inn</option>
        </select>
      </div>

      <div class="day">
        <label>Day 2</label>
        <select name="day_2_hotel" class="hotel-select">
          <option value="">-- Select Hotel --</option>
          <option value="1">Grand Plaza Hotel</option>
          <option value="2">Skyline Resort</option>
          <option value="3">Coastal View Inn</option>
        </select>
      </div>

      <div class="day">
        <label>Day 3</label>
        <select name="day_3_hotel" class="hotel-select">
          <option value="">-- Select Hotel --</option>
          <option value="1">Grand Plaza Hotel</option>
          <option value="2">Skyline Resort</option>
          <option value="3">Coastal View Inn</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Assign hotels to days   -->
  <div class="assign-section">
    <h3 style="margin-top: 35px; margin-bottom: 15px;font-size: 25px;">Assign Custom Hotels To Days</h3>
    <p style="margin-bottom: 25px;">You can custom your hotels and assign them to your trip's days</p>

    <div class="day-input">
      <label>Day 1:</label>
      <input type="text" placeholder="Enter hotel name for Day 1">
    </div>

    <div class="day-input">
      <label>Day 2:</label>
      <input type="text" placeholder="Enter hotel name for Day 2">
    </div>

    <div class="day-input">
      <label>Day 3:</label>
      <input type="text" placeholder="Enter hotel name for Day 3">
    </div>
  </div>
</div>


<!-- Step 4 -->
<div class="bottom-container step">
  <div class="form-header">
    <h2>Select Activities</h2>
    <p>Add fun activities to make your trip memorable</p>
  </div>

  <!-- Available activities cards -->
  <div class="destinations-container">
    <h3 class="dest-title"> Available Activities</h3>

    <div class="destination-cards">
      <div class="activity-card" data-id="1">
        <img src="images/city-tour.jpg" alt="City Tour">
        <div class="content">
          <h4>City Walking Tour</h4>
          <p>Explore the city's landmarks with a guided tour.</p>
        </div>
      </div>

      <div class="activity-card" data-id="2">
        <img src="images/boat-cruise.jpg" alt="Boat Cruise">
        <div class="content">
          <h4>Boat Cruise</h4>
          <p>Enjoy a relaxing cruise along the river or coast.</p>
        </div>
      </div>

      <div class="activity-card" data-id="3">
        <img src="./images/aldrin-rachman-pradana-xdtXX5_iAdA-unsplash.jpg" alt="Cooking Class">
        <div class="content">
          <h4>Cooking Class</h4>
          <p>Learn to prepare traditional dishes with local chefs.</p>
        </div>
      </div>

      <div class="activity-card" data-id="4">
        <img src="./images/atul-vinayak-NQOA7YlcyF8-unsplash.jpg" alt="Hiking Adventure">
        <div class="content">
          <h4>Hiking Adventure</h4>
          <p>Discover nature trails and breathtaking views.</p>
        </div>
      </div>
    </div>

  </div>

  <!-- Assign Activities to days -->
  <div class="days-container">
    <h3 class="dest-title" style="margin-top:30px;">Assign Activities to Days</h3>
    <p>Select which activity you’ll do each day:</p>

    <div class="days-selection">
      <div class="day">
        <label>Day 1</label>
        <select name="day_1_activity" class="activity-select">
          <option value="">-- Select Activity --</option>
          <option value="1">City Walking Tour</option>
          <option value="2">Boat Cruise</option>
          <option value="3">Cooking Class</option>
          <option value="4">Hiking Adventure</option>
        </select>
      </div>

      <div class="day">
        <label>Day 2</label>
        <select name="day_2_activity" class="activity-select">
          <option value="">-- Select Activity --</option>
          <option value="1">City Walking Tour</option>
          <option value="2">Boat Cruise</option>
          <option value="3">Cooking Class</option>
          <option value="4">Hiking Adventure</option>
        </select>
      </div>

      <div class="day">
        <label>Day 3</label>
        <select name="day_3_activity" class="activity-select">
          <option value="">-- Select Activity --</option>
          <option value="1">City Walking Tour</option>
          <option value="2">Boat Cruise</option>
          <option value="3">Cooking Class</option>
          <option value="4">Hiking Adventure</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Custom activities -->
  <div class="assign-section">
    <h3 style="margin-top: 35px; margin-bottom: 15px;font-size: 25px;">Assign Custom Activities To Days</h3>
    <p style="margin-bottom: 25px;">You can add your own activities and assign them to your trip days</p>

    <div class="day-input">
      <label>Day 1:</label>
      <input type="text" placeholder="Enter custom activity for Day 1">
    </div>

    <div class="day-input">
      <label>Day 2:</label>
      <input type="text" placeholder="Enter custom activity for Day 2">
    </div>

    <div class="day-input">
      <label>Day 3:</label>
      <input type="text" placeholder="Enter custom activity for Day 3">
    </div>
  </div>
</div>

 <!-- Step 5 / flights  -->
 <div class="bottom-container step">
  <div class="form-header">
    <h2>Flight Information</h2>
    <p>Add your flight details or click on this link to search for available flights <a href=" ">Available Flights</a></p>
  </div>
  <div class="form-container">
    <div class="first-section">
      <div class="container">
        <label for="airline">Airline</label>
        <input type="text" id="airline" placeholder="e.g., American Airlines" name="airline">
      </div>
      <div class="container">
        <label for="flight-number">Flight Number</label>
        <input type="text" id="flight-number" placeholder="e.g., AA123" name="flight-number">
      </div>
    </div>

    <div class="second-section">
      <div class="container">
        <label for="from">From</label>
        <input type="text" id="from" placeholder="e.g., New York (JFK)" name="from">
      </div>
      <div class="container">
        <label for="to">To</label>
        <input type="text" id="to" placeholder="e.g., Paris (CDG)" name="to">
      </div>
    </div>

    <div class="second-section">
      <div class="container">
        <label for="flight-date">Flight Date</label>
        <input type="date" id="flight-date" name="flight-date">
      </div>
      <div class="container">
        <label for="departure-date">Departure Date</label>
        <input type="time" id="departure-date" name="departure-date">
      </div>

      <div class="container">
        <label for="price">Price</label>
        <input type="number" id="price" name="price">
      </div>
    </div>
  </div>

</div>


     <!-- Navigation Buttons  -->
      <div class="next-pre-buttons">
        <button type="button" name="previous" id="pre-btn">Previous Step</button>
        <button type="submit" name="next" id="next-btn">Next Step</button>
      </div>
    </form>

  </div>



<script>
//here

document.addEventListener("DOMContentLoaded", function () {
//store current step number (hidden input)
  const stepInput = document.getElementById("step-input");
  //step number from server
  let currentStep = {{ $currentStep }};
  //sync
  stepInput.value = currentStep;

  const form = document.getElementById('trip-form');

  const steps = document.querySelectorAll(".step");
  const circles = document.querySelectorAll(".circle");
  const nextBtn = document.getElementById("next-btn");
  const prevBtn = document.getElementById("pre-btn");

  //calculate current index
  let currentStepIndex = currentStep - 1;


  function updateSteps() {
    steps.forEach((step, index) => step.style.display = index === currentStepIndex ? "block" : "none");

    //coloring circles
    circles.forEach((circle, index) => {
      if (index === currentStepIndex) {
        circle.style.backgroundColor = "#628ECB";
        circle.style.color = "white";
        circle.style.border = "2px solid #628ECB";
      } else {
        circle.style.backgroundColor = "white";
        circle.style.color = "#052659";
        circle.style.border = "2px solid #628ECB";
      }
    });

    prevBtn.style.display = currentStepIndex === 0 ? "none" : "inline-block";
    nextBtn.textContent = currentStepIndex === steps.length - 1 ? "Finish" : "Next Step";
  }

  //click on Next event
  nextBtn.addEventListener("click", function (e) {
  e.preventDefault(); // prevent automatic submit
  stepInput.value = currentStepIndex + 1;  // send step number to server
  form.submit(); //send data to server

});

//click on previous event
  prevBtn.addEventListener("click", function () {
    if (currentStepIndex > 0) {
    currentStepIndex--;
    stepInput.value = currentStepIndex + 1;  // reverse current step
    updateSteps();               // update view
  }
  });

    // store selected id's before sending form
    form.addEventListener("submit", function () {
    hiddenInput.value = selectedDestinations.join(",");
  });
  updateSteps();
});

//handling selected ID's
  const selectedDestinations = [];
  const hiddenInput = document.getElementById("selected_destination");

  // get selected id from + button  
  document.querySelectorAll(".add-btn").forEach(btn => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const destId = this.dataset.id;

      // add selected destination if it's not selected before
      if (!selectedDestinations.includes(destId)) {
        selectedDestinations.push(destId);
        this.textContent = "✔";
        this.style.backgroundColor = "#2e8b57"; 
        this.style.color = "white";
      } else {
        // delete it if it was selected before
        const index = selectedDestinations.indexOf(destId);
        selectedDestinations.splice(index, 1);
        this.textContent = "+";
        this.style.backgroundColor = "var(--light-blue)";
        this.style.color = "#052659";
      }

      // update hidden input with new values
      hiddenInput.value = selectedDestinations.join(",");

      console.log("Selected Destinations:", selectedDestinations);
    });
  });



/*
    // Handle selecting destination
const selectedDestinations = [];

document.querySelectorAll('.add-btn').forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.preventDefault();

    const card = this.closest('.dist-card');
    const destId = card.dataset.id;

    if (!selectedDestinations.includes(destId)) {
      selectedDestinations.push(destId);
      this.textContent = "✔";
      this.style.backgroundColor = "#2e8b57";
    } else {
      const index = selectedDestinations.indexOf(destId);
      selectedDestinations.splice(index, 1);
      this.textContent = "+";
      this.style.backgroundColor = "var(--light-blue)";
    }

    console.log("Selected Destinations:", selectedDestinations);
  });
});
*/

  </script>

</x-app-layout>

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
        <h1>AI Trip Creator</h1>
        <!--<p>Choose how you'd like to create your perfect itinerary</p>-->
        <p>Describe your ideal trip and let AI do the planning. </p>
      </div>
    </header>


    <!-- Form containing all steps -->
    <form  method="POST" action="{{route('manual.step')}}">
      @csrf

      <!-- Step 1 Basic trip info -->
      <div class="main-container">

        <div class="form-header">
          <h2>✨ Tell us about your dream trip</h2>
        </div>
        @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <div class="form-container" style="display:flex;flex-direction:column;justify-content:center;align-items:center;">
          <div class="first-section">
            <div class="container">
              <x-input-label for="description"/>Trip Description
              <textarea name="description" id="description"
    placeholder="e.g., A romantic 5-day trip to Paris..."
    style="width:100%; height:150px;"
    class="w-full rounded-md border-gray-300"></textarea>
              @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="second-section">
            <div class="container">
              <x-input-label for="travelers_number"/>Number of Travelers
              <x-text-input type="number" name="travelers_number" id="travelers_number" min="1" placeholder="Enter number of Travelers" value=""/>
              @error('travelers_number') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="container">
              <x-input-label for="budget"/>Estimated Budget $
              <x-text-input type="number" name="budget" id="budget" placeholder="$ optional" value=""/>
              @error('budget') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="container">
                <x-input-label for="duration"/>Duration (Days)
                <x-text-input type="number" name="duration" id="duration" value=""/>
                @error('budget') <div class="error">{{ $message }}</div> @enderror

                <div class="generate-wrapper">
                    <button type="submit" class="generate-btn" id="generateBtn" style="color:white;">
                        <span class="btn-text" style="color:white;">Generate My Trip </span>
                        <span class="spinner"></span>
                    </button>
                </div>
              </div>



          </div>

        </div>


      </div>





    </form>


  </div>



<style>
    .first-section, .second-section {
        width:948px;
        height:218px;
    }

    form {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-direction: column;
    background-color: white;
    border-radius: 15px;
    min-height: 710px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);

    }

    .generate-wrapper {
    margin-top:40px;
    margin-left:30px;


}

.generate-btn {
    position: relative;
    padding: 14px 42px;
    font-size: 17px;
    font-weight: 600;
    border-radius: 30px;
    border: none;
    cursor: pointer;
    color: white;

    background: linear-gradient(135deg, #7f53ac, #647dee);
    box-shadow: 0 10px 25px rgba(127, 83, 172, 0.35);

    transition: all 0.3s ease;
    overflow: hidden;
}

.generate-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(100, 125, 238, 0.45);
}

.generate-btn:disabled {
    cursor: not-allowed;
    opacity: 0.85;
}

/* Spinner */
.spinner {
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.4);
    border-top: 3px solid white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;

    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: none;
}

.generate-btn.loading .spinner {
    display: block;
}

.generate-btn.loading .btn-text {
    visibility: hidden;
}

@keyframes spin {
    to {
        transform: rotate(360deg) translate(-50%, -50%);
    }
}

</style>

<script>
    const form = document.querySelector('form');
    const btn = document.getElementById('generateBtn');

    form.addEventListener('submit', () => {
        btn.classList.add('loading');
        btn.disabled = true;
    });
</script>


</x-app-layout>

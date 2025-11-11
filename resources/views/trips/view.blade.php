<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/createTrip.css')}}">
    @endpush
    <div class="main-container">
        <header>
            <a href="">
            <button class="back">Back</button>
            </a>
            <div class="head">
               
                <h1>Create New Trip Plan</h1>
                <p>Choose how you'd like to create your perfect itinerary</p>
                @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
            </div>
        </header>
        <div class="main-section">
            <a href="{{route('manual.create')}}" class="card-link">
                <div class="manual cardd">
                    <img src="{{asset('/images/ChatGPT Image Oct 1, 2025, 01_34_32 PM.png')}}" alt="icon" class="icon">
                    <div class="plan-heading">
                        <h2>Manual Creation</h2>
                        <p>Build your trip step by step with full control over every detail</p>
                    </div>
                    <ul class="overview">
                        <li>Choose your destination and dates</li>
                        <li>Select hotels and accommodations</li>
                        <li>Add activities and flight details</li>
                        <li>Arrange transportation</li>
                    </ul>
                    <div class="time">
                        <h4>Time required:</h4>
                        <span>2-3 minutes</span>
                    </div>
                </div>
            </a>
        
            <a href="ai.html" class="card-link">
                <div class="ai cardd">
                    <img src="{{asset('/images/ChatGPT Image Oct 1, 2025, 01_37_30 PM.png')}}" alt="icon" class="icon">
                    <div class="plan-heading">
                        <h2>AI-Powered Creation</h2>
                        <p>Let AI create a complete itinerary based on your preferences</p>
                    </div>
                    <ul class="overview">
                        <li>Describe your ideal trip in a few words</li>
                        <li>AI suggests hotels, activities & transport</li>
                        <li>Review and customize the suggestions</li>
                        <li>Perfect itinerary in minutes</li>
                    </ul>
                    <div class="time">
                        <h4>Time required:</h4>
                        <span>15-30 minutes</span>
                    </div>
                </div>
            </a>
        </div>
</x-app-layout>

<style>
    body{
        background-image:url('');
    }
   
    </style>
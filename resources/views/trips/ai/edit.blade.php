<x-app-layout>
    @push('styles')
    <link rel='stylesheet' href="{{asset('css/manual.css')}}">
    <style>
        .generate-btn { position: relative; padding: 14px 42px; font-size: 17px; font-weight: 600; border-radius: 30px; border: none; cursor: pointer; color: white; background: linear-gradient(135deg, #7f53ac, #647dee); box-shadow: 0 10px 25px rgba(127, 83, 172, 0.35); transition: all 0.3s ease; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; }
        .generate-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(100, 125, 238, 0.45); }
        .generate-btn:disabled { cursor: not-allowed; opacity: 0.8; }
        .spinner { width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-top: 3px solid white; border-radius: 50%; animation: spin 0.8s linear infinite; display: none; margin-left: 10px; }
        .generate-btn.loading .spinner { display: block; }
        .generate-btn.loading .btn-text { opacity: 0.7; }
        .category-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(130px,1fr)); gap: 10px; margin-top: 8px; }
        .category-box { border: 1px solid #d1d5db; border-radius: 10px; padding: 8px 10px; display: flex; gap: 8px; align-items: center; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .form-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
    @endpush

    <div class="main-container">
        <header class="mb-8">
            <a href="{{route('trips.index')}}"><button class="back">Back</button></a>
            <div class="head text-center">
                <h1 class="text-3xl font-bold">Edit AI Trip</h1>
                <p class="text-gray-600">Update the same AI trip inputs used at creation time.</p>
            </div>
        </header>

        <div class="max-w-4xl mx-auto">
            @include('trips.ai.partials.form', [
                'action' => route('ai.update', $trip),
                'method' => 'PUT',
                'submitLabel' => 'Update Trip',
                'formData' => $formData,
            ])
        </div>
    </div>

    <script>
        document.getElementById('aiTripForm').addEventListener('submit', function() {
            const btn = document.getElementById('generateBtn');
            btn.classList.add('loading');
            btn.disabled = true;
        });
    </script>
</x-app-layout>

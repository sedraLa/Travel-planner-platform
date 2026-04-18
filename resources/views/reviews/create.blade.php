<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">

        <style>
            .review-wrapper {
                min-height: 80vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .review-card {
                width: 100%;
                max-width: 500px;
                background: white;
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                animation: fadeIn 0.5s ease-in-out;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .stars {
                display: flex;
                flex-direction: row-reverse;
                justify-content: center;
                gap: 5px;
                margin-top: 10px;
            }

            .stars input {
                display: none;
            }

            .stars label {
                font-size: 28px;
                color: #ddd;
                cursor: pointer;
                transition: 0.2s;
            }

            .stars input:checked ~ label,
            .stars label:hover,
            .stars label:hover ~ label {
                color: #fbbf24;
            }

            .btn-review {
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #22c55e, #16a34a);
                color: white;
                border: none;
                border-radius: 10px;
                font-weight: bold;
                cursor: pointer;
                transition: 0.2s;
            }

            .btn-review:hover {
                transform: scale(1.02);
            }

            textarea {
                resize: none;
            }

            .title {
                text-align: center;
                font-size: 22px;
                font-weight: bold;
                margin-bottom: 20px;
            }

            .success {
                background: #ecfdf5;
                color: #065f46;
                padding: 10px;
                border-radius: 10px;
                margin-bottom: 15px;
                text-align: center;
            }
        </style>
    @endpush

    <div class="review-wrapper">

        <div class="review-card">

            <div class="title">Rate Your Experience ⭐</div>

            {{-- success message --}}
            @if(session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
                </div>
            @endif

            @if(session('review_error'))
                <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
                 {{ session('review_error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('reviews.store') }}">
                @csrf

                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="reservation_id" value="{{ $reservationId }}">

                {{-- STARS --}}
                <div class="mb-4 text-center">
                    <label class="block mb-2 font-semibold">Rating</label>

                    <div class="stars">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}">
                            <label for="star{{ $i }}">★</label>
                        @endfor
                    </div>
                </div>

                {{-- REVIEW --}}
                <div class="mb-4">
                    <label class="block mb-2 font-semibold">Review (optional)</label>
                    <textarea
                        name="review"
                        rows="4"
                        class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-green-400"
                        placeholder="Write something (optional)..."
                    ></textarea>
                </div>

                <button class="btn-review">
                    Submit Review
                </button>
            </form>

        </div>
    </div>
</x-app-layout>

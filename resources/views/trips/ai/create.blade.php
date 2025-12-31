<x-app-layout>
    @push('styles')
    <link rel='stylesheet' href="{{asset('css/manual.css')}}">
    <style>
        /* تنسيقات إضافية لضمان عمل الـ Spinner */
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(100, 125, 238, 0.45);
        }

        .generate-btn:disabled {
            cursor: not-allowed;
            opacity: 0.8;
        }

        /* تصميم الـ Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none; /* مخفي افتراضياً */
            margin-left: 10px;
        }

        /* إظهار الـ Spinner عند التحميل */
        .generate-btn.loading .spinner {
            display: block;
        }

        .generate-btn.loading .btn-text {
            opacity: 0.7;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
    </style>
    @endpush

    <div class="main-container">
        <header class="mb-8">
            <a href="{{route('trip.view')}}">
                <button class="back">Back</button>
            </a>
            <div class="head text-center">
                <h1 class="text-3xl font-bold">AI Trip Creator</h1>
                <p class="text-gray-600">Describe your ideal trip and let AI do the planning.</p>
            </div>
        </header>

        <div class="max-w-4xl mx-auto">
            <form method="POST" action="{{route('ai.generate')}}" id="aiTripForm">
                @csrf
                <div class="form-container">
                    <h2 class="text-xl font-semibold mb-6">✨ Tell us about your dream trip</h2>

                    @if ($errors->any())
                        <div class="mb-4 px-4 py-3 bg-red-100 text-red-800 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="description">Trip Description</x-input-label>
                            <textarea name="description" id="description" 
                                placeholder="e.g., A romantic 5-day trip to Paris..."
                                class="w-full rounded-md border-gray-300 h-32">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="travelers_number">Number of Travelers</x-input-label>
                                <x-text-input type="number" name="travelers_number" id="travelers_number" min="1" value="{{ old('travelers_number', 1) }}" class="w-full"/>
                            </div>
                            <div>
                                <x-input-label for="duration">Duration (Days)</x-input-label>
                                <x-text-input type="number" name="duration" id="duration" min="1" value="{{ old('duration', 3) }}" class="w-full"/>
                            </div>
                            <div>
                                <x-input-label for="start_date">Start Date (Optional)</x-input-label>
                                <x-text-input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="w-full"/>
                            </div>
                            <div>
                                <x-input-label for="end_date">End Date (Optional)</x-input-label>
                                <x-text-input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="w-full"/>
                            </div>
                            <div>
                                <x-input-label for="budget">Estimated Budget ($)</x-input-label>
                                <x-text-input type="number" name="budget" id="budget" placeholder="Optional" value="{{ old('budget') }}" class="w-full"/>
                            </div>
                            <div>
                                <x-input-label for="language">Language</x-input-label>
                                <select name="language" id="language" class="w-full rounded-md border-gray-300">
                                    <option value="en">English</option>
                                    <option value="ar">Arabic</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center pt-6">
                            <button type="submit" class="generate-btn" id="generateBtn">
                                <span class="btn-text" style="color:white;">Generate My Trip</span>
                                <div class="spinner" id="btnSpinner"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('aiTripForm').addEventListener('submit', function() {
            const btn = document.getElementById('generateBtn');
            const spinner = document.getElementById('btnSpinner');
            
            // إضافة الـ Class لتفعيل التنسيقات
            btn.classList.add('loading');
            // تعطيل الزر لمنع الإرسال المتكرر
            btn.disabled = true;
            // إظهار الـ Spinner يدوياً للتأكيد
            spinner.style.display = 'block';
        });
    </script>
</x-app-layout>
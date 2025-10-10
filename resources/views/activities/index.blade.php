@php use App\Enums\UserRole; @endphp
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/destinations.css') }}">
    @endpush

    <div class="main-wrapper">
        <div class="hero-background activity-page">
            <div class="heading">
                <div class="header" style="margin-top:55px;">
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <h2 style="font-size:80px;font-weight:500;">Activities</h2>
                    </div>
                </div>

                <p style="color:white;font-size:20px;width:50%">
                    Choose from our curated selection of premium activities each designed to elevate your travel
                    experience
                </p>

                <div class="flex justify-end mb-4 px-6 pt-6">
                    @if (Auth::user()->role === UserRole::ADMIN->value)
                        <a href="{{ route('activities.create') }}" class="add-btn">+ Add New Activity</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="main">
            <!--Activities cards-->
            <div class="exp-cards">
                @forelse($activities as $activity)
                    <div class="exp-card">
                        <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->name }}">
                        <h3>{{ $activity->name }}</h3>

                        <a href="{{ route('activities.show', $activity->id) }}">
                            <button type="button"
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium mt-2">
                                More Details
                            </button>
                        </a>
                    </div>
                @empty
                    <p>No activities found.</p>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .exp-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 83px auto;
            width: 88%;
        }

        .exp-card {
            background-color: #ffffff;
            border-radius: 25px;
            width: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform 0.3s ease;
            margin-bottom: 15px;
        }

        .exp-card img {
            width: 100%;
            border-radius: 15px;
            margin-bottom: 15px;
            object-fit: cover;
            height: 180px;
        }

        .exp-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--indigo);
        }

        .exp-card p {
            font-size: 14px;
            margin: 5px 15px;
            color: #333;
        }

        .exp-card button {
            background-color: var(--indigo);
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            margin-top: 10px;
            margin-bottom: 40px;
        }

        .exp-card button:hover {
            background-color: #fff;
            color: var(--indigo);
            border: 2px solid var(--indigo);
        }

        .exp-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</x-app-layout>
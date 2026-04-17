<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">

    <style>
        .reviews-wrap{
            max-width:900px;
            margin:40px auto;
            padding:0 16px;
        }

        .header-box{
            background: linear-gradient(135deg, #185FA5, #3FA7F5);
            color:#fff;
            padding:20px;
            border-radius:14px;
            margin-bottom:20px;
        }

        .header-box h1{
            font-size:22px;
            margin-bottom:6px;
        }

        .header-box p{
            font-size:13px;
            opacity:.9;
        }

        .rating-summary{
            margin-top:10px;
            font-size:14px;
        }

        .review-card{
            background:#fff;
            border-radius:14px;
            padding:18px;
            margin-bottom:14px;
            box-shadow:0 4px 12px rgba(0,0,0,0.08);
            transition:.2s;
        }

        .review-card:hover{
            transform:translateY(-2px);
        }

        .review-top{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:8px;
        }

        .user-name{
            font-weight:600;
            color:#111827;
        }

        .date{
            font-size:12px;
            color:#9ca3af;
        }

        .rating{
            color:#f59e0b;
            font-size:14px;
            margin-bottom:6px;
        }

        .review-text{
            color:#374151;
            font-size:14px;
            line-height:1.6;
        }

        .empty{
            text-align:center;
            color:#9ca3af;
            padding:40px;
        }

        .stars-big{
            font-size:18px;
            margin-top:5px;
        }
    </style>
    @endpush


    <div class="reviews-wrap">

        {{-- HEADER --}}
        <div class="header-box">
            <h1>Reviews for {{ $guide->user?->name }}</h1>

            <p>{{ $guide->years_of_experience ?? 0 }} years experience • Guide profile</p>

            <div class="rating-summary">
                @php $avg = $avg ?? 0; @endphp

                <div class="stars-big">
                    @for($i=1; $i<=5; $i++)
                        <span style="color:{{ $i <= $avg ? '#fff' : 'rgba(255,255,255,0.4)' }}">★</span>
                    @endfor
                </div>

                <div style="margin-top:4px;">
                    Average rating: {{ number_format($avg,1) }}
                </div>
            </div>
        </div>


        {{-- REVIEWS LIST --}}
        @forelse($reviews as $review)
            <div class="review-card">

                <div class="review-top">
                    <div class="user-name">
                        {{ $review->user?->full_name ?? 'Anonymous' }}
                    </div>

                    <div class="date">
                        {{ $review->created_at->format('d M Y') }}
                    </div>
                </div>

                <div class="rating">
                    @for($i=0; $i < 5; $i++)
                        <span style="color:{{ $i < $review->rating ? '#f59e0b' : '#e5e7eb' }}">★</span>
                    @endfor
                </div>

                <div class="review-text">
                    {{ $review->review }}
                </div>

            </div>
        @empty
            <div class="empty">
                No reviews yet for this guide.
            </div>
        @endforelse

    </div>

</x-app-layout>
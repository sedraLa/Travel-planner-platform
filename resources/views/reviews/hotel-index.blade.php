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
    </style>
    @endpush

    <div class="reviews-wrap">

        <h1 style="font-size:22px;font-weight:700;margin-bottom:20px;">
            Reviews for {{ $hotel->name }}
        </h1>

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
                    @for($i=0; $i < $review->rating; $i++)
                        ⭐
                    @endfor
                </div>

                <div class="review-text">
                    {{ $review->review }}
                </div>

            </div>
        @empty
            <div class="empty">
                No reviews yet. People are either polite… or traumatized 😶
            </div>
        @endforelse

    </div>

</x-app-layout>
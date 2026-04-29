<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
<link rel="stylesheet" href="{{ asset('css/tripreviews.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

{{-- ═══ HERO ═══ --}}
<div class="trv-hero">
    <div class="trv-hero-left">
        <div class="trv-hero-icon">
            <svg fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </div>
        <p class="trv-hero-sub">Reviews & Ratings</p>
        <h1>{{ $trip->name }}</h1>
    </div>
    <div class="trv-hero-stats">
        <div class="trv-stat-pill">
            <div class="num">{{ $reviews->count() }}</div>
            <div class="lbl">Reviews</div>
        </div>
        <div class="trv-stat-pill gold">
            <div class="num">★ {{ number_format($reviews->avg('rating'), 1) }}</div>
            <div class="lbl">Avg Rating</div>
        </div>
        <div class="trv-stat-pill">
            <div class="num">{{ $reviews->where('rating', 5)->count() }}</div>
            <div class="lbl">5 Stars</div>
        </div>
    </div>
</div>

{{-- ═══ CARDS ═══ --}}
<div class="trv-grid">
    @forelse($reviews as $review)
    @php
        $rating = $review->rating ?? 0;
        $starColor = match(true) {
            $rating >= 5 => '#10b981',
            $rating >= 4 => '#84cc16',
            $rating >= 3 => '#f59e0b',
            $rating >= 2 => '#f97316',
            default      => '#ef4444',
        };
        $barColors = ['#6366f1','#10b981','#f59e0b','#3b82f6','#f43f5e','#8b5cf6','#14b8a6'];
        $barColor  = $barColors[$loop->index % count($barColors)];
    @endphp

    <div class="trv-card">

        <div class="trv-card-bar" style="background: {{ $barColor }};"></div>

        <div class="trv-card-body">

            {{-- Head --}}
            <div class="trv-head">
                <div class="trv-user">
                    <div class="trv-avatar" style="background: {{ $barColor }};">
                        {{ strtoupper(substr($review->user?->full_name ?? $review->user?->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="trv-name-wrap">
                        <span class="trv-uname">{{ $review->user?->full_name ?? $review->user?->name ?? 'Anonymous' }}</span>
                        <span class="trv-date">{{ $review->created_at->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="trv-rating-circle" style="border-color: {{ $starColor }}; color: {{ $starColor }};">
                    {{ $rating }}<span>★</span>
                </div>
            </div>

            <div class="trv-divider"></div>

            {{-- Stars + bar --}}
            <div class="trv-stars-row">
                <div class="trv-stars" style="color: {{ $starColor }};">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating)
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @else
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:.2;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
                <div class="trv-rating-bar">
                    <div class="trv-rating-fill" style="width: {{ ($rating / 5) * 100 }}%; background: {{ $starColor }};"></div>
                </div>
            </div>

            <div class="trv-divider"></div>

            {{-- Review text --}}
            <p class="trv-review-text">"{{ $review->review }}"</p>

        </div>
    </div>
    @empty
    <div class="trv-empty">
        <svg fill="currentColor" viewBox="0 0 24 24" style="opacity:.15;">
            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
        </svg>
        <p>No reviews yet for this trip.</p>
    </div>
    @endforelse
</div>

</x-app-layout>
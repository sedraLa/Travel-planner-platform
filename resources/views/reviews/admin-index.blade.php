<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/reviews.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
@endpush

{{-- ═══ HERO ═══ --}}
<div class="rv-hero">
    <div class="rv-hero-left">
        <div class="rv-hero-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </div>
        <h1>Reviews</h1>
        <p>All user reviews across the system.</p>
    </div>
    <div class="rv-hero-stats">
        <div class="rv-stat-pill">
            <div class="num">{{ $reviews->total() }}</div>
            <div class="lbl">Total</div>
        </div>
        <div class="rv-stat-pill gold">
            <div class="num">{{ number_format($reviews->avg('rating'), 1) }}</div>
            <div class="lbl">Avg Rating</div>
        </div>
        <div class="rv-stat-pill">
            <div class="num">{{ $reviews->where('rating', 5)->count() }}</div>
            <div class="lbl">5 Stars</div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="rv-alert rv-alert-success">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="rv-alert rv-alert-error">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- ═══ FILTER ═══ --}}
<div class="rv-filter-wrap">
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="rv-filter">
        <div class="rv-field grow">
            <label>Search User</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="User name...">
        </div>
        <div class="rv-field">
            <label>Type</label>
            <select name="type">
                <option value="">All Types</option>
                <option value="hotel"  @selected(request('type')=='hotel')>Hotel</option>
                <option value="trip"   @selected(request('type')=='trip')>Trip</option>
                <option value="guide"  @selected(request('type')=='guide')>Guide</option>
                <option value="driver" @selected(request('type')=='driver')>Driver</option>
            </select>
        </div>
        <div class="rv-field">
            <label>Rating</label>
            <select name="rating">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected(request('rating') == $i)>
                        {{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }} {{ $i }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="rv-filter-actions">
            <button type="submit" class="rv-btn-filter">🔍 Filter</button>
            <a href="{{ route('admin.reviews.index') }}" class="rv-btn-reset">Reset</a>
        </div>
    </form>
</div>

{{-- ═══ CARDS ═══ --}}
<div class="rv-grid">
    @forelse($reviews as $review)
    @php
        $rating = $review->rating ?? 0;
        $type   = strtolower(class_basename($review->reviewable_type));
        $typeColors = [
            'hotel'  => ['bar'=>'#6366f1','bg'=>'#f5f3ff','text'=>'#3730a3','border'=>'#c4b5fd'],
            'trip'   => ['bar'=>'#10b981','bg'=>'#e0faf2','text'=>'#065f46','border'=>'#6ee7b7'],
            'guide'  => ['bar'=>'#3b82f6','bg'=>'#eff6ff','text'=>'#1e3a8a','border'=>'#bfdbfe'],
            'driver' => ['bar'=>'#f59e0b','bg'=>'#fef9ec','text'=>'#78350f','border'=>'#fde68a'],
        ];
        $tc = $typeColors[$type] ?? ['bar'=>'#8b5cf6','bg'=>'#f5f3ff','text'=>'#3730a3','border'=>'#ddd6fe'];
        $starColors = [1=>'#ef4444', 2=>'#f97316', 3=>'#f59e0b', 4=>'#84cc16', 5=>'#10b981'];
        $starColor  = $starColors[$rating] ?? '#f59e0b';
    @endphp

    <div class="rv-card">

        <div class="rv-card-bar" style="background: {{ $tc['bar'] }};"></div>

        <div class="rv-card-body">

            {{-- Head: user + stars --}}
            <div class="rv-head">
                <div class="rv-user">
                    <div class="rv-avatar" style="background: {{ $tc['bar'] }};">
                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="rv-name-wrap">
                        <span class="rv-uname">{{ $review->user->name ?? '—' }}</span>
                        <span class="rv-date">
                            {{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}
                        </span>
                    </div>
                </div>

                {{-- Star rating --}}
                <div class="rv-stars" style="color: {{ $starColor }};">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rating)
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @else
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:.3;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endif
                    @endfor
                    <span class="rv-rating-num" style="color: {{ $starColor }};">{{ $rating }}.0</span>
                </div>
            </div>

            <div class="rv-divider"></div>

            {{-- Item + Type --}}
            <div class="rv-item-row">
                <div class="rv-item-info">
                    <p class="rv-item-name">
                        {{ $review->reviewable->name ?? $review->reviewable->title ?? '—' }}
                    </p>
                    <span class="rv-type-badge"
                          style="background: {{ $tc['bg'] }}; color: {{ $tc['text'] }}; border-color: {{ $tc['border'] }};">
                        {{ ucfirst($type) }}
                    </span>
                </div>
            </div>

            <div class="rv-divider"></div>

            {{-- Review text --}}
            <p class="rv-review-text">
                "{{ $review->review }}"
            </p>

            <div class="rv-divider"></div>

            {{-- Footer: delete --}}
            <div class="rv-footer">
                <div class="rv-rating-bar">
                    <div class="rv-rating-fill"
                         style="width: {{ ($rating / 5) * 100 }}%; background: {{ $starColor }};"></div>
                </div>
                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST"
                      onsubmit="return confirm('Delete this review?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rv-delete-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>

        </div>
    </div>
    @empty
    <div class="rv-empty">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
        </svg>
        <p>No reviews found.</p>
    </div>
    @endforelse
</div>

@if($reviews->hasPages())
<div class="rv-pagination">
    {{ $reviews->withQueryString()->links() }}
</div>
@endif

</x-app-layout>
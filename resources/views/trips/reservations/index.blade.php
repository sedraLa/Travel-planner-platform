<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
<link rel="stylesheet" href="{{ asset('css/tripreservations.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

{{-- ═══ HERO ═══ --}}
<div class="tr2-hero">
    <div class="tr2-hero-left">
        <div class="tr2-hero-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 10m0 7V10M9 7l6 3"/>
            </svg>
        </div>
        <h1>Trip Reservations</h1>
        @if(Auth::user()->role === 'admin')
            <p>All trip reservations in your system.</p>
        @else
            <p>A list of all your trip reservations.</p>
        @endif
    </div>
    <div class="tr2-hero-stats">
        <div class="tr2-stat-pill">
            <div class="num">{{ $reservations->total() }}</div>
            <div class="lbl">Total</div>
        </div>
        <div class="tr2-stat-pill">
            <div class="num">${{ number_format($reservations->sum('total_price'), 0) }}</div>
            <div class="lbl">Revenue</div>
        </div>
        <div class="tr2-stat-pill">
            <div class="num">{{ $reservations->sum('people_count') }}</div>
            <div class="lbl">People</div>
        </div>
    </div>
</div>

{{-- ═══ FILTER ═══ --}}
<div class="tr2-filter-wrap">
    <form method="GET" action="{{ route('trip.reservations.index') }}" class="tr2-filter">
        <div class="tr2-field grow">
            <label>Search</label>
            <input type="text" name="keyword" value="{{ request('keyword') }}"
                   placeholder="Trip name, package{{ Auth::user()->role === 'admin' ? ', user' : '' }}">
        </div>
        <div class="tr2-field">
            <label>Month</label>
            <select name="month">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected(request('month') == $m)>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="tr2-field">
            <label>Year</label>
            <select name="year">
                <option value="">All Years</option>
                @for($y = now()->year; $y >= 2020; $y--)
                    <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="tr2-filter-actions">
            <button type="submit" class="tr2-btn-filter">🔍 Filter</button>
            <a href="{{ route('trip.reservations.index') }}" class="tr2-btn-reset">Reset</a>
        </div>
    </form>
</div>

{{-- ═══ CARDS ═══ --}}
@php
    $colors = [
        ['bar'=>'#6366f1','light'=>'#f5f3ff'],
        ['bar'=>'#10b981','light'=>'#e0faf2'],
        ['bar'=>'#f59e0b','light'=>'#fef9ec'],
        ['bar'=>'#3b82f6','light'=>'#eff6ff'],
        ['bar'=>'#f43f5e','light'=>'#fff1f2'],
        ['bar'=>'#8b5cf6','light'=>'#f5f3ff'],
        ['bar'=>'#14b8a6','light'=>'#f0fdfa'],
    ];
@endphp

<div class="tr2-grid">
    @forelse($reservations as $index => $r)
    @php $c = $colors[$index % count($colors)]; @endphp
    <div class="tr2-card">

        <div class="tr2-card-bar" style="background: {{ $c['bar'] }};"></div>

        <div class="tr2-card-body">

            {{-- Head --}}
            <div class="tr2-head">
                @if(Auth::user()->role === 'admin')
                <div class="tr2-user">
                    <div class="tr2-avatar" style="background: {{ $c['bar'] }};">
                        {{ strtoupper(substr($r->user?->name ?? 'N', 0, 1)) }}
                    </div>
                    <span class="tr2-uname">{{ $r->user?->name ?? 'N/A' }}</span>
                </div>
                @else
                <div></div>
                @endif
                <span class="tr2-price">${{ number_format($r->total_price, 2) }}</span>
            </div>

            <div class="tr2-divider"></div>

            {{-- Trip + Package --}}
            <div class="tr2-trip-info">
                <p class="tr2-trip-name">{{ $r->trip?->name ?? 'N/A' }}</p>
                <p class="tr2-pkg-name">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    {{ $r->package?->name ?? 'N/A' }}
                </p>
            </div>

            <div class="tr2-divider"></div>

            {{-- Chips --}}
            <div class="tr2-chips">
                <span class="tr2-chip">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($r->schedule?->start_date)->format('d M Y') }}
                </span>
                <span class="tr2-chip">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                    {{ $r->people_count }} {{ $r->people_count > 1 ? 'people' : 'person' }}
                </span>
            </div>

            <div class="tr2-divider"></div>

            {{-- Status --}}
            <div class="tr2-status-row">
                <span class="tr2-badge tr2-badge-{{ $r->status }}">
                    @if($r->status === 'pending')
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @elseif($r->status === 'paid')
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    @else
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                        </svg>
                    @endif
                    {{ ucfirst($r->status) }}
                </span>
                <span class="tr2-accent-dot" style="background: {{ $c['bar'] }};"></span>
            </div>

        </div>
    </div>
    @empty
    <div class="tr2-empty">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 10m0 7V10M9 7l6 3"/>
        </svg>
        <p>No trip reservations found.</p>
    </div>
    @endforelse
</div>

@if($reservations->hasPages())
<div class="tr2-pagination">
    {{ $reservations->withQueryString()->links() }}
</div>
@endif

</x-app-layout>
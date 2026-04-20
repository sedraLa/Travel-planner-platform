<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
<link rel="stylesheet" href="{{ asset('css/completed.css') }}">

@endpush

{{-- ═══ HERO ═══ --}}
<div class="cr-hero">
    <div class="cr-hero-left">
        <div class="cr-hero-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        @if(Auth::user()->role === \App\Enums\UserRole::DRIVER->value)
            <h1>My Completed Reservations</h1>
            <p>A list of all your completed trips.</p>
        @else
            <h1>Reservations — {{ $driver->user->name }}</h1>
            <p>A list of all completed reservations for this driver.</p>
        @endif
    </div>
    <div class="cr-hero-stats">
        <div class="cr-stat-pill">
            <div class="num">{{ $reservations->total() }}</div>
            <div class="lbl">Total</div>
        </div>
        <div class="cr-stat-pill">
            <div class="num">${{ number_format($reservations->sum('total_price'), 0) }}</div>
            <div class="lbl">Revenue</div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="cr-alert">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- ═══ CARDS ═══ --}}
@php
    $colors = ['#6366f1','#10b981','#f59e0b','#3b82f6','#f43f5e','#8b5cf6','#14b8a6'];
@endphp

<div class="cr-grid">
    @forelse($reservations as $index => $reservation)
    @php $color = $colors[$index % count($colors)]; @endphp
    <div class="cr-card">

        <div class="cr-card-bar" style="background: {{ $color }};"></div>

        <div class="cr-card-body">

            {{-- Head --}}
            <div class="cr-head">
                <div class="cr-user">
                    <div class="cr-avatar" style="background: {{ $color }};">
                        {{ strtoupper(substr($reservation->user?->name ?? 'N', 0, 1)) }}
                    </div>
                    <span class="cr-uname">{{ $reservation->user?->name ?? 'N/A' }}</span>
                </div>
                <span class="cr-price">${{ number_format($reservation->total_price, 2) }}</span>
            </div>

            <div class="cr-divider"></div>

            {{-- Route --}}
            <div class="cr-route">
                <div class="cr-loc">
                    <div class="cr-loc-lbl">📍 Pickup</div>
                    <div class="cr-loc-val" title="{{ $reservation->pickup_location }}">
                        {{ $reservation->pickup_location }}
                    </div>
                </div>
                <div class="cr-arrow">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
                <div class="cr-loc">
                    <div class="cr-loc-lbl">🏁 Dropoff</div>
                    <div class="cr-loc-val" title="{{ $reservation->dropoff_location }}">
                        {{ $reservation->dropoff_location }}
                    </div>
                </div>
            </div>

            <div class="cr-divider"></div>

            {{-- Chips --}}
            <div class="cr-chips">
                <span class="cr-chip">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($reservation->pickup_datetime)->format('d M Y, H:i') }}
                </span>
                <span class="cr-chip">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                    {{ $reservation->passengers }} passenger{{ $reservation->passengers > 1 ? 's' : '' }}
                </span>
            </div>

            <div class="cr-divider"></div>

            {{-- Status --}}
            <div class="cr-status-row">
                <span class="cr-badge-completed">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Completed
                </span>
                <span class="cr-date-sub">
                    {{ \Carbon\Carbon::parse($reservation->dropoff_datetime)->format('d M Y') }}
                </span>
            </div>

        </div>
    </div>
    @empty
    <div class="cr-empty">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 10m0 7V10M9 7l6 3"/>
        </svg>
        <p>No completed reservations found.</p>
    </div>
    @endforelse
</div>

@if($reservations->hasPages())
<div class="cr-pagination">
    {{ $reservations->withQueryString()->links() }}
</div>
@endif

</x-app-layout>
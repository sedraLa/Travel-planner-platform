<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
<link rel="stylesheet" href="{{ asset('css/transportreservation.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

{{-- ═══ HERO ═══ --}}
<div class="tr-hero">
    <div class="tr-hero-inner">
        <div class="tr-hero-top">
            <div>
                <div class="tr-hero-icon">
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
            <div class="tr-hero-stats">
                <div class="tr-stat-pill">
                    <div class="num">{{ $reservations->total() }}</div>
                    <div class="lbl">Total</div>
                </div>
                <div class="tr-stat-pill">
                    <div class="num">${{ number_format($reservations->sum('earnings_balance'), 0) }}</div>
                    <div class="lbl">Revenue</div>
                </div>
                
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="tr-alert-success">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- ═══ CARDS ═══ --}}
<div class="tr-list tr-list-grid">
    @forelse($reservations as $reservation)
    <div class="tr-card tr-card-compact">

        {{-- User + Price --}}
        <div class="tc-head">
            <div class="tc-user">
                <div class="tc-avatar">
                    {{ strtoupper(substr($reservation->user?->name ?? 'N', 0, 1)) }}
                </div>
                <span class="tc-uname">{{ $reservation->user?->name ?? 'N/A' }}</span>
            </div>
            <span class="tc-price">${{ number_format($reservation->total_price, 2) }}</span>
        </div>

        <hr class="tc-divider">

        {{-- Route --}}
        <div class="tc-route">
            <div class="tc-loc">
                <div class="tc-loc-lbl">📍 Pickup</div>
                <div class="tc-loc-val" title="{{ $reservation->pickup_location }}">
                    {{ $reservation->pickup_location }}
                </div>
            </div>
            <div class="tc-arrow">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </div>
            <div class="tc-loc">
                <div class="tc-loc-lbl">🏁 Dropoff</div>
                <div class="tc-loc-val" title="{{ $reservation->dropoff_location }}">
                    {{ $reservation->dropoff_location }}
                </div>
            </div>
        </div>

        <hr class="tc-divider">

        {{-- Date + Passengers --}}
        <div class="tc-chips">
            <span class="tc-chip">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ \Carbon\Carbon::parse($reservation->pickup_datetime)->format('d M Y, H:i') }}
            </span>
            <span class="tc-chip">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
                {{ $reservation->passengers }} passenger{{ $reservation->passengers > 1 ? 's' : '' }}
            </span>
        </div>

        <hr class="tc-divider">

        {{-- Status Badge --}}
        <div class="tc-status-row">
            <span class="tc-status-badge completed">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Completed
            </span>
            <span class="tc-date-sub">
                {{ \Carbon\Carbon::parse($reservation->dropoff_datetime)->format('d M Y') }}
            </span>
        </div>

    </div>
    @empty
    <div class="tr-empty">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 10m0 7V10M9 7l6 3"/>
        </svg>
        <p>No completed reservations found.</p>
    </div>
    @endforelse
</div>

@if($reservations->hasPages())
<div class="tr-pagination">
    {{ $reservations->withQueryString()->links() }}
</div>
@endif

</x-app-layout>
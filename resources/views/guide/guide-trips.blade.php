<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/guidetrips.css') }}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
@endpush

{{-- ═══ HERO ═══ --}}
<div class="gt-hero">
    <div class="gt-hero-left">
        <div class="gt-hero-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <h1>Trips for {{ $guide->user->name }}</h1>
        <p>All assigned trips for this guide.</p>
    </div>
    <div class="gt-hero-stats">
        <div class="gt-stat-pill">
            <div class="num">{{ $assignments->count() }}</div>
            <div class="lbl">Total Trips</div>
        </div>
        <div class="gt-stat-pill">
            <div class="num">{{ $assignments->where('status', 'active')->count() }}</div>
            <div class="lbl">Active</div>
        </div>
        <div class="gt-stat-pill">
            <div class="num">{{ $assignments->sum(fn($a) => $a->trip->max_participants ?? 0) }}</div>
            <div class="lbl">Participants</div>
        </div>
    </div>
</div>

{{-- ═══ CARDS ═══ --}}
@php
    $colors = [
        ['bar'=>'#6366f1','avatar'=>'#6366f1'],
        ['bar'=>'#10b981','avatar'=>'#10b981'],
        ['bar'=>'#f59e0b','avatar'=>'#f59e0b'],
        ['bar'=>'#3b82f6','avatar'=>'#3b82f6'],
        ['bar'=>'#f43f5e','avatar'=>'#f43f5e'],
        ['bar'=>'#8b5cf6','avatar'=>'#8b5cf6'],
        ['bar'=>'#14b8a6','avatar'=>'#14b8a6'],
    ];
@endphp

<div class="gt-grid">
    @forelse($assignments as $index => $assignment)
    @php
        $trip = $assignment->trip;
        $c    = $colors[$index % count($colors)];
    @endphp
    <div class="gt-card">

        <div class="gt-card-bar" style="background: {{ $c['bar'] }};"></div>

        <div class="gt-card-body">

            {{-- Head: guide + duration --}}
            <div class="gt-head">
                <div class="gt-user">
                    <div class="gt-avatar" style="background: {{ $c['avatar'] }};">
                        {{ strtoupper(substr($assignment->guide->user->name ?? 'G', 0, 1)) }}
                    </div>
                    <div class="gt-name-wrap">
                        <span class="gt-uname">{{ $assignment->guide->user->name ?? 'N/A' }}</span>
                        <span class="gt-role">Guide</span>
                    </div>
                </div>
                <span class="gt-duration">
                    {{ $trip->duration_days ?? 'N/A' }}d
                </span>
            </div>

            <div class="gt-divider"></div>

            {{-- Trip name --}}
            <div class="gt-trip-block">
                <p class="gt-trip-name">{{ $trip->name ?? 'N/A' }}</p>
                <p class="gt-destination">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $trip->primaryDestination->city ?? '' }}{{ isset($trip->primaryDestination->city) && isset($trip->primaryDestination->country) ? ', ' : '' }}{{ $trip->primaryDestination->country ?? 'N/A' }}
                </p>
            </div>

            <div class="gt-divider"></div>

            {{-- Chips --}}
            <div class="gt-chips">
                <span class="gt-chip">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ optional($trip->schedules->first())->start_date ?? 'N/A' }}
                </span>
                <span class="gt-chip">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                    {{ $trip->max_participants ?? 'N/A' }} participants
                </span>
            </div>

            <div class="gt-divider"></div>

            {{-- Status --}}
            <div class="gt-status-row">
                <span class="gt-badge gt-badge-{{ strtolower($assignment->status) }}">
                    @if(strtolower($assignment->status) === 'active')
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    @elseif(strtolower($assignment->status) === 'pending')
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    @endif
                    {{ ucfirst($assignment->status) }}
                </span>
                <span class="gt-accent-dot" style="background: {{ $c['bar'] }};"></span>
            </div>

        </div>
    </div>
    @empty
    <div class="gt-empty">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 10m0 7V10M9 7l6 3"/>
        </svg>
        <p>No assigned trips found.</p>
    </div>
    @endforelse
</div>

</x-app-layout>
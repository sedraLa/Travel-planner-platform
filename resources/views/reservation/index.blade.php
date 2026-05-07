<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/hotel-reservations.css') }}">
         <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700&family=DM+Mono:wght@400;500&family=Instrument+Sans:wght@400;500&display=swap" rel="stylesheet">
    @endpush

    <div class="hr-wrap">

        {{-- ─── HEADER ─── --}}
        <div class="hr-hdr">
            <div class="hr-hdr-left">
                <div class="hr-eyebrow">Reservations · Hotels</div>
                <h1>Hotel Bookings</h1>
                @if(Auth::user()->role === 'admin')
                    <p>A list of all the hotel reservations in your system.</p>
                @else
                    <p>A list of all your hotel reservations.</p>
                @endif
            </div>

            @if(Auth::user()->role === 'admin')
            <div class="hr-stats">
                <div class="hr-stat">
                    <div class="hr-stat-num">{{ $reservations->total() }}</div>
                    <div class="hr-stat-lbl">Total</div>
                </div>
                <div class="hr-stat">
                    <div class="hr-stat-num is-green">{{ $reservations->where('reservation_status','confirmed')->count() }}</div>
                    <div class="hr-stat-lbl">Confirmed</div>
                </div>
                <div class="hr-stat">
                    <div class="hr-stat-num is-amber">{{ $reservations->where('reservation_status','pending')->count() }}</div>
                    <div class="hr-stat-lbl">Pending</div>
                </div>
                <div class="hr-stat">
                    <div class="hr-stat-num is-red">{{ $reservations->where('reservation_status','cancelled')->count() }}</div>
                    <div class="hr-stat-lbl">Cancelled</div>
                </div>
            </div>
            @endif
        </div>

        {{-- ─── FILTER BAR ─── --}}
        <form method="GET" action="{{ route('reservations.index') }}" class="hr-filter">

            <div class="hr-fg hr-fg-grow">
                <label>Search</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                    placeholder="Hotel, destination{{ Auth::user()->role === 'admin' ? ', user' : '' }}...">
            </div>

            <div class="hr-fg">
                <label>Month</label>
                <select name="month">
                    <option value="">All months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected(request('month') == $m)>
                            {{ Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="hr-fg">
                <label>Year</label>
                <select name="year">
                    <option value="">All years</option>
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="hr-fg">
                <label>Status</label>
                <select name="reservation_status">
                    <option value="">All statuses</option>
                    <option value="pending"   @selected(request('reservation_status') == 'pending')>Pending</option>
                    <option value="confirmed" @selected(request('reservation_status') == 'confirmed')>Confirmed</option>
                </select>
            </div>

            <div class="hr-filter-actions">
                <button type="submit" class="hr-btn-primary">Apply filter</button>
                <a href="{{ route('reservations.index') }}" class="hr-btn-ghost">Reset</a>
            </div>
        </form>

        {{-- ─── ALERTS ─── --}}
        @if(session('success'))
            <div class="hr-alert hr-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="hr-alert hr-alert-error">{{ session('error') }}</div>
        @endif

        {{-- ─── EMPTY STATE ─── --}}
        @if($reservations->isEmpty())
            <div class="hr-empty">
                <div class="hr-empty-ring">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <p>No hotel reservations found</p>
                <span>Try adjusting your search or filters.</span>
            </div>

        {{-- ─── TABLE ─── --}}
        @else
            <div class="hr-table-card">
                <div class="hr-table-scroll">
                    <table class="hr-table">
                        <thead>
                            <tr>
                                @if(Auth::user()->role === 'admin')
                                    <th>User</th>
                                @endif
                                <th>Hotel</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th class="center">Rooms</th>
                                <th class="center">Guests</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                                @php
                                    $name     = $reservation->user?->full_name ?? 'N/A';
                                    $words    = array_filter(explode(' ', $name));
                                    $initials = collect($words)->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                                    $status   = $reservation->reservation_status ?? 'default';
                                @endphp
                                <tr>
                                    @if(Auth::user()->role === 'admin')
                                        <td>
                                            <div class="hr-user">
                                                <div class="hr-av">{{ $initials }}</div>
                                                <span class="hr-uname">{{ $name }}</span>
                                            </div>
                                        </td>
                                    @endif

                                    <td>
                                        <span class="hr-hotel-name">{{ $reservation->hotel->name ?? 'N/A' }}</span>
                                    </td>

                                    <td><span class="hr-date">{{ $reservation->check_in_date }}</span></td>
                                    <td><span class="hr-date">{{ $reservation->check_out_date }}</span></td>

                                    <td class="center">
                                        <span class="hr-pill">{{ $reservation->rooms_count }}</span>
                                    </td>
                                    <td class="center">
                                        <span class="hr-pill">{{ $reservation->guest_count }}</span>
                                    </td>

                                    <td>
                                        <span class="hr-price">${{ number_format($reservation->total_price, 2) }}</span>
                                    </td>

                                    <td>
                                        <span class="hr-badge hr-badge-{{ $status }}">{{ ucfirst($status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ─── PAGINATION ─── --}}
            @if($reservations->hasPages())
                <div class="hr-pagination">
                    {{ $reservations->withQueryString()->links() }}
                </div>
            @endif
        @endif

    </div>
</x-app-layout>

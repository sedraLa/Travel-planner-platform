<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/activity-reservations.css') }}">
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700&family=DM+Mono:wght@400;500&family=Instrument+Sans:wght@400;500&display=swap" rel="stylesheet">
    @endpush

    <div class="ar-wrap">

        {{-- ─── HEADER ─── --}}
        <div class="ar-hdr">
            <div class="ar-hdr-left">
                <div class="ar-eyebrow">Reservations · Activities</div>
                <h1>Activity Bookings</h1>
                @if(Auth::user()->role === 'admin')
                    <p>All users' activity reservations</p>
                @else
                    <p>Your activity reservations</p>
                @endif
            </div>

            @if(Auth::user()->role === 'admin')
            <div class="ar-stats">
                <div class="ar-stat">
                    <div class="ar-stat-num">{{ $reservations->total() }}</div>
                    <div class="ar-stat-lbl">Total</div>
                </div>
                <div class="ar-stat">
                    <div class="ar-stat-num is-green">{{ $reservations->where('status','paid')->count() }}</div>
                    <div class="ar-stat-lbl">Paid</div>
                </div>
                <div class="ar-stat">
                    <div class="ar-stat-num is-amber">{{ $reservations->where('status','pending')->count() }}</div>
                    <div class="ar-stat-lbl">Pending</div>
                </div>
                <div class="ar-stat">
                    <div class="ar-stat-num is-red">{{ $reservations->where('status','cancelled')->count() }}</div>
                    <div class="ar-stat-lbl">Cancelled</div>
                </div>
            </div>
            @endif
        </div>

        {{-- ─── FILTER BAR ─── --}}
        <form method="GET" action="{{ route('activity.reservations.index') }}" class="ar-filter">
            <div class="ar-fg ar-fg-grow">
                <label>Search</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                    placeholder="Activity, destination{{ Auth::user()->role === 'admin' ? ', user' : '' }}...">
            </div>

            <div class="ar-fg">
                <label>Month</label>
                <select name="month">
                    <option value="">All months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected((int) request('month') === $m)>
                            {{ Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="ar-fg">
                <label>Year</label>
                <select name="year">
                    <option value="">All years</option>
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" @selected((int) request('year') === $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div class="ar-fg">
                <label>Status</label>
                <select name="status">
                    <option value="">All statuses</option>
                    <option value="pending"   @selected(request('status') === 'pending')>Pending</option>
                    <option value="paid"      @selected(request('status') === 'paid')>Paid</option>
                    <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                </select>
            </div>

            <div class="ar-filter-actions">
                <button type="submit" class="ar-btn-primary">Apply filter</button>
                <a href="{{ route('activity.reservations.index') }}" class="ar-btn-ghost">Reset</a>
            </div>
        </form>

        {{-- ─── SUCCESS ALERT ─── --}}
        @if(session('success'))
            <div class="ar-alert">{{ session('success') }}</div>
        @endif

        {{-- ─── EMPTY STATE ─── --}}
        @if($reservations->isEmpty())
            <div class="ar-empty">
                <div class="ar-empty-ring">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                        <path d="M9 12h6M9 16h4"/>
                    </svg>
                </div>
                <p>No reservations found</p>
                <span>Try adjusting your search or filters.</span>
            </div>

        {{-- ─── TABLE ─── --}}
        @else
            <div class="ar-table-card">
                <div class="ar-table-scroll">
                    <table class="ar-table">
                        <thead>
                            <tr>
                                @if(Auth::user()->role === 'admin')
                                    <th>User</th>
                                @endif
                                <th>Activity &amp; Destination</th>
                                <th>Date</th>
                                <th class="center">PAX</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                                @php
                                    $name     = $reservation->user?->full_name ?? $reservation->user?->name ?? 'N/A';
                                    $words    = array_filter(explode(' ', $name));
                                    $initials = collect($words)->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                                    $status   = $reservation->status ?? 'default';
                                @endphp
                                <tr>
                                    @if(Auth::user()->role === 'admin')
                                        <td>
                                            <div class="ar-user">
                                                <div class="ar-av">{{ $initials }}</div>
                                                <span class="ar-uname">{{ $name }}</span>
                                            </div>
                                        </td>
                                    @endif

                                    <td>
                                        <div class="ar-act-name">{{ $reservation->activity?->name ?? 'N/A' }}</div>
                                        <div class="ar-act-dest">
                                            <span class="ar-dot"></span>
                                            {{ $reservation->activity?->destination?->name ?? '—' }}
                                        </div>
                                    </td>

                                    <td>
                                        <span class="ar-date">
                                            {{ optional($reservation->activity_date)->format('Y-m-d') ?? $reservation->activity_date ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="center">
                                        <span class="ar-pax">{{ $reservation->participants_count }}</span>
                                    </td>

                                    <td>
                                        <span class="ar-price">${{ number_format($reservation->total_price, 2) }}</span>
                                    </td>

                                    <td>
                                        <span class="ar-badge ar-badge-{{ $status }}">{{ ucfirst($status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($reservations->hasPages())
                <div class="ar-pagination">
                    {{ $reservations->withQueryString()->links() }}
                </div>
            @endif
        @endif

    </div>
</x-app-layout>

<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/trips-index.css') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link href="https://fonts.googleapis.com/css2?family=Syne:wght@500;700&family=DM+Mono:wght@400;500&family=Instrument+Sans:wght@400;500&display=swap" rel="stylesheet">
    @endpush

    <div class="ti-wrap">

        {{-- ─── HEADER ─── --}}
        <div class="ti-hdr">
            <div class="ti-hdr-left">
                <div class="ti-eyebrow">Admin · Trips</div>
                <h1>All Trips</h1>
                <p>Manage and monitor all system trips</p>
            </div>
            <a href="{{ route('trip.view') }}" class="ti-btn-create">
                <span>+</span> Create Trip
            </a>
        </div>

        {{-- ─── ALERTS ─── --}}
        @if(session('success'))
            <div class="ti-alert ti-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="ti-alert ti-alert-error">{{ session('error') }}</div>
        @endif

        {{-- ─── FILTER ─── --}}
        <form method="GET" action="{{ route('trips.index') }}" class="ti-filter">

            <div class="ti-fg ti-fg-grow">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, description...">
            </div>

            

            <div class="ti-fg">
                <label>Status</label>
                <select name="status">
                    <option value="">All</option>
                    <option value="draft"     @selected(request('status') == 'draft')>Draft</option>
                    <option value="ready_for_assignment"     @selected(request('status') == 'ready_for_assignment')>Ready For Assignment</option>
                    <option value="ready_for_staffing" @selected(request('status') == 'ready_for_staffing')>Ready For Staffing</option>
                    <option value="staffing_in_progress" @selected(request('status') == 'staffing_in_progress')>Staffing In Progress</option>
                    <option value="published" @selected(request('status') == 'published')>Published</option>
                </select>
            </div>

            <div class="ti-fg">
                <label>Destination</label>
                <input type="text" name="destination" value="{{ request('destination') }}"
                    placeholder="Destination...">
            </div>

            <div class="ti-fa">
                <button type="submit" class="ti-bp">Apply filter</button>
                <a href="{{ route('trips.index') }}" class="ti-bg">Reset</a>
            </div>
        </form>

        {{-- ─── EMPTY ─── --}}
        @if($trips->isEmpty())
            <div class="ti-empty">
                <div class="ti-empty-ring">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <p>No trips found</p>
                <span>Try adjusting your search or filters.</span>
            </div>

        {{-- ─── GRID ─── --}}
        @else
            <div class="ti-grid">
                @foreach($trips as $trip)
                    <div class="ti-card">
                        <div class="ti-card-top">

                            {{-- Name + AI badge --}}
                            <div class="ti-name-row">
                                <span class="ti-name">{{ $trip->name }}</span>
                                @if($trip->is_ai_generated)
                                    <span class="ti-ai-badge">AI trip</span>
                                @endif
                            </div>

                            {{-- Description --}}
                            <p class="ti-desc">
                                {{ \Illuminate\Support\Str::limit($trip->ai_prompt, 100) }}
                            </p>

                            {{-- Meta --}}
                            <div class="ti-meta">
                                <div class="ti-meta-row">
                                    <span class="ti-dot"></span>
                                    Destination:
                                    <span class="ti-meta-val">{{ $trip->destination?->name ?? '—' }}</span>
                                </div>
                                <div class="ti-meta-row">
                                    <span class="ti-dot"></span>
                                    Duration:
                                    <span class="ti-meta-val">{{ $trip->duration_days }} day(s)</span>
                                </div>
                                <div class="ti-meta-row">
                                    <span class="ti-dot"></span>
                                    Travelers:
                                    <span class="ti-meta-val">{{ $trip->max_participants ?? '—' }}</span>
                                </div>
                                <div class="ti-meta-row">
                                    <span class="ti-dot"></span>
                                    Status:
                                    <span class="ti-status ti-status-{{ $trip->status }}">
                                        {{ ucfirst($trip->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ─── Actions ─── --}}
                        <div class="ti-card-bottom">

                            {{-- View Details --}}
                            @if($trip->is_ai_generated)
                                <a href="{{ route('user.trips.show', $trip->id) }}" class="ti-btn-view">View Details</a>
                            @else
                                <a href="{{ route('manual.show', $trip->id) }}" class="ti-btn-view">View Details</a>
                            @endif

                            {{-- Complete Creating (AI + draft only) --}}
                            @if($trip->is_ai_generated && $trip->status === 'draft')
                                <a href="{{ route('trip.complete.edit', $trip->id) }}" class="ti-btn-complete">Complete</a>
                            @endif

                            {{-- Edit (AI only) --}}
                            @if($trip->is_ai_generated)
                                <a href="{{ route('ai.edit', $trip->id) }}" class="ti-btn-edit">Edit</a>
                            @endif

                            {{-- Delete --}}
                            <form action="{{ route('trip.destroy', $trip->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this trip?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ti-btn-del">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ─── PAGINATION ─── --}}
            @if($trips->hasPages())
                <div class="ti-pagination">
                    {{ $trips->withQueryString()->links() }}
                </div>
            @endif
        @endif

    </div>
</x-app-layout>

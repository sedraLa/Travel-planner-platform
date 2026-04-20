<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/vehicles.css') }}">
<link rel="stylesheet" href="{{ asset('css/transportreservation.css') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

@endpush

{{-- ═══ HERO ═══ --}}
<div class="tr-hero">
    <div class="tr-hero-inner">
        <div class="tr-hero-top">
            <div>
                <div class="tr-hero-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>

                 
                <h1>Pending Booking</h1>
               
                    <p>A list of all the transport reservations  not done yet.</p>
               
                   
               
            </div>
            <div class="tr-hero-stats">
                <div class="tr-stat-pill">
                    <div class="num">{{ $reservations->total() }}</div>
                    <div class="lbl">Total</div>
                </div>
                
                
            </div>
        </div>
    </div>
</div>


 @if (session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif


{{-- ═══ CARDS ═══ --}}
<div class="tr-list">
    @forelse($reservations as $reservation)
    <div class="tr-card">
    <td class="hidden">{{ $reservation->id }}</td>
        {{-- ── Left: Info ── --}}
        <div class="tr-info-col">

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
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($reservation->dropoff_datetime)->format('d M Y, H:i') }}
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

           
<div class="tc-assign">

    <div class="tc-assign-row">
          @if(auth()->check() && auth()->user()->role === \App\Enums\UserRole::DRIVER->value)

           @php
            $canComplete = \Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($reservation->pickup_datetime));
          @endphp

        <form action="{{ route('reservations.complete', $reservation->id) }}" method="POST" class="inline">
                                                 @csrf
                                              <button type="submit"
                                                 class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed"
                                                {{ $canComplete ? '' : 'disabled' }}>
                                                    Complete
                                                </button>
                                            </form>
    </div>

    
    <div class="tc-assign-row">
        <form action="{{ route('reservation.cancel', $reservation->id) }}" method="POST" class="inline">
                                                      @csrf
                                                <button type="submit"
                                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600
                                                     {{ $canComplete ? '' : 'disabled opacity-50 cursor-not-allowed' }}">
                                                         Cancel
                                                </button>
                                           </form>
    </div>
    @endif

    

</div>
        </div>

        {{-- ── Right: Map ── --}}
        <div class="tr-map-col">
            <div class="tr-map"
                 id="map-{{ $reservation->id }}"
                 data-pickup="{{ $reservation->pickup_location }}"
                 data-dropoff="{{ $reservation->dropoff_location }}">
            </div>
            <div class="tr-map-pill">
                <span><span class="tr-dot tr-dot-blue"></span>Pickup</span>
                <span><span class="tr-dot tr-dot-red"></span>Dropoff</span>
            </div>
        </div>

    </div>
    @empty
    <div class="tr-empty">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 10m0 7V10M9 7l6 3"/>
        </svg>
        <p>No transport reservations found.</p>
    </div>
    @endforelse
</div>

{{-- ═══ PAGINATION ═══ --}}
@if($reservations->hasPages())
<div class="tr-pagination">
   
    {{ $reservations->withQueryString()->links() }}
</div>
@endif

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    async function geocode(address) {
        try {
            const res  = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(address));
            const data = await res.json();
            if (data.length > 0) return { lat: parseFloat(data[0].lat), lng: parseFloat(data[0].lon) };
        } catch(e) {}
        return null;
    }

    const mkIcon = color => L.divIcon({
        className: '',
        html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:2.5px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3)"></div>`,
        iconSize: [14,14], iconAnchor: [7,7], popupAnchor: [0,-10],
    });

    const iBlue = mkIcon('#6366f1');
    const iRed  = mkIcon('#ef4444');

    document.querySelectorAll('.tr-map').forEach(async function(el) {
        const pickup  = el.dataset.pickup;
        const dropoff = el.dataset.dropoff;

        const map = L.map(el, {
            zoomControl: true,
            attributionControl: false,
            scrollWheelZoom: false,
        }).setView([20, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

        el.addEventListener('click',      () => map.scrollWheelZoom.enable());
        el.addEventListener('mouseleave', () => map.scrollWheelZoom.disable());

        const [pCoord, dCoord] = await Promise.all([geocode(pickup), geocode(dropoff)]);

        if (pCoord && dCoord) {
            L.marker([pCoord.lat, pCoord.lng], { icon: iBlue }).addTo(map).bindPopup('<b>Pickup:</b> ' + pickup);
            L.marker([dCoord.lat, dCoord.lng], { icon: iRed  }).addTo(map).bindPopup('<b>Dropoff:</b> ' + dropoff);
            L.polyline([[pCoord.lat, pCoord.lng],[dCoord.lat, dCoord.lng]], {
                color: '#6366f1', weight: 3, opacity: .8, dashArray: '7,7',
            }).addTo(map);
            map.fitBounds([[pCoord.lat, pCoord.lng],[dCoord.lat, dCoord.lng]], { padding: [30,30] });
        } else {
            el.innerHTML = '<div style="height:100%;display:flex;align-items:center;justify-content:center;font-size:.8rem;color:#9ca3af;">Map unavailable</div>';
        }
    });
});
</script>
@endpush

</x-app-layout>

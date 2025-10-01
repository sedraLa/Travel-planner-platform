@php use App\Enums\UserRole; @endphp

<x-app-layout>
    @push('styles')
   <link rel="stylesheet" href="{{asset('css/destinations.css')}}">
    @endpush

    <div class="main-wrapper">
        @if (Auth::user()->role === UserRole::ADMIN->value)
            <!-- Create Hotel Button -->
            <div class="flex justify-end mb-4 px-6 pt-6">
                <a href="{{ route('hotels.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-200">
                    + Add New Hotel
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
        @endif

        <!-- Hero Background (for all users) -->
        <div class="hero-background hotels-page"></div>

                            <!-- Search Form -->
       <form class="search-form" method="GET" action="{{ route('hotels.index') }}">
    <h1>Find Your Hotel</h1>

    <div class="search-container" style="position:relative;">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
        </svg>

        <input
            type="search"
            id="hotelSearch"
            name="search"
            class="search-input"
            placeholder="Search hotels or locations..."
            autocomplete="off"
            required
        />

        <!-- صندوق الاقتراحات -->
        <div id="searchSuggestions" class="suggestions-list" role="listbox" aria-label="Search suggestions">
            <!-- أمثلة قابلة للتعديل -->
            <button type="button" class="suggestion-item" data-value="Paris, France">Paris, France</button>
            <button type="button" class="suggestion-item" data-value="Istanbul, Turkey">Istanbul, Turkey</button>
            <button type="button" class="suggestion-item" data-value="Dubai, UAE">Dubai, UAE</button>
            <button type="button" class="suggestion-item" data-value="Rome, Italy">Rome, Italy</button>
            <button type="button" class="suggestion-item" data-value="Cairo, Egypt">Cairo, Egypt</button>
            <button type="button" class="suggestion-item" data-value="Riyadh, Saudi Arabia">Riyadh, Saudi Arabia</button>
            <button type="button" class="suggestion-item" data-value="Doha, Qatar">Doha, Qatar</button>
            <button type="button" class="suggestion-item" data-value="Beirut, Lebanon">Beirut, Lebanon</button>
        </div>

        <button type="submit" class="search-button">Search</button>
    </div>

{{-- ==========خط الفلتر ========== --}}


<!-- 🔽 فلاتر إضافية -->
<div class="filters-bar">
    <!-- Global Rating -->
    <div class="filter-group">
        <label for="rating">Global Rating</label>
        <select name="rating" id="rating">
            <option value="">Any</option>
            <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
            <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
            <option value="3">⭐⭐⭐ (3 Stars)</option>
            <option value="2">⭐⭐ (2 Stars)</option>
            <option value="1">⭐ (1 Star)</option>
        </select>
    </div>

    <!-- Price Range -->
    <div class="filter-group">
        <label for="price">Price</label>
        <input type="number" name="price" id="price" placeholder="Enter price" min="0">
    </div>
    <!-- زر All Filters -->
<button type="button" id="allFiltersBtn" class="all-filters-btn">
  All Filters (<span id="filtersCount">0</span>)
</button>
<style>

    

.suggestions-list{
    display:none; 
    position:absolute; 
    left:0;
    right:0;
    top:100%;
    background:#fff;
     border:1px solid #e5e7eb;
      border-radius:8px;
    margin-top:6px; max-height:240px;
     overflow:auto; z-index:40;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
  }
  .suggestions-list.show{ display:block; }
  .suggestion-item{
    width:100%; 
    text-align:left; 
    padding:10px 12px; 
    background:transparent;
    border:0;
     cursor:pointer; 
     font-size:14px;
  }
  .suggestion-item:hover, .suggestion-item[aria-selected="true"]{ background:#f3f4f6; }


/*-- ========== CSS لخط الفلتر========== --*/

/* فلتر أسفل البحث */


.filters-bar {
  margin-top: 120px;
  padding: 24px;
  background: linear-gradient(135deg, #ffffff, #f9faff);
  border: 2px solid transparent;
  border-radius: 18px;
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  justify-content: center;

  /* إطار مميز بتدرج */
  background-image: linear-gradient(135deg, #ffffff, #f9faff),
    linear-gradient(135deg, #4a90e2, #7f5af0);
  background-origin: border-box;
  background-clip: content-box, border-box;

  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.filters-bar:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.filter-group {
  display: flex;
  flex-direction: column;
  font-size: 14px;
  color: #666;
  min-width: 160px;
}

.filter-group label {
  margin-bottom: 8px;
  font-weight: 600;
  color: #4a90e2;
  font-size: 15px;
}

.filter-group select,
.filter-group input[type="number"] {
  padding: 10px 12px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  font-size: 14px;
  width: 100%;
  background: #fff;
  transition: all 0.25s ease;
}

.filter-group select:focus,
.filter-group input:focus {
  border-color: #7f5af0;
  box-shadow: 0 0 0 3px rgba(127, 90, 240, 0.2);
  outline: none;
}

.price-range {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* زر كل الفلاتر */
.all-filters-btn {
  padding: 10px 18px;
  background: linear-gradient(135deg, #4a90e2, #7f5af0);
  color: #fff;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
}

.all-filters-btn:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 4px 12px rgba(127, 90, 240, 0.4);
}

/* الرقم */
.all-filters-btn span {
  font-weight: 700;
  color: #ffdf5d;
  font-size: 15px;
}



</style>


</div>
</form>



        <!-- Hotels Cards -->
        <div class="cards" style="margin: 50px auto;">
            @forelse ($hotels as $hotel)
            @php
                $primaryImage = $hotel->images->where('is_primary', true)->first();
            @endphp
            <div class="card">
                <a href="{{ route('hotel.show', $hotel->id) }}">
                    <div class="card-img">
                        <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : asset('images/default.jpg') }}" alt="Hotel Image">
                    </div>
                    <h5>{{ $hotel->name }}</h5>
                    <p class="overview">{{ Str::limit($hotel->address, 80) }}</p>
                </a>
            </div>
        @empty
            <p style="text-align:center;">No hotels found.</p>
        @endforelse

        <div class="pagination-wrapper">
                     {{ $hotels->appends(request()->query())->links() }}
                  </div>
        
    </div>

  {{-- ========== JS: فتح/إغلاق + فلترة + اختيار بالكيبورد ========== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('hotelSearch');
  const box = document.getElementById('searchSuggestions');
  const container = input.closest('.search-container');

  // افتح الاقتراحات عند التركيز
  input.addEventListener('focus', () => {
    box.classList.add('show');
  });

  // فلترة الاقتراحات أثناء الكتابة
  input.addEventListener('input', () => {
    const q = input.value.trim().toLowerCase();
    const items = box.querySelectorAll('.suggestion-item');
    let any = false;
    items.forEach(btn => {
      const match = btn.dataset.value.toLowerCase().includes(q);
      btn.style.display = match ? 'block' : 'none';
      if (match) any = true;
    });
    // إظهار الصندوق إذا في نتائج أو إذا الحقل فاضي (اقتراحات عامة)
    box.classList.toggle('show', any || q.length === 0);
  });

  // اختيار اقتراح بالماوس
  box.addEventListener('click', (e) => {
    const btn = e.target.closest('.suggestion-item');
    if (!btn) return;
    input.value = btn.dataset.value;
    box.classList.remove('show');
    input.focus();
  });

  // إغلاق عند الضغط خارج الصندوق
  document.addEventListener('click', (e) => {
    if (!container.contains(e.target)) {
      box.classList.remove('show');
    }
  });

  // تنقّل بالكيبورد (سهمين/Enter/Escape)
  input.addEventListener('keydown', (e) => {
    const visible = Array.from(box.querySelectorAll('.suggestion-item'))
      .filter(btn => btn.style.display !== 'none');
    if (!box.classList.contains('show') || visible.length === 0) return;

    const currentIdx = visible.findIndex(btn => btn.getAttribute('aria-selected') === 'true');

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      const nextIdx = currentIdx < visible.length - 1 ? currentIdx + 1 : 0;
      visible.forEach(btn => btn.setAttribute('aria-selected', 'false'));
      visible[nextIdx].setAttribute('aria-selected', 'true');
      visible[nextIdx].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      const prevIdx = currentIdx > 0 ? currentIdx - 1 : visible.length - 1;
      visible.forEach(btn => btn.setAttribute('aria-selected', 'false'));
      visible[prevIdx].setAttribute('aria-selected', 'true');
      visible[prevIdx].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'Enter') {
      const active = visible[currentIdx] || visible[0];
      if (active) {
        e.preventDefault();
        input.value = active.dataset.value;
        box.classList.remove('show');
      }
    } else if (e.key === 'Escape') {
      box.classList.remove('show');
    }
  });
});

</script>

<script>
  const rating = document.getElementById('rating');
  const price = document.getElementById('price');
  const allBtn = document.getElementById('allFiltersBtn');
  const countSpan = document.getElementById('filtersCount');

  function updateCount() {
    let count = 0;
    if (rating.value) count++;
    if (price.value) count++;
    countSpan.textContent = count;
  }

  rating.addEventListener('change', updateCount);
  price.addEventListener('input', updateCount);

  allBtn.addEventListener('click', () => {
    rating.value = '';
    price.value = '';
    updateCount();
  });
</script>


</x-app-layout>
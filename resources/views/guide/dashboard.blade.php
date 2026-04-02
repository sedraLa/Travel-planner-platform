@push('styles')
<link rel="stylesheet" href="{{asset('css/vehicles.css')}}">
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/driver_dash.css') }}">
<link rel="stylesheet" href="{{ asset('css/availability.css') }}">
@endpush

<x-app-layout>
<div class="main">

    <div class="driver_profile">
        <div class="personal_info">
            <img class="personal-photo"
                 src="{{ $guide && $guide->personal_image ? asset('storage/' . $guide->personal_image) : asset('images/ian-dooley-d1UPkiFd04A-unsplash.jpg') }}">
            
            <div class="personal-details">
                <span>Good day</span>
                <h2>Welcome back, {{ $guide?->user?->name ?? auth()->user()->name }} 👋</h2>

                <button onclick="openModal()" class="schedule-btn">
                    Create Availability
                </button>
            </div>
        </div>
    </div>

</div>
@if ($errors->any())
    <div style="color:red;">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (session('success'))
    <div style="color:green;">
        {{ session('success') }}
    </div>
@endif
<!-- ✅ MODAL -->
<div id="availabilityModal" class="modal hidden">
  <div class="modal-box">

    <button class="close-btn" onclick="closeModal()">×</button>

    <form method="POST" action="{{ route('guide.availabilities.store') }}">
      @csrf

      <div class="av-form">

        <div class="av-header">
          <h2 class="av-title">Set your <em>availability</em></h2>
           <p class="av-sub">Pick a date and time window when you're free.</p>
           <div class="av-error" id="av-error"></div>
          
        </div>

        <div class="av-section">
          <div class="av-label">Date</div>
          <input type="date" id="av-date" name="date" required>
        </div>

        <div class="av-time-grid">
          <div>
            <div class="av-label">Start time</div>
            <input type="time" id="av-start" name="start_time" value="09:00" required>
          </div>
          <div>
            <div class="av-label">End time</div>
            <input type="time" id="av-end" name="end_time" value="17:00" required>
          </div>
        </div>

        <div class="av-footer">
          <button type="button" class="av-btn-cancel" onclick="closeModal()">Cancel</button>
          <button type="submit" class="av-btn-save">Save slot</button>
        </div>

      </div>
    </form>

  </div>
</div>

</x-app-layout>
<script>
function openModal() {
    document.getElementById('availabilityModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('availabilityModal').classList.add('hidden');
}

// اغلاق لما تكبس برا
document.getElementById('availabilityModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// default date اليوم
document.getElementById('av-date').value = new Date().toISOString().split('T')[0];


const form = document.querySelector('.av-form form');
const errorDiv = document.getElementById('av-error');

form.addEventListener('submit', function(e) {
    e.preventDefault(); // منع الفورم التقليدي

    const data = {
        date: document.getElementById('av-date').value,
        start_time: document.getElementById('av-start').value,
        end_time: document.getElementById('av-end').value,
    };

    fetch(this.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(async res => {
        if (!res.ok) {
            const err = await res.json();
            errorDiv.style.display = 'flex';
            errorDiv.textContent = err.message;
        } else {
            const result = await res.json();
            // success
            errorDiv.style.display = 'none';
            closeModal();
            alert(result.message); // أو عرض رسالة داخل الصفحة
        }
    })
    .catch(() => {
        errorDiv.style.display = 'flex';
        errorDiv.textContent = 'Something went wrong.';
    });
});
</script>
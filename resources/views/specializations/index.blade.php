@push('styles')
<link rel="stylesheet" href="{{ asset('css/transport.css') }}">
<link rel="stylesheet" href="{{ asset('css/specialization.css') }}">

@endpush

<x-app-layout>
<div class="spec-page">

    <div class="spec-header">
        <h2>Specializations</h2>
        <button class="btn-add" onclick="toggleForm()">+ Add New</button>
    </div>

    <span class="spec-count">
        {{ $specializations->count() }} {{ Str::plural('specialization', $specializations->count()) }} registered
    </span>

    <div id="addForm" style="display:none;">
        <div class="add-form-wrap">
            <form method="POST" action="{{ route('specialization.store') }}">
                @csrf
                <input type="text" name="name" placeholder="e.g. Historical Tours, Adventure..." required>
                <button type="submit" class="btn-save">Save</button>
            </form>
        </div>
    </div>

    <div class="spec-list">
        @forelse($specializations as $spec)
        <div class="spec-item">
            <div class="spec-item-left">
                <div class="spec-dot">⭐</div>
                <span class="spec-name">{{ $spec->name }}</span>
            </div>
            <form method="POST" action="{{ route('specialization.destroy', $spec->id) }}"
                  onsubmit="return confirm('Delete \'{{ $spec->name }}\'?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete" title="Delete">✕</button>
            </form>
        </div>
        @empty
        <div class="spec-empty">
            <div class="empty-icon">🗂️</div>
            <p>No specializations yet. Add your first one!</p>
        </div>
        @endforelse
    </div>

</div>

<script>
    function toggleForm() {
        const f = document.getElementById('addForm');
        const isHidden = f.style.display === 'none' || f.style.display === '';
        f.style.display = isHidden ? 'block' : 'none';
        if (isHidden) f.querySelector('input').focus();
    }
</script>
</x-app-layout>

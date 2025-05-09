@if (session('success'))
    <div class="mb-4 font-medium text-sm text-green-600">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4">
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
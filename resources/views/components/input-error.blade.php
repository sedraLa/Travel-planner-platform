@props(['messages'])

@if ($messages)
    <ul class="text-sm mt-1">
        @foreach ((array) $messages as $message)
            <li style="color: #dc2626;">
                {{ $message }}
            </li>
        @endforeach
    </ul>
@endif

@php use App\Enums\UserRole; @endphp
<x-app-layout>
    <div class="main-wrapper p-6 md:p-8">
        <form method="GET" action="{{ route('shift-templates.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
            <div class="flex-1 min-w-[220px]">
                <label class="text-sm text-gray-600">Search</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Shift template name"
                    class="w-full border rounded-lg p-2"
                >
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
                <a href="{{ route('shift-templates.index') }}" class="px-4 py-2 border rounded-lg text-gray-600">Reset</a>
            </div>
        </form>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Shift Templates</h2>
                    <p class="text-gray-500 mt-1">Manage the reusable shift templates.</p>
                </div>

               
            </div>

            @if (session('success'))
                <div class="m-4 px-4 py-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($shiftTemplates as $template)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $template->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($template->start_time)->format('H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ \Carbon\Carbon::parse($template->end_time)->format('H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ implode(', ', $template->days_of_week ?? []) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500">No shift templates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4">
                {{ $shiftTemplates->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    {{-- أفترض أن لديك ملف CSS مشترك للفورم، يمكنك إضافته هنا --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}"> {{-- إعادة استخدام نفس الستايل --}}
        <link rel="stylesheet" href="{{ asset('css/vehicles.css') }}"> {{-- إعادة استخدام نفس الستايل --}}
    @endpush

    <div class="vehicle-form-container"> {{-- إعادة استخدام نفس الكلاس الرئيسي للفورم --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Add New Driver</h2>

        <form action="{{ route('drivers.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            {{-- عرض أخطاء التحقق من الصحة (Validation Errors) --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="first-section"> {{-- تقسيم الفورم إلى قسمين كما في مثالك --}}

                {{-- القسم الأيسر --}}
                <div class="left">
                    <x-input-label for="name" value="Full Name" />
                    <x-text-input id="name" type="text" name="name" :value="old('name')" required
                        placeholder="Enter driver's full name" />

                    <x-input-label for="email" value="Email Address" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required
                        placeholder="e.g. driver@example.com" />

                    <x-input-label for="phone" value="Phone Number" />
                    <x-text-input id="phone" type="text" name="phone" :value="old('phone')" required
                        placeholder="Enter phone number" />

                    <x-input-label for="address" value="Address" />
                    <x-text-input id="address" type="text" name="address" :value="old('address')"
                        placeholder="Enter driver's address" />

                    <x-input-label for="age" value="Age" />
                    <x-text-input id="age" type="number" name="age" :value="old('age')" placeholder="e.g. 25" />
                </div>

                {{-- القسم الأيمن --}}
                <div class="right">
                    <x-input-label for="date_of_hire" value="Date of Hire" />
                    <x-text-input id="date_of_hire" type="date" name="date_of_hire" :value="old('date_of_hire')" />


                    <x-input-label for="license_category" value="License Category" />
                    <select id="license_category" name="license_category"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">-- Select Category --</option>
                        <option value="A" @selected(old('license_category') == 'A')>Category A </option>
                        <option value="B" @selected(old('license_category') == 'B')>Category B </option>
                    </select>

                    <x-input-label for="experience" value="Experience" />
                    <textarea id="experience" name="experience"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="3" placeholder="Describe driver's experience">{{ old('experience') }}</textarea>

                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status"
                        class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="active" @selected(old('status') == 'Available')>Available</option>
                        <option value="inactive" @selected(old('status') == 'Unavailable')>Unavailable</option>
                    </select>
                </div>
            </div>

            {{-- صورة رخصة القيادة --}}
            <div class="mt-6">
                <x-input-label for="license_image" value="Driver's License Image" />
                {{-- ملاحظة: حقل الملفات لا يدعم old() --}}
                <input type="file" id="license_image" name="license_image" accept="image/png, image/jpeg, image/jpg"
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100" required>
                <p class="mt-1 text-xs text-gray-500">PNG, JPG, JPEG up to 2MB.</p>
            </div>

            {{-- أزرار الحفظ والإلغاء --}}
            <div class="popup-buttons mt-8">
                <button type="submit" class="btn btn-primary">Save Driver</button>
                <a href="{{ route('drivers.index') }}" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
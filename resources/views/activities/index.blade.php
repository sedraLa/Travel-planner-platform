@php use App\Enums\UserRole; @endphp
<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/transport.css') }}">
        <link rel="stylesheet" href="{{ asset('css/destinations.css') }}">
    @endpush

    <div class="main-wrapper">
        <div class="hero-background activity-page">
            <div class="heading">
                <div class="header" style="margin-top:55px;">
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <h2 style="font-size:80px;font-weight:500;">Activities</h2>
                    </div>
                </div>

                <p style="color:white;font-size:20px;width:50%">
                    Choose from our curated selection of premium activities each designed to elevate your travel
                    experience
                </p>

                <div class="flex justify-end mb-4 px-6 pt-6">
                    @if (Auth::user()->role === UserRole::ADMIN->value)
                        <a href="{{ route('activities.create') }}" class="add-btn">+ Add New Activity</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="main">
            <!--Activities cards-->
            <div class="exp-cards">
                @forelse($activities as $activity)
                    <div class="exp-card">
                        <img src="{{ asset('storage/' . $activity->image) }}" alt="{{ $activity->name }}">
                        <h3>{{ $activity->name }}</h3>

                        <button type="button" class="details-btn" data-name="{{ $activity->name }}"
                            data-description="{{ $activity->description ?? 'No description available.' }}"
                            data-destination="{{ $activity->destination->name ?? 'N/A' }}"
                            data-duration="{{ $activity->duration }} {{ $activity->duration_unit }}"
                            data-price="{{ number_format($activity->price, 2) }}"
                            data-category="{{ ucfirst($activity->category) }}"
                            data-guide_name="{{ $activity->guide_name ?? 'N/A' }}"
                            data-guide_language="{{ $activity->guide_language ?? 'N/A' }}"
                            data-availability="{{ $activity->availability ?? 'N/A' }}"
                            data-requirements="{{ $activity->requirements ?? 'N/A' }}"
                            data-amenities="{{ implode(', ', $activity->amenities ?? []) }}"
                            data-highlights="{{ $activity->highlights ?? 'N/A' }}"
                            data-family_friendly="{{ ucwords(str_replace('_', ' ', $activity->family_friendly)) }}"
                            data-pets_allowed="{{ $activity->pets_allowed ? 'Yes' : 'No' }}"
                            data-requires_booking="{{ $activity->requires_booking ? 'Yes' : 'No' }}"
                            data-image="{{ asset('storage/' . $activity->image) }}">
                            More Details
                        </button>
                    </div>
                @empty
                    <p>No activities found.</p>
                @endforelse
            </div>

            <!-- Modal -->
            <div id="activityModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <img id="modalImage" src="" alt="" class="modal-img">
                    <h2 id="modalTitle"></h2>
                    <p id="modalDescription"></p>
                    <div class="modal-info">
                        <p><strong>Destination:</strong> <span id="modalDestination"></span></p>
                        <p><strong>Duration:</strong> <span id="modalDuration"></span></p>
                        <p><strong>Price:</strong> $<span id="modalPrice"></span></p>
                        <p><strong>Category:</strong> <span id="modalCategory"></span></p>
                        <p><strong>Guide Name:</strong> <span id="modalGuideName"></span></p>
                        <p><strong>Guide Language:</strong> <span id="modalGuideLanguage"></span></p>
                        <p><strong>Availability:</strong> <span id="modalAvailability"></span></p>
                        <p><strong>Requirements:</strong> <span id="modalRequirements"></span></p>
                        <p><strong>Amenities:</strong> <span id="modalAmenities"></span></p>
                        <p><strong>Highlights:</strong> <span id="modalHighlights"></span></p>
                        <p><strong>Family Friendly:</strong> <span id="modalFamily"></span></p>
                        <p><strong>Pets Allowed:</strong> <span id="modalPets"></span></p>
                        <p><strong>Requires Booking:</strong> <span id="modalBooking"></span></p>
                    </div>
                </div>
            </div>

            <style>
                /* Modal styling */
                .modal {
                    display: none;
                    position: fixed;
                    z-index: 1000;
                    padding-top: 80px;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    overflow: auto;
                    background: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(4px);
                }

                .modal-content {
                    background: linear-gradient(135deg, #ffffff 0%, #f7f9fc 100%);
                    margin: auto;
                    padding: 35px;
                    border-radius: 25px;
                    width: 700px;
                    max-width: 90%;
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
                    position: relative;
                    text-align: left;
                    animation: slideDown 0.4s ease;
                    font-family: 'Poppins', sans-serif;
                }

                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-30px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .modal-img {
                    width: 100%;
                    border-radius: 20px;
                    margin-bottom: 20px;
                    height: 250px;
                    object-fit: cover;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
                }

                .modal h2 {
                    color: var(--indigo);
                    font-size: 28px;
                    margin-bottom: 15px;
                    text-align: center;
                }

                .modal p {
                    color: #555;
                    font-size: 15.5px;
                    line-height: 1.6;
                }

                .modal-info {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 15px 20px;
                    margin-top: 25px;
                    padding: 20px;
                    background: #f9fbff;
                    border-radius: 15px;
                    border: 1px solid #e5eaf2;
                }

                .modal-info p {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 15px;
                    color: #333;
                    margin: 0;
                }

                .modal-info strong {
                    color: var(--indigo);
                    font-weight: 600;
                }

                .close {
                    position: absolute;
                    top: 18px;
                    right: 25px;
                    font-size: 28px;
                    font-weight: bold;
                    color: #666;
                    cursor: pointer;
                    transition: color 0.2s;
                }

                .close:hover {
                    color: var(--indigo);
                }

                .exp-cards {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 20px;
                    justify-content: center;
                    margin: 83px auto;
                    width: 88%;
                }

                .exp-card {
                    background-color: #ffffff;
                    border-radius: 25px;
                    width: 250px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                    transition: transform 0.3s ease;
                    margin-bottom: 15px;
                }

                .exp-card img {
                    width: 100%;
                    border-radius: 15px;
                    margin-bottom: 15px;
                    object-fit: cover;
                    height: 180px;
                }

                .exp-card h3 {
                    font-size: 20px;
                    margin-bottom: 10px;
                    color: var(--indigo);
                }

                .exp-card p {
                    font-size: 14px;
                    margin: 5px 15px;
                    color: #333;
                }

                .exp-card button {
                    background-color: var(--indigo);
                    color: #fff;
                    border: none;
                    padding: 10px 18px;
                    border-radius: 10px;
                    cursor: pointer;
                    font-weight: bold;
                    transition: all 0.3s ease;
                    margin-top: 10px;
                    margin-bottom: 40px;
                }

                .exp-card button:hover {
                    background-color: #fff;
                    color: var(--indigo);
                    border: 2px solid var(--indigo);
                }

                .exp-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const modal = document.getElementById('activityModal');
                    const closeBtn = document.querySelector('.close');

                    const title = document.getElementById('modalTitle');
                    const description = document.getElementById('modalDescription');
                    const destination = document.getElementById('modalDestination');
                    const duration = document.getElementById('modalDuration');
                    const price = document.getElementById('modalPrice');
                    const category = document.getElementById('modalCategory');
                    const image = document.getElementById('modalImage');

                    const guideName = document.getElementById('modalGuideName');
                    const guideLang = document.getElementById('modalGuideLanguage');
                    const availability = document.getElementById('modalAvailability');
                    const requirements = document.getElementById('modalRequirements');
                    const amenities = document.getElementById('modalAmenities');
                    const highlights = document.getElementById('modalHighlights');
                    const family = document.getElementById('modalFamily');
                    const pets = document.getElementById('modalPets');
                    const booking = document.getElementById('modalBooking');

                    document.querySelectorAll('.details-btn').forEach(button => {
                        button.addEventListener('click', () => {
                            title.textContent = button.dataset.name;
                            description.textContent = button.dataset.description;
                            destination.textContent = button.dataset.destination;
                            duration.textContent = button.dataset.duration;
                            price.textContent = button.dataset.price;
                            category.textContent = button.dataset.category;
                            image.src = button.dataset.image;

                            guideName.textContent = button.dataset.guide_name;
                            guideLang.textContent = button.dataset.guide_language;
                            availability.textContent = button.dataset.availability;
                            requirements.textContent = button.dataset.requirements;
                            amenities.textContent = button.dataset.amenities;
                            highlights.textContent = button.dataset.highlights;
                            family.textContent = button.dataset.family_friendly;
                            pets.textContent = button.dataset.pets_allowed;
                            booking.textContent = button.dataset.requires_booking;

                            modal.style.display = 'block';
                        });
                    });

                    closeBtn.addEventListener('click', () => modal.style.display = 'none');
                    window.addEventListener('click', (e) => {
                        if (e.target === modal) modal.style.display = 'none';
                    });
                });
            </script>
        </div>
    </div>
</x-app-layout>
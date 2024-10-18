@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Edit Service</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('provider.updateService', $serviceProviderService->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="service_category_id" class="form-label">Service Category</label>
                            <select class="form-select" id="service_category_id" name="service_category_id" required>
                                @foreach($serviceCategories as $serviceCategory)
                                <option value="{{ $serviceCategory->id }}"
                                    {{ $serviceProviderService->service_category_id == $serviceCategory->id ? 'selected' : '' }}>
                                    {{ $serviceCategory->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $serviceProviderService->description) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="availability" class="form-label">Availability</label>
                            <input type="text" class="form-control" id="availability" name="availability" value="{{ old('availability', $serviceProviderService->availability) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="rate" class="form-label">Rate</label>
                            <input type="text" class="form-control" id="rate" name="rate" value="{{ old('rate', $serviceProviderService->rate) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">Working Area/Location</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $serviceProviderService->city) }}" required>
                        </div>

                        <input type="hidden" id="latitude" name="latitude" value="{{ $serviceProviderService->latitude }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ $serviceProviderService->longitude }}">
                        <div class="mb-3" id="map" style="height: 300px;"></div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Update Service</button>
                            <a href="{{ route('provider.profileView') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    $(document).ready(function() {
        // Get the initial coordinates
        var initialLat = $('#latitude').val() || 6.9271; // Default to Colombo if not provided
        var initialLng = $('#longitude').val() || 79.9615;

        // Initialize the map
        var map = L.map('map').setView([initialLat, initialLng], 13);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Create a marker if coordinates are available
        var marker;
        if ($('#latitude').val() && $('#longitude').val()) {
            marker = L.marker([initialLat, initialLng]).addTo(map);
            marker.bindPopup("Current Location: " + initialLat + ", " + initialLng).openPopup();
        }

        // Map click event to set location and marker
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            // If marker exists, remove it
            if (marker) {
                map.removeLayer(marker);
            }

            // Add a new marker
            marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup("You clicked at " + lat + ", " + lng).openPopup();

            // Set the latitude and longitude input fields
            $('#latitude').val(lat);
            $('#longitude').val(lng);

            // Reverse geocode the coordinates to get the location name
            $.get(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`, function(data) {
                if (data && data.display_name) {
                    // Update the location input with the formatted address
                    $('#city').val(data.display_name);
                } else {
                    $('#city').val('Location not found');
                }
            }).fail(function() {
                $('#city').val('Error fetching location');
            });
        });
    });
</script>
@endpush
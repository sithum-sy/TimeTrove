@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <form action="{{ route('provider.service.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="service_category_id" class="form-label">Service Category</label>
                    <select class="form-select" id="service_category_id" name="service_category_id" required>
                        @foreach($serviceCategories as $serviceCategory)
                        <option value="{{ $serviceCategory->id }}">{{ $serviceCategory->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-label">Service Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="availability" class="form-label">Availability</label>
                    <input type="text" name="availability" id="availability" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="rate" class="form-label">Rate</label>
                    <input type="number" name="rate" id="rate" class="form-control" step="0.01" required>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="city" class="form-label">Working Area/Location</label>
                    <input type="text" name="city" id="city" class="form-control" placeholder="Select from map" required readonly>
                </div>
            </div>
        </div>

        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <div class="mb-3" id="map" style="height: 300px;"></div>


        <div class="row mb-3">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </div>
    </form>
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
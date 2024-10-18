@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Edit Service Request</h2>
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

                    <form action="{{ route('client.updateRequest', $serviceRequest->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="serviceCategory" class="form-label">Service Category</label>
                            <select name="service_category_id" id="serviceCategory" class="form-select" required>
                                @foreach($serviceCategories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $serviceRequest->service_category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control" value="{{ $serviceRequest->location }}" required>
                        </div>

                        <input type="hidden" id="latitude" name="latitude" value="{{ $serviceRequest->latitude }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ $serviceRequest->longitude }}">
                        <div id="map" style="height: 300px;"></div> <!-- Add a div for the map -->

                        <div class=" mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ $serviceRequest->date }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" name="time" id="time" class="form-control" value="{{ $serviceRequest->time }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required>{{ $serviceRequest->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="requestPicture" class="form-label">Request Picture</label>
                            <div class="mb-2">
                                @if($serviceRequest->request_picture)
                                <img src="{{ asset($serviceRequest->request_picture) }}" alt="Request Picture" class="img-fluid rounded mb-2" style="max-width: 100%; height: auto;">
                                @else
                                <p class="text-muted">No picture provided</p>
                                @endif
                            </div>
                            <input type="file" name="request_picture" id="requestPicture" class="form-control">
                            <small class="form-text text-muted">Upload a new picture to replace the existing one.</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Update Request</button>
                            <a href="{{ route('client.singleRequest.view', $serviceRequest->id) }}" class="btn btn-secondary">Cancel</a>
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
                    $('#location').val(data.display_name);
                } else {
                    $('#location').val('Location not found');
                }
            }).fail(function() {
                $('#location').val('Error fetching location');
            });
        });
    });
</script>
@endpush
@extends('layouts.app')

@section('title', 'Client Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-4">Client Dashboard</h1>
    <div class="row">
        <!-- Left Column - Control Panels -->
        <div class="col-md-3 mb-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-success btn-block mb-2" data-bs-toggle="modal" data-bs-target="#addServiceRequestModal">
                        <i class="fas fa-plus-circle"></i> New Service Request
                    </button>
                    <a href="{{ route('client.profileView') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-user-cog"></i> View Profile
                    </a>
                    <a href="#" class="btn btn-warning btn-block">
                        <i class="fas fa-history"></i> View History
                    </a>
                </div>
            </div>

            <!-- search and filter -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Search & Filter</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('search') }}">
                        <div class="mb-2">
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Search by client name or service..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="mb-2">
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <select class="form-select" id="service_category" name="service_category">
                                <option value="">All Services</option>
                                @foreach($serviceCategories as $category)
                                <option value="{{ $category->id }}" {{ request('service_category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ request('date') }}">
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            </div>
                            <div class="col">
                                <a href="{{ route('home') }}" class="btn btn-secondary w-100">Clear Filters</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <p><strong>Total Requests:</strong> <span id="totalRequests">{{ $totalServiceRequests }}</span></p>
                    <p><strong>Pending Requests:</strong> <span id="pendingRequests">{{ $totalPendingRequests }}</span></p>
                    <p><strong>Confirmed Requests:</strong> <span id="comfirmedRequests">{{ $totalConfirmedRequests }}</span></p>
                    <p><strong>Pending-Payment:</strong> <span id="comfirmedRequests">{{ $totalPendingPayments }}</span></p>
                    <p><strong>Completed Requests:</strong> <span id="completedRequests">{{ $totalCompletedRequests }}</span></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>Contact our support team:</p>
                    <p><i class="fas fa-phone"></i> +94 (011) 456-7890</p>
                    <p><i class="fas fa-envelope"></i> support@timetrove.com</p>
                </div>
            </div>
        </div>

        <!-- Right Column - Service Requests Table -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Your Service Requests</h5>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Service</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <!-- <th>Picture</th> -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- To iterate over a collection or array, but also want to provide a fallback or default message when the collection is empty -->
                                @forelse($serviceRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->serviceCategory->name }}</td>
                                    <td>{{ Str::limit($request->description, 30) }}</td>
                                    <td>{{ $request->location }}</td>
                                    <td>{{ $request->date }} at {{ $request->time }}</td>
                                    <td><span class="badge bg-{{ $request->status == 'pending' ? 'warning' : 'success' }}">{{ ucfirst($request->status) }}</span></td>
                                    <!-- <td>
                                        @if($request->request_picture)
                                        <img src="{{ asset('storage/' . $request->request_picture) }}" alt="Request Picture" width="50" height="50" class="img-thumbnail">
                                        @else
                                        <span class="text-muted">No image</span>
                                        @endif
                                    </td> -->
                                    <td>
                                        <!-- <button class="btn btn-sm btn-primary edit-request" data-request-id="{{ $request->id }}"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('client.deleteRequest', $request->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this request?')"><i class="fas fa-trash"></i></button>
                                        </form> -->
                                        <a href="{{ route('client.singleRequest.view', $request->id) }}" class="btn btn-primary btn-sm">Manage</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No records found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        {{ $serviceRequests->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Service Request Modal -->
<div class="modal fade" id="addServiceRequestModal" tabindex="-1" aria-labelledby="addServiceRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addServiceRequestModalLabel">Add New Service Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('client.addRequest') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="service_category" class="form-label">Service Category</label>
                        <select class="form-select" id="service_category" name="service_category_id" required>
                            <option value="" disabled selected>Select a service</option>
                            @foreach($serviceCategories as $serviceCategory)
                            <option value="{{ $serviceCategory->id }}">{{ $serviceCategory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" class="form-control" name="description" rows="4" required>{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Select from map" required readonly>
                    </div>
                    <input type="hidden" id="latitude" name="latitude" required>
                    <input type="hidden" id="longitude" name="longitude" required>
                    <div id="map" style="height: 300px;"></div> <!-- Add a div for the map -->
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    <div class="mb-3">
                        <label for="request_picture" class="form-label">Pictures</label>
                        <input type="file" class="form-control" id="request_picture" name="request_pictures">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Initialize the map when the modal is shown
    $('#addServiceRequestModal').on('shown.bs.modal', function() {
        var map = L.map('map').setView([6.9271, 79.9615], 13); // Set initial view to Colombo

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Create a marker
        var marker;

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
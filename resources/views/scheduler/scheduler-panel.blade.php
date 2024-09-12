@extends('layouts.app')

@section('title', 'Scheduler Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-4">Scheduler Dashboard</h1>
    <div class="row">
        <!-- Left Column - Control Panels -->
        <div class="col-md-3 mb-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-success btn-block mb-2" data-bs-toggle="modal" data-bs-target="#scheduleAppointmentModal">
                        <i class="fas fa-calendar-plus"></i> Schedule Appointment
                    </button>
                    <a href="{{ route('scheduler.serviceProvider') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-user-tie"></i> Manage Service Providers
                    </a>
                    <a href="#" class="btn btn-warning btn-block">
                        <i class="fas fa-users"></i> Manage Clients
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <p><strong>Total Appointments:</strong> <span id="totalAppointments">Loading...</span></p>
                    <p><strong>Upcoming Appointments:</strong> <span id="upcomingAppointments">Loading...</span></p>
                    <p><strong>Total Clients:</strong> <span id="totalClients">Loading...</span></p>
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

        <!-- Right Column - Appointments and Quotations Tables -->
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Upcoming Appointments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Client Name</th>
                                    <th>Service</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientServiceRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->client->first_name }} {{ $request->client->last_name }}</td>
                                    <td>{{ $request->serviceCategory->name }}</td>
                                    <td>{{ Str::limit($request->description, 30) }}</td>
                                    <td>{{ $request->location }}</td>
                                    <td>{{ $request->date }} at {{ $request->time }}</td>
                                    <td><span class="badge bg-{{ $request->status == 'pending' ? 'warning' : 'success' }}">{{ ucfirst($request->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('scheduler.singleRequest.view', ['request_id' => $request->id, 'client_id' => $request->client_id]) }}" class="btn btn-primary btn-sm">Assign</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $clientServiceRequests->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Quotations by Service Providers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Client Name</th>
                                    <th>Service</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientServiceRequests as $request)
                                @if($request->status === 'quoted' || $request->status === 're-quoted' || $request->status === 'pending-approval')
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->client->first_name }} {{ $request->client->last_name }}</td>
                                    <td>{{ $request->serviceCategory->name }}</td>
                                    <td>{{ Str::limit($request->description, 30) }}</td>
                                    <td>{{ $request->location }}</td>
                                    <td>{{ $request->date }} at {{ $request->time }}</td>
                                    <td><span class="badge bg-{{ $request->status == 'pending-approval' ? 'info' : 'warning' }}">{{ ucfirst($request->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('scheduler.singleRequest.view', ['request_id' => $request->id, 'client_id' => $request->client_id]) }}" class="btn btn-primary btn-sm">View</a>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $clientServiceRequests->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Appointment Modal -->
<div class="modal fade" id="scheduleAppointmentModal" tabindex="-1" aria-labelledby="scheduleAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="scheduleAppointmentModalLabel">Schedule New Appointment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleAppointmentForm" method="POST" action="#">
                @csrf
                <div class="modal-body">
                    <!-- Add form fields for scheduling an appointment -->
                    <div class="mb-3">
                        <label for="client" class="form-label">Client</label>
                        <select class="form-select" id="client" name="client_id" required>
                            <option value="" disabled selected>Select a client</option>
                            <!-- Add options dynamically from your client list -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="service_category" class="form-label">Service Category</label>
                        <select class="form-select" id="service_category" name="service_category_id" required>
                            <option value="" disabled selected>Select a service</option>
                            <!-- Add options dynamically from your service categories -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Schedule Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Populate statistics
        document.getElementById('totalAppointments').textContent = '{{ $clientServiceRequests->count() }}';
        document.getElementById('upcomingAppointments').textContent = '{{ $clientServiceRequests->where("status", "pending")->count() }}';
        document.getElementById('totalClients').textContent = '{{ App\Models\User::count() }}';

        // Handle appointment scheduling form submission
        document.getElementById('scheduleAppointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add logic to handle form submission
            alert('Appointment scheduled successfully!');
            $('#scheduleAppointmentModal').modal('hide');
        });
    });
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-4">Scheduler Control Panel</h2>
    <div class="row">
        <!-- Left column - Task handling -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">Appointment Management</div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary btn-sm mb-2 w-100" data-bs-toggle="modal" data-bs-target="#scheduleAppointmentModal">
                        Schedule Appointment
                    </button>
                    <a href="" class="btn btn-secondary btn-sm w-100">View Appointments</a>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Client Management</div>
                <div class="card-body">
                    <a href="" class="btn btn-primary btn-sm w-100">Manage Clients</a>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Payment Management</div>
                <div class="card-body">
                    <a href="" class="btn btn-primary btn-sm w-100">Manage Payments</a>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Reminders</div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#sendReminderModal">
                        Send Reminder
                    </button>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-header">Service Provider Management</div>
                <div class="card-body">
                    <a href="{{ route('scheduler.serviceProvider') }}" class="btn btn-primary btn-sm w-100">Manage Service Providers</a>
                </div>
            </div>
        </div>

        <!-- Right column - User Request Table and Stats -->
        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-header">
                    Upcoming Appointments
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Client Name</th>
                                    <th scope="col">Service Category</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Pictures</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientServiceRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->client->first_name }} {{ $request->client->last_name }}</td>
                                    <td>{{ $request->serviceCategory->name }}</td>
                                    <td>{{ $request->description }}</td>
                                    <td>{{ $request->location }}</td>
                                    <td>{{ $request->date }}</td>
                                    <td>{{ $request->time }}</td>
                                    <td>
                                        @if($request->request_picture)
                                        <img src="{{ asset('storage/' . $request->request_picture) }}" alt="Request Picture" style="max-width: 50px; max-height: 50px;">
                                        @else
                                        No picture
                                        @endif
                                    </td>
                                    <td>{{ $request->status }}</td>
                                    <td>
                                        <a href="{{ route('scheduler.singleRequest.view', ['request_id' => $request->id, 'client_id' => $request->client_id]) }}" class="btn btn-primary btn-sm">Assign</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Links -->
                    <div class=" d-flex justify-content-center">
                        {{ $clientServiceRequests->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Statistics</div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Total Appointments: <span id="totalAppointments">Loading...</span></li>
                                <li class="list-group-item">Upcoming Appointments: <span id="upcomingAppointments">Loading...</span></li>
                                <li class="list-group-item">Total Clients: <span id="totalClients">Loading...</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Recent Activity</div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush" id="recentActivity">
                                <li class="list-group-item">Loading recent activity...</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Populate statistics
        document.getElementById('totalAppointments').textContent = '123';
        document.getElementById('upcomingAppointments').textContent = '45';
        document.getElementById('totalClients').textContent = '67';

        // Handle appointment scheduling form submission
        document.getElementById('scheduleAppointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add logic to handle form submission
            alert('Appointment scheduled successfully!');
            $('#scheduleAppointmentModal').modal('hide');
        });

        // Handle reminder form submission
        document.getElementById('sendReminderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add logic to handle form submission
            alert('Reminder sent successfully!');
            $('#sendReminderModal').modal('hide');
        });
    });
</script>
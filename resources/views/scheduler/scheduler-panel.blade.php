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
                    <a href="{{ route('scheduler.serviceCategories') }}" class="btn btn-warning btn-block">
                        <i class="fa-solid fa-layer-group"></i> Manage Service Categories
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
                                @foreach(['pending', 'confirmed', 'completed', 'quoted', 're-quoted', 'pending-approval', 'started'] as $status)
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
                    <p><strong>Total Appointments:</strong> <span id="totalAppointments">{{ $totalAppointments }}</span></p>
                    <p><strong>Ongoing Appointments:</strong> <span id="upcomingAppointments">{{ $totalUpcomingAppointments }}</span></p>
                    <p><strong>Completed Appointments:</strong> <span id="upcomingAppointments">{{ $totalCompletedAppointments }}</span></p>
                    <p><strong>Total Clients:</strong> <span id="totalClients">{{ $totalClients }}</span></p>
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
            @foreach(['Ongoing Appointments', 'Quotations by Service Providers', 'Completed Appointments'] as $section)
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">{{ $section }}</h5>
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
                                @php
                                $data = $section == 'Ongoing Appointments' ? $upcomingAppointments :
                                ($section == 'Quotations by Service Providers' ? $quotations :
                                $completedAppointments);
                                @endphp
                                @forelse($data as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->client->first_name }} {{ $request->client->last_name }}</td>
                                    <td>{{ $request->serviceCategory->name }}</td>
                                    <td>{{ Str::limit($request->description, 30) }}</td>
                                    <td>{{ $request->location }}</td>
                                    <td>{{ $request->date }} at {{ $request->time }}</td>
                                    <td><span class="badge bg-{{ $request->status == 'pending' ? 'warning' : 'success' }}">{{ ucfirst($request->status) }}</span></td>
                                    <td>
                                        @if($request->status === 'pending')
                                        <a href="{{ route('scheduler.singleRequest.view', ['request_id' => $request->id, 'client_id' => $request->client_id]) }}" class="btn btn-primary btn-sm">
                                            Assign
                                        </a>
                                        @else
                                        <a href="{{ route('scheduler.singleRequest.view', ['request_id' => $request->id, 'client_id' => $request->client_id]) }}" class="btn btn-primary btn-sm">
                                            View
                                        </a>
                                        @endif
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
                    <div class="d-flex justify-content-center">
                        {{ $data->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
            @endforeach


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
                    <div class="mb-3">
                        <label for="client" class="form-label">Client</label>
                        <select class="form-select" id="client" name="client_id" required>
                            <option value="" disabled selected>Select a client</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="service_category" class="form-label">Service Category</label>
                        <select class="form-select" id="service_category" name="service_category_id" required>
                            <option value="" disabled selected>Select a service</option>
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
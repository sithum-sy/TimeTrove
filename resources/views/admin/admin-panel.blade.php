@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-4">Admin Dashboard</h1>
    <div class="row">
        <!-- Left Column - Control Panels -->
        <div class="col-md-3 mb-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Admin Actions</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-success btn-block mb-2" data-bs-toggle="modal" data-bs-target="#createSchedulerModal">
                        <i class="fas fa-user-plus"></i> Create Scheduler
                    </button>
                    <a href="#" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-user-cog"></i> Manage Users
                    </a>
                    <a href="#" class="btn btn-warning btn-block">
                        <i class="fas fa-chart-line"></i> View Reports
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <p><strong>Total Schedulers:</strong> <span id="totalUsers">{{ $schedulers->count() }}</span></p>
                    <p><strong>Active Schedulers:</strong> <span id="totalAppointments">{{ $activeSchedulersCount }}</span></p>
                    <p><strong>Active Service Categories:</strong> <span id="activeServices">{{ $serviceCategories->count() }}</span></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Support</h5>
                </div>
                <div class="card-body">
                    <p>Contact our support team:</p>
                    <p><i class="fas fa-phone"></i> +94 (011) 456-7890</p>
                    <p><i class="fas fa-envelope"></i> support@timetrove.com</p>
                </div>
            </div>
        </div>

        <!-- Right Column - Schedulers Table -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Schedulers Management</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>DOB</th>
                                    <th>Address</th>
                                    <th>Contact Number</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($schedulers as $scheduler)
                                <tr>
                                    <td>{{ $scheduler->id }}</td>
                                    <td>{{ $scheduler->first_name }}</td>
                                    <td>{{ $scheduler->last_name }}</td>
                                    <td>{{ $scheduler->email }}</td>
                                    <td>{{ $scheduler->date_of_birth }}</td>
                                    <td>{{ $scheduler->address }}</td>
                                    <td>{{ $scheduler->phone_number }}</td>
                                    <td>{{ $scheduler->gender }}</td>
                                    <td><span class="badge bg-{{ $scheduler->is_active ? 'success' : 'secondary' }}">{{ $scheduler->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td>
                                        <a href="{{ route('scheduler.view', $scheduler->id) }}" class="btn btn-sm btn-primary">View</a>
                                        <a href="{{ route('scheduler.edit', $scheduler->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('scheduler.delete', $scheduler->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this scheduler?')">Delete</button>
                                        </form>
                                        <form action="{{ route('scheduler.status', $scheduler->id) }}" method="GET" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $scheduler->is_active ? 'btn-secondary' : 'btn-success' }}">
                                                {{ $scheduler->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Pagination Links -->
            <div class="d-flex justify-content-center mt-3">
                {{ $schedulers->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal for Creating Scheduler -->
<div class="modal fade" id="createSchedulerModal" tabindex="-1" aria-labelledby="createSchedulerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createSchedulerModalLabel">Create Scheduler</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('scheduler.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input id="first_name" type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input id="last_name" type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input id="phone_number" type="text" class="form-control" name="phone_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input id="date_of_birth" type="date" class="form-control" name="date_of_birth" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input id="address" type="text" class="form-control" name="address" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" class="form-select" name="gender" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
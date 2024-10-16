@extends('layouts.app')

@section('title', 'Service Provider Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-4">Service Provider Dashboard</h1>
    <div class="row">
        <!-- Left Column - Control Panels -->
        <div class="col-md-3 mb-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="#" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-tasks"></i> View Assigned Tasks
                    </a>
                    <a href="{{ route('provider.profileView') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-user-cog"></i> View Profile
                    </a>
                    <a href="#" class="btn btn-warning btn-block">
                        <i class="fas fa-cog"></i> Account Settings
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
                                @foreach(['assigned', 'quoted', 'new-quote-requested', 're-quoted', 'pending-approval', 'confirmed', 'started', 'completed'] as $status)
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
                    <p><strong>Total Assigned Tasks:</strong> <span id="totalAssignedTasks">{{ $totalAssignedTasks }}</span></p>
                    <p><strong>Total Upcoming Tasks:</strong> <span id="completedTasks">{{ $totalUpcomimgTasks }}</span></p>
                    <p><strong>Completed Tasks:</strong> <span id="completedTasks">{{ $totalCompletedTasks }}</span></p>
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

        <!-- Right Column - Assigned Tasks Table -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Your Tasks</h5>
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
                                    <th>Client Name</th>
                                    <th>Service Category</th>
                                    <th>Description</th>
                                    <th>Location</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                <tr>
                                    <td>{{ $task->id }}</td>
                                    <td>{{ $task->client->first_name }} {{ $task->client->last_name }}</td>
                                    <td>{{ $task->serviceCategory->name }}</td>
                                    <td>{{ Str::limit($task->description, 30) }}</td>
                                    <td>{{ $task->location }}</td>
                                    <td>{{ $task->date }} at {{ $task->time }}</td>
                                    <td><span class="badge bg-{{ $task->status == 'assigned' ? 'warning' : 'success' }}">{{ ucfirst($task->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('provider.serviceRequest.view', ['task_id' => $task->id, 'client_id' => $task->client_id]) }}" class="btn btn-primary btn-sm">Manage</a>
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
                        {{ $tasks->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@extends('layouts.app')

@section('title', 'Scheduler Control Panel')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-4">Scheduler Control Panel</h1>
    <div class="row">
        <!-- Left Column - Control Panels -->
        <div class="col-md-3 mb-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('scheduler.serviceProviderForm1') }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-user-plus"></i> Create Service Provider
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-calendar-alt"></i> Manage Appointments
                    </a>
                    <a href="#" class="btn btn-warning btn-block">
                        <i class="fas fa-cogs"></i> System Settings
                    </a>
                </div>
            </div>

            <!-- search and filter -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Search & Filter</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('scheduler.serviceProvider.search') }}">
                        <div class="mb-2">
                            <input type="text" class="form-control" id="search" name="search"
                                placeholder="Search by client name or service..."
                                value="{{ request('search') }}">
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
                            <select class="form-select" id="status" name="status">
                                <option value="">Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            </div>
                            <div class="col">
                                <a href="{{ route('scheduler.serviceProvider') }}" class="btn btn-secondary w-100">Clear Filters</a>
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
                    <p><strong>Total Service Providers:</strong> <span id="totalUsers">{{ $serviceProviders->count() }}</span></p>
                    <p><strong>Active Service Providers:</strong> <span id="activeServices">{{ $activeServiceProvidersCount }}</span></p>
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

        <!-- Right Column - Service Providers Table -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Service Providers</h5>
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
                                    <th>Name</th>
                                    <th>Service Category</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($serviceProviders as $serviceProviderService)
                                <tr>
                                    <td>{{ $serviceProviderService->user->id }}</td>
                                    <td>{{ $serviceProviderService->user->first_name }} {{ $serviceProviderService->user->last_name }}</td>
                                    <td>{{ $serviceProviderService->serviceCategory ? $serviceProviderService->serviceCategory->name : 'N/A' }}</td>
                                    <td>{{ $serviceProviderService->user->email }}</td>
                                    <td>{{ $serviceProviderService->user->phone_number }}</td>
                                    <td>{{ ucfirst($serviceProviderService->user->gender) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $serviceProviderService->user->is_active ? 'success' : 'danger' }}">
                                            {{ $serviceProviderService->user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('scheduler.serviceProvider.view', $serviceProviderService->user->id) }}" class="btn btn-primary btn-sm">View</a>
                                        <a href="{{ route('scheduler.serviceProvider.edit', $serviceProviderService->user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('scheduler.serviceProvider.delete', $serviceProviderService->user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        <form action="{{ route('scheduler.serviceProvider.status', $serviceProviderService->user->id) }}" method="GET" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn {{ $serviceProviderService->user->is_active ? 'btn-secondary' : 'btn-success' }} btn-sm">
                                                {{ $serviceProviderService->user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
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
                        {{ $serviceProviders->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Populate statistics
        document.getElementById('totalUsers').textContent = '1,234';
        document.getElementById('totalAppointments').textContent = '5,678';
        document.getElementById('activeServices').textContent = '42';

        // Populate recent activity
        const recentActivity = [
            'New user registered: John Doe',
            'Appointment scheduled: Jane Smith - Haircut',
            'New service added: Massage Therapy'
        ];
        const activityList = document.getElementById('recentActivity');
        activityList.innerHTML = '';
        recentActivity.forEach(activity => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = activity;
            activityList.appendChild(li);
        });
    });
</script>
@endsection
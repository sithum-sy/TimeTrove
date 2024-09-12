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
                    <a href="#" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-calendar-alt"></i> Manage Appointments
                    </a>
                    <a href="#" class="btn btn-warning btn-block">
                        <i class="fas fa-cogs"></i> System Settings
                    </a>
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
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="recentActivity">
                        <li class="list-group-item">Loading recent activity...</li>
                    </ul>
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
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serviceProviders as $serviceProvider)
                                <tr>
                                    <td>{{ $serviceProvider->id }}</td>
                                    <td>{{ $serviceProvider->first_name }} {{ $serviceProvider->last_name }}</td>
                                    <td>{{ $serviceProvider->email }}</td>
                                    <td>{{ $serviceProvider->phone_number }}</td>
                                    <td>{{ ucfirst($serviceProvider->gender) }}</td>
                                    <td><span class="badge bg-{{ $serviceProvider->is_active ? 'success' : 'danger' }}">{{ $serviceProvider->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td>
                                        <a href="{{ route('scheduler.serviceProvider.view', $serviceProvider->id) }}" class="btn btn-primary btn-sm">View</a>
                                        <a href="{{ route('scheduler.serviceProvider.edit', $serviceProvider->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('scheduler.serviceProvider.delete', $serviceProvider->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        <form action="{{ route('scheduler.serviceProvider.status', $serviceProvider->id) }}" method="GET" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn {{ $serviceProvider->is_active ? 'btn-secondary' : 'btn-success' }} btn-sm">
                                                {{ $serviceProvider->is_active ? 'Deactivate' : 'Activate' }}
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
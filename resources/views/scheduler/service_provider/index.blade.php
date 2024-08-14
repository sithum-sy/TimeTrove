@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <h2 class="mb-4">Scheduler Control Panel</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Service Provider Management
                </div>
                <div class="card-body">
                    <h5 class="card-title">Service Provider Management</h5>
                    <p class="card-text">Create new service providers or manage existing ones.</p>
                    <a href="{{ route('scheduler.serviceProviderForm1') }}" class="btn btn-sm btn-primary">Create Service Provider</a>

                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    System Overview
                </div>
                <div class="card-body">
                    <h5 class="card-title">Statistics</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Total Users: <span id="totalUsers">Loading...</span></li>
                        <li class="list-group-item">Total Appointments: <span id="totalAppointments">Loading...</span></li>
                        <li class="list-group-item">Active Services: <span id="activeServices">Loading...</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    Recent Activity
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="recentActivity">
                        <li class="list-group-item">Loading recent activity...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    Service Providers
                </div>
                <div class="card-body">

                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">DOB</th>
                                <th scope="col">Address</th>
                                <th scope="col">Contact Number</th>
                                <th scope="col">Gender</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($serviceProviders as $serviceProvider)
                            <tr>
                                <td>{{ $serviceProvider->id }}</td>
                                <td>{{ $serviceProvider->first_name }}</td>
                                <td>{{ $serviceProvider->last_name }}</td>
                                <td>{{ $serviceProvider->email }}</td>
                                <td>{{ $serviceProvider->date_of_birth }}</td>
                                <td>{{ $serviceProvider->address }}</td>
                                <td>{{ $serviceProvider->phone_number }}</td>
                                <td>{{ $serviceProvider->gender }}</td>
                                <td>{{ $serviceProvider->is_active ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <!-- Action buttons -->
                                    <a href="" class="btn btn-sm btn-primary">View</a>
                                    <a href="" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                    <form action="" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $serviceProvider->is_active ? 'btn-secondary' : 'btn-success' }}">
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
@endsection

<!-- Modal for Creating Scheduler -->
<!-- <div class="modal fade" id="createServiceProviderModal" tabindex="-1" aria-labelledby="createServiceProviderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createServiceProviderModalLabel">Create Service Provider</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('scheduler.serviceProvider.store') }}">
                    @csrf

                    <div class="row mb-3">
                        <label for="first_name" class="col-md-4 col-form-label text-md-end">{{ __('First Name') }}</label>

                        <div class="col-md-6">
                            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>

                            @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="last_name" class="col-md-4 col-form-label text-md-end">{{ __('Last Name') }}</label>

                        <div class="col-md-6">
                            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus>

                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="phone_number" class="col-md-4 col-form-label text-md-end">{{ __('Phone Number') }}</label>

                        <div class="col-md-6">
                            <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" autofocus>

                            @error('phone_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="date_of_birth" class="col-md-4 col-form-label text-md-end">{{ __('Date of Birth') }}</label>

                        <div class="col-md-6">
                            <input id="date_of_birth" type="date" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth') }}" required autocomplete="date_of_birth" autofocus>

                            @error('date_of_birth')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="address" class="col-md-4 col-form-label text-md-end">{{ __('Address') }}</label>

                        <div class="col-md-6">
                            <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address" autofocus>

                            @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="gender" class="col-md-4 col-form-label text-md-end">{{ __('Gender') }}</label>

                        <div class="col-md-6">
                            <input id="male" type="radio" class="form-check-input @error('address') is-invalid @enderror" name="gender" value="{{ 'male' }}" required autocomplete="gender" autofocus>
                            <label for="male">
                                Male
                            </label>

                            <input id="female" type="radio" class="form-check-input @error('address') is-invalid @enderror" name="gender" value="{{ 'female' }}" required autocomplete="gender" autofocus>
                            <label for="male">
                                Female
                            </label>

                            @error('gender')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>


                    <div class="row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Create Service Provider') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->

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

        // Handle scheduler creation form submission
        // document.getElementById('createSchedulerForm').addEventListener('submit', function(e) {
        //     e.preventDefault();
        //     const name = document.getElementById('schedulerName').value;
        //     const email = document.getElementById('schedulerEmail').value;
        //     alert(`Scheduler creation request sent for ${name} (${email})`);
        //     $('#createSchedulerModal').modal('hide');
        // });
    });
</script>
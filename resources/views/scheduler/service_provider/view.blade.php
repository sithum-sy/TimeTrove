@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">{{ $serviceProvider->first_name }} {{ $serviceProvider->last_name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <img src="https://via.placeholder.com/150" alt="Profile Picture" class="img-fluid rounded-circle mb-3">
                            <h4 class="text-primary">ID: {{ $serviceProvider->id }}</h4>
                        </div>
                        <div class="col-md-8">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Email:</strong>
                                    <span>{{ $serviceProvider->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Phone Number:</strong>
                                    <span>{{ $serviceProvider->phone_number }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Date of Birth:</strong>
                                    <span>{{ $serviceProvider->date_of_birth }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Gender:</strong>
                                    <span>{{ ucfirst($serviceProvider->gender) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Status:</strong>
                                    <span class="badge {{ $serviceProvider->is_active ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                        {{ $serviceProvider->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-primary">Address</h4>
                        <p>{{ $serviceProvider->address }}</p>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-primary">Services Offered</h4>
                        <ul class="list-group">

                        </ul>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('scheduler.serviceProvider.edit', $serviceProvider->id) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('scheduler.serviceProvider') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
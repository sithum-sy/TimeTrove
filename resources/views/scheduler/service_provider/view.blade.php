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
                        <div class="d-flex justify-content-end mb-4">
                            <a href="{{ route('scheduler.serviceProvider.edit', $serviceProvider->id) }}" class="btn btn-warning"> <i class="fas fa-edit me-1"></i> Edit Profile</a>
                        </div>
                        <div class="col-md-4 text-center">
                            <img src="{{ asset($serviceProvider->profile_picture) }}" alt="Profile Picture" class="img-fluid rounded-circle mb-3">
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
                                    <strong>Ratings:</strong>
                                    <span><i class="fas fa-star" style="color: gold;"></i>{{ number_format($serviceProvider->averageRating(), 1) }}/5.0
                                        ({{ $serviceProvider->ratingCount() }} {{ Str::plural('review', $serviceProvider->ratingCount()) }})</span>
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
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="text-primary mb-0">Services Offered</h4>
                        </div>
                        @if($serviceProviderServices->isNotEmpty())
                        <div class="row">
                            @foreach($serviceProviderServices as $serviceProviderService)
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">{{ $serviceProviderService->serviceCategory->name }}</h5>

                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <strong>Description:</strong>
                                                <p class="mb-0">{{ $serviceProviderService->description }}</p>
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Availability:</strong>
                                                <p class="mb-0">{{ $serviceProviderService->availability }}</p>
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Rate:</strong>
                                                <p class="mb-0">{{ $serviceProviderService->rate }}</p>
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Working Area/Location:</strong>
                                                <p class="mb-0">{{ $serviceProviderService->city }}</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="alert alert-info" role="alert">
                            No services available.
                        </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('scheduler.serviceProvider') }}" class="btn btn-secondary"><i class="fa-solid fa-backward"></i> Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
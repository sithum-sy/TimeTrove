@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">{{ $client->first_name }} {{ $client->last_name }}</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <img src="{{ asset($client->profile_picture) }}" alt="Profile Picture" class="img-fluid rounded-circle mb-3">
                            <h4 class="text-primary">ID: {{ $client->id }}</h4>
                        </div>
                        <div class="col-md-8">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Email:</strong>
                                    <span>{{ $client->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Phone Number:</strong>
                                    <span>{{ $client->phone_number }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Date of Birth:</strong>
                                    <span>{{ $client->date_of_birth }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong>Gender:</strong>
                                    <span>{{ ucfirst($client->gender) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-primary">Address</h4>
                        <p>{{ $client->address }}</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('client.editProfile') }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('home') }}" class="btn btn-secondary">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
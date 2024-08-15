@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Display Service Request Details -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Service Request Details</h5>
        </div>
        <div class="card-body">
            <p><strong>Request ID:</strong> {{ $serviceRequest->id }}</p>
            <p><strong>Client:</strong> {{ $serviceRequest->client->name }} ({{ $serviceRequest->client->email }})</p>
            <p><strong>Service Category:</strong> {{ $serviceRequest->serviceCategory->name }}</p>
            <p><strong>Date:</strong> {{ $serviceRequest->date }}</p>
            <p><strong>Time:</strong> {{ $serviceRequest->time }}</p>
            <p><strong>Description:</strong> {{ $serviceRequest->description }}</p>
            <p><strong>Status:</strong> {{ ucfirst($serviceRequest->status) }}</p>
        </div>
    </div>

    <!-- Assign Service Providers Form -->
    <div class="card">
        <div class="card-header">
            <h5>Assign Service Providers</h5>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                @method('POST')

                <!-- List of Available Service Providers -->
                <div class="form-group">
                    <label for="service_providers">Select Service Providers</label>
                    <select id="service_providers" name="service_provider_ids[]" multiple class="form-control">
                        @foreach($serviceProviders as $provider)
                        <option value="{{ $provider->id }}">
                            {{ $provider->name }} - {{ $provider->phone_number }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Assign Providers</button>
            </form>
        </div>
    </div>
</div>
@endsection
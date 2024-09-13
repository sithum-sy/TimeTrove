@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Edit Service</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('provider.updateService', $serviceProviderService->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="service_category_id" class="form-label">Service Category</label>
                            <select class="form-select" id="service_category_id" name="service_category_id" required>
                                @foreach($serviceCategories as $serviceCategory)
                                <option value="{{ $serviceCategory->id }}"
                                    {{ $serviceProviderService->service_category_id == $serviceCategory->id ? 'selected' : '' }}>
                                    {{ $serviceCategory->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $serviceProviderService->description) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="availability" class="form-label">Availability</label>
                            <input type="text" class="form-control" id="availability" name="availability" value="{{ old('availability', $serviceProviderService->availability) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="rate" class="form-label">Rate</label>
                            <input type="text" class="form-control" id="rate" name="rate" value="{{ old('rate', $serviceProviderService->rate) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">Date of Birth</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $serviceProviderService->city) }}" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Update Service</button>
                            <a href="{{ route('provider.profileView') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
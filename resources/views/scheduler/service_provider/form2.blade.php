@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <form action="{{ route('scheduler.serviceProvider.store') }}" method="POST">
        @csrf
        <input type="hidden" name="first_name" value="{{ old('first_name', $data['first_name']) }}">
        <input type="hidden" name="last_name" value="{{ old('last_name', $data['last_name']) }}">
        <input type="hidden" name="email" value="{{ old('email', $data['email']) }}">
        <input type="hidden" name="phone_number" value="{{ old('phone_number', $data['phone_number']) }}">
        <input type="hidden" name="date_of_birth" value="{{ old('date_of_birth', $data['date_of_birth']) }}">
        <input type="hidden" name="address" value="{{ old('address', $data['address']) }}">
        <input type="hidden" name="gender" value="{{ old('gender', $data['gender']) }}">

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="service_category_id" class="form-label">Service Category</label>
                    <select class="form-select" id="service_category_id" name="service_category_id" required>
                        @foreach($serviceCategories as $serviceCategory)
                        <option value="{{ $serviceCategory->id }}">{{ $serviceCategory->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-label">Service Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="availability" class="form-label">Availability</label>
                    <input type="text" name="availability" id="availability" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="rate" class="form-label">Rate</label>
                    <input type="number" name="rate" id="rate" class="form-control" step="0.01" required>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="city" class="form-label">City</label>
                    <input type="text" name="city" id="city" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </div>
    </form>
</div>
@endsection
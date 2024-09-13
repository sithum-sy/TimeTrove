@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <form action="{{ route('provider.service.store') }}" method="POST">
        @csrf

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
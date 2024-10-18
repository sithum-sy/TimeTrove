@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Edit Service Request</h2>
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

                    <form action="{{ route('client.updateRequest', $serviceRequest->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="serviceCategory" class="form-label">Service Category</label>
                            <select name="service_category_id" id="serviceCategory" class="form-select" required>
                                @foreach($serviceCategories as $category)
                                <option value="{{ $category->id }}" {{ $category->id == $serviceRequest->service_category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control" value="{{ $serviceRequest->location }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ $serviceRequest->date }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" name="time" id="time" class="form-control" value="{{ $serviceRequest->time }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required>{{ $serviceRequest->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="requestPicture" class="form-label">Request Picture</label>
                            <div class="mb-2">
                                @if($serviceRequest->request_picture)
                                <img src="{{ asset($serviceRequest->request_picture) }}" alt="Request Picture" class="img-fluid rounded mb-2" style="max-width: 100%; height: auto;">
                                @else
                                <p class="text-muted">No picture provided</p>
                                @endif
                            </div>
                            <input type="file" name="request_picture" id="requestPicture" class="form-control">
                            <small class="form-text text-muted">Upload a new picture to replace the existing one.</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Update Request</button>
                            <a href="{{ route('client.singleRequest.view', $serviceRequest->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
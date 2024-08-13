@extends('layouts.app')

@section('title', 'Client Panel')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Client Panel</h1>

    <!-- Add Service Request Button -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addServiceRequestModal">
        Add Service Request
    </button>

    <!-- Service Requests Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service Category</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Request Picture</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceRequests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->serviceCategory->name }}</td>
                    <td>{{ $request->date }}</td>
                    <td>{{ $request->time }}</td>
                    <td>{{ $request->description }}</td>
                    <td>{{ $request->status }}</td>
                    <td>
                        @if($request->request_picture)
                        <img src="{{ asset('storage/' . $request->request_picture) }}" alt="Request Picture" width="100">
                        @endif
                    </td>
                    <td>
                        <a href="" class="btn btn-warning">Edit</a>
                        <form action="{{ route('client.deleteRequest', $request->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Service Request Modal -->
    <div class="modal fade" id="addServiceRequestModal" tabindex="-1" aria-labelledby="addServiceRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceRequestModalLabel">Add Service Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('client.addRequest') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="service_category" class="form-label">Service Category</label>
                            <select class="form-select" id="service_category" name="service_category_id" required>
                                @foreach($serviceCategories as $serviceCategory)
                                <option value="{{ $serviceCategory->id }}">{{ $serviceCategory->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" class="form-control" name="description" rows="4" required autocomplete="description" autofocus>{{ old('description') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="time" name="time" required>
                        </div>
                        <div class="mb-3">
                            <label for="request_picture" class="form-label">Pictures</label>
                            <input type="file" class="form-control" id="request_picture" name="request_picture">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Request Modal -->
    <div class="modal fade" id="editServiceRequestModal" tabindex="-1" aria-labelledby="editServiceRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServiceRequestModalLabel">Edit Service Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editServiceRequestForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_service_category" class="form-label">Service Category</label>
                            <select class="form-select" id="edit_service_category" name="service_category_id" required>
                                @foreach($serviceCategories as $serviceCategory)
                                <option value="{{ $serviceCategory->id }}">{{ $serviceCategory->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea id="edit_description" class="form-control" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="edit_time" name="time" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_request_picture" class="form-label">Pictures</label>
                            <input type="file" class="form-control" id="edit_request_picture" name="request_picture">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle edit button clicks
        const editButtons = document.querySelectorAll('.edit-request');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const requestId = this.getAttribute('data-request-id');
                const form = document.getElementById('editServiceRequestForm');
                form.action = `/client/requests/${requestId}`;

                // Here you would typically fetch the current request data and populate the form
                // For this example, we'll just set the form action
            });
        });
    });
</script>
@endsection
@extends('layouts.app')

@section('title', 'Scheduler Dashboard')

@section('content')
<div class="container-fluid mt-4">
    <h1 class="mb-4">Service Category Control Panel</h1>
    <div class="row">
        <!-- Left Column - Control Panels -->
        <div class="col-md-3 mb-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-success btn-block mb-2" data-bs-toggle="modal" data-bs-target="#scheduleAppointmentModal">
                        <i class="fas fa-calendar-plus"></i> New Service Category
                    </button>
                    <a href="{{ route('scheduler.serviceProvider') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-user-tie"></i> Manage Service Providers
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-calendar-alt"></i> Manage Appointments
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <p><strong>Total Service Categories:</strong> <span id="totalServiceCategories">{{ $statistics['total'] }}</span></p>
                    <p><strong>Active Service Categories:</strong> <span id="activeServiceCategories">{{ $statistics['active'] }}</span></p>
                    <p><strong>Inactive Service Categories:</strong> <span id="inactiveServiceCategories">{{ $statistics['inactive'] }}</span></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>Contact our support team:</p>
                    <p><i class="fas fa-phone"></i> +94 (011) 456-7890</p>
                    <p><i class="fas fa-envelope"></i> support@timetrove.com</p>
                </div>
            </div>
        </div>

        <!-- Right Column - Appointments and Quotations Tables -->
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Service Categories</h5>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($serviceCategories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editServiceCategoryModal">Edit</a>
                                        <form action="{{ route('scheduler.serviceCategories.delete',  $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        <form action="{{ route('scheduler.serviceCategories.status', $category->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn {{ $category->is_active ? 'btn-secondary' : 'btn-success' }} btn-sm">
                                                {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No service categories found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $serviceCategories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Service Category Modal -->
<div class="modal fade" id="scheduleAppointmentModal" tabindex="-1" aria-labelledby="addServiceCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addServiceCategoryModalLabel">Add New Service Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addServiceCategoryForm" method="POST" action="{{ route('scheduler.serviceCategories.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Service Category Modal -->
<div class="modal fade" id="editServiceCategoryModal" tabindex="-1" aria-labelledby="editServiceCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="editServiceCategoryModalLabel">Update Service Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateServiceCategoryForm" method="POST" action="{{ route('scheduler.serviceCategories.update',  $category->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
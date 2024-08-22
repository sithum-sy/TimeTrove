@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h2 class="mb-4">Service Provider Panel</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-header">Task Management</div>
                        <div class="card-body">
                            <a href="#" class="btn btn-primary btn-sm mb-2 w-100">View Assigned Tasks</a>
                            <a href="#" class="btn btn-secondary btn-sm w-100">Completed Tasks</a>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">Profile</div>
                        <div class="card-body">
                            <a href="#" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#profileModal">Update Profile</a>
                        </div>
                    </div>
                    <!-- <div class="card mb-3">
                        <div class="card-header">Earnings</div>
                        <div class="card-body">
                            <a href="#" class="btn btn-primary btn-sm w-100">View Earnings</a>
                        </div>
                    </div> -->
                    <div class="card mb-3">
                        <div class="card-header">Settings</div>
                        <div class="card-body">
                            <a href="#" class="btn btn-primary btn-sm w-100">Account Settings</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card mb-3">
                        <div class="card-header">
                            Assigned Tasks
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Client Name</th>
                                            <th scope="col">Service Category</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Location</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Time</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignedTasks as $task)
                                        <tr>
                                            <th scope="row">{{ $task->id }}</th>
                                            <td>{{ $task->client->first_name }} {{ $task->client->last_name }}</td>
                                            <td>{{ $task->serviceCategory->name }}</td>
                                            <td>{{ $task->description }}</td>
                                            <td>{{ $task->location }}</td>
                                            <td>{{ $task->date }}</td>
                                            <td>{{ $task->time }}</td>
                                            <td>{{ ucfirst($task->status) }}</td>
                                            <td>
                                                @if($task->status === 'assigned')
                                                <a href="{{ route('provider.serviceRequest.view', ['task_id' => $task->id, 'client_id' => $task->client_id]) }}" class="btn btn-sm btn-primary">Manage</a>
                                                @elseif($task->status === 'quoted')
                                                <a href="{{ route('provider.serviceRequest.view', ['task_id' => $task->id, 'client_id' => $task->client_id]) }}" class="btn btn-sm btn-info">View</a>
                                                @elseif($task->status === 'new-quote-requested')
                                                <a href="{{ route('provider.serviceRequest.view', ['task_id' => $task->id, 'client_id' => $task->client_id]) }}" class="btn btn-sm btn-secondary">Requote</a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $assignedTasks->links('vendor.pagination.bootstrap-4') }}

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Statistics</div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">Total Assigned Tasks: <span id="totalAssignedTasks">Loading...</span></li>
                                        <li class="list-group-item">Completed Tasks: <span id="completedTasks">Loading...</span></li>
                                        <li class="list-group-item">This Month's Earnings: $<span id="monthlyEarnings">Loading...</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Recent Activity</div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush" id="recentActivity">
                                        <li class="list-group-item">Loading recent activity...</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quote Modal -->
<div class="modal fade" id="quoteModal" tabindex="-1" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quoteModalLabel">Create Quote</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="quoteForm">
                    <input type="hidden" id="taskId" name="task_id">
                    <div class="mb-3">
                        <label for="quoteAmount" class="form-label">Quote Amount ($)</label>
                        <input type="number" class="form-control" id="quoteAmount" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="quoteDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="quoteDescription" name="description" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveQuote">Save and Send Quote</button>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Update Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="profileForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="skills" class="form-label">Skills</label>
                        <textarea class="form-control" id="skills" name="skills" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveProfile">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Populate statistics
        document.getElementById('totalAssignedTasks').textContent = '10';
        document.getElementById('completedTasks').textContent = '5';
        document.getElementById('monthlyEarnings').textContent = '1500';

        // Handle quote creation
        document.getElementById('saveQuote').addEventListener('click', function() {
            // Add logic to save quote as PDF and send to scheduler
            alert('Quote saved and sent to scheduler!');
            $('#quoteModal').modal('hide');
        });

        // Handle profile update
        document.getElementById('saveProfile').addEventListener('click', function() {
            // Add logic to update profile
            alert('Profile updated successfully!');
            $('#profileModal').modal('hide');
        });

        // Set task ID when opening quote modal
        $('#quoteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var taskId = button.data('task-id');
            $('#taskId').val(taskId);
        });
    });
</script>
@endsection
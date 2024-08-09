<div class="container mt-4">
    <h2 class="mb-4">Scheduler Control Panel</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Appointment Management
                </div>
                <div class="card-body">
                    <h5 class="card-title">Schedule Appointment</h5>
                    <p class="card-text">Create a new appointment or manage existing ones.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scheduleAppointmentModal">
                        Schedule Appointment
                    </button>
                    <a href="" class="btn btn-secondary">View Appointments</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Client Management
                </div>
                <div class="card-body">
                    <h5 class="card-title">Manage Clients</h5>
                    <p class="card-text">View and manage client information.</p>
                    <a href="" class="btn btn-primary">Manage Clients</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Payment Management
                </div>
                <div class="card-body">
                    <h5 class="card-title">Handle Payments</h5>
                    <p class="card-text">Process payments and view transaction history.</p>
                    <a href="" class="btn btn-primary">Manage Payments</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Reminders
                </div>
                <div class="card-body">
                    <h5 class="card-title">Send Reminders</h5>
                    <p class="card-text">Send appointment reminders to clients.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendReminderModal">
                        Send Reminder
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Service Provider Management
                </div>
                <div class="card-body">
                    <h5 class="card-title">Manage Service Providers</h5>
                    <p class="card-text">View and manage service provider information and schedules.</p>
                    <a href="" class="btn btn-primary">Manage Service Providers</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Statistics
                </div>
                <div class="card-body">
                    <h5 class="card-title">Overview</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Total Appointments: <span id="totalAppointments">Loading...</span></li>
                        <li class="list-group-item">Upcoming Appointments: <span id="upcomingAppointments">Loading...</span></li>
                        <li class="list-group-item">Total Clients: <span id="totalClients">Loading...</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    Upcoming Appointments
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Client Name</th>
                                <th scope="col">Service</th>
                                <th scope="col">Date</th>
                                <th scope="col">Time</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    Recent Activity
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="recentActivity">
                        <li class="list-group-item">Loading recent activity...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal for Scheduling Appointment -->
<div class="modal fade" id="scheduleAppointmentModal" tabindex="-1" aria-labelledby="scheduleAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleAppointmentModalLabel">Schedule Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleAppointmentForm" method="POST" action="">
                    @csrf
                    <!-- Add form fields for scheduling an appointment -->
                    <div class="mb-3">
                        <label for="client" class="form-label">Client</label>
                        <select class="form-select" id="client" name="client_id" required>
                            <!-- Populate with clients from the database -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label">Service</label>
                        <select class="form-select" id="service" name="service_id" required>
                            <!-- Populate with services from the database -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="time" name="time" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Sending Reminder -->
<div class="modal fade" id="sendReminderModal" tabindex="-1" aria-labelledby="sendReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendReminderModalLabel">Send Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendReminderForm" method="POST" action="">
                    @csrf
                    <div class="mb-3">
                        <label for="appointment" class="form-label">Appointment</label>
                        <select class="form-select" id="appointment" name="appointment_id" required>
                            <!-- Populate with upcoming appointments from the database -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Reminder Message</label>
                        <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reminder</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Populate statistics
        document.getElementById('totalAppointments').textContent = '123';
        document.getElementById('upcomingAppointments').textContent = '45';
        document.getElementById('totalClients').textContent = '67';

        // Handle appointment scheduling form submission
        document.getElementById('scheduleAppointmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add logic to handle form submission
            alert('Appointment scheduled successfully!');
            $('#scheduleAppointmentModal').modal('hide');
        });

        // Handle reminder form submission
        document.getElementById('sendReminderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add logic to handle form submission
            alert('Reminder sent successfully!');
            $('#sendReminderModal').modal('hide');
        });
    });
</script>
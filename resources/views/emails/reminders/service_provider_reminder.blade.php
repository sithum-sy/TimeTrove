<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Provider Reminder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
        }
    </style>
</head>

<body>
    <div class="container" style="max-width: 600px; margin: 0 auto;">
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-primary text-white text-center py-4">
                <h1 class="h3 mb-0">Service Request Reminder</h1>
            </div>
            <div class="card-body p-4">
                <p class="lead">Dear {{ $serviceRequest->serviceProvider->first_name }},</p>
                <p>This is a friendly reminder that you have a service request scheduled for <strong>{{ $serviceRequest->date }}</strong> at <strong>{{ $serviceRequest->time }}</strong>.</p>

                <div class="alert alert-info mt-4" role="alert">
                    <h4 class="alert-heading">Service Request Details:</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Client:</strong> {{ $serviceRequest->client->first_name }} {{ $serviceRequest->client->last_name }}</p>
                            <p><strong>Service:</strong> {{ $serviceRequest->serviceCategory->name }}</p>
                            <p><strong>Date:</strong> {{ $serviceRequest->date }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Time:</strong> {{ $serviceRequest->time }}</p>
                            <p><strong>Location:</strong> {{ $serviceRequest->location }}</p>
                            <p><strong>Client Phone:</strong> {{ $serviceRequest->client->phone_number }}</p>
                        </div>
                    </div>
                    <p class="mb-0"><strong>Description:</strong> {{ $serviceRequest->description }}</p>
                </div>

                <div class="alert alert-warning mt-4" role="alert">
                    <h5 class="alert-heading">Important Reminders:</h5>
                    <ul>
                        <li>Please arrive on time or a few minutes early.</li>
                        <li>Bring all necessary equipment and supplies.</li>
                        <li>If you encounter any issues or delays, please contact us immediately.</li>
                    </ul>
                </div>

                <p class="mt-4">If you have any questions or need to make changes, please contact our support team as soon as possible.</p>
                <p>Thank you for your dedicated service!</p>
                <p>Best regards,<br>TimeTrove Team</p>
            </div>
            <div class="card-footer text-center text-muted py-3">
                &copy; 2024 TimeTrove. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
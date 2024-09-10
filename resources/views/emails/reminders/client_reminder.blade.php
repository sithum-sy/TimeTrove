<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Reminder</title>
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
                <p class="lead">Dear {{ $serviceRequest->client->first_name }},</p>
                <p>This is a friendly reminder that you have a service request scheduled for <strong>{{ $serviceRequest->date }}</strong> at <strong>{{ $serviceRequest->time }}</strong>.</p>

                <div class="alert alert-info mt-4" role="alert">
                    <h4 class="alert-heading">Service Request Details:</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Service:</strong> {{ $serviceRequest->serviceCategory->name }}</p>
                            <p><strong>Date:</strong> {{ $serviceRequest->date }}</p>
                            <p><strong>Time:</strong> {{ $serviceRequest->time }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Location:</strong> {{ $serviceRequest->location }}</p>
                            <p><strong>Provider:</strong> {{ $serviceRequest->serviceProvider->first_name }} {{ $serviceRequest->serviceProvider->last_name }}</p>
                        </div>
                    </div>
                    <p class="mb-0"><strong>Description:</strong> {{ $serviceRequest->description }}</p>
                </div>

                <p class="mt-4">If you need to make any changes or have any questions, please don't hesitate to contact us.</p>
                <p>We look forward to serving you!</p>
                <p>Best regards,<br>TimeTrove Team</p>
            </div>
            <div class="card-footer text-center text-muted py-3">
                &copy; 2024 TimeTrove. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
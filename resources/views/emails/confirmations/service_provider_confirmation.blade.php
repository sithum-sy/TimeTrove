<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Request Confirmation</title>
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
                <h1 class="h3 mb-0">Service Request Confirmed</h1>
            </div>
            <div class="card-body p-4">
                <p class="lead">Dear {{ $serviceRequest->serviceProvider->first_name }},</p>
                <p>The following service request has been confirmed and assigned to you:</p>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row" class="text-primary">Client:</th>
                                <td>{{ $serviceRequest->client->first_name }} {{ $serviceRequest->client->last_name }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-primary">Service Category:</th>
                                <td>{{ $serviceRequest->serviceCategory->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-primary">Description:</th>
                                <td>{{ $serviceRequest->description }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-primary">Location:</th>
                                <td>{{ $serviceRequest->location }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-primary">Date:</th>
                                <td>{{ $serviceRequest->date }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-primary">Time:</th>
                                <td>{{ $serviceRequest->time }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-4">Please provide the following security code to the customer before beginning the service:
                <h5><strong>{{ $securityCode }}</strong></h5>
                </p>

                <p class="mt-4">Thank you</p>
                <p>Best regards,<br>TimeTrove Team</p>
            </div>
            <div class="card-footer text-center text-muted py-3">
                &copy; 2024 TimeTrove. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
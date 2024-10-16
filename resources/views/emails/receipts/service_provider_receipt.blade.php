<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Completion Receipt</title>
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
            <div class="card-header bg-success text-white text-center py-4">
                <h1 class="h3 mb-0">Service Request Completed</h1>
            </div>
            <div class="card-body p-4">
                <p class="lead">Dear {{ $serviceRequest->serviceProvider->first_name }},</p>
                <p>You have successfully completed the following service request:</p>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row" class="text-success">Service Category:</th>
                                <td>{{ $serviceRequest->serviceCategory->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Description:</th>
                                <td>{{ $serviceRequest->description }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Location:</th>
                                <td>{{ $serviceRequest->location }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Date:</th>
                                <td>{{ $serviceRequest->date }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Time:</th>
                                <td>{{ $serviceRequest->time }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Client:</th>
                                <td>{{ $serviceRequest->client->first_name }} {{ $serviceRequest->client->last_name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h4 class="mt-4">Invoice Details</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row" class="text-success">Actual Hours Worked:</th>
                                <td>{{ $invoice->actual_hours }} hours</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Hourly Rate:</th>
                                <td>Rs.{{ number_format($invoice->final_hourly_rate, 2) }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Materials Cost:</th>
                                <td>Rs.{{ number_format($invoice->final_materials_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Additional Charges:</th>
                                <td>Rs.{{ number_format($invoice->final_additional_charges, 2) }}</td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Total Amount:</th>
                                <td><strong>Rs.{{ number_format($invoice->final_total_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-success">Invoice Notes:</th>
                                <td>{{ $invoice->invoice_notes }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="mt-4">Thank you for your professional service!</p>
                <p>Best regards,<br>TimeTrove Team</p>
            </div>
            <div class="card-footer text-center text-muted py-3">
                &copy; 2024 TimeTrove. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <title>Client Reminder</title>
</head>

<body>
    <p>Dear {{ $serviceRequest->client->first_name }},</p>
    <p>This is a reminder that you have a service request scheduled for {{ $serviceRequest->date }}.</p>
    <p>Best regards,<br>TimeTrove Team</p>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <title>Service Provider Reminder</title>
</head>

<body>
    <p>Dear {{ $serviceRequest->serviceProvider->first_name }},</p>
    <p>This is a reminder that you have a service request scheduled for {{ $serviceRequest->date }}.</p>
    <p>Best regards,<br>TimeTrove Team</p>
</body>

</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Guide Application Status</title>
</head>
<body>
    <h2>Hello {{ $guideName }},</h2>

    <p>Your guide application status is now: <strong>{{ ucfirst($status) }}</strong>.</p>
    <p>{{ $messageText }}</p>

    <p>Thank you,<br>Travel Planner Team</p>
</body>
</html>

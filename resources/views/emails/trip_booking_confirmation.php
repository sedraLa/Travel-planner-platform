<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Trip Booking Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f6f6f6; padding:20px;">

    <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:10px;">

        <h2 style="color:#1e40af;">
            ✈️ Your Trip is Confirmed!
        </h2>

        <p>Hi <strong>{{ $user->name }}</strong>,</p>

        <p>
            Good news — your trip booking has been successfully confirmed.
            Get ready, this isn’t just a trip… it’s a planned escape from your normal life.
        </p>

        <hr>

        <h3> Trip Details</h3>

        <p><strong>Trip:</strong> {{ $trip->name }}</p>
        <p><strong>Destination:</strong> {{ $trip->primaryDestination?->name }}</p>
        <p><strong>Package:</strong> {{ $package->name }}</p>
        <p><strong>Schedule:</strong> {{ $schedule->start_date }} → {{ $schedule->end_date }}</p>

        <p><strong>People:</strong> {{ $reservation->people_count }}</p>
        <p><strong>Total Paid:</strong> ${{ number_format($reservation->total_price, 2) }}</p>

        <hr>

        <h3> What happens next?</h3>
        <ul>
            <li>You will receive trip instructions before departure</li>
            <li>Your guide will be assigned shortly</li>
            <li>All trip details will be available in your account</li>
        </ul>

        <p style="margin-top:20px;">
            Pack light. The system already did all the complicated thinking for you.
        </p>

        <br>

        <p style="color:#888; font-size:12px;">
            This is an automated booking confirmation. Please do not reply.
        </p>

    </div>

</body>
</html>
<h2>Transport Reservation Payment Confirmation</h2>

<p>Hello {{ $reservation->user->name }},</p>

<p>Your payment for your transport reservation has been successfully completed.</p>

<h3>Reservation Details:</h3>
<ul>
    <li><strong>Service:</strong> {{ $reservation->transport->name }}</li>
    <li><strong>Plate Number:</strong> {{ $reservation->vehicle->plate_number }}</li>
<li><strong>Car Model:</strong> {{ $reservation->vehicle->car_model }}</li>
<li><strong>Driver:</strong> {{ $reservation->driver->name ?? 'Auto-assigned' }}</li>

    <li><strong>Total Price:</strong> ${{ number_format($reservation->total_price, 2) }}</li>
    <li><strong>Status:</strong> {{ ucfirst($reservation->status) }}</li>
</ul>

<h3>Trip Information:</h3>
<ul>
    <li><strong>Pickup Location:</strong> {{ $reservation->pickup_location }}</li>
    <li><strong>Drop-off Location:</strong> {{ $reservation->dropoff_location }}</li>
    <li><strong>Pickup Time:</strong> {{ $reservation->pickup_datetime }}</li>
    <li><strong>Drop-off Time:</strong> {{ $reservation->dropoff_datetime }}</li>
    <li><strong>Passengers:</strong> {{ $reservation->passengers }}</li>
    <li><strong>Distance:</strong> {{ $reservation->distance ?? 'N/A' }} km</li>
    <li><strong>Driver:</strong> {{ $reservation->driver->name ?? 'Auto-assigned' }}</li>
</ul>

<p>Thank you for choosing our transport services!</p>

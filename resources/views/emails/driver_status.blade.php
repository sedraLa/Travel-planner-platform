<h1>Welocome to Triplry Website</h1>
<h2>Driver Application Status</h2>

<p>Dear {{ $driverName }},</p>

<p>Your driver account request has been reviewed.</p>

<p>
    <strong>Status:</strong>
    @if ($status === 'approved')
        ✅ Approved
    @elseif ($status === 'rejected')
        ❌ Rejected
    @else
        ⏳ Pending
    @endif
</p>

@if(!empty($messageText))
    <p>{{ $messageText }}</p>
@endif

<p>Thank you for your patience.</p>
<p>The Admin Team</p>
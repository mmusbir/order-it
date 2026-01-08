<!DOCTYPE html>
<html>

<head>
    <title>Request Status Update</title>
</head>

<body style="font-family: Arial, sans-serif;">
    <h2>Order-IT Request Update</h2>
    <p>Hello {{ $requestData->requester->name }},</p>

    <p>Your request <strong>#{{ $requestData->ticket_no }}</strong> has been updated.</p>

    <p><strong>New Status:</strong> <span style="color: blue;">{{ str_replace('_', ' ', $requestData->status) }}</span>
    </p>

    <p>You can view the details of your request by clicking the link below:</p>
    <p>
        <a href="{{ route('requests.show', $requestData->id) }}">View Request</a>
    </p>

    <p>Thank you,<br>The Order-IT Team</p>
</body>

</html>
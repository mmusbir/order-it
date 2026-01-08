<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Request Detail - Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
        }

        h1 {
            color: #4F46E5;
        }
    </style>
</head>

<body>
    <h1>Request Detail - {{ $request->ticket_no }}</h1>
    <p><strong>Status:</strong> {{ $request->status }}</p>
    <p><strong>Requester:</strong> {{ $request->requester->name }}</p>
</body>

</html>
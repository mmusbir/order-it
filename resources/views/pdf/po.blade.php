<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $requestData->po_number }}</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .details {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .signature-box {
            border: 2px solid #000;
            padding: 20px;
            margin-top: 50px;
            width: 300px;
            text-align: center;
        }

        .qr {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>PURCHASE ORDER</h1>
        <h3>Order-IT Procurement</h3>
    </div>

    <div class="details">
        <p><strong>PO Number:</strong> {{ $requestData->po_number }}</p>
        <p><strong>Ticket Ref:</strong> {{ $requestData->ticket_no }}</p>
        <p><strong>Date:</strong> {{ date('d F Y') }}</p>
        <p><strong>Requester:</strong> {{ $requestData->requester->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Spec</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requestData->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($item->product->specs, 50) }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->snap_price, 0) }}</td>
                    <td>{{ number_format($item->snap_price * $item->qty, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Grand Total</td>
                <td style="font-weight: bold;">Rp {{ number_format($requestData->total_amount, 0) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-box">
        <p style="font-weight: bold;">APPROVED BY</p>
        <p style="font-size: 24px;">&#10003;</p> <!-- Checkmark -->
        <p>Digitally Signed</p>
        <p>Date: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <div class="qr">
        <!-- Mock QR Code -->
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ $requestData->ticket_no }}" alt="QR">
    </div>

</body>

</html>
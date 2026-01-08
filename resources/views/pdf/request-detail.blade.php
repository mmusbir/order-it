<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Request Detail - {{ $request->ticket_no }}</title>
    <style>
        @page {
            margin: 20mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18pt;
            color: #4F46E5;
        }

        .header .ticket-no {
            font-size: 14pt;
            color: #666;
            margin-top: 5px;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 5px;
            background-color: #F3F4F6;
        }

        .info-value {
            display: table-cell;
            padding: 5px;
            border-bottom: 1px solid #E5E7EB;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #4F46E5;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #4F46E5;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 9pt;
        }

        table tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .status-completed {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-rejected {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .handover-section {
            background-color: #DBEAFE;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            page-break-inside: avoid;
        }

        .handover-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1E40AF;
            margin-bottom: 10px;
        }

        .handover-photo {
            max-width: 400px;
            max-height: 300px;
            border: 2px solid #3B82F6;
            border-radius: 5px;
            margin: 10px 0;
        }

        .handover-info {
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #E5E7EB;
            padding-top: 5px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>REQUEST DETAIL</h1>
        <div class="ticket-no">Request ID #{{ $request->ticket_no }}</div>
        <div style="font-size: 9pt; color: #999; margin-top: 5px;">
            Printed on {{ \Carbon\Carbon::now()->format('d M Y, H:i') }} WIB
        </div>
    </div>

    {{-- Request Information --}}
    <div class="section">
        <div class="section-title">Request Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge 
                        @if($request->status == 'COMPLETED') status-completed
                        @elseif($request->status == 'REJECTED') status-rejected
                        @else status-pending @endif">
                        {{ str_replace('_', ' ', $request->status) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Request Type</div>
                <div class="info-value">{{ str_replace('_', ' ', $request->request_type) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Requester</div>
                <div class="info-value">{{ $request->requester->name }} ({{ $request->requester->email }})</div>
            </div>
            <div class="info-row">
                <div class="info-label">Department</div>
                <div class="info-value">{{ $request->requester->department ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Beneficiary</div>
                <div class="info-value">{{ $request->beneficiary_name }} ({{ $request->beneficiary_type }})</div>
            </div>
            <div class="info-row">
                <div class="info-label">Shipping Address</div>
                <div class="info-value">{{ $request->shipping_address ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Created Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y, H:i') }} WIB
                </div>
            </div>
            @if($request->po_number)
                <div class="info-row">
                    <div class="info-label">PO Number</div>
                    <div class="info-value">{{ $request->po_number }}</div>
                </div>
            @endif
            @if($request->courier)
                <div class="info-row">
                    <div class="info-label">Ekspedisi</div>
                    <div class="info-value">{{ $request->courier }}</div>
                </div>
            @endif
            @if($request->tracking_no)
                <div class="info-row">
                    <div class="info-label">No. Resi</div>
                    <div class="info-value">{{ $request->tracking_no }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Requested Items --}}
    <div class="section">
        <div class="section-title">Requested Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Product</th>
                    <th style="width: 35%;">Specifications</th>
                    <th style="width: 15%;">Asset Name</th>
                    <th style="width: 15%;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($request->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->product->specs ?? '-' }}</td>
                        <td>{{ $item->asset_name ?? '-' }}</td>
                        <td>{{ $item->qty }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Synced Asset Details (for NEW_HIRE and NEW_REPLACEMENT only) --}}
    @if(in_array($request->request_type, ['NEW_HIRE', 'NEW_REPLACEMENT']) && $request->items->where('is_synced', true)->count() > 0)
        <div class="section">
            <div class="section-title">Synced Asset Details (Snipe-IT)</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 20%;">Asset Tag</th>
                        <th style="width: 20%;">Serial Number</th>
                        <th style="width: 25%;">Item Name</th>
                        <th style="width: 15%;">Location</th>
                        <th style="width: 15%;">Synced At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($request->items->where('is_synced', true) as $index => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->asset_tag ?? '-' }}</td>
                            <td>{{ $item->serial_number ?? '-' }}</td>
                            <td>{{ $item->synced_item_name ?? $item->asset_name ?? '-' }}</td>
                            <td>{{ $item->synced_location_name ?? '-' }}</td>
                            <td>{{ $item->synced_at ? \Carbon\Carbon::parse($item->synced_at)->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 10px; padding: 8px; background-color: #E0F2FE; border-radius: 4px; font-size: 8pt;">
                <strong>Note:</strong> Asset details above have been synced to Snipe-IT asset management system.
            </div>
        </div>
    @endif

    {{-- Consumable Checkout Details (for NEW_CONSUMABLE only) --}}
    @if($request->request_type === 'NEW_CONSUMABLE' && $request->items->where('is_synced', true)->count() > 0)
        <div class="section">
            <div class="section-title">Consumable Checkout Details (Snipe-IT)</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Consumable Name</th>
                        <th style="width: 30%;">Checkout To</th>
                        <th style="width: 15%;">Qty</th>
                        <th style="width: 20%;">Checkout Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($request->items->where('is_synced', true) as $index => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->synced_item_name ?? $item->product->name ?? '-' }}</td>
                            <td>{{ $item->synced_location_name ?? '-' }}</td>
                            <td>{{ $item->synced_qty ?? $item->qty ?? 1 }}</td>
                            <td>{{ $item->synced_at ? \Carbon\Carbon::parse($item->synced_at)->format('d M Y, H:i') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 10px; padding: 8px; background-color: #FEF3C7; border-radius: 4px; font-size: 8pt;">
                <strong>Note:</strong> Consumables above have been checked out to users via Snipe-IT system.
            </div>
        </div>
    @endif

    {{-- Approval History --}}
    @if($request->logs && $request->logs->count() > 0)
        <div class="section">
            <div class="section-title">Approval History</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">Date</th>
                        <th style="width: 25%;">Approver</th>
                        <th style="width: 15%;">Level</th>
                        <th style="width: 15%;">Action</th>
                        <th style="width: 20%;">Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($request->logs->sortBy('created_at') as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>
                            <td>{{ $log->level ?? '-' }}</td>
                            <td>
                                <span class="status-badge 
                                                                    @if($log->action == 'APPROVE') status-completed
                                                                    @elseif($log->action == 'REJECT') status-rejected
                                                                    @else status-pending @endif">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>{{ $log->comments ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Asset Handover Information --}}
    @if($request->status == 'COMPLETED' && $request->asset_photo_path && $request->e_form_confirmed_at)
        <div class="page-break"></div>
        <div class="handover-section">
            <div class="handover-title">✓ Asset Handover Information</div>

            <div style="margin-bottom: 15px;">
                <strong>Asset Photo Received:</strong><br>
                <img src="{{ public_path('storage/' . $request->asset_photo_path) }}" alt="Asset Photo"
                    class="handover-photo">
            </div>

            <div class="handover-info">
                <table style="border: none;">
                    <tr style="background: none;">
                        <td style="border: none; width: 30%; font-weight: bold;">Confirmed By:</td>
                        <td style="border: none;">{{ $request->requester->name }}</td>
                    </tr>
                    <tr style="background: none;">
                        <td style="border: none; font-weight: bold;">Confirmation Date:</td>
                        <td style="border: none;">
                            {{ \Carbon\Carbon::parse($request->e_form_confirmed_at)->format('d M Y, H:i') }} WIB
                        </td>
                    </tr>
                    <tr style="background: none;">
                        <td style="border: none; font-weight: bold;">E-Form Status:</td>
                        <td style="border: none;">
                            <span class="status-badge status-completed">CONFIRMED</span>
                        </td>
                    </tr>
                </table>

                <div
                    style="background-color: #DBEAFE; padding: 10px; border-radius: 5px; margin-top: 10px; font-size: 9pt;">
                    <strong>Declaration:</strong> The requester has confirmed that the asset was received in good condition
                    and matches the specifications stated in this request.
                </div>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>This is a system-generated document from Order IT System | Bina Artha Ventura</p>
    </div>
</body>

</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 15px;
        }
        .header .company-name {
            color: #1e3a8a;
            font-size: 18px;
            font-weight: bold;
        }
        .header .company-details {
            font-size: 10px;
            color: #666;
        }
        .header h1 {
            color: #1e3a8a;
            margin: 5px 0 0;
            font-size: 20px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
            font-size: 12px;
        }
        .filters {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 11px;
        }
        .filters span {
            margin-right: 15px;
        }
        .summary {
            background: #e9ecef;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .summary strong {
            font-size: 14px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        .table th {
            background: #1e3a8a;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        .table tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge-success {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #6c757d;
        }
        .footer .report-details {
            margin-top: 10px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Keretapi Tanah Melayu Berhad</div>
        <div class="company-details">
            KTMB Headquarters, Jalan Sultan Hishamuddin, 50621 Kuala Lumpur<br>
            Company Registration No: 199101015631 | SST No: W10-1808-31002103
        </div>
        <h1>{{ $title }}</h1>
        <p>Generated: {{ $date_generated }}</p>
    </div>

    <div class="filters">
        <span><strong>Filters Applied:</strong></span>
        @if($filters['start_date'])
            <span>Start: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}</span>
        @endif
        @if($filters['end_date'])
            <span>End: {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}</span>
        @endif
        @if($filters['module'] && $filters['module'] != 'all')
            <span>Module: {{ $filters['module'] }}</span>
        @endif
        @if($filters['status'] && $filters['status'] != 'all')
            <span>Status: {{ $filters['status'] }}</span>
        @endif
    </div>

    <div class="summary">
        <strong>Total Logs: {{ $total_logs }}</strong>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Timestamp</th>
                <th>Username</th>
                <th>Module</th>
                <th>Action</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditLogs as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    @php
                        $timestamp = $log->timestamp instanceof \Carbon\Carbon 
                            ? $log->timestamp 
                            : \Carbon\Carbon::parse($log->timestamp);
                    @endphp
                    {{ $timestamp->format('Y-m-d H:i:s') }}
                </td>
                <td>{{ $log->username }}</td>
                <td>{{ $log->module }}</td>
                <td>{{ $log->action }}</td>
                <td>{{ $log->description }}</td>
                <td>
                    <span class="badge-{{ $log->status == 'Success' ? 'success' : 'danger' }}">
                        {{ $log->status }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="report-details">
            <strong>Report Details:</strong>
            Total Logs: {{ $total_logs }} | Generated: {{ $date_generated }}
        </div>
        <p>&copy; {{ date('Y') }} Keretapi Tanah Melayu Berhad. All rights reserved.</p>
        <p>This is a system-generated audit log report. Please do not reply to this document.</p>
    </div>
</body>
</html>
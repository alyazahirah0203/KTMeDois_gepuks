<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { color: #1e3a8a; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th { background: #1e3a8a; color: white; padding: 10px; text-align: left; }
        .table td { border: 1px solid #ddd; padding: 8px; }
        .table tr:nth-child(even) { background: #f9fafb; }
        .text-end { text-align: right; }
        .summary { margin-top: 20px; padding: 10px; background: #f1f5f9; border-radius: 5px; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KTM eDOIS - {{ $title }}</h2>
        <p>Generated: {{ $date_generated }}</p>
    </div>
    
    <div class="summary">
        <p><strong>Total Invoices:</strong> {{ $total_invoices }} | <strong>Total Amount:</strong> RM {{ number_format($total_amount, 2) }}</p>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Vendor</th>
                <th>Date</th>
                <th class="text-end">Total (RM)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_no }}</td>
                <td>{{ $invoice->vendor_name }}</td>
                <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                <td class="text-end">{{ number_format($invoice->total, 2) }}</td>
                <td>{{ $invoice->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        &copy; {{ date('Y') }} KTM eDOIS. All rights reserved.
    </div>
</body>
</html>
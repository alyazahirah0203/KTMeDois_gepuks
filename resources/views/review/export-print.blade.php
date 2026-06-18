<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
        body { background: white; padding: 20px; }
        .header { border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { color: #1e3a8a; }
    </style>
</head>
<body>
    <div class="header">
        <h2>KTM eDOIS - Invoice Report</h2>
        <p>Generated: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6>Total Invoices</h6>
                    <h3>{{ $invoices->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6>Total Amount</h6>
                    <h3>RM {{ number_format($invoices->sum('total'), 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6>Paid</h6>
                    <h3>{{ $invoices->where('status', 'Paid')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h3>{{ $invoices->where('status', 'Submitted')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <table class="table table-bordered">
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
    
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print</button>
        <a href="{{ route('review.export') }}" class="btn btn-secondary">Back</a>
    </div>
</body>
</html>
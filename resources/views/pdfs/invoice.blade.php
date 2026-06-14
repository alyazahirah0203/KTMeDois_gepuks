<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .company-details { font-size: 10px; }
        .invoice-title { font-size: 24px; font-weight: bold; text-align: center; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .totals-table { width: 300px; float: right; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 10px; text-align: center; }
        .payment-details { margin-top: 20px; padding: 10px; background: #f9f9f9; }
        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="company-details">
            {{ $company['address'] }}<br>
            Company Registration No: {{ $company['reg_no'] }}<br>
            SST No: {{ $company['sst_no'] }}<br>
            Tel: {{ $company['tel'] }} | Web: {{ $company['website'] }}
        </div>
    </div>

    <div class="invoice-title">INVOICE</div>

    <table>
        <tr>
            <td width="50%">
                <strong>Bill-to</strong><br>
                {{ $vendor->supplier_comp_name ?? 'N/A' }}<br>
                {{ $vendor->supplierid ?? 'N/A' }}
            </td>
            <td width="50%">
                <strong>Ship-to</strong><br>
                {{ $do->shipping_address ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="50%">
                <strong>Invoice No:</strong> {{ $invoice->invoice_no }}<br>
                <strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d-M-Y') }}<br>
                <strong>UUID:</strong> {{ $invoice->uuid }}<br>
                <strong>Customer No:</strong> {{ $invoice->customer_no }}
            </td>
            <td width="50%">
                <strong>Line Total:</strong> {{ number_format($invoice->line_total, 2) }}<br>
                <strong>Service Tax:</strong> {{ number_format($invoice->service_tax, 2) }}<br>
                <strong>Shipping:</strong> {{ number_format($invoice->shipping, 2) }}<br>
                <strong>Total:</strong> <strong>{{ number_format($invoice->total, 2) }}</strong>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Product</th>
                <th>Description</th>
                <th>UOM</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product_code }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->uom }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-table">
        <table>
            <tr><td>Line Total</td><td class="text-right">{{ number_format($invoice->line_total, 2) }}</td></tr>
            @if($invoice->discount > 0)
            <tr><td>Discount (Credit Note)</td><td class="text-right">- {{ number_format($invoice->discount, 2) }}</td></tr>
            @endif
            @if($invoice->penalty > 0)
            <tr><td>Penalty (Late Delivery 1%)</td><td class="text-right">- {{ number_format($invoice->penalty, 2) }}</td></tr>
            @endif
            <tr><td>Service Tax (6%)</td><td class="text-right">{{ number_format($invoice->service_tax, 2) }}</td></tr>
            <tr style="font-weight:bold;"><td>Total</td><td class="text-right">{{ number_format($invoice->total, 2) }}</td></tr>
        </table>
    </div>

    <div class="clearfix"></div>

    <div class="payment-details">
        <strong>Please make payment to:</strong><br>
        Account Name: {{ $payment_details['account_name'] }}<br>
        Acc. No: {{ $payment_details['account_no'] }}<br>
        Bank Name: {{ $payment_details['bank_name'] }}
    </div>

    <div class="footer">
        <strong>Payment Terms:</strong> {{ $invoice->payment_terms }} | Due Date: {{ $invoice->due_date->format('d-M-Y') }}<br>
        Thank you for your business!
    </div>
</body>
</html>
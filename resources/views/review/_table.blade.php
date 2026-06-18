<div class="table-responsive">
    <table class="table table-hover" id="datatable-{{ $status }}">
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice Number</th>
                <th>Vendor</th>
                <th>Date</th>
                <th>Total (RM)</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $index => $invoice)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <span class="fw-bold">{{ $invoice->invoice_no }}</span>
                    <br>
                    <small class="text-muted">{{ $invoice->uuid }}</small>
                </td>
                <td>{{ $invoice->vendor_name }}</td>
                <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                <td class="fw-bold">RM {{ number_format($invoice->total, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $invoice->status == 'Submitted' ? 'warning' : ($invoice->status == 'Finance Review' ? 'info' : ($invoice->status == 'Payment Processing' ? 'primary' : 'success')) }}">
                        {{ $invoice->status }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('review.invoice.show', $invoice->invoice_id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye me-1"></i> 
                        @if($status == 'pending')
                            Review
                        @else
                            View
                        @endif
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                    No invoices found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
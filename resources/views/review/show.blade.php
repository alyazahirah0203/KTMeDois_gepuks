@extends('layouts.officer')

@section('title', 'Review Invoice')
@section('page-title', 'Review Invoice: ' . $invoice->invoice_no)

@section('content')
<div class="card-modern bg-white">
    <div class="card-header-custom">
        <i class="fas fa-file-invoice me-2"></i> Invoice Details
    </div>
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_no }}</p>
                <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d-m-Y') }}</p>
                <p><strong>Due Date:</strong> {{ $invoice->due_date->format('d-m-Y') }}</p>
                <p><strong>Vendor:</strong> {{ $invoice->vendor_name }}</p>
                <p><strong>DO Number:</strong> {{ $invoice->deliveryOrder->do_number ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $invoice->status == 'Submitted' ? 'warning' : ($invoice->status == 'Finance Review' ? 'info' : ($invoice->status == 'Payment Processing' ? 'primary' : 'success')) }}">
                        {{ $invoice->status }}
                    </span>
                </p>
                <p><strong>Line Total:</strong> RM {{ number_format($invoice->line_total, 2) }}</p>
                <p><strong>Tax:</strong> RM {{ number_format($invoice->service_tax, 2) }}</p>
                <p><strong>Total:</strong> RM {{ number_format($invoice->total, 2) }}</p>
                <p><strong>Balance Due:</strong> RM {{ number_format($invoice->balance_due, 2) }}</p>
            </div>
        </div>

        <hr>

        <h5>Invoice Items</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->product_code }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="text-end">RM {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">RM {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Line Total:</th>
                    <th class="text-end">RM {{ number_format($invoice->line_total, 2) }}</th>
                </tr>
                @if($invoice->discount > 0)
                <tr>
                    <th colspan="5" class="text-end text-success">Discount (Credit Note):</th>
                    <th class="text-end text-success">- RM {{ number_format($invoice->discount, 2) }}</th>
                </tr>
                @endif
                @if($invoice->penalty > 0)
                <tr>
                    <th colspan="5" class="text-end text-danger">Penalty (Late 1%):</th>
                    <th class="text-end text-danger">- RM {{ number_format($invoice->penalty, 2) }}</th>
                </tr>
                @endif
                <tr>
                    <th colspan="5" class="text-end">Service Tax (6%):</th>
                    <th class="text-end">+ RM {{ number_format($invoice->service_tax, 2) }}</th>
                </tr>
                <tr class="table-primary">
                    <th colspan="5" class="text-end">Total:</th>
                    <th class="text-end">RM {{ number_format($invoice->total, 2) }}</th>
                </tr>
                @if($invoice->payments > 0)
                <tr>
                    <th colspan="5" class="text-end">Payments Received:</th>
                    <th class="text-end">- RM {{ number_format($invoice->payments, 2) }}</th>
                </tr>
                <tr class="table-info">
                    <th colspan="5" class="text-end">Balance Due:</th>
                    <th class="text-end">RM {{ number_format($invoice->balance_due, 2) }}</th>
                </tr>
                @endif
            </tfoot>
        </table>

        <!-- Payment Information Section -->
        @if($invoice->payment)
        <hr>
        <h5><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
        <div class="card bg-light">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($invoice->payment->payment_date)->format('d-m-Y') }}</p>
                        <p><strong>Payment Amount:</strong> RM {{ number_format($invoice->payment->payment_amount, 2) }}</p>
                        <p><strong>Payment Method:</strong> {{ $invoice->payment->payment_method }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($invoice->payment->transaction_ref)
                            <p><strong>Transaction Ref:</strong> {{ $invoice->payment->transaction_ref }}</p>
                        @endif
                        @if($invoice->payment->remarks)
                            <p><strong>Remarks:</strong> {{ $invoice->payment->remarks }}</p>
                        @endif
                        @if($invoice->payment->proof_of_payment)
                            <p><strong>Proof of Payment:</strong> 
                                <a href="{{ asset('storage/' . $invoice->payment->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file me-1"></i> View Document
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($invoice->status == 'Rejected')
        <div class="mt-4">
            <div class="alert alert-danger">
                <div class="d-flex align-items-start">
                    <i class="fas fa-times-circle fa-2x me-3 mt-1"></i>
                    <div>
                        <h5 class="mb-1">Invoice Rejected</h5>
                        <p class="mb-1"><strong>Reason:</strong> {{ $invoice->reason }}</p>
                        @if($invoice->rejected_by)
                            <p class="mb-0">
                                <strong>Rejected By:</strong> {{ $invoice->rejector->name ?? 'Unknown' }}
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i> 
                                {{ $invoice->rejected_at ? $invoice->rejected_at->format('d/m/Y H:i') : 'N/A' }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <hr>

        <!-- Status Progress -->
        <h5>Workflow Progress</h5>
        <div class="row text-center">
            <div class="col-3">
                <div class="step">
                    <div class="step-circle {{ $invoice->status == 'Submitted' ? 'step-active' : ($invoice->status != 'Submitted' ? 'step-completed' : '') }}">
                        1
                    </div>
                    <div class="step-label">Submitted</div>
                </div>
            </div>
            <div class="col-3">
                <div class="step">
                    <div class="step-circle {{ $invoice->status == 'Finance Review' ? 'step-active' : ($invoice->status == 'Payment Processing' || $invoice->status == 'Paid' ? 'step-completed' : '') }}">
                        2
                    </div>
                    <div class="step-label">Review Officer</div>
                </div>
            </div>
            <div class="col-3">
                <div class="step">
                    <div class="step-circle {{ $invoice->status == 'Payment Processing' ? 'step-active' : ($invoice->status == 'Paid' ? 'step-completed' : '') }}">
                        3
                    </div>
                    <div class="step-label">Finance Officer</div>
                </div>
            </div>
            <div class="col-3">
                <div class="step">
                    <div class="step-circle {{ $invoice->status == 'Paid' ? 'step-completed' : '' }}">
                        4
                    </div>
                    <div class="step-label">Paid</div>
                </div>
            </div>
        </div>

        <hr>

        <!-- Review Officer Actions -->
        @if(auth()->user()->isReviewOfficer() && $invoice->status == 'Submitted')
        <div class="mt-4">
            <h5>Review Officer Actions</h5>
            <div class="btn-group">
                <form action="{{ route('review.invoice.approve', $invoice->invoice_id) }}" method="POST" class="d-inline me-2">
                    @csrf
                    <button type="submit" class="btn btn-gradient-success">
                        <i class="fas fa-check-circle me-1"></i> Approve & Forward to Finance
                    </button>
                </form>
                <button type="button" class="btn btn-gradient-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times-circle me-1"></i> Reject
                </button>
            </div>
        </div>
        @endif

        <!-- Finance Officer Actions -->
        @if(auth()->user()->isFinanceOfficer() && $invoice->status == 'Finance Review')
        <div class="mt-4">
            <h5>Finance Officer Actions</h5>
            <a href="{{ route('review.payment.form', $invoice->invoice_id) }}" class="btn btn-gradient">
                <i class="fas fa-credit-card me-1"></i> Process Payment
            </a>
        </div>
        @endif

        @if(auth()->user()->isFinanceOfficer() && $invoice->status == 'Payment Processing')
        <div class="mt-4">
            <h5>Finance Officer Actions</h5>
            <a href="{{ route('review.payment.confirm', $invoice->invoice_id) }}" class="btn btn-gradient-success">
                <i class="fas fa-check-double me-1"></i> Confirm & Mark as Paid
            </a>
        </div>
        @endif

        <!-- Waiting Messages -->
        @if(auth()->user()->isReviewOfficer() && $invoice->status == 'Finance Review')
        <div class="mt-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> 
                This invoice has been approved and is waiting for Finance Officer to process payment.
            </div>
        </div>
        @endif

        @if(auth()->user()->isReviewOfficer() && $invoice->status == 'Payment Processing')
        <div class="mt-4">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> 
                This invoice is being processed by Finance Officer.
            </div>
        </div>
        @endif

        @if(auth()->user()->isFinanceOfficer() && $invoice->status == 'Submitted')
        <div class="mt-4">
            <div class="alert alert-warning">
                <i class="fas fa-clock me-2"></i> 
                This invoice is waiting for Review Officer to approve.
            </div>
        </div>
        @endif

        @if($invoice->status == 'Paid')
        <div class="mt-4">
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i> This invoice has been paid.
                @if($invoice->payment)
                    <br><small>Paid on: {{ $invoice->payment->payment_date->format('d-m-Y') }}</small>
                @endif
            </div>
        </div>
        @endif

        <div class="text-center mt-4">
            <a href="{{ route('review.invoices') }}" class="btn btn-secondary">Back to Invoices</a>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('review.invoice.reject', $invoice->invoice_id) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Reject Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Rejection Reason *</label>
                        <textarea name="reason" id="reason" rows="3" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .step {
        text-align: center;
        padding: 10px;
    }
    .step-circle {
        width: 40px;
        height: 40px;
        background: #e0e7ff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 8px;
    }
    .step-completed {
        background: #28a745;
        color: white;
    }
    .step-active {
        background: #667eea;
        color: white;
        box-shadow: 0 0 0 4px rgba(102,126,234,0.3);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(102,126,234,0.4); }
        70% { box-shadow: 0 0 0 10px rgba(102,126,234,0); }
        100% { box-shadow: 0 0 0 0 rgba(102,126,234,0); }
    }
    .step-label {
        font-size: 12px;
        font-weight: 500;
    }
</style>
@endsection
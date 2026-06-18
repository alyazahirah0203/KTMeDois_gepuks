@extends('layouts.officer')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details: ' . $invoice->invoice_no)

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card-modern bg-white">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-credit-card me-2"></i> Payment Details
                </div>
                <div>
                    @if($invoice->status != 'Paid')
                    <a href="{{ route('review.payment.edit', $invoice->invoice_id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Invoice:</strong> {{ $invoice->invoice_no }}
                        </div>
                        <div class="col-md-6">
                            <strong>Vendor:</strong> {{ $invoice->vendor_name }}
                        </div>
                        <div class="col-md-6 mt-2">
                            <strong>Total Amount:</strong> RM {{ number_format($invoice->total, 2) }}
                        </div>
                        <div class="col-md-6 mt-2">
                            <strong>Status:</strong> <span class="badge bg-{{ $invoice->status == 'Paid' ? 'success' : 'primary' }}">
                                {{ $invoice->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <strong><i class="fas fa-receipt me-2"></i>Payment Record</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Payment Date:</strong> {{ $invoice->payment->payment_date->format('d-m-Y') }}</p>
                                <p><strong>Payment Amount:</strong> RM {{ number_format($invoice->payment->payment_amount, 2) }}</p>
                                <p><strong>Payment Method:</strong> {{ $invoice->payment->payment_method }}</p>
                            </div>
                            <div class="col-md-6">
                                @if($invoice->payment->transaction_ref)
                                    <p><strong>Transaction Ref:</strong> {{ $invoice->payment->transaction_ref }}</p>
                                @endif
                                @if($invoice->payment->payment_status)
                                    <p><strong>Payment Status:</strong> 
                                        <span class="badge bg-success">{{ $invoice->payment->payment_status }}</span>
                                    </p>
                                @endif
                                <p><strong>Processed By:</strong> {{ $invoice->payment->processor->name ?? 'N/A' }}</p>
                                @if($invoice->payment->created_at)
                                    <p><strong>Processed At:</strong> {{ $invoice->payment->created_at->format('d-m-Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                        @if($invoice->payment->remarks)
                            <div class="mt-2">
                                <strong>Remarks:</strong> {{ $invoice->payment->remarks }}
                            </div>
                        @endif
                        @if($invoice->payment->proof_of_payment)
                            <div class="mt-3">
                                <strong>Proof of Payment:</strong><br>
                                <a href="{{ asset('storage/' . $invoice->payment->proof_of_payment) }}" target="_blank" class="btn btn-outline-primary mt-2">
                                    <i class="fas fa-eye me-1"></i> View Document
                                </a>
                                <a href="{{ asset('storage/' . $invoice->payment->proof_of_payment) }}" download class="btn btn-outline-success mt-2">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('review.invoices') }}" class="btn btn-secondary">Back to Invoices</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Delete Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payment record?</p>
                <p><strong>Invoice:</strong> {{ $invoice->invoice_no }}</p>
                <p><strong>Amount:</strong> RM {{ number_format($invoice->payment->payment_amount, 2) }}</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('review.payment.delete', $invoice->invoice_id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
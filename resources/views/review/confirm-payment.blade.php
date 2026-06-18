@extends('layouts.officer')

@section('title', 'Confirm Payment')
@section('page-title', 'Confirm Payment: ' . $invoice->invoice_no)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-check-double me-2"></i> Confirm Payment
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
                            <strong>Status:</strong> <span class="badge bg-primary">Payment Processing</span>
                        </div>
                    </div>
                </div>

                <div class="card bg-light mb-4">
                    <div class="card-header">
                        <strong><i class="fas fa-credit-card me-2"></i>Payment Details</strong>
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
                                <p><strong>Processed By:</strong> {{ $invoice->payment->processor->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @if($invoice->payment->remarks)
                            <div class="mt-2">
                                <strong>Remarks:</strong> {{ $invoice->payment->remarks }}
                            </div>
                        @endif
                        @if($invoice->payment->proof_of_payment)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $invoice->payment->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file me-1"></i> View Proof of Payment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Confirm:</strong> Are you sure you want to mark this invoice as PAID?
                    <br>
                    <small>This action cannot be undone.</small>
                </div>

                <form method="POST" action="{{ route('review.payment.paid', $invoice->invoice_id) }}">
                    @csrf
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-gradient-success btn-lg">
                            <i class="fas fa-check-double me-2"></i> Confirm & Mark as Paid
                        </button>
                        <a href="{{ route('review.invoices') }}" class="btn btn-secondary">Back to Invoices</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
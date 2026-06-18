@extends('layouts.officer')

@section('title', 'Process Payment')
@section('page-title', 'Process Payment: ' . $invoice->invoice_no)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-credit-card me-2"></i> Process Payment
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
                            <strong>Status:</strong> <span class="badge bg-info">Finance Review</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('review.payment.process', $invoice->invoice_id) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_date" class="form-label fw-bold">Payment Date *</label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_amount" class="form-label fw-bold">Payment Amount (RM) *</label>
                                <input type="number" name="payment_amount" id="payment_amount" class="form-control" 
                                       step="0.01" min="0.01" value="{{ $invoice->total }}" required>
                                <small class="text-muted">Full amount: RM {{ number_format($invoice->total, 2) }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label fw-bold">Payment Method *</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_ref" class="form-label fw-bold">Transaction Reference</label>
                                <input type="text" name="transaction_ref" id="transaction_ref" class="form-control" 
                                       placeholder="e.g., MB-2025-001234, CHQ-12345, Cash Receipt #001">
                                <small class="text-muted">Bank ref, cheque number, or receipt number</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="proof_of_payment" class="form-label fw-bold">Proof of Payment</label>
                        <input type="file" name="proof_of_payment" id="proof_of_payment" class="form-control" 
                               accept=".pdf,.jpg,.png,.jpeg">
                        <small class="text-muted">Upload bank slip, receipt, or payment confirmation (PDF, JPG, PNG, Max: 5MB)</small>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3" class="form-control" 
                                  placeholder="Additional notes about this payment..."></textarea>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i> Process Payment
                        </button>
                        <a href="{{ route('review.invoices') }}" class="btn btn-secondary">Back to Invoices</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
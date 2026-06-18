@extends('layouts.officer')

@section('title', 'Edit Payment')
@section('page-title', 'Edit Payment: ' . $invoice->invoice_no)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-edit me-2"></i> Edit Payment
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
                            <strong>Status:</strong> <span class="badge bg-primary">{{ $invoice->status }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('review.payment.update', $invoice->invoice_id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_date" class="form-label fw-bold">Payment Date *</label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control" 
                                       value="{{ $invoice->payment->payment_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_amount" class="form-label fw-bold">Payment Amount (RM) *</label>
                                <input type="number" name="payment_amount" id="payment_amount" class="form-control" 
                                    step="0.01" min="0.01" value="{{ $invoice->payment->payment_amount }}" required>
                                <small class="text-muted">Invoice total: RM {{ number_format($invoice->total, 2) }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label fw-bold">Payment Method *</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="Bank Transfer" {{ $invoice->payment->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="Credit Card" {{ $invoice->payment->payment_method == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="Cheque" {{ $invoice->payment->payment_method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="Cash" {{ $invoice->payment->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transaction_ref" class="form-label fw-bold">Transaction Reference</label>
                                <input type="text" name="transaction_ref" id="transaction_ref" class="form-control" 
                                       value="{{ $invoice->payment->transaction_ref }}" 
                                       placeholder="e.g., MB-2025-001234, CHQ-12345, Cash Receipt #001">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="proof_of_payment" class="form-label fw-bold">Proof of Payment</label>
                        @if($invoice->payment->proof_of_payment)
                            <div class="mb-2">
                                <strong>Current file:</strong>
                                <a href="{{ asset('storage/' . $invoice->payment->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file me-1"></i> View Current Document
                                </a>
                            </div>
                        @endif
                        <input type="file" name="proof_of_payment" id="proof_of_payment" class="form-control" 
                               accept=".pdf,.jpg,.png,.jpeg">
                        <small class="text-muted">Upload new file to replace current (PDF, JPG, PNG, Max: 5MB)</small>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3" class="form-control" 
                                  placeholder="Additional notes about this payment...">{{ $invoice->payment->remarks }}</textarea>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-save me-2"></i> Update Payment
                        </button>
                        <a href="{{ route('review.invoices') }}" class="btn btn-secondary">Back to Invoices</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
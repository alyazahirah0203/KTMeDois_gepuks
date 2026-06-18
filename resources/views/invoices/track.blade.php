@extends('layouts.app')

@section('title', 'Track Claim Status')

@section('content')
@php
    $isVendorGuard = auth()->guard('vendor')->check();
    $dashboardRoute = $isVendorGuard ? route('vendor.dashboard') : route('dashboard');
@endphp

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-search-dollar me-2"></i> Track Your Claim
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('invoices.track') }}" class="mb-5">
                    <div class="input-group">
                        <input type="text" name="invoice_no" class="form-control form-control-lg" 
                               placeholder="Enter Invoice Number (e.g., KTMB/INV/20250611/0001)" 
                               value="{{ request('invoice_no') }}">
                        <button type="submit" class="btn-gradient">
                            <i class="fas fa-search"></i> Track
                        </button>
                    </div>
                </form>

                @if(isset($invoice) && $invoice)
                    <!-- Invoice Summary Card -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted">Invoice Number</small>
                                <h5 class="mb-0">{{ $invoice->invoice_no }}</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <small class="text-muted">Status</small>
                                <h5 class="mb-0">
                                    <span class="badge bg-{{ $invoice->status == 'Submitted' ? 'warning' : ($invoice->status == 'Finance Review' ? 'info' : ($invoice->status == 'Payment Processing' ? 'primary' : 'success')) }} p-2">
                                        <i class="fas fa-{{ $invoice->status == 'Paid' ? 'check-circle' : 'clock' }} me-1"></i>
                                        {{ $invoice->status }}
                                    </span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body text-center py-4">
                            <div class="row">
                                <div class="col-4">
                                    <small>Total Invoice</small>
                                    <h3 class="mb-0">RM {{ number_format($invoice->total, 2) }}</h3>
                                </div>
                                <div class="col-4 border-start border-end border-white-50">
                                    <small>Amount Paid</small>
                                    <h3 class="mb-0 text-success">RM {{ number_format($invoice->payments ?? 0, 2) }}</h3>
                                </div>
                                <div class="col-4">
                                    <small>Balance Due</small>
                                    <h3 class="mb-0 {{ $invoice->balance_due > 0 ? 'text-warning' : 'text-success' }}">
                                        RM {{ number_format($invoice->balance_due, 2) }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Progress Bar -->
                    @php
                        $stages = ['Submitted', 'Finance Review', 'Payment Processing', 'Paid'];
                        $currentIndex = array_search($invoice->status, $stages);
                        $progressPercent = (($currentIndex + 1) / 4) * 100;
                    @endphp
                    
                    <div class="mb-4">
                        <label class="mb-2 fw-bold">Claim Progress</label>
                        <div class="progress" style="height: 12px; border-radius: 10px;">
                            <div class="progress-bar bg-{{ $invoice->status == 'Paid' ? 'success' : ($invoice->status == 'Payment Processing' ? 'primary progress-bar-striped progress-bar-animated' : 'info') }}" 
                                 style="width: {{ $progressPercent }}%">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            @foreach($stages as $index => $stage)
                                <small class="text-{{ $index <= $currentIndex ? 'success' : 'secondary' }}">
                                    <i class="fas fa-{{ $index <= $currentIndex ? 'check-circle' : 'circle' }} me-1"></i>
                                    {{ $stage }}
                                </small>
                            @endforeach
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-bold"><i class="fas fa-calendar-alt me-2"></i>Invoice Date</h6>
                                    <p>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</p>
                                    
                                    <h6 class="fw-bold mt-3"><i class="fas fa-calendar-check me-2"></i>Due Date</h6>
                                    <p>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-bold"><i class="fas fa-credit-card me-2"></i>Payment Terms</h6>
                                    <p>{{ $invoice->payment_terms }}</p>
                                    
                                    <h6 class="fw-bold mt-3"><i class="fas fa-building me-2"></i>Vendor</h6>
                                    <p>{{ $invoice->vendor_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calculation Breakdown -->
                    <div class="mt-4">
                        <h6 class="fw-bold"><i class="fas fa-calculator me-2"></i>Calculation Breakdown</h6>
                        <table class="table table-bordered bg-light">
                            <tr>
                                <th>Line Total</th>
                                <td class="text-end">RM {{ number_format($invoice->line_total, 2) }}</td>
                                <td></td>
                            </tr>
                            @if($invoice->discount > 0)
                            <tr class="text-success">
                                <th>Discount (Credit Note)</th>
                                <td class="text-end">- RM {{ number_format($invoice->discount, 2) }}</td>
                                <td><small>From supplier credit note</small></td>
                            </tr>
                            @endif
                            @if($invoice->penalty > 0)
                            <tr class="text-danger">
                                <th>Penalty (Late 1%)</th>
                                <td class="text-end">- RM {{ number_format($invoice->penalty, 2) }}</td>
                                <td><small>Late delivery penalty</small></td>
                            </tr>
                            @endif
                            <tr class="text-warning">
                                <th>Service Tax (6%)</th>
                                <td class="text-end">+ RM {{ number_format($invoice->service_tax, 2) }}</td>
                                <td><small>Based on after discount amount</small></td>
                            </tr>
                            <tr class="table-primary">
                                <th><strong>Total Claim Amount</strong></th>
                                <td class="text-end"><strong>RM {{ number_format($invoice->total, 2) }}</strong></td>
                                <td></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Back to Dashboard button - ADDED -->
                    <div class="text-center mt-4">
                        <a href="{{ route('invoices.download', $invoice->invoice_id) }}" class="btn-gradient success">
                            <i class="fas fa-download me-2"></i> Download Invoice PDF
                        </a>
                        <a href="{{ route('invoices.show', $invoice->invoice_id) }}" class="btn-gradient ms-2">
                            <i class="fas fa-eye me-2"></i> View Full Details
                        </a>
                        <a href="{{ $dashboardRoute }}" class="btn btn-primary ms-2">
                            <i class="fas fa-home me-2"></i> Back to Dashboard
                        </a>
                    </div>
                @elseif(request('invoice_no'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i> 
                        Invoice not found. Please check the invoice number.
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> 
                        Enter an invoice number to track your claim status and payment information.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
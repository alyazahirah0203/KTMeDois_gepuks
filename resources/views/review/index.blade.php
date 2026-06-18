@extends('layouts.officer')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card-modern bg-white p-4">
            <h4 class="mb-2">
                <i class="fas fa-hand-wave me-2"></i> Welcome, {{ auth()->user()->name }}!
            </h4>
            <p class="text-muted mb-0">
                @if(auth()->user()->isReviewOfficer())
                    You are logged in as <strong>Review Officer</strong>. Review and approve invoices and delivery orders.
                @elseif(auth()->user()->isFinanceOfficer())
                    You are logged in as <strong>Finance Officer</strong>. Process payments and mark invoices as paid.
                @endif
            </p>
        </div>
    </div>
</div>

<!-- Invoice Stats -->
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
                <h5 class="mb-1">Pending Review</h5>
                <h2 class="mb-0 text-warning">{{ $pendingInvoices->count() }}</h2>
                <small class="text-muted">Awaiting approval</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-search fa-2x text-info"></i>
                </div>
                <h5 class="mb-1">Under Review</h5>
                <h2 class="mb-0 text-info">{{ $reviewingInvoices->count() }}</h2>
                <small class="text-muted">Being reviewed</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-spinner fa-2x text-primary"></i>
                </div>
                <h5 class="mb-1">Processing</h5>
                <h2 class="mb-0 text-primary">{{ $processingInvoices->count() }}</h2>
                <small class="text-muted">Payment processing</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h5 class="mb-1">Paid</h5>
                <h2 class="mb-0 text-success">{{ $paidInvoices->count() }}</h2>
                <small class="text-muted">Completed payments</small>
            </div>
        </div>
    </div>
</div>

<!-- DO Stats -->
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-truck fa-2x text-warning"></i>
                </div>
                <h5 class="mb-1">Pending DOs</h5>
                <h2 class="mb-0 text-warning">{{ $pendingDOs }}</h2>
                <small class="text-muted">Awaiting approval</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h5 class="mb-1">Approved DOs</h5>
                <h2 class="mb-0 text-success">{{ $approvedDOs }}</h2>
                <small class="text-muted">Ready for invoicing</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                </div>
                <h5 class="mb-1">Rejected DOs</h5>
                <h2 class="mb-0 text-danger">{{ $rejectedDOs }}</h2>
                <small class="text-muted">Need correction</small>
            </div>
        </div>
    </div>
</div>

<!-- Status Summary -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-chart-pie me-2"></i> Invoice Status Summary
            </div>
            <div class="card-body p-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = array_sum($statusData);
                        @endphp
                        @foreach($statusData as $status => $count)
                        <tr>
                            <td>{{ $status }}</td>
                            <td>{{ $count }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar bg-{{ $status == 'Submitted' ? 'warning' : ($status == 'Finance Review' ? 'info' : ($status == 'Payment Processing' ? 'primary' : 'success')) }}" 
                                         style="width: {{ $total > 0 ? ($count / $total) * 100 : 0 }}%">
                                        {{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-trophy me-2"></i> Top Vendors
            </div>
            <div class="card-body p-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Invoices</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topVendors as $vendor)
                        <tr>
                            <td>{{ $vendor->vendor->supplier_comp_name ?? 'N/A' }}</td>
                            <td>{{ $vendor->total_invoices }}</td>
                            <td>RM {{ number_format($vendor->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trends -->
<div class="row">
    <div class="col-md-12">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-chart-line me-2"></i> Monthly Trends
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Invoices</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthlyData as $data)
                            <tr>
                                <td>{{ $data->month }}</td>
                                <td>{{ $data->total }}</td>
                                <td>RM {{ number_format($data->amount, 2) }}</td>
                                <td>RM {{ number_format($data->paid_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
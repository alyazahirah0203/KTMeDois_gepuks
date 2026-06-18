@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'System Reports')

@section('content')
<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Total Invoices</span>
                        <h3 class="mb-0 text-primary">{{ $totalInvoices }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-file-invoice text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Total Amount</span>
                        <h3 class="mb-0 text-primary">RM {{ number_format($totalAmount, 2) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-money-bill text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Total Paid</span>
                        <h3 class="mb-0 text-success">RM {{ number_format($totalPaid, 2) }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Pending Amount</span>
                        <h3 class="mb-0 text-warning">RM {{ number_format($totalPending, 2) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="fas fa-clock text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-chart-pie me-2"></i> Status Summary
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
    
    <div class="col-md-6">
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
                        @forelse($topVendors as $vendorData)
                        <tr>
                            <td>
                                @php
                                    $vendorName = 'N/A';
                                    // Try to get vendor name from external DB first
                                    if ($vendorData->vendorExternal) {
                                        $vendorName = $vendorData->vendorExternal->SUPPLIER_COMP_NAME;
                                    } elseif ($vendorData->vendor) {
                                        $vendorName = $vendorData->vendor->supplier_comp_name;
                                    }
                                @endphp
                                {{ $vendorName }}
                            </td>
                            <td>{{ $vendorData->total_invoices }}</td>
                            <td>RM {{ number_format($vendorData->total_amount, 2) }}</td>
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

<div class="text-center mt-3">
    <a href="{{ route('review.export') }}" class="btn btn-gradient">
        <i class="fas fa-file-export me-2"></i> Export Full Report
    </a>
</div>
@endsection
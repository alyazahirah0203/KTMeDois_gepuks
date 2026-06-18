@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'IT Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card-modern bg-white p-4">
            <h4 class="mb-2">
                <i class="fas fa-hand-wave me-2"></i> Welcome, {{ auth()->user()->name }}!
            </h4>
            <p class="text-muted mb-0">IT Admin Dashboard - System overview and management.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-building fa-2x text-primary"></i>
                </div>
                <h5 class="mb-1">Total Vendors</h5>
                <h2 class="mb-0 text-primary">{{ $totalVendors }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-file-invoice fa-2x text-success"></i>
                </div>
                <h5 class="mb-1">Total Invoices</h5>
                <h2 class="mb-0 text-success">{{ $totalInvoices }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-users fa-2x text-warning"></i>
                </div>
                <h5 class="mb-1">Total Users</h5>
                <h2 class="mb-0 text-warning">{{ $totalUsers }}</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card-modern bg-white">
            <div class="card-body p-3 text-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-block mb-2">
                    <i class="fas fa-money-bill fa-2x text-success"></i>
                </div>
                <h5 class="mb-1">Total Paid</h5>
                <h2 class="mb-0 text-success">RM {{ number_format($totalPaid, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-clock me-2"></i> Pending Invoices
            </div>
            <div class="card-body p-3 text-center py-4">
                <h2 class="text-warning">{{ $pendingInvoices }}</h2>
                <p class="text-muted">Invoices waiting for review</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-history me-2"></i> Recent Audit Logs
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                            <tr>
                                <td>{{ $log->timestamp->format('H:i:s') }}</td>
                                <td>{{ $log->username }}</td>
                                <td>{{ $log->action }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No logs found</td>
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
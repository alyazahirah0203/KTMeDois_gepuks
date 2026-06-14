@extends('layouts.app')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card-modern bg-white p-4 rounded-4">
            <h2 class="mb-0">
                <i class="fas fa-tachometer-alt text-primary me-2"></i> 
                Welcome back, {{ auth()->user()->name }}!
            </h2>
            <p class="text-muted mt-2">Manage your invoices and track claims from here.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card-modern bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Approved DOs</h6>
                        <h2 class="mb-0">{{ $deliveryOrders ?? 0 }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-truck fa-2x text-primary"></i>
                    </div>
                </div>
                <a href="{{ route('invoices.create') }}" class="btn btn-gradient w-100">
                    <i class="fas fa-plus-circle"></i> Submit Invoice
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card-modern bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Total Invoices</h6>
                        <h2 class="mb-0">{{ $invoices ?? 0 }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-file-invoice fa-2x text-success"></i>
                    </div>
                </div>
                <a href="{{ route('invoices.track') }}" class="btn btn-gradient-success w-100">
                    <i class="fas fa-search"></i> Track Claims
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card-modern bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Pending Claims</h6>
                        <h2 class="mb-0">{{ $pendingInvoices ?? 0 }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
                @php
                    $total = ($invoices ?? 1);
                    $pending = ($pendingInvoices ?? 0);
                    $percentage = ($total > 0) ? (($total - $pending) / $total * 100) : 0;
                @endphp
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                </div>
                <small class="text-muted">{{ number_format($percentage, 1) }}% completed</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card-modern bg-white">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-history me-2"></i> Invoice History
                </div>
                <div>
                    <button class="btn btn-sm btn-light" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table id="invoicesTable" class="table table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> No</th>
                                <th><i class="fas fa-file-invoice"></i> Invoice Number</th>
                                <th><i class="fas fa-calendar-alt"></i> Date</th>
                                <th><i class="fas fa-money-bill-wave"></i> Total (RM)</th>
                                <th><i class="fas fa-chart-line"></i> Status</th>
                                <th><i class="fas fa-chart-pie"></i> Progress</th>
                                <th><i class="fas fa-cogs"></i> Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices ?? [] as $index => $inv)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $inv->invoice_no }}</span>
                                    <br>
                                    <small class="text-muted">{{ $inv->uuid }}</small>
                                </td>
                                <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                                <td class="fw-bold">RM {{ number_format($inv->total, 2) }}</td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'Submitted' => ['class' => 'warning', 'icon' => 'fa-paper-plane'],
                                            'Finance Review' => ['class' => 'info', 'icon' => 'fa-clipboard-list'],
                                            'Payment Processing' => ['class' => 'primary', 'icon' => 'fa-spinner fa-pulse'],
                                            'Paid' => ['class' => 'success', 'icon' => 'fa-check-circle']
                                        ];
                                        $config = $statusConfig[$inv->status] ?? ['class' => 'secondary', 'icon' => 'fa-circle'];
                                    @endphp
                                    <span class="badge bg-{{ $config['class'] }}">
                                        <i class="fas {{ $config['icon'] }} me-1"></i>
                                        {{ $inv->status }}
                                    </span>
                                </td>
                                <td style="min-width: 120px;">
                                    @php
                                        $stages = ['Submitted', 'Finance Review', 'Payment Processing', 'Paid'];
                                        $currentStage = array_search($inv->status, $stages);
                                        $progressPercent = (($currentStage + 1) / 4) * 100;
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $inv->status == 'Paid' ? 'success' : ($inv->status == 'Payment Processing' ? 'primary' : ($inv->status == 'Finance Review' ? 'info' : 'warning')) }}" 
                                                     style="width: {{ $progressPercent }}%">
                                                </div>
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ round($progressPercent) }}%</small>
                                    </div>
                                    <small class="text-muted">Stage {{ $currentStage + 1 }}/4</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('invoices.show', $inv->invoice_id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('invoices.download', $inv->invoice_id) }}" 
                                           class="btn btn-sm btn-outline-success"
                                           data-bs-toggle="tooltip" 
                                           title="Download PDF">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-info"
                                                onclick="copyToClipboard('{{ $inv->invoice_no }}')"
                                                data-bs-toggle="tooltip" 
                                                title="Copy Invoice Number">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">No invoices found</h5>
                                    <p class="text-muted">You haven't submitted any invoices yet.</p>
                                    <a href="{{ route('invoices.create') }}" class="btn btn-gradient mt-2">
                                        <i class="fas fa-plus-circle"></i> Submit Your First Invoice
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="fw-bold text-primary" id="totalAmount">RM 0.00</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Table Styles */
.dataTables_wrapper .dataTables_length select {
    padding: 4px 8px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    margin: 0 4px;
}

.dataTables_wrapper .dataTables_filter input {
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    margin-left: 8px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px;
    margin: 0 2px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    background: white;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    border-color: #667eea;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
}

table.dataTable thead th {
    border-bottom: 2px solid #dee2e6;
    padding: 12px;
    font-weight: 600;
    background: #f8f9fa;
}

table.dataTable tbody td {
    padding: 12px;
    vertical-align: middle;
}

.dataTables_info {
    padding-top: 12px;
    font-size: 13px;
    color: #6c757d;
}

/* Tooltip */
.btn-group .btn {
    margin: 0 2px;
    border-radius: 8px !important;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease;
}

/* Export buttons */
.dt-buttons {
    margin-bottom: 15px;
}

.dt-buttons .btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 8px;
    margin-right: 5px;
}

.dt-buttons .btn:hover {
    transform: translateY(-2px);
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#invoicesTable').DataTable({
        responsive: true,
        order: [[2, 'desc']], // Sort by date descending
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        language: {
            search: "<i class='fas fa-search'></i> Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ invoices",
            infoEmpty: "No invoices found",
            infoFiltered: "(filtered from _MAX_ total invoices)",
            zeroRecords: "No matching invoices found",
            paginate: {
                first: "<i class='fas fa-angle-double-left'></i>",
                last: "<i class='fas fa-angle-double-right'></i>",
                previous: "<i class='fas fa-angle-left'></i>",
                next: "<i class='fas fa-angle-right'></i>"
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn-sm'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn-sm'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn-sm'
            }
        ],
        columnDefs: [
            { orderable: false, targets: [6] } // Disable sorting on action column
        ]
    });
    
    // Calculate total amount
    var total = 0;
    $('#invoicesTable tbody tr').each(function() {
        var amount = $(this).find('td:eq(3)').text().replace('RM', '').replace(/,/g, '').trim();
        if (amount) {
            total += parseFloat(amount);
        }
    });
    $('#totalAmount').text('RM ' + total.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show temporary notification
        var notification = document.createElement('div');
        notification.className = 'alert alert-success alert-modern position-fixed top-0 start-50 translate-middle-x mt-3';
        notification.style.zIndex = '9999';
        notification.innerHTML = '<i class="fas fa-check-circle me-2"></i> Invoice number copied to clipboard!';
        document.body.appendChild(notification);
        setTimeout(function() {
            notification.remove();
        }, 2000);
    });
}
</script>
@endpush
@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('page-title', 'System Audit Logs')

@section('content')
<div class="card-modern bg-white">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-history me-2"></i> Audit Logs
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </button>
            <a href="{{ route('dashboard.admin.audit-logs.export-csv') }}" class="btn btn-sm btn-success">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover" id="auditTable">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Username</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auditLogs as $log)
                    <tr>
                        <td style="white-space: nowrap;">
                            @php
                                $timestamp = $log->timestamp instanceof \Carbon\Carbon 
                                    ? $log->timestamp 
                                    : \Carbon\Carbon::parse($log->timestamp);
                            @endphp
                            {{ $timestamp->format('Y-m-d H:i:s') }}
                        </td>
                        <td>{{ $log->username }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $log->module }}</span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $log->action }}</span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td>
                            <span class="badge bg-{{ $log->status == 'Success' ? 'success' : 'danger' }}">
                                {{ $log->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('dashboard.admin.audit-logs.export-pdf') }}" target="_blank">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-file-pdf me-2"></i>Export Audit Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label fw-bold">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                       value="{{ date('Y-m-01') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label fw-bold">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module" class="form-label fw-bold">Module</label>
                                <select name="module" id="module" class="form-select">
                                    <option value="all">All Modules</option>
                                    <option value="Invoice">Invoice</option>
                                    <option value="DO">Delivery Order</option>
                                    <option value="Payment">Payment</option>
                                    <option value="User">User</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="all">All Status</option>
                                    <option value="Success">Success</option>
                                    <option value="Failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf me-1"></i> Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#auditTable')) {
        $('#auditTable').DataTable().destroy();
    }
    
    var table = $('#auditTable');
    var hasData = table.find('tbody tr').length > 0;
    
    if (hasData) {
        table.DataTable({
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            language: {
                search: "<i class='fas fa-search me-1'></i> Search:",
                searchPlaceholder: "Search logs...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ logs",
                infoEmpty: "No logs found",
                infoFiltered: "(filtered from _MAX_ total logs)",
                zeroRecords: "No matching logs found",
                paginate: {
                    first: '<i class="fas fa-angle-double-left"></i>',
                    last: '<i class="fas fa-angle-double-right"></i>',
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>'
                }
            }
        });
    }
});
</script>
@endpush
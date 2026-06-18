@extends('layouts.officer')

@section('title', 'Review DOs')
@section('page-title', 'Review Delivery Orders')

@section('content')
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-truck me-2"></i> Pending Delivery Orders
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="doTable">
                        <thead>
                            <tr>
                                <th>DO Number</th>
                                <th>Vendor</th>
                                <th>PO Number</th>
                                <th>Order Date</th>
                                <th>Delivery Date</th>
                                <th>Items</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingDOs as $do)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $do->do_number }}</span>
                                </td>
                                <td>{{ $do->vendor_name }}</td>
                                <td>{{ $do->po_number }}</td>
                                <td>{{ $do->order_date->format('d-m-Y') }}</td>
                                <td>{{ $do->delivery_date->format('d-m-Y') }}</td>
                                <td>{{ $do->items->count() }} items</td>
                                <td>
                                    <a href="{{ route('review.do.show', $do->do_id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i> Review
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    No pending DOs for review.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approved DOs -->
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card-modern bg-white">
            <div class="card-header-custom bg-success">
                <i class="fas fa-check-circle me-2"></i> Approved Delivery Orders
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="approvedTable">
                        <thead>
                            <tr>
                                <th>DO Number</th>
                                <th>Vendor</th>
                                <th>PO Number</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($approvedDOs as $do)
                            <tr>
                                <td>{{ $do->do_number }}</td>
                                <td>{{ $do->vendor_name }}</td>
                                <td>{{ $do->po_number }}</td>
                                <td>{{ $do->delivery_date->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge bg-success">Approved</span>
                                </td>
                                <td>
                                    <a href="{{ route('review.do.show', $do->do_id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">No approved DOs.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rejected DOs -->
<div class="row">
    <div class="col-md-12 mb-3">
        <div class="card-modern bg-white">
            <div class="card-header-custom bg-danger">
                <i class="fas fa-times-circle me-2"></i> Rejected Delivery Orders
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="rejectedTable">
                        <thead>
                            <tr>
                                <th>DO Number</th>
                                <th>Vendor</th>
                                <th>PO Number</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rejectedDOs as $do)
                            <tr>
                                <td>{{ $do->do_number }}</td>
                                <td>{{ $do->vendor_name }}</td>
                                <td>{{ $do->po_number }}</td>
                                <td>{{ Str::limit($do->reason, 50) }}</td>
                                <td>
                                    <a href="{{ route('review.do.show', $do->do_id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">No rejected DOs.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this Delivery Order?</p>
                <p><strong>DO Number:</strong> <span id="deleteDONumber"></span></p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete DO</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function initDataTableIfHasData(tableId) {
        var table = $(tableId);
        var hasData = table.find('tbody tr:not(:has(td[colspan]))').length > 0;
        
        if (hasData) {
            table.DataTable({
                responsive: true,
                order: [[3, 'asc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    search: "<i class='fas fa-search'></i> Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ DOs",
                    infoEmpty: "No DOs found",
                    infoFiltered: "(filtered from _MAX_ total DOs)",
                    zeroRecords: "No matching DOs found"
                }
            });
        }
    }

    function initApprovedDataTableIfHasData(tableId) {
        var table = $(tableId);
        var hasData = table.find('tbody tr:not(:has(td[colspan]))').length > 0;
        
        if (hasData) {
            table.DataTable({
                responsive: true,
                order: [[3, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    search: "<i class='fas fa-search'></i> Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ approved DOs",
                    infoEmpty: "No approved DOs found",
                    infoFiltered: "(filtered from _MAX_ total approved DOs)",
                    zeroRecords: "No matching approved DOs found"
                }
            });
        }
    }

    function initRejectedDataTableIfHasData(tableId) {
        var table = $(tableId);
        var hasData = table.find('tbody tr:not(:has(td[colspan]))').length > 0;
        
        if (hasData) {
            table.DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    search: "<i class='fas fa-search'></i> Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ rejected DOs",
                    infoEmpty: "No rejected DOs found",
                    infoFiltered: "(filtered from _MAX_ total rejected DOs)",
                    zeroRecords: "No matching rejected DOs found"
                }
            });
        }
    }

    initDataTableIfHasData('#doTable');
    initApprovedDataTableIfHasData('#approvedTable');
    initRejectedDataTableIfHasData('#rejectedTable');
});

var deleteDOId = null;

function confirmDelete(doId, doNumber) {
    deleteDOId = doId;
    document.getElementById('deleteDONumber').textContent = doNumber;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteDOId) {
        document.getElementById('delete-form-' + deleteDOId).submit();
    }
});
</script>
@endpush
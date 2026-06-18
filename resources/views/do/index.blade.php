@extends('layouts.app')

@section('title', 'Delivery Orders')

@section('content')
@php
    $isVendorGuard = auth()->guard('vendor')->check();
    $isWebGuard = auth()->check();
    $isVendor = false;
    $isOfficer = false;
    $isAdmin = false;
    $dashboardRoute = $isVendorGuard ? route('vendor.dashboard') : route('dashboard');
    
    if ($isVendorGuard) {
        $isVendor = true;
    } elseif ($isWebGuard) {
        $user = auth()->user();
        $isVendor = $user->isVendor();
        $isOfficer = $user->isOfficer();
        $isAdmin = $user->isITOfficer();
    }
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card-modern bg-white">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-truck me-2"></i> Delivery Orders
                </div>
                <div>

                    @if($isVendor)
                    <a href="{{ route('do.create') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-plus-circle"></i> New DO
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="doTable">
                        <thead>
                            <tr>
                                <th>DO Number</th>
                                <th>PO Number</th>
                                <th>Order Date</th>
                                <th>Delivery Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dos as $do)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $do->do_number }}</span>
                                    <br>
                                    <small class="text-muted">Vendor: {{ $do->vendor_name }}</small>
                                </td>
                                <td>{{ $do->po_number }}</td>
                                <td>{{ $do->order_date->format('d-m-Y') }}</td>
                                <td>{{ $do->delivery_date->format('d-m-Y') }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Draft' => 'secondary',
                                            'Submitted' => 'warning',
                                            'Under Review' => 'info',
                                            'Approved' => 'success',
                                            'Rejected' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$do->status] ?? 'secondary' }}">
                                        <i class="fas fa-{{ $do->status == 'Approved' ? 'check-circle' : ($do->status == 'Rejected' ? 'times-circle' : 'clock') }} me-1"></i>
                                        {{ $do->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('do.show', $do->do_id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($isVendor && in_array($do->status, ['Draft', 'Submitted', 'Rejected']))
                                        <a href="{{ route('do.edit', $do->do_id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                        
                                        @if($isOfficer && $do->status == 'Submitted')
                                        <form action="{{ route('do.approve', $do->do_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $do->do_id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                        
                                        @if(($isVendor && in_array($do->status, ['Draft', 'Submitted', 'Rejected'])) || $isAdmin)
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $do->do_id }}', '{{ $do->do_number }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $do->do_id }}" method="POST" action="{{ route('do.destroy', $do->do_id) }}" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <h5 class="text-muted">No delivery orders found</h5>
                                    @if($isVendor)
                                        <a href="{{ route('do.create') }}" class="btn btn-gradient mt-2">
                                            <i class="fas fa-plus-circle"></i> Create Your First DO
                                        </a>
                                    @endif
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

<!-- Reject Modals -->
@foreach($dos as $do)
@if($do->status == 'Submitted' && $isOfficer)
<div class="modal fade" id="rejectModal{{ $do->do_id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('do.reject', $do->do_id) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Reject DO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>DO Number:</strong> {{ $do->do_number }}</p>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Rejection Reason *</label>
                        <textarea name="reason" id="reason" rows="3" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject DO</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

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

<style>
.btn-group .btn {
    margin: 0 2px;
    border-radius: 6px !important;
}
</style>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#doTable');
    var hasData = table.find('tbody tr:not(:has(td[colspan]))').length > 0;
    
    if (hasData) {
        table.DataTable({
            responsive: true,
            order: [[2, 'desc']],
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
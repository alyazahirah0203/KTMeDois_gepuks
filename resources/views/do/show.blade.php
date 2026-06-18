@extends('layouts.app')

@section('title', 'DO Details')

@section('content')
@php
    $isVendorGuard = auth()->guard('vendor')->check();
    $isWebGuard = auth()->check();
    $isVendor = false;
    $isOfficer = false;
    $dashboardRoute = $isVendorGuard ? route('vendor.dashboard') : route('dashboard');
    
    if ($isVendorGuard) {
        $isVendor = true;
    } elseif ($isWebGuard) {
        $user = auth()->user();
        $isVendor = $user->isVendor();
        $isOfficer = $user->isOfficer();
    }
@endphp

<div class="card-modern bg-white">
    <div class="card-header-custom">
        <i class="fas fa-truck me-2"></i> DO Details: {{ $do->do_number }}
    </div>
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6">
                <p><strong>DO Number:</strong> {{ $do->do_number }}</p>
                <p><strong>PO Number:</strong> {{ $do->po_number }}</p>
                <p><strong>Order Date:</strong> {{ $do->order_date->format('d-m-Y') }}</p>
                <p><strong>Delivery Date:</strong> {{ $do->delivery_date->format('d-m-Y') }}</p>
                <p><strong>Delivery Time:</strong> {{ $do->delivery_time }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Vendor:</strong> {{ $do->vendor_name }}</p>
                <p><strong>Status:</strong> 
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
                </p>
                @if($do->reason)
                    <p><strong>Rejection Reason:</strong> {{ $do->reason }}</p>
                @endif
                <p><strong>Shipping Address:</strong><br>{{ $do->shipping_address }}</p>
                <p><strong>Invoice Address:</strong><br>{{ $do->invoice_address }}</p>
            </div>
        </div>

        <hr>

        <h5>DO Items</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Description</th>
                    <th class="text-end">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($do->items as $item)
                <tr>
                    <td>{{ $item->item_no }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($isVendor && in_array($do->status, ['Draft', 'Submitted', 'Rejected']))
        <div class="mt-4">
            <div class="btn-group">
                <a href="{{ route('do.edit', $do->do_id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit DO
                </a>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash me-1"></i> Delete DO
                </button>
            </div>
        </div>
        
        <form id="deleteForm" method="POST" action="{{ route('do.destroy', $do->do_id) }}" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this Delivery Order? This action cannot be undone.')) {
                document.getElementById('deleteForm').submit();
            }
        }
        </script>
        @endif

        @if($isOfficer && $do->status == 'Submitted')
        <div class="mt-4">
            <div class="btn-group">
                <form action="{{ route('do.approve', $do->do_id) }}" method="POST" class="d-inline me-2">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Approve DO
                    </button>
                </form>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times-circle me-1"></i> Reject DO
                </button>
            </div>
        </div>
        @endif

        <!-- FIXED: Back to Dashboard button added -->
        <div class="text-center mt-4">
            <a href="{{ route('do.index') }}" class="btn btn-secondary">Back to List</a>
            <a href="{{ $dashboardRoute }}" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</div>

@if($isOfficer && $do->status == 'Submitted')
<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('do.reject', $do->do_id) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Reject DO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
@endsection
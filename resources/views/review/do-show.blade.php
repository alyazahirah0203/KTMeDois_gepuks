@extends('layouts.officer')

@section('title', 'Review DO')
@section('page-title', 'Review DO: ' . $do->do_number)

@section('content')
<div class="card-modern bg-white">
    <div class="card-header-custom">
        <i class="fas fa-truck me-2"></i> Delivery Order Details
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
                            'Approved' => 'success',
                            'Rejected' => 'danger'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$do->status] ?? 'secondary' }}">
                        <i class="fas fa-{{ $do->status == 'Approved' ? 'check-circle' : ($do->status == 'Rejected' ? 'times-circle' : 'clock') }} me-1"></i>
                        {{ $do->status }}
                    </span>
                </p>
                <p><strong>Shipping Address:</strong><br>{{ $do->shipping_address }}</p>
                <p><strong>Invoice Address:</strong><br>{{ $do->invoice_address }}</p>
                @if($do->remarks)
                    <p><strong>Remarks:</strong> {{ $do->remarks }}</p>
                @endif
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

        @if($do->status == 'Submitted')
        <div class="mt-4">
            <h5>Review Actions</h5>
            <div class="btn-group">
                <form action="{{ route('review.do.approve', $do->do_id) }}" method="POST" class="d-inline me-2">
                    @csrf
                    <button type="submit" class="btn btn-gradient-success">
                        <i class="fas fa-check-circle me-1"></i> Approve DO
                    </button>
                </form>
                <button type="button" class="btn btn-gradient-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times-circle me-1"></i> Reject DO
                </button>
            </div>
        </div>
        @endif

        @if($do->status == 'Approved')
        <div class="mt-4">
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i> 
                This Delivery Order has been <strong>Approved</strong> and is ready for invoicing.
            </div>
        </div>
        @endif

        @if($do->status == 'Rejected')
        <div class="mt-4">
            <div class="alert alert-danger">
                <i class="fas fa-times-circle me-2"></i> 
                This Delivery Order has been <strong>Rejected</strong>.
                @if($do->reason)
                    <br><strong>Reason:</strong> {{ $do->reason }}
                @endif
            </div>
        </div>
        @endif

        <div class="text-center mt-4">
            <a href="{{ route('review.dos') }}" class="btn btn-secondary">Back to DO List</a>
        </div>
    </div>
</div>

@if($do->status == 'Submitted')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('review.do.reject', $do->do_id) }}">
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
@endsection
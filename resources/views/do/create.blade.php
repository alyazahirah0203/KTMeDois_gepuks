@extends('layouts.app')

@section('title', 'Create Delivery Order')

@section('content')
@php
    $isVendorGuard = auth()->guard('vendor')->check();
    $dashboardRoute = $isVendorGuard ? route('vendor.dashboard') : route('dashboard');
@endphp

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-plus-circle me-2"></i> Create Delivery Order
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Your Delivery Order will be automatically submitted for review upon creation.
                </div>

                <form method="POST" action="{{ route('do.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="do_number" class="form-label fw-bold">DO Number *</label>
                                <input type="text" name="do_number" id="do_number" class="form-control" 
                                       placeholder="DO/2025/001" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="po_number" class="form-label fw-bold">PO Number *</label>
                                <input type="text" name="po_number" id="po_number" class="form-control" 
                                       placeholder="PO/2025/001" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order_date" class="form-label fw-bold">Order Date *</label>
                                <input type="date" name="order_date" id="order_date" class="form-control" 
                                       value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delivery_date" class="form-label fw-bold">Delivery Date *</label>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control" 
                                       value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delivery_time" class="form-label fw-bold">Delivery Time *</label>
                                <input type="time" name="delivery_time" id="delivery_time" class="form-control" 
                                       value="14:00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vendor_id" class="form-label fw-bold">Vendor</label>
                                <input type="text" class="form-control" value="{{ $vendor->SUPPLIER_COMP_NAME ?? $vendor->supplier_comp_name ?? 'N/A' }}" disabled>
                                <small class="text-muted">Vendor ID: {{ $vendor->SUPPLIERID ?? $vendor->supplierid ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="shipping_address" class="form-label fw-bold">Shipping Address *</label>
                        <textarea name="shipping_address" id="shipping_address" rows="2" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="invoice_address" class="form-label fw-bold">Invoice Address *</label>
                        <textarea name="invoice_address" id="invoice_address" rows="2" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="2" class="form-control" placeholder="Any additional notes..."></textarea>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>DO Items</h5>
                        <button type="button" id="add-item" class="btn btn-sm btn-gradient">
                            <i class="fas fa-plus-circle"></i> Add Item
                        </button>
                    </div>

                    <div id="items-container">
                        <div class="item-row row mb-3">
                            <div class="col-md-3">
                                <input type="text" name="items[0][item_no]" class="form-control" placeholder="Item No." required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="items[0][description]" class="form-control" placeholder="Description" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][quantity]" class="form-control" placeholder="Qty" step="1" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-item" style="display:none;">X</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-gradient-success btn-lg">
                            <i class="fas fa-paper-plane me-2"></i> Create & Submit for Review
                        </button>
                        <a href="{{ route('do.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ $dashboardRoute }}" class="btn btn-secondary">Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const newRow = document.createElement('div');
    newRow.className = 'item-row row mb-3';
    newRow.innerHTML = `
        <div class="col-md-3">
            <input type="text" name="items[${itemIndex}][item_no]" class="form-control" placeholder="Item No." required>
        </div>
        <div class="col-md-5">
            <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="Description" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Qty" step="1" min="1" required>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-item">X</button>
        </div>
    `;
    container.appendChild(newRow);
    itemIndex++;
    attachEvents();
});

function attachEvents() {
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.removeEventListener('click', removeItem);
        btn.addEventListener('click', removeItem);
    });
}

function removeItem(e) {
    if (document.querySelectorAll('.item-row').length > 1) {
        e.target.closest('.item-row').remove();
    }
}

attachEvents();
</script>
@endpush
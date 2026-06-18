@extends('layouts.app')

@section('title', 'Edit DO')

@section('content')
@php
    $isVendorGuard = auth()->guard('vendor')->check();
    $dashboardRoute = $isVendorGuard ? route('vendor.dashboard') : route('dashboard');
@endphp

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-edit me-2"></i> Edit DO: {{ $do->do_number }}
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Your Delivery Order will be automatically resubmitted for review after updating.
                </div>

                <form method="POST" action="{{ route('do.update', $do->do_id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="do_number" class="form-label fw-bold">DO Number *</label>
                                <input type="text" name="do_number" id="do_number" class="form-control" 
                                       value="{{ $do->do_number }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="po_number" class="form-label fw-bold">PO Number *</label>
                                <input type="text" name="po_number" id="po_number" class="form-control" 
                                       value="{{ $do->po_number }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order_date" class="form-label fw-bold">Order Date *</label>
                                <input type="date" name="order_date" id="order_date" class="form-control" 
                                       value="{{ $do->order_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delivery_date" class="form-label fw-bold">Delivery Date *</label>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control" 
                                       value="{{ $do->delivery_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delivery_time" class="form-label fw-bold">Delivery Time *</label>
                                <input type="time" name="delivery_time" id="delivery_time" class="form-control" 
                                       value="{{ $do->delivery_time }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="shipping_address" class="form-label fw-bold">Shipping Address *</label>
                        <textarea name="shipping_address" id="shipping_address" rows="2" class="form-control" required>{{ $do->shipping_address }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="invoice_address" class="form-label fw-bold">Invoice Address *</label>
                        <textarea name="invoice_address" id="invoice_address" rows="2" class="form-control" required>{{ $do->invoice_address }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label fw-bold">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="2" class="form-control">{{ $do->remarks }}</textarea>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>DO Items</h5>
                        <button type="button" id="add-item" class="btn btn-sm btn-gradient">
                            <i class="fas fa-plus-circle"></i> Add Item
                        </button>
                    </div>

                    <div id="items-container">
                        @foreach($do->items as $index => $item)
                        <div class="item-row row mb-3">
                            <div class="col-md-3">
                                <input type="text" name="items[{{ $index }}][item_no]" class="form-control" 
                                       value="{{ $item->item_no }}" placeholder="Item No." required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="items[{{ $index }}][description]" class="form-control" 
                                       value="{{ $item->description }}" placeholder="Description" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control" 
                                       value="{{ $item->quantity }}" placeholder="Qty" step="1" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-item" {{ count($do->items) == 1 ? 'style=display:none;' : '' }}>X</button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-gradient-success btn-lg">
                            <i class="fas fa-save me-2"></i> Update & Resubmit for Review
                        </button>
                        <a href="{{ route('do.show', $do->do_id) }}" class="btn btn-secondary">Cancel</a>
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
let itemIndex = {{ count($do->items) }};

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
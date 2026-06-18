@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
@php
    $isVendorGuard = auth()->guard('vendor')->check();
    $dashboardRoute = $isVendorGuard ? route('vendor.dashboard') : route('dashboard');
@endphp

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-edit me-2"></i> Edit Invoice: {{ $invoice->invoice_no }}
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    You can edit this invoice while it is in <strong>Submitted</strong> or <strong>Rejected</strong> status.
                </div>

                <form method="POST" action="{{ route('invoices.update', $invoice->invoice_id) }}" id="invoiceForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="do_id" class="form-label fw-bold">Approved Delivery Order</label>
                        <select name="do_id" id="do_id" class="form-select" required>
                            <option value="">Select DO</option>
                            @foreach($deliveryOrders as $do)
                                <option value="{{ $do->do_id }}" 
                                        data-delivery-date="{{ $do->delivery_date }}"
                                        {{ $do->do_id == $invoice->do_id ? 'selected' : '' }}>
                                    {{ $do->do_number }} - PO: {{ $do->po_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="invoice_date" class="form-label fw-bold">Invoice Date</label>
                        <input type="date" name="invoice_date" id="invoice_date" class="form-control" 
                               value="{{ $invoice->invoice_date->format('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="credit_note" class="form-label fw-bold">Credit Note (Discount from Supplier)</label>
                        <input type="number" name="credit_note" id="credit_note" class="form-control" 
                               step="0.01" min="0" value="{{ $invoice->credits }}">
                        <small class="text-muted">Enter amount if supplier provided credit note</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Invoice Items</h5>
                        <button type="button" id="add-item" class="btn btn-sm btn-gradient">
                            <i class="fas fa-plus-circle"></i> Add Item
                        </button>
                    </div>

                    <div id="items-container">
                        @foreach($invoice->items as $index => $item)
                        <div class="item-row row mb-3">
                            <div class="col-md-3">
                                <input type="text" name="items[{{ $index }}][product_code]" class="form-control" 
                                       placeholder="Product Code" value="{{ $item->product_code }}" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="items[{{ $index }}][description]" class="form-control" 
                                       placeholder="Description" value="{{ $item->description }}" required>
                            </div>
                            <div class="col-md-1">
                                <input type="text" name="items[{{ $index }}][uom]" class="form-control" 
                                       placeholder="UOM" value="{{ $item->uom }}">
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty" 
                                       placeholder="Qty" step="1" min="1" value="{{ $item->quantity }}" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[{{ $index }}][unit_price]" class="form-control price" 
                                       placeholder="Unit Price" step="0.01" min="0" value="{{ $item->unit_price }}" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-item" {{ count($invoice->items) == 1 ? 'style=display:none;' : '' }}>X</button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Line Total</th>
                                    <td class="text-end">RM <span id="lineTotal">{{ number_format($invoice->line_total, 2) }}</span></td>
                                </tr>
                                <tr id="penaltyRow" style="display: {{ $invoice->penalty > 0 ? 'table-row' : 'none' }};">
                                    <th>Penalty (Late 1%)</th>
                                    <td class="text-end text-danger">- RM <span id="penalty">{{ number_format($invoice->penalty, 2) }}</span></td>
                                </tr>
                                <tr id="discountRow" style="display: {{ $invoice->discount > 0 ? 'table-row' : 'none' }};">
                                    <th>Discount (Credit Note)</th>
                                    <td class="text-end text-success">- RM <span id="discountAmount">{{ number_format($invoice->discount, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Service Tax (6%)</th>
                                    <td class="text-end">+ RM <span id="taxAmount">{{ number_format($invoice->service_tax, 2) }}</span></td>
                                </tr>
                                <tr class="table-primary">
                                    <th><strong>Total Claim</strong></th>
                                    <td class="text-end"><strong>RM <span id="totalAmount">{{ number_format($invoice->total, 2) }}</span></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- FIXED: Cancel buttons use dashboard route -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-gradient-success btn-lg">
                            <i class="fas fa-save me-2"></i> Update Invoice
                        </button>
                        <a href="{{ route('invoices.show', $invoice->invoice_id) }}" class="btn btn-secondary">Cancel</a>
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
let itemIndex = {{ count($invoice->items) }};

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        const price = parseFloat(row.querySelector('.price')?.value) || 0;
        subtotal += qty * price;
    });

    let creditNote = parseFloat(document.getElementById('credit_note')?.value) || 0;
    
    let penalty = 0;
    let selectedOption = document.getElementById('do_id').options[document.getElementById('do_id').selectedIndex];
    let deliveryDate = selectedOption.getAttribute('data-delivery-date');
    
    if (deliveryDate) {
        let doDate = new Date(deliveryDate);
        let today = new Date();
        let expectedDate = new Date(doDate);
        expectedDate.setDate(expectedDate.getDate() + 30);
        
        if (today > expectedDate) {
            penalty = subtotal * 0.01;
        }
    }

    let afterPenalty = subtotal - penalty;
    let afterDiscount = afterPenalty - creditNote;
    let tax = afterDiscount * 0.06;
    let total = afterDiscount + tax;

    document.getElementById('lineTotal').textContent = subtotal.toFixed(2);
    document.getElementById('penalty').textContent = penalty.toFixed(2);
    document.getElementById('discountAmount').textContent = creditNote.toFixed(2);
    document.getElementById('taxAmount').textContent = tax.toFixed(2);
    document.getElementById('totalAmount').textContent = total.toFixed(2);

    document.getElementById('penaltyRow').style.display = penalty > 0 ? 'table-row' : 'none';
    document.getElementById('discountRow').style.display = creditNote > 0 ? 'table-row' : 'none';
}

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const newRow = document.createElement('div');
    newRow.className = 'item-row row mb-3';
    newRow.innerHTML = `
        <div class="col-md-3">
            <input type="text" name="items[${itemIndex}][product_code]" class="form-control" placeholder="Product Code" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="Description" required>
        </div>
        <div class="col-md-1">
            <input type="text" name="items[${itemIndex}][uom]" class="form-control" placeholder="UOM" value="EA">
        </div>
        <div class="col-md-1">
            <input type="number" name="items[${itemIndex}][quantity]" class="form-control qty" placeholder="Qty" step="1" min="1" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="items[${itemIndex}][unit_price]" class="form-control price" placeholder="Unit Price" step="0.01" min="0" required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger remove-item">X</button>
        </div>
    `;
    container.appendChild(newRow);
    itemIndex++;
    attachEvents();
});

function attachEvents() {
    document.querySelectorAll('.qty, .price, #credit_note, #do_id').forEach(input => {
        input.removeEventListener('input', calculateTotals);
        input.removeEventListener('change', calculateTotals);
        input.addEventListener('input', calculateTotals);
        input.addEventListener('change', calculateTotals);
    });
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.removeEventListener('click', function(e) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotals();
            }
        });
        btn.addEventListener('click', function(e) {
            if (document.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotals();
            }
        });
    });
}

attachEvents();
calculateTotals();
</script>
@endpush
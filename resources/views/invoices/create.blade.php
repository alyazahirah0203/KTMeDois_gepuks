@extends('layouts.app')

@section('title', 'Submit Invoice')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-file-invoice me-2"></i> Submit New Invoice
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('invoices.store') }}" enctype="multipart/form-data" id="invoiceForm">
                    @csrf

                    <div class="mb-3">
                        <label for="do_id" class="form-label fw-bold">Select Approved Delivery Order</label>
                        <select name="do_id" id="do_id" class="form-select" required>
                            <option value="">-- Select Delivery Order --</option>
                            @foreach($deliveryOrders as $do)
                                <option value="{{ $do->do_id }}" data-do-number="{{ $do->do_number }}">
                                    {{ $do->do_number }} - PO: {{ $do->po_number }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select a DO to automatically load its items</small>
                    </div>

                    <div class="mb-3">
                        <label for="invoice_date" class="form-label fw-bold">Invoice Date</label>
                        <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="credit_note" class="form-label fw-bold">Credit Note (Discount from Supplier)</label>
                        <input type="number" name="credit_note" id="credit_note" class="form-control" step="0.01" min="0" value="0">
                        <small class="text-muted">Enter amount if supplier provided credit note</small>
                    </div>

                    <div class="mb-3">
                        <label for="proof_of_delivery" class="form-label fw-bold">Proof of Delivery</label>
                        <input type="file" name="proof_of_delivery" id="proof_of_delivery" class="form-control" accept=".pdf,.jpg,.png" required>
                        <small class="text-muted">Delivery slip or acknowledgement receipt (PDF, JPG, PNG, Max: 5MB)</small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Invoice Items</h5>
                        <button type="button" id="add-item" class="btn btn-sm btn-gradient">
                            <i class="fas fa-plus-circle"></i> Add Manual Item
                        </button>
                    </div>

                    <div id="items-container">
                        <div class="alert alert-info" id="no-items-message">
                            <i class="fas fa-info-circle me-2"></i> 
                            Select a Delivery Order to load its items automatically.
                        </div>
                        <table class="table table-bordered" id="items-table" style="display: none;">
                            <thead>
                                <tr class="table-secondary">
                                    <th>Product Code</th>
                                    <th>Description</th>
                                    <th>UOM</th>
                                    <th class="text-center" style="width: 100px;">Quantity</th>
                                    <th class="text-end">Unit Price (RM)</th>
                                    <th class="text-end">Amount (RM)</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody">
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <th>Line Total:</th>
                                            <td class="text-end">RM <span id="lineTotal">0.00</span></td>
                                        </tr>
                                        <tr id="penaltyRow" style="display:none;">
                                            <th class="text-danger">Penalty (Late 1%):</th>
                                            <td class="text-end text-danger">- RM <span id="penalty">0.00</span></td>
                                        </tr>
                                        <tr id="discountRow" style="display:none;">
                                            <th class="text-success">Discount (Credit Note):</th>
                                            <td class="text-end text-success">- RM <span id="discountAmount">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <th>Service Tax (6%):</th>
                                            <td class="text-end">+ RM <span id="taxAmount">0.00</span></td>
                                        </tr>
                                        <tr class="table-primary">
                                            <th><strong>Total Claim:</strong></th>
                                            <td class="text-end"><strong>RM <span id="totalAmount">0.00</span></strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-gradient-success btn-lg">
                            <i class="fas fa-paper-plane me-2"></i> Submit Invoice
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 0;
let currentDOHtml = '';

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
        const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
        subtotal += qty * price;
        
        const amount = qty * price;
        const amountSpan = row.querySelector('.item-amount');
        if (amountSpan) {
            amountSpan.textContent = amount.toFixed(2);
        }
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

function attachItemEvents() {
    document.querySelectorAll('.item-qty, .item-price, #credit_note').forEach(input => {
        input.removeEventListener('input', calculateTotals);
        input.addEventListener('input', calculateTotals);
    });
    
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.removeEventListener('click', function(e) {
            const row = e.target.closest('.item-row');
            if (row && document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateTotals();
            } else if (row) {
                alert('You must have at least one item.');
            }
        });
        btn.addEventListener('click', function(e) {
            const row = e.target.closest('.item-row');
            if (row && document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateTotals();
            } else if (row) {
                alert('You must have at least one item.');
            }
        });
    });
}

function loadDOItems(doId) {
    if (!doId) {
        document.getElementById('items-table').style.display = 'none';
        document.getElementById('no-items-message').style.display = 'block';
        return;
    }
    
    fetch(`/api/do-items/${doId}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('items-tbody');
            tbody.innerHTML = '';
            itemIndex = 0;
            
            if (data.items && data.items.length > 0) {
                data.items.forEach((item, index) => {
                    addItemRow({
                        product_code: item.item_no,
                        description: item.description,
                        uom: 'EA',
                        quantity: item.quantity,
                        unit_price: 0
                    }, index);
                });
                document.getElementById('items-table').style.display = 'table';
                document.getElementById('no-items-message').style.display = 'none';
            } else {
                document.getElementById('items-table').style.display = 'none';
                document.getElementById('no-items-message').style.display = 'block';
                addItemRow(null, 0);
            }
            
            calculateTotals();
        })
        .catch(error => {
            console.error('Error loading DO items:', error);
            document.getElementById('items-table').style.display = 'none';
            document.getElementById('no-items-message').style.display = 'block';
            addItemRow(null, 0);
        });
}

function addItemRow(itemData = null, index = null) {
    const tbody = document.getElementById('items-tbody');
    const rowId = index !== null ? index : itemIndex;
    const productCode = itemData ? itemData.product_code : '';
    const description = itemData ? itemData.description : '';
    const uom = itemData ? (itemData.uom || 'EA') : 'EA';
    const quantity = itemData ? itemData.quantity : 1;
    const unitPrice = itemData ? itemData.unit_price : 0;
    
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td>
            <input type="text" name="items[${rowId}][product_code]" class="form-control form-control-sm" value="${escapeHtml(productCode)}" required>
        </td>
        <td>
            <input type="text" name="items[${rowId}][description]" class="form-control form-control-sm" value="${escapeHtml(description)}" required>
        </td>
        <td>
            <input type="text" name="items[${rowId}][uom]" class="form-control form-control-sm" value="${escapeHtml(uom)}">
        </td>
        <td class="text-center">
            <input type="number" name="items[${rowId}][quantity]" class="form-control form-control-sm item-qty" value="${quantity}" step="1" min="1" style="width: 80px; margin: 0 auto;" required>
        </td>
        <td class="text-end">
            <input type="number" name="items[${rowId}][unit_price]" class="form-control form-control-sm item-price text-end" value="${unitPrice}" step="0.01" min="0" style="width: 120px;" required>
        </td>
        <td class="text-end align-middle">
            <span class="item-amount">${(quantity * unitPrice).toFixed(2)}</span>
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-danger remove-item-btn">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    itemIndex++;
    attachItemEvents();
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

document.getElementById('do_id').addEventListener('change', function() {
    const doId = this.value;
    if (doId) {
        loadDOItems(doId);
    } else {
        document.getElementById('items-table').style.display = 'none';
        document.getElementById('no-items-message').style.display = 'block';
    }
});

document.getElementById('add-item').addEventListener('click', function() {
    addItemRow(null, itemIndex);
    document.getElementById('items-table').style.display = 'table';
    document.getElementById('no-items-message').style.display = 'none';
});

attachItemEvents();
calculateTotals();
</script>
@endpush
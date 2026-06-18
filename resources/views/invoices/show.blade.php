@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
@php
    // Check which guard is authenticated
    $isVendorGuard = auth()->guard('vendor')->check();
    $isWebGuard = auth()->check();
    $isVendor = false;
    $isOfficer = false;
    
    if ($isVendorGuard) {
        $isVendor = true;
    } elseif ($isWebGuard) {
        $user = auth()->user();
        $isVendor = $user->isVendor();
        $isOfficer = $user->isOfficer();
    }
    
    // Determine dashboard route based on guard
    $dashboardRoute = $isVendorGuard ? route('vendor.dashboard') : route('dashboard');
@endphp

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Invoice Details: {{ $invoice->invoice_no }}</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_no }}</p>
                <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d-m-Y') }}</p>
                <p><strong>Due Date:</strong> {{ $invoice->due_date->format('d-m-Y') }}</p>
                <p><strong>UUID:</strong> {{ $invoice->uuid }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>DO Number:</strong> {{ $invoice->deliveryOrder->do_number ?? 'N/A' }}</p>
                <p><strong>Vendor:</strong> {{ $invoice->vendor_name }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $invoice->status == 'Submitted' ? 'warning' : ($invoice->status == 'Finance Review' ? 'info' : ($invoice->status == 'Payment Processing' ? 'primary' : 'success')) }}">
                        {{ $invoice->status }}
                    </span>
                </p>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <div class="card-modern bg-white">
                    <div class="card-header-custom">
                        <i class="fas fa-chart-simple me-2"></i> Status Process
                    </div>
                    <div class="card-body p-4">
                        <div class="steps-wrapper">
                            <div class="steps-container">
                                @php
                                    $stepStatuses = [
                                        'Submitted' => [
                                            'icon' => 'fa-file-invoice', 
                                            'color' => '#ffc107', 
                                            'bg' => '#fff3cd',
                                            'desc' => 'Invoice submitted and waiting for review'
                                        ],
                                        'Finance Review' => [
                                            'icon' => 'fa-clipboard-list', 
                                            'color' => '#17a2b8', 
                                            'bg' => '#d1ecf1',
                                            'desc' => 'Being reviewed by finance team'
                                        ],
                                        'Payment Processing' => [
                                            'icon' => 'fa-spinner', 
                                            'color' => '#007bff', 
                                            'bg' => '#cce5ff',
                                            'desc' => 'Payment is being processed'
                                        ],
                                        'Paid' => [
                                            'icon' => 'fa-check-circle', 
                                            'color' => '#28a745', 
                                            'bg' => '#d4edda',
                                            'desc' => 'Payment completed successfully'
                                        ]
                                    ];
                                    $currentStep = $invoice->status;
                                    $stepNames = array_keys($stepStatuses);
                                    $currentIndex = array_search($currentStep, $stepNames);
                                @endphp

                                @foreach($stepStatuses as $stepName => $stepInfo)
                                    @php
                                        $stepIndex = array_search($stepName, $stepNames);
                                        $isCompleted = $stepIndex < $currentIndex;
                                        $isActive = $stepName == $currentStep;
                                        $isPaymentProcessing = ($stepName == 'Payment Processing' && $currentStep == 'Payment Processing');
                                        $isPaid = ($stepName == 'Paid' && $currentStep == 'Paid');
                                    @endphp
                                    
                                    <div class="step-item {{ $isActive ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}">
                                        <div class="step-icon">
                                            <div class="icon-circle {{ $isActive ? 'active-circle' : '' }} {{ $isCompleted ? 'completed-circle' : '' }}">
                                                @if($isCompleted || $isPaid)
                                                    <i class="fas fa-check"></i>
                                                @elseif($isPaymentProcessing)
                                                    <i class="fas fa-spinner fa-pulse"></i>
                                                @else
                                                    <i class="fas {{ $stepInfo['icon'] }}"></i>
                                                @endif
                                            </div>
                                            <div class="step-number">{{ $stepIndex + 1 }}</div>
                                        </div>
                                        <div class="step-info">
                                            <div class="step-title {{ $isActive ? 'active-title' : '' }} {{ $isCompleted ? 'completed-title' : '' }}">
                                                {{ $stepName }}
                                                @if($isPaymentProcessing)
                                                    <span class="processing-badge">
                                                        <i class="fas fa-sync-alt fa-spin"></i> IN PROGRESS
                                                    </span>
                                                @endif
                                                @if($isPaid)
                                                    <span class="paid-badge">
                                                        <i class="fas fa-check-circle"></i> COMPLETED
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="step-description">{{ $stepInfo['desc'] }}</div>
                                            
                                            @if($isPaymentProcessing)
                                                <div class="step-loading mt-2">
                                                    <div class="loading-bar">
                                                        <div class="loading-progress"></div>
                                                    </div>
                                                    <span class="loading-text">Processing payment... Please wait</span>
                                                </div>
                                            @endif
                                            
                                            @if($isPaid)
                                                <div class="mt-2">
                                                    <span class="badge-paid">
                                                        <i class="fas fa-calendar-check"></i> Paid on {{ $invoice->updated_at->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        @if(!$loop->last)
                                            <div class="step-connector {{ $isCompleted ? 'connector-completed' : '' }} {{ $isActive ? 'connector-active' : '' }}"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Summary Card -->
                            <div class="summary-card mt-4 p-4 rounded-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if($currentStep == 'Paid')
                                                    <div class="summary-icon-paid">
                                                        <i class="fas fa-check-circle fa-3x"></i>
                                                    </div>
                                                @elseif($currentStep == 'Payment Processing')
                                                    <div class="summary-icon-processing">
                                                        <i class="fas fa-spinner fa-pulse fa-3x"></i>
                                                    </div>
                                                @else
                                                    <div class="summary-icon-default">
                                                        <i class="fas fa-chart-line fa-3x"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Claim Summary</h6>
                                                <small class="text-muted">Invoice #{{ $invoice->invoice_no }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <div class="d-inline-block me-4 text-center">
                                            <div class="small text-muted">Total Amount</div>
                                            <h4 class="mb-0 text-primary fw-bold">RM {{ number_format($invoice->total, 2) }}</h4>
                                        </div>
                                        <div class="d-inline-block text-center">
                                            <div class="small text-muted">Balance Due</div>
                                            <h4 class="mb-0 fw-bold {{ $invoice->balance_due > 0 ? 'text-warning' : 'text-success' }}">
                                                RM {{ number_format($invoice->balance_due, 2) }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .steps-wrapper {
            padding: 30px 20px;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
            border-radius: 20px;
        }

        .steps-container {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 30px;
        }

        .step-item {
            flex: 1;
            position: relative;
            text-align: center;
            padding: 20px 10px;
            z-index: 2;
        }

        .step-icon {
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #e8ecf1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #6c757d;
            margin: 0 auto;
            transition: all 0.3s ease;
            position: relative;
            border: 3px solid #dee2e6;
        }

        .step-item.completed .icon-circle {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-color: #28a745;
            transform: scale(1.05);
        }

        .step-item.active .icon-circle {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            color: white;
            border-color: #007bff;
            transform: scale(1.15);
            box-shadow: 0 0 0 8px rgba(0,123,255,0.2), 0 10px 20px rgba(0,0,0,0.1);
        }

        .step-item.active .icon-circle i {
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .step-item.completed:last-child .icon-circle {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 0 0 8px rgba(40,167,69,0.2);
        }

        .step-number {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 28px;
            height: 28px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: bold;
            color: #333;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            border: 2px solid #fff;
        }

        .step-connector {
            position: absolute;
            top: 55px;
            left: 50%;
            width: 100%;
            height: 6px;
            background: #e0e4e8;
            z-index: 0;
            border-radius: 3px;
        }

        .step-connector.connector-completed {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            height: 8px;
            box-shadow: 0 2px 4px rgba(40,167,69,0.3);
        }

        .step-connector.connector-active {
            background: linear-gradient(90deg, #007bff 0%, #6610f2 100%);
            height: 8px;
            box-shadow: 0 0 8px rgba(0,123,255,0.5);
            animation: connectorPulse 1.5s infinite;
        }

        @keyframes connectorPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0,123,255,0.4);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(0,123,255,0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0,123,255,0);
            }
        }

        .step-item:last-child .step-connector {
            display: none;
        }

        .step-info {
            margin-top: 15px;
        }

        .step-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 6px;
            color: #495057;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .step-title.active-title {
            color: #007bff;
            font-size: 18px;
            font-weight: 800;
            text-shadow: 0 0 8px rgba(0,123,255,0.2);
        }

        .step-title.completed-title {
            color: #28a745;
        }

        .step-description {
            font-size: 11px;
            color: #6c757d;
            max-width: 180px;
            margin: 0 auto;
        }

        .processing-badge {
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            animation: badgePulse 1.5s infinite;
        }

        @keyframes badgePulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0,123,255,0.4);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(0,123,255,0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0,123,255,0);
            }
        }

        .paid-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-paid {
            background: #d4edda;
            color: #155724;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .step-loading {
            margin-top: 12px;
            padding: 0 20px;
        }

        .loading-bar {
            width: 100%;
            height: 6px;
            background: #e0e4e8;
            border-radius: 3px;
            overflow: hidden;
        }

        .loading-progress {
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, #007bff 0%, #6610f2 100%);
            border-radius: 3px;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                width: 0%;
                margin-left: 0%;
            }
            50% {
                width: 100%;
                margin-left: 0%;
            }
            100% {
                width: 0%;
                margin-left: 100%;
            }
        }

        .loading-text {
            font-size: 10px;
            color: #007bff;
            display: inline-block;
            margin-left: 8px;
            font-weight: 600;
        }

        .summary-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .summary-icon-paid {
            background: #d4edda;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #28a745;
        }

        .summary-icon-processing {
            background: #cce5ff;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #007bff;
        }

        .summary-icon-default {
            background: #e8ecf1;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .steps-container {
                flex-direction: column;
            }
            .step-connector {
                display: none;
            }
            .step-item {
                margin-bottom: 25px;
            }
            .icon-circle {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
            .step-item.active .icon-circle {
                transform: scale(1.08);
            }
            .step-title {
                font-size: 14px;
            }
            .step-title.active-title {
                font-size: 16px;
            }
        }
        </style>

        <hr>

        <h5>Invoice Items</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Code</th>
                    <th>Description</th>
                    <th>UOM</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Unit Price (RM)</th>
                    <th class="text-end">Amount (RM)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->product_code }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->uom }}</td>
                    <td class="text-end">{{ $item->quantity }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Line Total:</th>
                    <th class="text-end">RM {{ number_format($invoice->line_total, 2) }}</th>
                </tr>
                @if($invoice->discount > 0)
                <tr>
                    <th colspan="5" class="text-end text-success">Discount (Credit Note):</th>
                    <th class="text-end text-success">- RM {{ number_format($invoice->discount, 2) }}</th>
                </tr>
                @endif
                @if($invoice->penalty > 0)
                <tr>
                    <th colspan="5" class="text-end text-danger">Penalty (Late 1%):</th>
                    <th class="text-end text-danger">- RM {{ number_format($invoice->penalty, 2) }}</th>
                </tr>
                @endif
                <tr>
                    <th colspan="5" class="text-end">Service Tax (6%):</th>
                    <th class="text-end">+ RM {{ number_format($invoice->service_tax, 2) }}</th>
                </tr>
                <tr class="table-primary">
                    <th colspan="5" class="text-end">Total:</th>
                    <th class="text-end">RM {{ number_format($invoice->total, 2) }}</th>
                </tr>
                @if($invoice->payments > 0)
                <tr>
                    <th colspan="5" class="text-end">Payments Received:</th>
                    <th class="text-end">- RM {{ number_format($invoice->payments, 2) }}</th>
                </tr>
                <tr class="table-info">
                    <th colspan="5" class="text-end">Balance Due:</th>
                    <th class="text-end">RM {{ number_format($invoice->balance_due, 2) }}</th>
                </tr>
                @endif
            </tfoot>
        </table>

        <!-- Payment Information Section -->
        @if($invoice->payment)
        <hr>
        <h5><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
        <div class="card bg-light">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($invoice->payment->payment_date)->format('d-m-Y') }}</p>
                        <p><strong>Payment Amount:</strong> RM {{ number_format($invoice->payment->payment_amount, 2) }}</p>
                        <p><strong>Payment Method:</strong> {{ $invoice->payment->payment_method }}</p>
                    </div>
                    <div class="col-md-6">
                        @if($invoice->payment->transaction_ref)
                            <p><strong>Transaction Ref:</strong> {{ $invoice->payment->transaction_ref }}</p>
                        @endif
                        @if($invoice->payment->remarks)
                            <p><strong>Remarks:</strong> {{ $invoice->payment->remarks }}</p>
                        @endif
                        @if($invoice->payment->proof_of_payment)
                            <p><strong>Proof of Payment:</strong> 
                                <a href="{{ asset('storage/' . $invoice->payment->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file me-1"></i> View Document
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Vendor Actions - Edit & Delete -->
        @if($isVendor && in_array($invoice->status, ['Submitted', 'Rejected']))
        <div class="mt-4">
            <div class="btn-group">
                <a href="{{ route('invoices.edit', $invoice->invoice_id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit Invoice
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i> Delete Invoice
                </button>
            </div>
        </div>

        <!-- Delete Form -->
        <form id="delete-form-{{ $invoice->invoice_id }}" method="POST" action="{{ route('invoices.destroy', $invoice->invoice_id) }}" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        @endif

        <!-- Back to Dashboard button - FIXED -->
        <div class="text-center mt-4">
            <a href="{{ route('invoices.download', $invoice->invoice_id) }}" class="btn btn-success">Download Invoice PDF</a>
            <a href="{{ route('invoices.track') }}" class="btn btn-secondary">Track Another Invoice</a>
            <a href="{{ $dashboardRoute }}" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this invoice?</p>
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_no }}</p>
                <p><strong>Total Amount:</strong> RM {{ number_format($invoice->total, 2) }}</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Invoice</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
        document.getElementById('delete-form-{{ $invoice->invoice_id }}').submit();
    });
</script>

@endsection
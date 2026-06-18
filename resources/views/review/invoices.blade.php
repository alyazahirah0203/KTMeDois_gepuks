@extends('layouts.officer')

@section('title', 'Review Invoices')
@section('page-title', auth()->user()->isReviewOfficer() ? 'Review Invoices' : 'Invoice Payment')

@section('content')
<div class="card-modern bg-white">
    <div class="card-header-custom">
        <i class="fas fa-clipboard-list me-2"></i> 
        {{ auth()->user()->isReviewOfficer() ? 'Invoice Review' : 'Finance Payment Processing' }}
    </div>
    <div class="card-body p-3">
        @php
            $user = auth()->user();
            $isReviewOfficer = $user->isReviewOfficer();
            $isFinanceOfficer = $user->isFinanceOfficer();
        @endphp

        <ul class="nav nav-tabs" id="reviewTabs" role="tablist">
            @if($isReviewOfficer)
            <li class="nav-item">
                <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending">
                    Pending <span class="badge bg-warning ms-1">{{ $pendingInvoices->count() }}</span>
                </a>
            </li>
            @endif

            @if($isFinanceOfficer)
            <li class="nav-item">
                <a class="nav-link active" id="reviewing-tab" data-bs-toggle="tab" href="#reviewing">
                    Finance Review <span class="badge bg-info ms-1">{{ $reviewingInvoices->count() }}</span>
                </a>
            </li>
            @endif

            @if($isReviewOfficer)
            <li class="nav-item">
                <a class="nav-link" id="reviewing-tab" data-bs-toggle="tab" href="#reviewing">
                    Reviewing <span class="badge bg-info ms-1">{{ $reviewingInvoices->count() }}</span>
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a class="nav-link" id="processing-tab" data-bs-toggle="tab" href="#processing">
                    {{ $isFinanceOfficer ? 'Payment Processing' : 'Processing' }}
                    <span class="badge bg-primary ms-1">{{ $processingInvoices->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="paid-tab" data-bs-toggle="tab" href="#paid">
                    Paid <span class="badge bg-success ms-1">{{ $paidInvoices->count() }}</span>
                </a>
            </li>

            @if($isReviewOfficer)
            <li class="nav-item">
                <a class="nav-link" id="rejected-tab" data-bs-toggle="tab" href="#rejected">
                    Rejected <span class="badge bg-danger ms-1">{{ $rejectedInvoices->count() ?? 0 }}</span>
                </a>
            </li>
            @endif
        </ul>

        <div class="tab-content mt-3">
            @if($isReviewOfficer)
            <div class="tab-pane fade show active" id="pending">
                @include('review._table', ['invoices' => $pendingInvoices, 'status' => 'pending'])
            </div>
            @endif

            @if($isFinanceOfficer)
            <div class="tab-pane fade show active" id="reviewing">
                @include('review._table', ['invoices' => $reviewingInvoices, 'status' => 'reviewing'])
            </div>
            @endif

            @if($isReviewOfficer)
            <div class="tab-pane fade" id="reviewing">
                @include('review._table', ['invoices' => $reviewingInvoices, 'status' => 'reviewing'])
            </div>
            @endif

            <div class="tab-pane fade" id="processing">
                @include('review._table', ['invoices' => $processingInvoices, 'status' => 'processing'])
            </div>

            <div class="tab-pane fade" id="paid">
                @include('review._table', ['invoices' => $paidInvoices, 'status' => 'paid'])
            </div>

            @if($isReviewOfficer)
            <div class="tab-pane fade" id="rejected">
                @include('review._table', ['invoices' => $rejectedInvoices ?? [], 'status' => 'rejected'])
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function initDataTableIfHasData(tableSelector) {
            var table = $(tableSelector);
            if (table.length === 0) return;
            
            var rowCount = table.find('tbody tr:not(:has(td[colspan]))').length;
            
            if (rowCount > 0) {
                table.DataTable({
                    responsive: true,
                    order: [[2, 'desc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    language: {
                        search: "<i class='fas fa-search'></i> Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ invoices",
                        infoEmpty: "No invoices found",
                        infoFiltered: "(filtered from _MAX_ total invoices)",
                        zeroRecords: "No matching invoices found"
                    }
                });
            }
        }

        var activeTab = $('.tab-pane.active');
        var table = activeTab.find('table');
        var tableId = table.attr('id');
        if (tableId) {
            initDataTableIfHasData('#' + tableId);
        }

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('href');
            var table = $(target).find('table');
            var tableId = table.attr('id');
            if (tableId) {
                if ($.fn.DataTable.isDataTable('#' + tableId)) {
                    $('#' + tableId).DataTable().destroy();
                }
                initDataTableIfHasData('#' + tableId);
            }
        });
    });
</script>
@endpush
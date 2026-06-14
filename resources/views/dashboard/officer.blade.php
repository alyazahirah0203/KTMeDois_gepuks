@extends('layouts.app')

@section('title', 'Officer Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Welcome, Officer {{ auth()->user()->name }}</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title">Pending Reviews</h5>
                <h2 class="display-4">{{ $pendingReviews ?? 0 }}</h2>
                <p class="card-text">Invoices waiting for review</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Total Invoices</h5>
                <h2 class="display-4">{{ $totalInvoices ?? 0 }}</h2>
                <p class="card-text">All time invoices</p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Paid</h5>
                <h2 class="display-4">RM {{ number_format($totalPaid ?? 0, 2) }}</h2>
                <p class="card-text">Total amount paid</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Pending Invoices for Review</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Total (RM)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingInvoicesList ?? [] as $inv)
                        <tr>
                            <td>{{ $inv->invoice_no }}</td>
                            <td>{{ $inv->vendor->supplier_comp_name ?? 'N/A' }}</td>
                            <td>{{ $inv->invoice_date->format('d-m-Y') }}</td>
                            <td class="text-end">{{ number_format($inv->total, 2) }}</td>
                            <td>
                                <span class="badge bg-warning">{{ $inv->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('invoices.show', $inv->invoice_id) }}" class="btn btn-sm btn-primary">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No pending invoices.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
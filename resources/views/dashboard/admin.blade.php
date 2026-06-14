@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Welcome, Admin {{ auth()->user()->name }}</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Vendors</h5>
                <h2 class="display-4">{{ $totalVendors ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Invoices</h5>
                <h2 class="display-4">{{ $totalInvoices ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Total Amount</h5>
                <h2 class="display-4">RM {{ number_format($totalAmount ?? 0, 2) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <h2 class="display-4">{{ $totalUsers ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Welcome to KTM eDOIS</h4>
            </div>
            <div class="card-body text-center">
                <h5>Electronic Delivery Order & Invoice System</h5>
                <p class="mt-3">Streamlining submission, verification, and approval process for Delivery Orders and Invoices.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.officer')

@section('title', 'Export Reports')
@section('page-title', 'Export Reports')

@section('content')
<div class="card-modern bg-white">
    <div class="card-header-custom">
        <i class="fas fa-file-export me-2"></i> Export Invoice Reports
    </div>
    <div class="card-body p-4">
        <form method="GET" action="{{ route('review.export') }}" id="exportForm">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="start_date" class="form-label fw-bold">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ date('Y-m-01') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="end_date" class="form-label fw-bold">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="all">All Status</option>
                            <option value="Submitted">Submitted</option>
                            <option value="Finance Review">Finance Review</option>
                            <option value="Payment Processing">Payment Processing</option>
                            <option value="Paid">Paid</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Export Format</label>
                        <div class="btn-group w-100" role="group">
                            <button type="submit" name="format" value="pdf" class="btn btn-gradient-success">
                                <i class="fas fa-file-pdf me-2"></i> Export PDF
                            </button>
                            <button type="submit" name="format" value="excel" class="btn btn-gradient">
                                <i class="fas fa-file-excel me-2"></i> Export Excel (CSV)
                            </button>
                            <button type="submit" name="format" value="print" class="btn btn-secondary">
                                <i class="fas fa-print me-2"></i> Print Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        <hr>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note:</strong> PDF and Excel exports will download automatically. Print preview will open in a new window.
        </div>
    </div>
</div>
@endsection
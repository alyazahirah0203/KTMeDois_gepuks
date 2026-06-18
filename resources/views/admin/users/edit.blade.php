@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('sub-title', 'Update officer account details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-modern bg-white">
            <div class="card-header-custom">
                <i class="fas fa-user-edit me-2"></i> Edit User: {{ $user->name }}
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Leave password blank to keep current password.
                </div>

                <form method="POST" action="{{ route('dashboard.admin.users.update', $user->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Full Name *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address *</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" placeholder="Enter email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">New Password (Optional)</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Enter new password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                               placeholder="Confirm new password">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-bold">Role *</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Select Role</option>
                            <option value="review_officer" {{ old('role', $user->role) == 'review_officer' ? 'selected' : '' }}>Review Officer</option>
                            <option value="finance_officer" {{ old('role', $user->role) == 'finance_officer' ? 'selected' : '' }}>Finance Officer</option>
                            <option value="it_officer" {{ old('role', $user->role) == 'it_officer' ? 'selected' : '' }}>IT Officer</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-gradient-success btn-lg flex-grow-1">
                            <i class="fas fa-save me-2"></i> Update User
                        </button>
                        <a href="{{ route('dashboard.admin.users.index') }}" class="btn btn-secondary btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
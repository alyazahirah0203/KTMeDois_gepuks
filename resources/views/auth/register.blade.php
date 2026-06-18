<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - KTM eDOIS</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0a2463 0%, #1e3a8a 50%, #3b82f6 100%);
            padding: 40px 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset("images/train-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.1;
            pointer-events: none;
        }
        
        .register-container {
            max-width: 550px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .register-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .register-card:hover {
            transform: translateY(-5px);
        }
        
        .register-header {
            background: linear-gradient(135deg, #0a2463 0%, #1e3a8a 50%, #3b82f6 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .register-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset("images/train-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
        }
        
        .register-header::after {
            content: '🚆';
            position: absolute;
            font-size: 100px;
            opacity: 0.1;
            bottom: -20px;
            right: -20px;
        }
        
        .logo-image {
            width: 70px;
            height: 70px;
            object-fit: contain;
            background: white;
            border-radius: 20px;
            padding: 12px;
            margin-bottom: 15px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
        }
        
        .register-header h3 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        
        .register-header p {
            color: rgba(255,255,255,0.85);
            font-size: 13px;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .register-body {
            padding: 35px 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 500;
            font-size: 13px;
            color: #333;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #667eea;
        }
        
        .input-group-custom {
            position: relative;
        }
        
        .input-group-custom input,
        .input-group-custom select {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e0e7ff;
            border-radius: 15px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9ff;
            appearance: none;
            -webkit-appearance: none;
        }
        
        .input-group-custom input:focus,
        .input-group-custom select:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 4px rgba(102,126,234,0.1);
            background: white;
        }
        
        .input-group-custom .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            pointer-events: none;
        }
        
        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(102,126,234,0.4);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
            color: #6c757d;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .helper-text {
            font-size: 11px;
            color: #6c757d;
            margin-top: 5px;
            display: block;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0 20px;
            color: #adb5bd;
            font-size: 11px;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e0e7ff;
        }
        
        .divider span {
            padding: 0 15px;
        }
        
        .role-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .role-badge-vendor {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .role-badge-review {
            background: #fef3c7;
            color: #92400e;
        }
        
        .role-badge-finance {
            background: #d1fae5;
            color: #065f46;
        }
        
        .error-text {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .role-description {
            font-size: 11px;
            color: #6c757d;
            margin-top: 4px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                @if(file_exists(public_path('images/ktmb-logo.png')))
                    <img src="{{ asset('images/ktmb-logo.png') }}" alt="KTMB Logo" class="logo-image">
                @else
                    <div style="width: 70px; height: 70px; background: white; border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <i class="fas fa-train fa-2x" style="color: #1e3a8a;"></i>
                    </div>
                @endif
                <h3>Create Account</h3>
                <p>Join KTM eDOIS to manage your invoices</p>
            </div>
            
            <div class="register-body">
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <div class="input-group-custom">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Enter your full name" required autofocus>
                            <i class="fas fa-user input-icon"></i>
                        </div>
                        @error('name')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <div class="input-group-custom">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" 
                                   placeholder="your@email.com" required>
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        @error('email')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Register As</label>
                        <div class="input-group-custom">
                            <select class="form-select @error('role') is-invalid @enderror" 
                                    id="role" name="role" required>
                                <option value="vendor" {{ old('role') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                                <option value="review_officer" {{ old('role') == 'review_officer' ? 'selected' : '' }}>Review Officer</option>
                                <option value="finance_officer" {{ old('role') == 'finance_officer' ? 'selected' : '' }}>Finance Officer</option>
                            </select>
                            <i class="fas fa-chevron-down input-icon"></i>
                        </div>
                        <small class="role-description">
                            @if(old('role') == 'review_officer')
                                Review Officer: Review and approve invoices
                            @elseif(old('role') == 'finance_officer')
                                Finance Officer: Process payments and mark as paid
                            @else
                                Vendor: Submit invoices and track claims
                            @endif
                        </small>
                        @error('role')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Vendor Fields -->
                    <div id="vendorFields">
                        <div class="form-group">
                            <label><i class="fas fa-id-card"></i> Vendor ID (Supplier ID)</label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control @error('supplierid') is-invalid @enderror" 
                                       id="supplierid" name="supplierid" value="{{ old('supplierid') }}" 
                                       placeholder="Enter your Vendor ID (e.g., SAZ001)">
                                <i class="fas fa-id-card input-icon"></i>
                            </div>
                            <small class="helper-text">Required only if registering as Vendor. Must match KTMB records.</small>
                            @error('supplierid')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div class="input-group-custom">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="Create a strong password" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                        <small class="helper-text">Minimum 8 characters with letters and numbers</small>
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-check-circle"></i> Confirm Password</label>
                        <div class="input-group-custom">
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" 
                                   placeholder="Confirm your password" required>
                            <i class="fas fa-check-circle input-icon"></i>
                        </div>
                    </div>
                    
                    <div class="divider">
                        <span>Secure Registration</span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <span id="roleDisplayVendor" class="role-badge role-badge-vendor" style="display: none;">
                                <i class="fas fa-building"></i> Vendor
                            </span>
                            <span id="roleDisplayReview" class="role-badge role-badge-review" style="display: none;">
                                <i class="fas fa-clipboard-check"></i> Review Officer
                            </span>
                            <span id="roleDisplayFinance" class="role-badge role-badge-finance" style="display: none;">
                                <i class="fas fa-coins"></i> Finance Officer
                            </span>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted">All fields are required</small>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus me-2"></i> Create Account
                    </button>
                    
                    <div class="login-link">
                        Already have an account? <a href="{{ route('login') }}">Sign in here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const vendorFields = document.getElementById('vendorFields');
            const supplierIdInput = document.getElementById('supplierid');
            const roleDisplayVendor = document.getElementById('roleDisplayVendor');
            const roleDisplayReview = document.getElementById('roleDisplayReview');
            const roleDisplayFinance = document.getElementById('roleDisplayFinance');
            const roleDescription = document.querySelector('.role-description');
            
            const descriptions = {
                'vendor': 'Vendor: Submit invoices and track claims',
                'review_officer': 'Review Officer: Review and approve invoices',
                'finance_officer': 'Finance Officer: Process payments and mark as paid'
            };
            
            function toggleFields() {
                const selectedRole = roleSelect.value;
                
                vendorFields.style.display = 'none';
                roleDisplayVendor.style.display = 'none';
                roleDisplayReview.style.display = 'none';
                roleDisplayFinance.style.display = 'none';
                
                if (selectedRole === 'vendor') {
                    vendorFields.style.display = 'block';
                    supplierIdInput.setAttribute('required', 'required');
                    roleDisplayVendor.style.display = 'inline-block';
                } else {
                    supplierIdInput.removeAttribute('required');
                    supplierIdInput.value = '';
                    
                    if (selectedRole === 'review_officer') {
                        roleDisplayReview.style.display = 'inline-block';
                    } else if (selectedRole === 'finance_officer') {
                        roleDisplayFinance.style.display = 'inline-block';
                    }
                }
                
                roleDescription.textContent = descriptions[selectedRole] || '';
            }
            
            roleSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
</body>
</html>
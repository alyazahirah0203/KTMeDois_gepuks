<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - KTM eDOIS</title>
    
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
            position: relative;
            overflow-x: hidden;
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
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        .login-card {
            background: rgba(255,255,255,0.98);
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(2px);
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px -15px rgba(0,0,0,0.4);
        }
        
        .login-left {
            background: linear-gradient(135deg, #0a2463 0%, #1e3a8a 50%, #3b82f6 100%);
            padding: 50px 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-left::before {
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
            pointer-events: none;
        }
        
        .login-left::after {
            content: '🚆';
            position: absolute;
            font-size: 180px;
            opacity: 0.08;
            bottom: -30px;
            right: -30px;
            transform: rotate(-15deg);
            pointer-events: none;
        }
        
        .logo-area {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            z-index: 1;
        }
        
        .logo-image {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-bottom: 20px;
            background: white;
            border-radius: 20px;
            padding: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
        }
        
        .logo-text {
            color: white;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 2px;
        }
        
        .logo-sub {
            color: rgba(255,255,255,0.85);
            font-size: 12px;
            margin-top: 5px;
        }
        
        .welcome-text {
            color: white;
            text-align: center;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }
        
        .welcome-text h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .welcome-text p {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .feature-list {
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 12px 15px;
            border-radius: 15px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        
        .feature-item:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        
        .feature-icon {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .feature-icon i {
            font-size: 22px;
            color: white;
        }
        
        .feature-text h6 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 3px;
            color: white;
        }
        
        .feature-text p {
            font-size: 11px;
            opacity: 0.8;
            margin: 0;
            color: rgba(255,255,255,0.8);
        }
        
        .login-right {
            padding: 50px 40px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }
        
        .login-header h4 {
            font-size: 28px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 8px;
        }
        
        .login-header p {
            font-size: 13px;
            color: #6c757d;
        }
        
        .form-group {
            margin-bottom: 25px;
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
        
        .input-group-custom input {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e0e7ff;
            border-radius: 15px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9ff;
        }
        
        .input-group-custom input:focus {
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
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .checkbox-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .checkbox-custom input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }
        
        .checkbox-custom span {
            font-size: 13px;
            color: #495057;
        }
        
        .forgot-link {
            font-size: 13px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(102,126,234,0.4);
        }
        
        .register-link {
            text-align: center;
            font-size: 13px;
            color: #6c757d;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: #adb5bd;
            font-size: 12px;
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
        
        .role-selector {
            display: flex;
            gap: 15px;
            margin-top: 5px;
            flex-wrap: wrap;
        }
        
        .role-option {
            flex: 1;
            min-width: 100px;
            padding: 12px 15px;
            border: 2px solid #e0e7ff;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9ff;
        }
        
        .role-option:hover {
            border-color: #667eea;
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.15);
        }
        
        .role-option input[type="radio"] {
            display: none;
        }
        
        .role-option.active {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        }
        
        .role-option.active i {
            color: white;
        }
        
        .role-option i {
            font-size: 24px;
            color: #667eea;
            display: block;
            margin-bottom: 5px;
        }
        
        .role-option .role-label {
            font-size: 12px;
            font-weight: 600;
            display: block;
        }
        
        .role-option .role-desc {
            font-size: 10px;
            opacity: 0.7;
            display: block;
            margin-top: 2px;
        }
        
        @media (max-width: 768px) {
            .login-left {
                display: none;
            }
            .login-card {
                max-width: 450px;
            }
            .role-option {
                min-width: 80px;
                padding: 10px;
            }
            .role-option i {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="login-card">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="login-left">
                                    <div class="logo-area">
                                        @if(file_exists(public_path('images/ktmb-logo.png')))
                                            <img src="{{ asset('images/ktmb-logo.png') }}" alt="KTMB Logo" class="logo-image">
                                        @else
                                            <div style="width: 90px; height: 90px; background: white; border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                                <i class="fas fa-train fa-3x" style="color: #1e3a8a;"></i>
                                            </div>
                                        @endif
                                        <div class="logo-text">KTM eDOIS</div>
                                        <div class="logo-sub">Electronic Delivery Order & Invoice System</div>
                                    </div>
                                    
                                    <div class="welcome-text">
                                        <h3>Welcome Back!</h3>
                                        <p>Access your account to manage invoices and track claims</p>
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div class="login-right">
                                    <div class="login-header">
                                        <h4>Sign In</h4>
                                        <p>Enter your credentials to access your account</p>
                                    </div>
                                    
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label><i class="fas fa-user-tag"></i> Login As</label>
                                            <div class="role-selector">
                                                <label class="role-option {{ old('role') === 'vendor' ? 'active' : '' }}">
                                                    <input type="radio" name="role" value="vendor" {{ old('role') === 'vendor' ? 'checked' : '' }} required>
                                                    <i class="fas fa-building"></i>
                                                    <span class="role-label">Vendor</span>
                                                    <span class="role-desc">Supplier Access</span>
                                                </label>
                                                <label class="role-option {{ old('role') === 'officer' ? 'active' : '' }}">
                                                    <input type="radio" name="role" value="officer" {{ old('role') === 'officer' ? 'checked' : '' }} required>
                                                    <i class="fas fa-user-tie"></i>
                                                    <span class="role-label">Officer</span>
                                                    <span class="role-desc">Review/Finance</span>
                                                </label>
                                                <label class="role-option {{ old('role') === 'admin' ? 'active' : '' }}">
                                                    <input type="radio" name="role" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} required>
                                                    <i class="fas fa-user-cog"></i>
                                                    <span class="role-label">Admin</span>
                                                    <span class="role-desc">IT Officer</span>
                                                </label>
                                            </div>
                                            @error('role')
                                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <label><i class="fas fa-envelope"></i> Email Address</label>
                                            <div class="input-group-custom">
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                       id="email" name="email" value="{{ old('email') }}" 
                                                       placeholder="your@email.com" required autofocus>
                                                <i class="fas fa-envelope input-icon"></i>
                                            </div>
                                            @error('email')
                                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
                                            <label><i class="fas fa-lock"></i> Password</label>
                                            <div class="input-group-custom">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" name="password" placeholder="••••••••" required>
                                                <i class="fas fa-lock input-icon"></i>
                                            </div>
                                            @error('password')
                                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        
                                        <div class="remember-forgot">
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="forgot-link">
                                                    Forgot Password?
                                                </a>
                                            @endif
                                        </div>
                                        
                                        <button type="submit" class="btn-login">
                                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                                        </button>
                                        
                                        <div class="divider">
                                            <span>OR</span>
                                        </div>
                                        
                                        <!-- REMOVED: Create Account link -->
                                        <div class="register-link">
                                            <a href="{{ route('password.request') }}" class="text-primary">
                                                <i class="fas fa-key me-1"></i> Forgot Password?
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleOptions = document.querySelectorAll('.role-option');
            
            roleOptions.forEach(option => {
                option.addEventListener('click', function() {
                    roleOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                    }
                });
            });
        });
    </script>
</body>
</html>
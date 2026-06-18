<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - KTM eDOIS</title>
    
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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
        
        .forgot-container {
            max-width: 500px;
            width: 100%;
            position: relative;
            z-index: 1;
        }
        
        .forgot-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .forgot-card:hover {
            transform: translateY(-5px);
        }
        
        .forgot-header {
            background: linear-gradient(135deg, #0a2463 0%, #1e3a8a 50%, #3b82f6 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .forgot-header::before {
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
        
        .forgot-header::after {
            content: '🔐';
            position: absolute;
            font-size: 80px;
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
        
        .forgot-header h3 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        
        .forgot-header p {
            color: rgba(255,255,255,0.85);
            font-size: 13px;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .forgot-body {
            padding: 35px 30px;
        }
        
        .contact-admin-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .contact-admin-box .icon-circle {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .contact-admin-box .icon-circle i {
            font-size: 30px;
            color: white;
        }
        
        .contact-admin-box h5 {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .contact-admin-box p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .contact-admin-box .email-link {
            display: inline-block;
            color: #667eea;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            padding: 8px 20px;
            border: 2px solid #667eea;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .contact-admin-box .email-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        }
        
        .contact-admin-box .email-link i {
            margin-right: 8px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .back-link i {
            margin-right: 8px;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
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
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-card">
            <div class="forgot-header">
                @if(file_exists(public_path('images/ktmb-logo.png')))
                    <img src="{{ asset('images/ktmb-logo.png') }}" alt="KTMB Logo" class="logo-image">
                @else
                    <div style="width: 70px; height: 70px; background: white; border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                        <i class="fas fa-train fa-2x" style="color: #1e3a8a;"></i>
                    </div>
                @endif
                <h3>Forgot Password</h3>
                <p>We'll help you reset your password</p>
            </div>
            
            <div class="forgot-body">
                <div class="contact-admin-box">
                    <div class="icon-circle">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5>Contact Administrator</h5>
                    <p>
                        To reset your password, please contact our IT Support team. 
                        They will assist you in resetting your account credentials.
                    </p>
                    <a href="mailto:it@ktmb.gov.my" class="email-link">
                        <i class="fas fa-envelope"></i> it@ktmb.gov.my
                    </a>
                </div>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="back-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
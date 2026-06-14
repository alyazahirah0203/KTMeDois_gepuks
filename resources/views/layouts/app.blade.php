<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM eDOIS - @yield('title')</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .main-content {
            flex: 1 0 auto;
            padding: 20px;
        }
        
        .footer-modern {
            flex-shrink: 0;
            background: rgba(26,26,46,0.9);
            backdrop-filter: blur(10px);
            color: rgba(255,255,255,0.7);
            padding: 20px 0;
            text-align: center;
            border-radius: 30px 30px 0 0;
            width: 100%;
            margin-top: auto;
        }
        
        .navbar-modern {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 60px;
            margin: 15px 20px;
            padding: 8px 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .navbar-modern:hover {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .logo-image {
            height: 50px;
            width: auto;
            background: white;
            border-radius: 12px;
            padding: 5px;
            object-fit: contain;
        }
        
        .logo-icon-circle {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .logo-icon-circle i {
            font-size: 28px;
            color: white;
        }
        
        .logo-text {
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            letter-spacing: 1px;
        }
        
        .logo-text small {
            font-size: 0.7rem;
            font-weight: 400;
            display: block;
            color: rgba(255,255,255,0.8);
        }
        
        .nav-link-modern {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 10px 22px !important;
            margin: 0 5px;
            border-radius: 50px;
            transition: all 0.3s ease;
            background: transparent;
        }
        
        .nav-link-modern i {
            margin-right: 8px;
        }
        
        .nav-link-modern:hover {
            background: rgba(255,255,255,0.2);
            color: white !important;
            transform: translateY(-2px);
        }
        
        .nav-link-modern.active {
            background: rgba(255,255,255,0.25);
            color: white !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .user-avatar-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.15);
            padding: 8px 18px;
            border-radius: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            color: white;
            text-decoration: none;
        }
        
        .user-avatar-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
        }
        
        .user-avatar-small {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            color: white;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 0;
            line-height: 1.2;
        }
        
        .user-role {
            font-size: 11px;
            opacity: 0.8;
        }
        
        .dropdown-menu-modern {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            padding: 8px;
            margin-top: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .dropdown-item-modern {
            border-radius: 12px;
            padding: 10px 18px;
            transition: all 0.2s ease;
            color: #333;
            font-weight: 500;
        }
        
        .dropdown-item-modern i {
            margin-right: 10px;
            width: 20px;
            color: #667eea;
        }
        
        .dropdown-item-modern:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(3px);
        }
        
        .dropdown-item-modern:hover i {
            color: white;
        }
        
        .card-modern {
            border: none;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
        }
        
        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 24px;
            border: none;
            font-weight: 600;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102,126,234,0.4);
            color: white;
        }
        
        .btn-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .alert-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        @media (max-width: 768px) {
            .navbar-modern {
                margin: 10px;
                padding: 8px 15px;
            }
            .user-info {
                display: none;
            }
            .nav-link-modern {
                padding: 8px 15px !important;
            }
            .user-avatar-btn {
                padding: 5px 12px;
            }
            .logo-text {
                font-size: 1rem;
            }
            .logo-text small {
                font-size: 0.6rem;
            }
            .logo-image {
                height: 40px;
            }
            .logo-icon-circle {
                width: 40px;
                height: 40px;
            }
            .logo-icon-circle i {
                font-size: 22px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-modern">
    <div class="container-fluid">
        <a class="navbar-brand-custom" href="{{ route('dashboard') }}">
            @if(file_exists(public_path('images/ktmb-logo.png')))
                <img src="{{ asset('images/ktmb-logo.png') }}" alt="KTMB Logo" class="logo-image">
            @else
                <div class="logo-icon-circle">
                    <i class="fas fa-train"></i>
                </div>
            @endif
            <div class="logo-text">
                KTM eDOIS
                <small>Electronic DO & Invoice System</small>
            </div>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarModern" style="background: rgba(255,255,255,0.2); border: none;">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarModern">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                @auth
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                           href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    
                    @if(auth()->user()->isVendor())
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('invoices.create') ? 'active' : '' }}" 
                           href="{{ route('invoices.create') }}">
                            <i class="fas fa-file-invoice"></i> Submit Invoice
                        </a>
                    </li>
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('invoices.track') ? 'active' : '' }}" 
                           href="{{ route('invoices.track') }}">
                            <i class="fas fa-search"></i> Track Claim
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="dropdown-toggle user-avatar-btn" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-small">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <span class="user-name">{{ auth()->user()->name }}</span>
                                <span class="user-role">{{ auth()->user()->role }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item dropdown-item-modern">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="main-content">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<footer class="footer-modern">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-start">
                <i class="fas fa-train me-2"></i> KTM eDOIS
            </div>
            <div class="col-md-6 text-end">
                &copy; {{ date('Y') }} Keretapi Tanah Melayu Berhad. All rights reserved.
            </div>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    document.querySelectorAll('.nav-link-modern').forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });
</script>

@stack('scripts')
</body>
</html>
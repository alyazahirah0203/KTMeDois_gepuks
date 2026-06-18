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
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
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
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            border-radius: 60px;
            margin: 15px 20px;
            padding: 8px 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.5);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1000;
        }
        
        .navbar-modern:hover {
            background: rgba(255, 255, 255, 0.35);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
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
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .logo-icon-circle i {
            font-size: 28px;
            color: white;
        }
        
        .logo-text {
            font-weight: 700;
            font-size: 1.2rem;
            color: #0f172a;
            letter-spacing: 1px;
        }
        
        .logo-text small {
            font-size: 0.7rem;
            font-weight: 400;
            display: block;
            color: #334155;
        }
        
        .nav-link-modern {
            color: #1e293b !important;
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
            background: rgba(255,255,255,0.6);
            color: #0f172a !important;
            transform: translateY(-2px);
        }
        
        .nav-link-modern.active {
            background: rgba(255,255,255,0.7);
            color: #0891b2 !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .nav-link-notification {
            padding: 10px 12px !important;
            position: relative;
        }
        
        .notification-bell {
            position: relative;
        }
        
        .notification-badge {
            position: absolute;
            top: -2px;
            left: -5px;
            background-color: #ef4444;
            color: white;
            font-size: 9px;
            padding: 1px 4px;
            border-radius: 50px;
            min-width: 16px;
            text-align: center;
            z-index: 10;
        }
        
        .notification-dropdown {
            width: 350px !important;
            min-width: 300px !important;
            max-width: 350px !important;
        }
        
        .notification-dropdown .dropdown-item-modern {
            padding: 8px 12px !important;
            font-size: 13px;
        }
        
        .notification-dropdown .dropdown-header {
            font-size: 12px;
            padding: 8px 12px !important;
        }
        
        .user-avatar-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.25);
            padding: 8px 18px;
            border-radius: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            color: #1e293b;
            text-decoration: none;
        }
        
        .user-avatar-btn:hover {
            background: rgba(255,255,255,0.45);
            transform: translateY(-2px);
        }
        
        .user-avatar-small {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
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
            color: #0f172a;
        }
        
        .user-role {
            font-size: 11px;
            opacity: 0.7;
            color: #334155;
        }
        
        .dropdown-menu-modern {
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 20px;
            padding: 8px;
            margin-top: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
            z-index: 9999 !important;
            position: absolute !important;
        }
        
        .dropdown-item-modern {
            border-radius: 12px;
            padding: 10px 18px;
            transition: all 0.2s ease;
            color: #333;
            font-weight: 500;
            white-space: normal !important;
        }
        
        .dropdown-item-modern i {
            margin-right: 10px;
            width: 20px;
            color: #0891b2;
        }
        
        .dropdown-item-modern:hover {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            color: white;
            transform: translateX(3px);
        }
        
        .dropdown-item-modern:hover i {
            color: white;
        }
        
        .dropdown-menu {
            z-index: 9999 !important;
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
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            color: white;
            padding: 16px 24px;
            border: none;
            font-weight: 600;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            border: none;
            color: white;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(8,145,178,0.4);
            color: white;
        }
        
        .btn-gradient-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
            .nav-link-notification {
                padding: 8px 10px !important;
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
            .dropdown-menu-modern {
                width: 300px !important;
            }
            .notification-badge {
                left: -3px;
                top: -1px;
                font-size: 8px;
                padding: 1px 3px;
                min-width: 14px;
            }
            .notification-dropdown {
                width: 280px !important;
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
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarModern" style="background: rgba(255,255,255,0.3); border: none;">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarModern">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                @php
                    // Determine which guard is authenticated
                    $isVendorGuard = auth()->guard('vendor')->check();
                    $isWebGuard = auth()->check();
                    $isAuthenticated = $isVendorGuard || $isWebGuard;
                    
                    // Get user info
                    $userName = 'Guest';
                    $userRole = '';
                    $userId = null;
                    $isVendor = false;
                    $isOfficer = false;
                    $isITOfficer = false;
                    
                    if ($isVendorGuard) {
                        $vendorUser = auth()->guard('vendor')->user();
                        $userName = $vendorUser->name;
                        $userRole = 'Vendor';
                        $userId = $vendorUser->id;
                        $isVendor = true;
                    } elseif ($isWebGuard) {
                        $user = auth()->user();
                        $userName = $user->name;
                        $userRole = $user->role;
                        $userId = $user->id;
                        $isVendor = $user->isVendor();
                        $isOfficer = $user->isOfficer();
                        $isITOfficer = $user->isITOfficer();
                    }
                @endphp

                @if($isAuthenticated)
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('dashboard') ? 'active' : '' }} {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}" 
                           href="{{ $isVendorGuard ? route('vendor.dashboard') : route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Vendor Links - Show for both vendor guard and vendor role in web guard -->
                    @if($isVendorGuard || $isVendor)
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('do.*') ? 'active' : '' }}" 
                           href="{{ route('do.index') }}">
                            <i class="fas fa-truck"></i> DO
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('invoices.create') ? 'active' : '' }}" 
                           href="{{ route('invoices.create') }}">
                            <i class="fas fa-file-invoice"></i> Submit Invoice
                        </a>
                    </li>
                    @endif
                    
                    <!-- Track Claim -->
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('invoices.track') ? 'active' : '' }}" 
                           href="{{ route('invoices.track') }}">
                            <i class="fas fa-search"></i> Track Claim
                        </a>
                    </li>
                    
                    <!-- Review (Officers) -->
                    @if($isOfficer)
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('review.*') ? 'active' : '' }}" 
                           href="{{ route('review.index') }}">
                            <i class="fas fa-clipboard-check"></i> Review
                        </a>
                    </li>
                    @endif

                    <!-- Admin Panel (IT Officer Only) -->
                    @if($isITOfficer)
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->routeIs('admin.*') ? 'active' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-shield-alt"></i> Admin Panel
                        </a>
                    </li>
                    @endif
                    
                    <!-- NOTIFICATIONS DROPDOWN -->
                    <li class="nav-item dropdown notification-bell">
                        <a class="nav-link nav-link-modern nav-link-notification dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative;">
                            <i class="fas fa-bell"></i>
                            @php
                                $unreadCount = 0;
                                if ($isVendorGuard && $userId) {
                                    // For vendors - use vendor_notifications table
                                    $unreadCount = DB::connection('vendor_db')->table('vendor_notifications')
                                        ->where('vendor_user_id', $userId)
                                        ->where('is_read', 0)
                                        ->count();
                                } elseif ($isWebGuard && $userId) {
                                    // For officers/admins - use notifications table
                                    $unreadCount = \App\Models\Notification::where('user_id', $userId)->where('is_read', 0)->count();
                                }
                            @endphp
                            @if($unreadCount > 0)
                                <span class="notification-badge">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end notification-dropdown" style="width: 350px; max-height: 450px; overflow-y: auto;">
                            <li class="dropdown-header bg-light rounded-3 px-3 py-2 text-center">
                                <strong><i class="fas fa-bell me-1"></i> Notifications</strong>
                                @if($unreadCount > 0)
                                    <button id="markAllReadBtn" class="btn btn-link btn-sm float-end text-primary p-0" style="text-decoration: none;">Mark all read</button>
                                @endif
                            </li>
                            <li><hr class="dropdown-divider m-0"></li>
                            @php
                                $notifications = collect();
                                if ($isVendorGuard && $userId) {
                                    // For vendors - get from vendor_notifications table
                                    $notifications = DB::connection('vendor_db')->table('vendor_notifications')
                                        ->where('vendor_user_id', $userId)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();
                                } elseif ($isWebGuard && $userId) {
                                    // For officers/admins - get from notifications table
                                    $notifications = \App\Models\Notification::where('user_id', $userId)
                                        ->orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();
                                }
                            @endphp
                            @if($notifications->count() > 0)
                                @foreach($notifications as $notif)
                                    <li>
                                        <a class="dropdown-item dropdown-item-modern {{ !$notif->is_read ? 'bg-light' : '' }}" 
                                           href="{{ $notif->link ? $notif->link : '#' }}"
                                           data-notif-id="{{ $notif->id ?? $notif->notification_id }}"
                                           data-notif-type="{{ $isVendorGuard ? 'vendor' : 'web' }}"
                                           style="white-space: normal;">
                                            <div class="d-flex align-items-start">
                                                <div class="me-2">
                                                    @if($notif->type == 'success')
                                                        <i class="fas fa-check-circle text-success fs-5"></i>
                                                    @elseif($notif->type == 'warning')
                                                        <i class="fas fa-exclamation-triangle text-warning fs-5"></i>
                                                    @elseif($notif->type == 'danger')
                                                        <i class="fas fa-times-circle text-danger fs-5"></i>
                                                    @else
                                                        <i class="fas fa-info-circle text-info fs-5"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong style="font-size: 13px;">{{ $notif->title }}</strong>
                                                    <p class="small mb-0 text-muted" style="font-size: 11px;">{{ Str::limit($notif->message, 80) }}</p>
                                                    <small class="text-muted" style="font-size: 10px;">
                                                        {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                                    </small>
                                                </div>
                                                @if(!$notif->is_read)
                                                    <div class="ms-2">
                                                        <span class="badge bg-primary rounded-pill" style="font-size: 9px;">New</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="text-center py-4">
                                    <i class="fas fa-bell-slash fa-2x text-muted mb-2 d-block"></i>
                                    <span class="text-muted" style="font-size: 12px;">No notifications</span>
                                </li>
                            @endif
                        </ul>
                    </li>
                    <!-- END NOTIFICATIONS DROPDOWN -->
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown" style="margin-left: 5px;">
                        <a class="dropdown-toggle user-avatar-btn" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-small">
                                {{ strtoupper(substr($userName, 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <span class="user-name">{{ $userName }}</span>
                                <span class="user-role">{{ $userRole }}</span>
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
                @endif
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

        @if(session('info'))
            <div class="alert alert-info alert-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
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
    // Active nav link highlighting
    document.querySelectorAll('.nav-link-modern').forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });

    // Mark single notification as read when clicked
    document.querySelectorAll('.dropdown-item-modern[data-notif-id]').forEach(item => {
        item.addEventListener('click', function(e) {
            const notifId = this.getAttribute('data-notif-id');
            const notifType = this.getAttribute('data-notif-type');
            fetch('/notifications/' + notifId + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                }
            }).catch(error => console.error('Error:', error));
        });
    });

    // Mark all as read button
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                }
            }).catch(error => console.error('Error:', error));
        });
    }
</script>

@stack('scripts')
</body>
</html>
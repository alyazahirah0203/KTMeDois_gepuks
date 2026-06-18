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
            min-height: 100vh;
            background: #f0f2f5;
        }
        
        /* ============================================ */
        /* SIDEBAR STYLES */
        /* ============================================ */
        .sidebar-wrapper {
            width: 280px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0a2463 0%, #1a3a7a 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
            padding: 0;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        
        .sidebar-wrapper::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar-wrapper::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar-wrapper::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }
        
        .sidebar-header {
            padding: 25px 20px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        
        .sidebar-header .brand-text {
            font-size: 20px;
            font-weight: 700;
            color: white;
        }
        
        .sidebar-header .brand-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.6);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-item {
            margin-bottom: 2px;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            cursor: pointer;
            font-size: 14px;
        }
        
        .sidebar-link i {
            width: 24px;
            font-size: 16px;
            margin-right: 12px;
            text-align: center;
        }
        
        .sidebar-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }
        
        .sidebar-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #667eea;
        }
        
        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 15px 20px;
            list-style: none;
        }
        
        .sidebar-label {
            font-size: 11px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            padding: 10px 20px 5px;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        /* User Profile in Sidebar */
        .sidebar-user {
            padding: 15px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin-top: 10px;
        }
        
        .sidebar-user .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
        }
        
        .sidebar-user .user-name {
            font-weight: 600;
            color: white;
            font-size: 14px;
        }
        
        .sidebar-user .user-role {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
        }
        
        .sidebar-user .dropdown-toggle::after {
            display: none;
        }
        
        .sidebar-user .dropdown-menu {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 8px;
            min-width: 200px;
        }
        
        .sidebar-user .dropdown-item {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .sidebar-user .dropdown-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-user .dropdown-item.text-danger {
            color: #ef4444 !important;
        }
        
        .sidebar-user .dropdown-item.text-danger:hover {
            background: rgba(239, 68, 68, 0.2);
        }
        
        .sidebar-user .dropdown-item i {
            width: 20px;
            margin-right: 10px;
        }
        
        /* ============================================ */
        /* MAIN CONTENT STYLES */
        /* ============================================ */
        .main-wrapper {
            flex: 1;
            margin-left: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            padding: 30px;
            padding-top: 30px;
        }
        
        /* ============================================ */
        /* TOP BAR - Only title and notifications */
        /* ============================================ */
        .top-bar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .top-bar .page-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .top-bar .page-title small {
            font-size: 14px;
            font-weight: 400;
            color: #94a3b8;
            margin-left: 10px;
        }
        
        /* ============================================ */
        /* NOTIFICATION BELL */
        /* ============================================ */
        .notification-bell {
            position: relative;
            cursor: pointer;
            color: #64748b;
            font-size: 20px;
            transition: color 0.3s ease;
        }
        
        .notification-bell:hover {
            color: #1e293b;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background: #ef4444;
            color: white;
            font-size: 9px;
            padding: 1px 5px;
            border-radius: 50%;
            min-width: 18px;
            text-align: center;
            font-weight: 600;
        }
        
        /* ============================================ */
        /* FOOTER STYLES */
        /* ============================================ */
        .footer-admin {
            background: white;
            padding: 15px 30px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            margin-top: auto;
        }
        
        /* ============================================ */
        /* RESPONSIVE */
        /* ============================================ */
        @media (max-width: 768px) {
            .sidebar-wrapper {
                width: 0;
                overflow: hidden;
                position: fixed;
                z-index: 9999;
            }
            
            .sidebar-wrapper.open {
                width: 280px;
                overflow-y: auto;
            }
            
            .main-wrapper {
                margin-left: 0;
            }
            
            .top-bar {
                padding: 10px 15px;
            }
            
            .top-bar .page-title small {
                display: none;
            }
            
            .main-content {
                padding: 15px;
            }
            
            .sidebar-toggle-btn {
                display: block !important;
            }
        }
        
        .sidebar-toggle-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: #1e293b;
            cursor: pointer;
            padding: 5px;
        }
        
        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        /* Card styles */
        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
        }
        
        .card-modern:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            color: white;
            padding: 14px 20px;
            border: none;
            font-weight: 600;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 10px;
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
        
        .btn-gradient-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(5,150,105,0.4);
            color: white;
        }
        
        .alert-modern {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
    
    @stack('styles')
</head>
<body>

<!-- ============================================ -->
<!-- SIDEBAR OVERLAY (Mobile) -->
<!-- ============================================ -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ============================================ -->
<!-- SIDEBAR -->
<!-- ============================================ -->
<div class="sidebar-wrapper" id="sidebarWrapper">
    <div class="sidebar-header">
        <div class="d-flex align-items-center gap-2">
            @if(file_exists(public_path('images/ktmb-logo.png')))
                <img src="{{ asset('images/ktmb-logo.png') }}" alt="KTMB Logo" style="height: 40px; background: white; border-radius: 10px; padding: 5px;">
            @else
                <i class="fas fa-train fs-3 text-white"></i>
            @endif
            <div>
                <div class="brand-text">KTM eDOIS</div>
                <div class="brand-sub">Administration Panel</div>
            </div>
        </div>
    </div>
    
    <ul class="sidebar-menu">
        @php
            $currentRoute = request()->route()->getName();
        @endphp
        
        <!-- Dashboard -->
        <li class="sidebar-item">
            <a href="{{ route('dashboard.admin') }}" 
               class="sidebar-link {{ $currentRoute == 'dashboard.admin' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="sidebar-divider"></li>
        <li class="sidebar-label">Management</li>
        
        <!-- User Management -->
        <li class="sidebar-item">
            <a href="{{ route('dashboard.admin.users.index') }}" 
               class="sidebar-link {{ str_starts_with($currentRoute, 'dashboard.admin.users') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>User Management</span>
            </a>
        </li>
        
        <li class="sidebar-divider"></li>
        <li class="sidebar-label">System</li>
        
        <!-- Audit Logs -->
        <li class="sidebar-item">
            <a href="{{ route('dashboard.admin.audit-logs') }}" 
               class="sidebar-link {{ $currentRoute == 'dashboard.admin.audit-logs' ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Audit Logs</span>
            </a>
        </li>
        
        <!-- Reports -->
        <li class="sidebar-item">
            <a href="{{ route('dashboard.admin.reports') }}" 
               class="sidebar-link {{ $currentRoute == 'dashboard.admin.reports' ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </li>
        
        <!-- User Profile Dropdown in Sidebar -->
        <li class="sidebar-item sidebar-user">
            <div class="dropdown">
                <a href="#" class="sidebar-link d-flex align-items-center justify-content-between" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-left: none;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ str_replace('_', ' ', ucfirst(auth()->user()->role)) }}</div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 12px; opacity: 0.6;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end w-100">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>

<!-- ============================================ -->
<!-- MAIN WRAPPER -->
<!-- ============================================ -->
<div class="main-wrapper">

    <!-- ============================================ -->
    <!-- TOP BAR - Only title + notifications -->
    <!-- ============================================ -->
    <div class="top-bar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle-btn" id="sidebarToggleBtn">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h5 class="page-title mb-0">
                    @yield('page-title', 'Admin Panel')
                    <small>@yield('sub-title', '')</small>
                </h5>
            </div>
        </div>
        
        <!-- Notifications Only -->
        <div class="notification-bell dropdown">
            <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #64748b; text-decoration: none;">
                <i class="fas fa-bell"></i>
                @php
                    $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', 0)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="notification-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 450px; overflow-y: auto; padding: 0;">
                <li class="dropdown-header bg-light py-2 px-3">
                    <strong><i class="fas fa-bell me-1"></i> Notifications</strong>
                    @if($unreadCount > 0)
                        <button id="markAllReadBtn" class="btn btn-link btn-sm float-end text-primary p-0" style="text-decoration: none;">Mark all read</button>
                    @endif
                </li>
                <li><hr class="dropdown-divider m-0"></li>
                @php
                    $notifications = \App\Models\Notification::where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                @endphp
                @if($notifications->count() > 0)
                    @foreach($notifications as $notif)
                        <li>
                            <a class="dropdown-item {{ !$notif->is_read ? 'bg-light' : '' }}" 
                               href="{{ $notif->link ? $notif->link : '#' }}"
                               data-notif-id="{{ $notif->notification_id }}"
                               style="white-space: normal; padding: 10px 15px;">
                                <div class="d-flex align-items-start">
                                    <div class="me-2">
                                        @if($notif->type == 'success')
                                            <i class="fas fa-check-circle text-success"></i>
                                        @elseif($notif->type == 'warning')
                                            <i class="fas fa-exclamation-triangle text-warning"></i>
                                        @elseif($notif->type == 'danger')
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @else
                                            <i class="fas fa-info-circle text-info"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong style="font-size: 13px;">{{ $notif->title }}</strong>
                                        <p class="small mb-0 text-muted" style="font-size: 11px;">{{ Str::limit($notif->message, 80) }}</p>
                                        <small class="text-muted" style="font-size: 10px;">
                                            {{ $notif->created_at->diffForHumans() }}
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
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================ -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {!! session('success') !!}
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

        @if(session('warning'))
            <div class="alert alert-warning alert-modern alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {!! session('warning') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- ============================================ -->
    <!-- FOOTER -->
    <!-- ============================================ -->
    <footer class="footer-admin">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <span>&copy; {{ date('Y') }} Keretapi Tanah Melayu Berhad. All rights reserved.</span>
            <span>
                <i class="fas fa-code me-1"></i> v2.0
                <span class="mx-2">|</span>
                <i class="fas fa-shield-alt me-1"></i> Secure Admin Panel
            </span>
        </div>
    </footer>

</div>

<!-- ============================================ -->
<!-- SCRIPTS -->
<!-- ============================================ -->
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
    // ============================================
    // SIDEBAR TOGGLE (Mobile)
    // ============================================
    document.getElementById('sidebarToggleBtn')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebarWrapper');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    });

    document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
        const sidebar = document.getElementById('sidebarWrapper');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });

    // ============================================
    // MARK NOTIFICATION AS READ
    // ============================================
    document.querySelectorAll('[data-notif-id]').forEach(item => {
        item.addEventListener('click', function(e) {
            const notifId = this.getAttribute('data-notif-id');
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

    // ============================================
    // MARK ALL NOTIFICATIONS AS READ
    // ============================================
    document.getElementById('markAllReadBtn')?.addEventListener('click', function(e) {
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
</script>

@stack('scripts')
</body>
</html>
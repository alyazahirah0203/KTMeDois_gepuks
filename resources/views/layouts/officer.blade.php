<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM eDOIS - Officer - @yield('title')</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <style>
        * { font-family: 'Poppins', sans-serif; }
        
        body {
            background: #f1f5f9;
            min-height: 100vh;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 270px;
            background: linear-gradient(180deg, #0a2463 0%, #1e3a8a 100%);
            padding: 20px 0;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-brand .logo-img {
            height: 50px;
            width: auto;
            background: white;
            border-radius: 12px;
            padding: 8px;
            margin-bottom: 10px;
        }
        
        .sidebar-brand .brand-text {
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            display: block;
        }
        
        .sidebar-brand .brand-text small {
            display: block;
            font-size: 0.6rem;
            font-weight: 400;
            opacity: 0.7;
        }
        
        .sidebar-menu {
            padding: 0 12px;
        }
        
        .sidebar-menu .menu-label {
            color: rgba(255,255,255,0.35);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 15px 10px 6px;
            font-weight: 600;
        }
        
        .sidebar-menu .nav-item {
            margin-bottom: 3px;
        }
        
        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 10px 14px;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 13px;
            text-decoration: none;
            display: block;
            cursor: pointer;
        }
        
        .sidebar-menu .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            font-size: 15px;
        }
        
        .sidebar-menu .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: white;
            transform: translateX(4px);
        }
        
        .sidebar-menu .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .sidebar-menu .nav-link .badge {
            float: right;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 15px;
            left: 0;
            right: 0;
            padding: 12px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-footer .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .sidebar-footer .user-name {
            color: white;
            font-weight: 500;
            font-size: 12px;
        }
        
        .sidebar-footer .user-role {
            color: rgba(255,255,255,0.5);
            font-size: 10px;
        }
        
        .sidebar-footer .logout-btn {
            color: rgba(255,255,255,0.4);
            transition: all 0.3s ease;
            padding: 4px 8px;
            border-radius: 6px;
            background: none;
            border: none;
        }
        
        .sidebar-footer .logout-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .main-content-officer {
            margin-left: 270px;
            padding: 20px 25px;
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            border-radius: 14px;
            padding: 10px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .top-navbar .page-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e3a8a;
        }
        
        .top-navbar .page-title i {
            margin-right: 8px;
        }
        
        .top-navbar .quick-stats {
            display: flex;
            gap: 12px;
        }
        
        .top-navbar .quick-stats .stat-item {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            background: #f1f5f9;
        }
        
        .notification-bell-top {
            position: relative;
            display: inline-block;
            margin-right: 15px;
            cursor: pointer;
        }
        
        .notification-badge-top {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            font-size: 9px;
            padding: 1px 5px;
            border-radius: 50px;
            min-width: 16px;
            text-align: center;
        }
        
        .notification-dropdown-top {
            width: 350px !important;
            max-height: 450px;
            overflow-y: auto;
        }
        
        .notification-dropdown-top .dropdown-item {
            white-space: normal;
            padding: 10px 15px;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .notification-dropdown-top .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .notification-dropdown-top .dropdown-item:hover {
            background: #f8f9ff;
        }
        
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: #1e3a8a;
            font-size: 22px;
            cursor: pointer;
        }
        
        .card-modern {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            background: white;
            margin-bottom: 20px;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 18px;
            border: none;
            font-weight: 600;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-gradient-success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
        
        .btn-gradient-danger {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        }
        
        @media (max-width: 992px) {
            .sidebar { left: -270px; }
            .sidebar.active { left: 0; }
            .main-content-officer { margin-left: 0; }
            .sidebar-toggle { display: block; }
        }
        
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
    </style>
    
    @stack('styles')
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        @if(file_exists(public_path('images/ktmb-logo.png')))
            <img src="{{ asset('images/ktmb-logo.png') }}" alt="KTMB Logo" class="logo-img">
        @else
            <div style="width: 50px; height: 50px; background: white; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <i class="fas fa-train fa-2x" style="color: #1e3a8a;"></i>
            </div>
        @endif
        <span class="brand-text">
            KTM eDOIS
            <small>Officer Dashboard</small>
        </span>
    </div>
    
    <div class="sidebar-menu">
        <div class="menu-label">Main</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('review.index') ? 'active' : '' }}" href="{{ route('review.index') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('review.invoices') ? 'active' : '' }}" href="{{ route('review.invoices') }}">
                    <i class="fas fa-clipboard-list"></i> 
                    @if(auth()->user()->isReviewOfficer())
                        Review Invoices
                    @else
                        Invoice Payment
                    @endif
                    @php
                        $user = auth()->user();
                        if ($user->isReviewOfficer()) {
                            $pendingCount = \App\Models\Invoice::where('status', 'Submitted')->count();
                            $badgeColor = 'bg-danger';
                        } elseif ($user->isFinanceOfficer()) {
                            $pendingCount = \App\Models\Invoice::where('status', 'Finance Review')->count();
                            $badgeColor = 'bg-info';
                        } else {
                            $pendingCount = 0;
                            $badgeColor = 'bg-secondary';
                        }
                    @endphp
                    @if($pendingCount > 0)
                        <span class="badge {{ $badgeColor }}">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('review.dos') ? 'active' : '' }}" href="{{ route('review.dos') }}">
                    <i class="fas fa-truck"></i> Review DOs
                    @php $pendingDOCount = \App\Models\DeliveryOrder::where('status', 'Submitted')->count(); @endphp
                    @if($pendingDOCount > 0)
                        <span class="badge bg-warning">{{ $pendingDOCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
        
        <div class="menu-label">Reports</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('review.export') ? 'active' : '' }}" href="{{ route('review.export') }}">
                    <i class="fas fa-file-export"></i> Export Reports
                </a>
            </li>
        </ul>
    </div>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role }}</div>
            </div>
            <div class="ms-auto">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="main-content-officer" id="mainContent">
    <div class="top-navbar">
        <div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <span class="page-title">
                <i class="fas fa-{{ request()->routeIs('review.index') ? 'tachometer-alt' : (request()->routeIs('review.export') ? 'file-export' : (request()->routeIs('review.dos') ? 'truck' : 'file-invoice')) }}"></i>
                @yield('page-title', 'Dashboard')
            </span>
        </div>
        <div class="d-flex align-items-center">
            <!-- Notification Bell -->
            <div class="dropdown notification-bell-top">
                <a class="text-dark position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fa-lg"></i>
                    @php
                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', 0)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="notification-badge-top">{{ $unreadCount }}</span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown-top">
                    <li class="dropdown-header bg-light rounded-3 p-2 text-center">
                        <strong><i class="fas fa-bell me-1"></i> Notifications</strong>
                        @if($unreadCount > 0)
                            <button id="markAllReadBtnTop" class="btn btn-link btn-sm float-end text-primary p-0" style="text-decoration: none;">Mark all read</button>
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
                                   style="white-space: normal;">
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
                                            <p class="small mb-0 text-muted" style="font-size: 11px;">{{ Str::limit($notif->message, 60) }}</p>
                                            <small class="text-muted" style="font-size: 10px;">{{ $notif->created_at->diffForHumans() }}</small>
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
                        <li class="text-center py-3">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-2 d-block"></i>
                            <span class="text-muted" style="font-size: 12px;">No notifications</span>
                        </li>
                    @endif
                </ul>
            </div>
            <!-- End Notification Bell -->
            
            <div class="quick-stats">
                <span class="stat-item"><span class="text-warning">●</span> Pending: {{ \App\Models\Invoice::where('status', 'Submitted')->count() }}</span>
                <span class="stat-item"><span class="text-info">●</span> Review: {{ \App\Models\Invoice::where('status', 'Finance Review')->count() }}</span>
                <span class="stat-item"><span class="text-primary">●</span> Processing: {{ \App\Models\Invoice::where('status', 'Payment Processing')->count() }}</span>
                <span class="stat-item"><span class="text-success">●</span> Paid: {{ \App\Models\Invoice::where('status', 'Paid')->count() }}</span>
            </div>
        </div>
    </div>
    
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });

    document.querySelectorAll('.dropdown-item[data-notif-id]').forEach(item => {
        item.addEventListener('click', function(e) {
            const notifId = this.getAttribute('data-notif-id');
            fetch('/notifications/' + notifId + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).catch(error => console.error('Error:', error));
        });
    });

    const markAllBtnTop = document.getElementById('markAllReadBtnTop');
    if (markAllBtnTop) {
        markAllBtnTop.addEventListener('click', function(e) {
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
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TDM | نظام إدارة التوزيع')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --primary-glow: rgba(37, 99, 235, 0.15);
            --bg-main: #f8fafc;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --text-main: #475569;
            --text-heading: #0f172a;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --border-hover: #cbd5e1;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -4px rgb(0 0 0 / 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Cairo', 'Outfit', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        .app-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--bg-sidebar);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 20px 10px;
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), #1d4ed8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.3em;
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .logo-text {
            font-size: 1.6em;
            font-weight: 800;
            color: var(--text-heading);
            font-family: 'Outfit';
            letter-spacing: -1px;
            display: flex;
            flex-direction: row-reverse;
            align-items: baseline;
            gap: 2px;
        }

        .logo-text span {
            color: var(--primary);
            font-size: 1.1em;
            font-weight: 700;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 15px 5px;
        }

        .nav-group {
            font-size: 0.75em;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            margin: 25px 8px 10px;
            letter-spacing: 0.1em;
        }

        .sidebar-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav ul li {
            padding: 0 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 8px;
            color: var(--text-main);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition);
            margin-bottom: 6px;
            font-size: 0.9em;
            position: relative;
            border: 1px solid transparent;
        }

        .nav-link i {
            font-size: 1.25em;
            opacity: 0.6;
            transition: var(--transition);
        }

        .nav-link:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary-glow);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 8px 20px var(--primary-glow);
        }

        .nav-link.active i {
            opacity: 1;
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--bg-main);
        }

        .top-bar {
            height: 70px;
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            z-index: 50;
        }

        .top-bar-start {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .top-bar h1 {
            font-size: 1.1em;
            font-weight: 800;
            color: var(--text-heading);
            margin: 0;
        }

        .top-bar-end {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
            font-weight: 600;
        }

        .user-role {
            font-size: 0.85em;
            color: var(--text-muted);
            background: var(--primary-light);
            padding: 2px 8px;
            border-radius: var(--radius-sm);
        }

        .icon-btn {
            width: 42px;
            height: 42px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            border-radius: 12px;
            cursor: pointer;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            text-decoration: none;
        }

        .icon-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .content-viewer {
            flex: 1;
            overflow-y: auto;
            padding: 50px 40px;
            scroll-behavior: smooth;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border);
            padding: 20px 30px;
            font-weight: 700;
            color: var(--text-heading);
        }

        .card-body {
            padding: 30px;
        }

        .btn {
            border-radius: var(--radius-md);
            font-weight: 600;
            padding: 10px 20px;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #10b981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .btn-danger {
            background: #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        }

        .btn-secondary {
            background: var(--text-muted);
            color: white;
        }

        .table {
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table thead th {
            background: var(--bg-main);
            border-bottom: 2px solid var(--border);
            font-weight: 800;
            color: var(--text-muted);
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 16px;
        }

        .table tbody td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }

        .table tbody tr:hover {
            background: var(--primary-light);
        }

        .badge {
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.8em;
        }

        .bg-warning {
            background: #f59e0b !important;
        }

        .bg-success {
            background: #10b981 !important;
        }

        .bg-danger {
            background: #ef4444 !important;
        }

        .alert {
            border-radius: var(--radius-md);
            border: none;
            padding: 16px 20px;
            margin-bottom: 25px;
        }

        .alert-success {
            background: linear-gradient(to right, #ecfdf5, #ffffff);
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-danger {
            background: linear-gradient(to right, #fef2f2, #ffffff);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .form-control, .form-select {
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 12px 16px;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .modal-content {
            border-radius: var(--radius-lg);
            border: none;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border);
            padding: 20px 30px;
        }

        .modal-body {
            padding: 30px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                right: -300px;
                top: 0;
                bottom: 0;
                width: 300px;
                box-shadow: -20px 0 60px rgba(0, 0, 0, 0.1);
            }

            .sidebar.open {
                right: 0;
            }

            .content-viewer {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon"><i class="bi bi-stack"></i></div>
                    <div class="logo-text">TDM<span>System</span></div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-group">الرئيسية</div>
                <ul>
                    <li><a href="{{ route('requests.index') }}" class="nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}"><i class="bi bi-list-ul"></i> <span>@auth @if(Auth::user()->isSalesman()) التعبئة و الطلبات السابقة @else الطلبات @endif @else الطلبات @endauth</span></a></li>
                </ul>

                @auth
                    @if(Auth::user()->isWarehouseKeeper())
                    <div class="nav-group">إدارة المخزن</div>
                    <ul>
                        <li><a href="#" class="nav-link"><i class="bi bi-building"></i> <span>المخزن الرئيسي</span></a></li>
                        <li><a href="#" class="nav-link"><i class="bi bi-truck"></i> <span>فواتير المصنع</span></a></li>
                    </ul>
                    @endif

                    @if(Auth::user()->isAdmin())
                    <div class="nav-group">الإدارة</div>
                    <ul>
                        <li><a href="#" class="nav-link"><i class="bi bi-people"></i> <span>المستخدمين</span></a></li>
                        <li><a href="#" class="nav-link"><i class="bi bi-box-seam"></i> <span>المنتجات</span></a></li>
                        <li><a href="#" class="nav-link"><i class="bi bi-shop"></i> <span>المتاجر</span></a></li>
                    </ul>
                    @endif
                @endauth
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <div class="top-bar-start">
                    <h1>@yield('title', 'نظام إدارة التوزيع')</h1>
                </div>
                <div class="top-bar-end">
                    @auth
                        <div class="user-info">
                            <span>مرحباً، {{ Auth::user()->name }}</span>
                            <span class="user-role">{{ Auth::user()->role->name }}</span>
                        </div>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="icon-btn" title="خروج">
                            <i class="bi bi-box-arrow-right"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endauth
                </div>
            </header>

            <div class="content-viewer">
                <div class="container">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
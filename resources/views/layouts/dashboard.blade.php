<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dashboard') - ReadCycle</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('assets/readcycle/img/favicon.ico') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons: Google Material Symbols Rounded (Outlined) -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"
        rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('assets/readcycle/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/readcycle/css/style.css') }}" rel="stylesheet">

    <!-- Project CSS/JS (Vite or Manifest fallback) -->
    @if (file_exists(public_path('build/manifest.json')))
    @php($manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true))
        @if (isset($manifest['resources/css/app.css']['file']))
            <link rel="stylesheet" href="{{ asset('build/' . $manifest['resources/css/app.css']['file']) }}">
        @endif
    @else
    @vite(['resources/css/app.css'])
    @endif

    <!-- Dashboard Styles -->
    <style>
        :root {
            --primary: #0a6d3a;
            --primary-foreground: #ffffff;
            --secondary: #35875b;
            --secondary-foreground: #ffffff;
            --muted: #f8f9fa;
            --muted-foreground: #6c757d;
            --accent: #e8f5e8;
            --accent-foreground: #0a6d3a;
            --destructive: #dc3545;
            --destructive-foreground: #ffffff;
            --border: #e9ecef;
            --input: #ffffff;
            --ring: #0a6d3a;
            --background: #ffffff;
            --foreground: #0f172a;
            --card: #ffffff;
            --card-foreground: #0f172a;
            --popover: #ffffff;
            --popover-foreground: #0f172a;
            --radius: 0.5rem;
        }

        * {
            border-color: var(--border);
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--background);
            color: var(--foreground);
        }

        .sidebar {
            background: var(--card);
            border-right: 1px solid var(--border);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .navbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
        }

        .nav-link {
            color: var(--muted-foreground);
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-link:hover {
            background: var(--accent);
            color: var(--accent-foreground);
        }

        .nav-link.active {
            background: var(--primary);
            color: var(--primary-foreground);
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .card-title {}

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--primary-foreground);
        }

        .btn-primary:hover {
            background: var(--secondary);
            border-color: var(--secondary);
        }

        .btn-secondary {
            background: var(--secondary);
            border-color: var(--secondary);
            color: var(--secondary-foreground);
        }

        .btn-outline {
            border: 1px solid var(--border);
            color: var(--foreground);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--muted);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background: var(--accent);
            color: var(--accent-foreground);
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .form-control {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 0.5rem 0.75rem;
            background: var(--input);
        }

        .form-control:focus {
            border-color: var(--ring);
            box-shadow: 0 0 0 2px rgb(10 109 58 / 0.2);
        }

        .table {
            background: var(--card);
        }

        .table th {
            background: var(--muted);
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        .table td {
            border-bottom: 1px solid var(--border);
        }

        .alert {
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        .alert-success {
            background: var(--accent);
            color: var(--accent-foreground);
            border-color: var(--secondary);
        }

        .alert-danger {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
        }

        .stats-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .stats-card .icon,
        .stats-card h3,
        .stats-card p {
            position: relative;
            z-index: 3;
        }

        .stats-card .icon {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
        }

        .stats-card .icon.primary {
            background: var(--accent);
            color: var(--accent-foreground);
        }

        .stats-card .icon.secondary {
            background: #dbeafe;
            color: #1e40af;
        }

        .stats-card .icon.success {
            background: #dcfce7;
            color: #16a34a;
        }

        .stats-card .icon.warning {
            background: #fef3c7;
            color: #d97706;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }

        /* Bangla font classes removed for this project */

        /* Book Cover Aspect Ratio Fix - 260x372 ratio (0.699:1) */
        .book-cover {
            width: 100%;
            height: 372px;
            /* Exact height for 260x372 aspect ratio */
            object-fit: cover;
            object-position: center;
        }

        .book-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .book-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Table Image Fixes - maintaining 260x372 aspect ratio */
        .table-book-image {
            width: 40px;
            height: 57px;
            /* 40x57 maintains 260x372 aspect ratio (0.699:1) */
            object-fit: cover;
            object-position: center;
            border-radius: 4px;
        }

        .table-book-image-large {
            width: 60px;
            height: 86px;
            /* 60x86 maintains 260x372 aspect ratio (0.699:1) */
            object-fit: cover;
            object-position: center;
            border-radius: 4px;
        }

        .swap-book-image {
            width: 50px;
            height: 72px;
            /* 50x72 maintains 260x372 aspect ratio (0.699:1) */
            object-fit: cover;
            object-position: center;
            border-radius: 4px;
        }

        .swap-card-image {
            width: 80px;
            height: 115px;
            /* 80x115 maintains 260x372 aspect ratio (0.699:1) */
            object-fit: cover;
            object-position: center;
            border-radius: 4px;
            margin: 0 auto;
        }

        /* Chart Styles */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .chart-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .chart-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        canvas {
            max-height: 300px !important;
        }

        /* Compact Dashboard Styles */
        .compact-card {
            margin-bottom: 1rem;
        }

        .compact-card .card-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        .compact-card .card-body {
            padding: 1rem;
        }

        .compact-card .card-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .compact-stats {
            padding: 0.75rem;
        }

        .compact-stats .icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
        }

        .compact-stats h3 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .compact-stats p {
            font-size: 0.875rem;
            margin-bottom: 0;
        }

        .compact-list .list-group-item {
            padding: 0.5rem 0;
            border: none;
            border-bottom: 1px solid var(--border);
        }

        .compact-list .list-group-item:last-child {
            border-bottom: none;
        }

        .compact-table {
            font-size: 0.875rem;
        }

        .compact-table th,
        .compact-table td {
            padding: 0.5rem 0.75rem;
            vertical-align: middle;
        }

        .compact-table .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .compact-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .compact-text {
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .compact-section {
            margin-bottom: 1.5rem;
        }

        .compact-section h2 {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .compact-section h5 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .compact-section h6 {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .compact-grid {
            gap: 0.75rem;
        }

        .compact-grid .col {
            padding: 0.375rem;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="p-4">
            <div class="d-flex align-items-center mb-4">
                <h4 class="mb-0 fw-bold" style="color: #0a6c3a">SendaSnap</h4>
            </div>

            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">home</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('dashboard.vehicles') }}"
                    class="nav-link {{ request()->routeIs('dashboard.vehicles') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">directions_car</span>
                    <span>Vehicles</span>
                </a>
                <a href="{{ route('dashboard.tasks') }}"
                    class="nav-link {{ request()->routeIs('dashboard.tasks') ? 'active' : '' }}">
                    <span class="material-symbols-rounded">task_alt</span>
                    <span>Tasks</span>
                </a>
                @can('viewAny', \App\Models\User::class)
                    <a href="{{ route('dashboard.users') }}"
                        class="nav-link {{ request()->routeIs('dashboard.users') ? 'active' : '' }}">
                        <span class="material-symbols-rounded">group</span>
                        <span>Users</span>
                    </a>
                @endcan
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <nav class="navbar">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline d-md-none me-3" id="sidebarToggle">
                        <span class="material-symbols-rounded">menu</span>
                    </button>
                    <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-outline dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <span class="material-symbols-rounded me-2">person</span>{{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <span class="material-symbols-rounded me-2">person</span><span>Profile</span>
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <span class="material-symbols-rounded me-2">logout</span><span>Logout</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span class="material-symbols-rounded me-2">check_circle</span>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span class="material-symbols-rounded me-2">error</span>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {!! \Devrabiul\ToastMagic\Facades\ToastMagic::scripts() !!}

    @if (file_exists(public_path('build/manifest.json')))
    @php($manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true))
    @if (isset($manifest['resources/js/app.js']['file']))
        <script defer src="{{ asset('build/' . $manifest['resources/js/app.js']['file']) }}"></script>
    @endif
    @endif

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
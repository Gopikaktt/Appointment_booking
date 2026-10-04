<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediCare - Appointment Booking System')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --bg-card-hover: #334155;
            --border-color: #334155;
            --primary: #0d9488;
            --primary-hover: #0f766e;
            --primary-light: rgba(13, 148, 136, 0.15);
            --accent: #38bdf8;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --info: #3b82f6;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
            --font-title: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: var(--font-body);
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 280px;
            background: rgba(15, 23, 42, 0.95);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
            transition: all 0.3s ease;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: var(--font-title);
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
            margin-bottom: 2rem;
            padding: 0.5rem;
        }

        .brand-logo i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            box-shadow: 0 0 20px rgba(13, 148, 136, 0.4);
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
            padding-left: 0.75rem;
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.85rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .nav-item a:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item.active a {
            color: #fff;
            background: linear-gradient(90deg, var(--primary), rgba(13, 148, 136, 0.6));
            box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
            font-weight: 600;
        }

        .nav-item a i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: var(--radius-md);
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .user-card:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-1px);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
        }

        .user-info {
            overflow: hidden;
        }

        .user-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--accent);
            text-transform: capitalize;
        }

        /* Main Content Layout */
        .main-wrapper {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 90;
            backdrop-filter: blur(10px);
        }

        .page-title {
            font-family: var(--font-title);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .content-container {
            padding: 2rem;
            flex: 1;
        }

        /* Alert Notifications */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            animation: fadeIn 0.3s ease-in-out;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* UI Cards */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-family: var(--font-title);
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-main);
        }

        /* Dashboard Metric Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .stat-info .stat-value {
            font-family: var(--font-title);
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-info .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-confirmed {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .badge-completed {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-inactive {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Table Design */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.95rem;
        }

        .custom-table th {
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.8px;
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .custom-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            vertical-align: middle;
        }

        .custom-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.65rem 1.25rem;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(13, 148, 136, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        .btn-sm {
            padding: 0.4rem 0.85rem;
            font-size: 0.8rem;
            border-radius: var(--radius-sm);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .form-control, .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: var(--text-main);
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.25);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.25rem;
        }

        /* Slots Grid Picker */
        .slots-picker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
            margin-top: 0.5rem;
        }

        .slot-btn {
            padding: 0.6rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }

        .slot-btn:hover {
            border-color: var(--accent);
            background: rgba(56, 189, 248, 0.1);
        }

        .slot-btn.selected {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 12px rgba(13, 148, 136, 0.5);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="brand-logo">
            <i class="fa-solid fa-hospital-user"></i>
            <span>MediCare</span>
        </a>

        <div class="nav-section">
            <div class="nav-title">Main Menu</div>
            <ul class="nav-menu">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
                </li>
                @if(auth()->user()->isPatient())
                <li class="nav-item {{ request()->routeIs('appointments.create') ? 'active' : '' }}">
                    <a href="{{ route('appointments.create') }}"><i class="fa-solid fa-calendar-plus"></i> Book Appointment</a>
                </li>
                <li class="nav-item {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                    <a href="{{ route('profile.show') }}"><i class="fa-solid fa-id-card"></i> My Profile</a>
                </li>
                @endif
                @if(auth()->user()->isDoctor() && auth()->user()->doctorProfile)
                <li class="nav-item {{ request()->routeIs('doctors.show') && request()->route('doctor')?->id == auth()->user()->doctorProfile->id ? 'active' : '' }}">
                    <a href="{{ route('doctors.show', auth()->user()->doctorProfile) }}"><i class="fa-solid fa-address-card"></i> My Schedule & Profile</a>
                </li>
                @endif
                <li class="nav-item {{ request()->routeIs('appointments.history') ? 'active' : '' }}">
                    <a href="{{ route('appointments.history') }}"><i class="fa-solid fa-clock-rotate-left"></i> Appointment History</a>
                </li>
                <li class="nav-item {{ request()->routeIs('doctors.index') ? 'active' : '' }}">
                    <a href="{{ route('doctors.index') }}"><i class="fa-solid fa-user-doctor"></i> Doctors Directory</a>
                </li>
                @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
                <li class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                    <a href="{{ route('patients.index') }}"><i class="fa-solid fa-users"></i> Patients Directory</a>
                </li>
                <li class="nav-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                    <a href="{{ route('reports.index') }}"><i class="fa-solid fa-chart-line"></i> Reports & Analytics</a>
                </li>
                @endif
            </ul>
        </div>

        <div class="sidebar-footer">
            @php
                $userProfileUrl = route('profile.show');
                if (auth()->user()->isDoctor() && auth()->user()->doctorProfile) {
                    $userProfileUrl = route('doctors.show', auth()->user()->doctorProfile);
                }
            @endphp
            <a href="{{ $userProfileUrl }}" class="user-card" style="text-decoration: none; cursor: pointer;" title="View Profile">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role"><i class="fa-solid fa-shield-halved"></i> {{ auth()->user()->role }}</div>
                </div>
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger" style="width: 100%;">
                    <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <h1 class="page-title">@yield('page-header', 'Dashboard')</h1>
            <div style="font-size: 0.9rem; color: var(--text-muted)">
                <i class="fa-regular fa-calendar"></i> {{ now()->format('l, F j, Y') }}
            </div>
        </header>

        <main class="content-container">
            @if(session('success'))
                <div class="alert alert-success" style="display: flex; justify-content: space-between; align-items: center;">
                    <div><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; font-size: 1.25rem; line-height: 1; cursor: pointer; padding: 0 0 0 10px;" title="Dismiss">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="display: flex; justify-content: space-between; align-items: center;">
                    <div><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; font-size: 1.25rem; line-height: 1; cursor: pointer; padding: 0 0 0 10px;" title="Dismiss">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex: 1;">
                        @if($errors->count() === 1)
                            <div><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first() }}</div>
                        @else
                            <ul style="margin-left: 1.2rem; font-size: 0.9rem; margin-bottom: 0;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; font-size: 1.25rem; line-height: 1; cursor: pointer; padding: 0 0 0 10px;" title="Dismiss">&times;</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(() => alert.remove(), 600);
                });
            }, 4000);
        });
    </script>
    @stack('scripts')
</body>
</html>

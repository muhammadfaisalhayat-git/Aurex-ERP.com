<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', $appName)</title>

    <!-- Bootstrap CSS -->
    @if(app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    @endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cairo:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Hotwire Turbo -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.0/dist/turbo.es2017-umd.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global functions
        window.toggleSidebar = function () {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            if (window.innerWidth >= 992) {
                // Desktop toggle (collapse)
                sidebar.classList.toggle('collapsed');
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            } else {
                // Mobile toggle (show/hide)
                sidebar.classList.toggle('show');
            }
        };

        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', function () {
            if (window.innerWidth >= 992) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    document.querySelector('.sidebar').classList.add('collapsed');
                }
            }
        });

        // Use a single persistent listener for all sidebar interactions
        document.addEventListener('click', function (e) {
            // Submenu toggle
            const submenuToggle = e.target.closest('.menu-link[data-submenu]');
            if (submenuToggle) {
                e.preventDefault();
                submenuToggle.closest('.menu-item').classList.toggle('open');
                return;
            }

            // Mobile/Desktop sidebar toggle
            const sidebarToggle = e.target.closest('.sidebar-toggle');
            if (sidebarToggle) {
                window.toggleSidebar();
                return;
            }
        });

        document.addEventListener('turbo:load', function () {
            // Auto-hide alerts after 5 seconds
            setTimeout(function () {
                document.querySelectorAll('.alert').forEach(function (alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) bsAlert.close();
                });
            }, 50000);
        });

        // Cleanup Bootstrap state before Turbo caches/replaces the body
        document.addEventListener('turbo:before-cache', function () {
            document.querySelectorAll('.dropdown-toggle.show').forEach(function (el) {
                const dropdown = bootstrap.Dropdown.getOrCreateInstance(el);
                if (dropdown) dropdown.hide();
            });

            // Also ensure tooltips/popovers are hidden if any
            document.querySelectorAll('.tooltip.show, .popover.show').forEach(function (el) {
                el.remove();
            });
        });

        // Global Turbo Error Handling
        document.addEventListener('turbo:fetch-request-error', function (event) {
            console.error('Turbo fetch error:', event.detail.response);

            // If it's a server error or network error, we might want to notify the user
            if (!event.detail.response || event.detail.response.statusCode >= 500) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("messages.error") ?? "Error" }}',
                    text: '{{ __("messages.server_error_occurred") ?? "A server error occurred. Please try again." }}',
                    background: 'rgba(255, 255, 255, 0.9)'
                });
            }
        });

        document.addEventListener('turbo:load', function () {
            // Re-initialize any components that might need it
        });
    </script>

    {{--
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}

    <style>
        /* Turbo Progress Bar */
        .turbo-progress-bar {
            height: 3px;
            background-color: var(--primary-color);
        }
    </style>

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --header-height: 60px;
        }

        * {
            font-family:
                {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif" }}
            ;
        }

        body {
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}
            : 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }



        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .sidebar-brand span,
        .sidebar.collapsed .menu-section,
        .sidebar.collapsed .menu-link span,
        .sidebar.collapsed .menu-arrow {
            display: none;
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
            padding: 0;
        }

        .sidebar.collapsed .sidebar-brand i {
            margin: 0;
            font-size: 1.5rem;
        }

        .sidebar.collapsed .menu-link {
            justify-content: center;
            padding: 15px 0;
        }

        .sidebar.collapsed .menu-link i {
            margin: 0;
            font-size: 1.25rem;
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand i {
            color: var(--primary-color);
        }

        .sidebar-menu {
            padding: 15px 0;
        }

        .menu-section {
            padding: 10px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            font-weight: 600;
            text-align:
                {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}
            ;
        }

        .menu-item {
            position: relative;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: all 0.2s ease;
            border-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 3px solid transparent;
        }

        .menu-link:hover,
        .menu-link.active {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            border-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}-color: var(--primary-color);
        }

        .menu-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 15px;
            text-align: center;
        }

        .menu-arrow {
            margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: auto;
            transition: transform 0.2s ease;
        }

        .menu-item.open .menu-arrow {
            transform: rotate(180deg);
        }

        .submenu {
            display: none;
            background: rgba(0, 0, 0, 0.2);
        }

        .menu-item.open .submenu {
            display: block;
        }

        .submenu .menu-link {
            padding-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 56px;
            font-size: 0.9rem;
        }

        /* Main Content */
        .main-content {
            margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
            width: auto;
        }

        .sidebar.collapsed+.main-content {
            margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: var(--sidebar-collapsed-width);
        }

        /* Header */
        .header {
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #64748b;
            cursor: pointer;
            padding: 5px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .language-switcher {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .language-switcher a {
            padding: 5px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .language-switcher a.active {
            background: var(--primary-color);
            color: #fff;
        }

        .language-switcher a:not(.active) {
            color: #64748b;
        }

        .language-switcher a:not(.active):hover {
            background: #f1f5f9;
        }

        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            background: none;
            border: none;
            padding: 5px 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: #f1f5f9;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info {
            text-align:
                {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}
            ;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1e293b;
        }

        .user-role {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Content Area */
        .content-wrapper {
            padding: 24px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .breadcrumb {
            margin: 0;
            font-size: 0.875rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-title {
            font-weight: 600;
            margin: 0;
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        /* Tables */
        .table {
            margin: 0;
        }

        .table th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
        }

        .table td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        /* Status Badges */
        .badge {
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 20px;
        }

        .badge-draft {
            background: #e2e8f0;
            color: #475569;
        }

        .badge-posted {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-paid {
            background: #d1fae5;
            color: #059669;
        }

        .badge-partial {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-overdue {
            background: #fee2e2;
            color: #dc2626;
        }

        .badge-cancelled {
            background: #f1f5f9;
            color: #64748b;
        }

        /* Forms */
        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 10px 14px;
            font-size: 0.875rem;
        }

        .form-select {
            padding-inline-end: 40px;
            /* Ensure space for the arrow */
        }

        .text-force-left {
            text-align: left !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .search-result-item .item-meta {
            font-size: 0.75rem;
            color: var(--primary-color);
            font-weight: 500;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX({{ app()->getLocale() === 'ar' ? '100%' : '-100%' }});
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 0 !important;
                width: 100% !important;
            }
        }

        /* Pagination Fixes */
        .pagination .page-link svg {
            width: 1rem;
            height: 1rem;
        }

        .pagination .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .pagination {
            flex-wrap: wrap;
            justify-content: center;
            gap: 5px;
        }

        /* Global Search Styles */
        .header-center {
            flex-grow: 1;
            max-width: 800px;
            margin-left: 2rem;
            margin-right: 2rem;
            position: relative;
        }

        .global-search-container {
            width: 100%;
            position: relative;
        }

        .global-search-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .global-search-input {
            width: 100%;
            height: 42px;
            padding: 0 1rem 0 3rem;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background: rgba(243, 244, 246, 0.8);
            backdrop-filter: blur(8px);
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .global-search-input:focus {
            outline: none;
            background: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .global-search-icon {
            position: absolute;
            left: 1.2rem;
            color: #9ca3af;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .global-search-input:focus+.global-search-icon {
            color: var(--primary-color);
        }

        .search-results-container {
            position: absolute;
            top: 110%;
            left: 0;
            right: 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            max-height: 450px;
            overflow-y: auto;
            z-index: 1050;
            display: none;
            animation: slideDown 0.2s ease-out;
            min-height: 50px;
        }

        .search-loading {
            padding: 1.5rem;
            text-align: center;
            color: var(--primary-color);
        }

        .search-error {
            padding: 1.5rem;
            text-align: center;
            color: var(--danger-color);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            text-decoration: none;
            color: inherit;
            transition: background 0.2s ease;
        }

        .search-result-item:hover,
        .search-result-item.active {
            background: #f9fafb;
            text-decoration: none;
            color: inherit;
        }

        .search-result-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(var(--primary-rgb), 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1rem;
        }

        [dir="rtl"] .search-result-icon {
            margin-right: 0;
            margin-left: 1rem;
        }

        .result-content {
            flex-grow: 1;
            overflow: hidden;
        }

        .result-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2px;
            display: block;
            white-space: normal;
            word-break: break-word;
        }

        .result-subtitle {
            font-size: 0.75rem;
            color: #6b7280;
            display: block;
        }

        .result-type {
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 4px;
            background: #f3f4f6;
            color: #4b5563;
            font-weight: 600;
            text-transform: uppercase;
        }

        .search-no-results {
            padding: 2rem;
            text-align: center;
            color: #6b7280;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Company/Branch Switcher -->
                <div class="d-none d-lg-flex align-items-center gap-3 ms-4">
                    @php
                        $activeCompany = \App\Models\Company::find(session('active_company_id'));
                        $activeBranch = \App\Models\Branch::find(session('active_branch_id'));
                        $userCompanies = auth()->user()->hasRole('Super Admin') ? \App\Models\Company::all() : collect([auth()->user()->company]);
                        $companyBranches = \App\Models\Branch::where('company_id', session('active_company_id'))->get();
                    @endphp

                    @if(auth()->user()->hasRole('Super Admin'))
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-building me-1"></i> {{ $activeCompany->name ?? __('Select Company') }}
                            </button>
                            <ul class="dropdown-menu">
                                @foreach($userCompanies as $company)
                                    <li>
                                        <form action="{{ route('switch-company') }}" method="POST" data-turbo="false">
                                            @csrf
                                            <input type="hidden" name="company_id" value="{{ $company->id }}">
                                            <button type="submit"
                                                class="dropdown-item {{ $company->id == session('active_company_id') ? 'active' : '' }}">
                                                {{ $company->name }}
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="text-muted small fw-bold">
                            <i class="fas fa-building me-1"></i> {{ $activeCompany->name ?? '' }}
                        </div>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ $activeBranch->name ?? __('Select Branch') }}
                        </button>
                        <ul class="dropdown-menu">
                            @foreach($companyBranches as $branch)
                                <li>
                                    <form action="{{ route('switch-branch') }}" method="POST" data-turbo="false">
                                        @csrf
                                        <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                                        <button type="submit"
                                            class="dropdown-item {{ $branch->id == session('active_branch_id') ? 'active' : '' }}">
                                            {{ $branch->name }}
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="header-center d-none d-md-block">
                <div class="global-search-container">
                    <div class="global-search-input-wrapper">
                        <input type="text" id="global-search-input" class="global-search-input"
                            placeholder="{{ __('messages.search_placeholder') ?? 'Search anything...' }}"
                            autocomplete="off">
                        <i class="fas fa-search global-search-icon"></i>
                    </div>
                    <div id="search-results" class="search-results-container"></div>
                </div>
            </div>

            <div class="header-right">
                <!-- Language Switcher -->
                <div class="language-switcher">
                    <a href="{{ route('language.switch', 'en') }}" data-turbo="false"
                        class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    <a href="{{ route('language.switch', 'ar') }}" data-turbo="false"
                        class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">عربي</a>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar" data-turbo-permanent id="user-avatar-initial">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="user-info d-none d-md-block">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">
                                {{ app()->getLocale() === 'ar' ? (auth()->user()->roles->first()?->display_name_ar ?? 'مستخدم') : (auth()->user()->roles->first()?->display_name_en ?? 'User') }}
                            </div>
                        </div>
                        <i class="fas fa-chevron-down ms-2" style="font-size: 0.75rem; color: #64748b;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i
                                    class="fas fa-user me-2"></i>{{ __('messages.profile') }}</a></li>
                        <li><a class="dropdown-item" href="#"><i
                                    class="fas fa-cog me-2"></i>{{ __('messages.settings') }}</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" data-turbo="false">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        <!-- Content -->
        <div class="content-wrapper">
            <turbo-frame id="main-frame" data-turbo-action="advance">
                @yield('content')
                @stack('scripts')
            </turbo-frame>
        </div>
    </div>

    <!-- Notifications -->
    <div id="notifications-container">
        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '{{ __("messages.success") }}',
                    text: '{{ session("success") }}',
                    timer: 3000,
                    showConfirmButton: false,
                    background: 'rgba(255, 255, 255, 0.9)',
                    backdrop: `rgba(0,0,123,0.1)`
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("messages.error") }}',
                    text: '{{ session("error") }}',
                    background: 'rgba(255, 255, 255, 0.9)'
                });
            </script>
        @endif
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('turbo:render', function () {
            // Update sidebar active states
            const currentUrl = window.location.href;
            document.querySelectorAll('.sidebar .menu-link').forEach(link => {
                if (link.href === currentUrl) {
                    link.classList.add('active');
                    // Open parent submenu if it exists
                    const parentSubmenu = link.closest('.submenu');
                    if (parentSubmenu) {
                        parentSubmenu.closest('.menu-item').classList.add('open');
                    }
                } else if (link.href && currentUrl.startsWith(link.href) && link.href !== window.location.origin + '/') {
                    // For resource routes like users.index when on users.create
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
        // Global Search Logic
        document.addEventListener('turbo:load', function () {
            const searchInput = document.getElementById('global-search-input');
            const resultsContainer = document.getElementById('search-results');
            let debounceTimer;
            let currentFocus = -1;

            if (!searchInput) return;

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const q = this.value;

                if (q.length < 2) {
                    resultsContainer.style.display = 'none';
                    return;
                }

                // Show loading state
                resultsContainer.innerHTML = `<div class="search-loading">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                </div>`;
                resultsContainer.style.display = 'block';

                debounceTimer = setTimeout(() => {
                    fetch(`{{ route('ajax.global.search') }}?q=${encodeURIComponent(q)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            renderResults(data);
                        })
                        .catch(err => {
                            console.error('Search error:', err);
                            resultsContainer.innerHTML = `<div class="search-error">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p class="mb-0">Error performing search. Please try again.</p>
                        </div>`;
                        });
                }, 400);
            });

            function renderResults(data) {
                if (data.length === 0) {
                    resultsContainer.innerHTML = `<div class="search-no-results">
                        <i class="fas fa-search-minus fa-2x mb-2 opacity-20"></i>
                        <p class="mb-0">{{ __('messages.no_results_found') ?? 'No results found' }}</p>
                    </div>`;
                } else {
                    let html = '';
                    data.forEach((item, index) => {
                        html += `
                            <a href="${item.url}" class="search-result-item" data-index="${index}">
                                <div class="search-result-icon">
                                    <i class="${item.icon}"></i>
                                </div>
                                <div class="result-content">
                                    <span class="result-title">${item.title}</span>
                                    <span class="result-subtitle">${item.subtitle}</span>
                                </div>
                                <span class="result-type">${item.type}</span>
                            </a>
                        `;
                    });
                    resultsContainer.innerHTML = html;
                }
                resultsContainer.style.display = 'block';
                currentFocus = -1;
            }

            searchInput.addEventListener('keydown', function (e) {
                const items = resultsContainer.getElementsByClassName('search-result-item');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentFocus++;
                    addActive(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentFocus--;
                    addActive(items);
                } else if (e.key === 'Enter') {
                    if (currentFocus > -1) {
                        if (items[currentFocus]) items[currentFocus].click();
                    } else if (items.length > 0) {
                        items[0].click();
                    }
                } else if (e.key === 'Escape') {
                    resultsContainer.style.display = 'none';
                }
            });

            function addActive(items) {
                if (!items) return false;
                removeActive(items);
                if (currentFocus >= items.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (items.length - 1);
                items[currentFocus].classList.add('active');
                items[currentFocus].scrollIntoView({ block: 'nearest' });
            }

            function removeActive(items) {
                for (let i = 0; i < items.length; i++) {
                    items[i].classList.remove('active');
                }
            }

            // Close when clicking outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
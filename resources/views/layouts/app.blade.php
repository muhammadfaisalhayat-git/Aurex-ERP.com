<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <!-- Custom CSS: Commented out as file doesn't exist -->
    {{--
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}

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
        }

        /* The instruction provided PHP array elements within a CSS block.
           To maintain syntactical correctness, these PHP elements are placed
           within a PHP comment block. If these are intended for a PHP file,
           they should be moved to the appropriate PHP translation file. */
            {
                {
                -- 'posted_by'=>'Posted By',
                'cash_customer'=>'Cash Customer',
                ];
                --
            }
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
            font-size: 1rem;
            margin-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}: 12px;
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

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Glassy Form Controls */
        .glassy-form .form-control,
        .glassy-form .form-select,
        .glassy-form .input-group-text,
        .glassy-form .btn-outline-secondary {
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .glassy-form .form-control:focus,
        .glassy-form .form-select:focus {
            background: rgba(255, 255, 255, 0.9) !important;
            border-color: var(--primary-color);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
        }

        .glassy-form .card {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .search-results-container.glassy {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            margin-top: 5px;
            overflow: hidden;
            z-index: 2000;
            width: max-content;
            min-width: 500px;
            max-width: 80vw;
        }

        .search-result-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background: rgba(37, 99, 235, 0.08);
            padding-left: 20px;
        }

        .search-result-item .item-title {
            font-weight: 600;
            color: #1e293b;
        }

        .search-result-item .item-subtitle {
            font-size: 0.75rem;
            color: #64748b;
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
                margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 0;
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
                <button class="sidebar-toggle d-lg-none" onclick="toggleSidebar()">
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
                                        <form action="{{ route('switch-company') }}" method="POST">
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
                                    <form action="{{ route('switch-branch') }}" method="POST">
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

            <div class="header-right">
                <!-- Language Switcher -->
                <div class="language-switcher">
                    <a href="{{ route('language.switch', 'en') }}"
                        class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                    <a href="{{ route('language.switch', 'ar') }}"
                        class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">عربي</a>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="user-info d-none d-md-block">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">{{ auth()->user()->roles->first()?->display_name_en ?? 'User' }}
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
                            <form action="{{ route('logout') }}" method="POST">
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }

        // Initialize functionality on Turbo load
        document.addEventListener('turbo:load', function () {
            // Auto-hide alerts after 5 seconds
            setTimeout(function () {
                document.querySelectorAll('.alert').forEach(function (alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Submenu toggle
            document.querySelectorAll('.menu-link[data-submenu]').forEach(function (link) {
                // Remove existing click listeners to avoid duplicates
                link.onclick = null;
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    this.closest('.menu-item').classList.toggle('open');
                });
            });
        });

        // SweetAlert2 Global Configuration
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '{{ __("messages.success") }}',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false,
                background: 'rgba(255, 255, 255, 0.9)',
                backdrop: `rgba(0,0,123,0.1)`
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '{{ __("messages.error") }}',
                text: '{{ session("error") }}',
                background: 'rgba(255, 255, 255, 0.9)'
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>
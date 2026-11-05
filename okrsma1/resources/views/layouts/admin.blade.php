<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title','Admin') - RS Mutiara Aini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        :root {
            --rs-green: #1b7a5a;
            --rs-green-dark: #0e4e37;
            --rs-green-darker: #093424;
            --rs-green-light: #eaf6f1;
            --rs-green-text: #d1fae5;
            --rs-blue: #1b7a5a;
            --rs-blue2: #22956e;
            --rs-light: #eaf6f1;
            --rs-bg: #f4f8f6;

            --bs-primary: #1b7a5a;
            --bs-primary-rgb: 27, 122, 90;
            --bs-link-color: #1b7a5a;
            --bs-link-hover-color: #145a43;
        }

        * {
            box-sizing: border-box
        }

        body {
            background: var(--rs-bg);
            color: #18384d
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1b7a5a, #0e4e37);
            color: #fff;
            position: sticky;
            top: 0;
            height: 100vh
        }

        .sidebar a {
            color: #d1fae5;
            text-decoration: none;
            border-radius: 9px;
            padding: .62rem .72rem;
            display: block
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, .18);
            color: #fff
        }

        .sidebar a.active {
            background: rgba(255, 255, 255, .24);
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .12)
        }

        .brand {
            font-weight: 800;
            letter-spacing: .2px
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #dcece3;
            position: sticky;
            top: 0;
            z-index: 20
        }

        .card {
            border: 0;
            box-shadow: 0 8px 25px rgba(18, 49, 74, .07)
        }

        .btn-rs {
            background: var(--rs-green, #1b7a5a);
            color: #fff
        }

        .btn-rs:hover {
            background: #145a43;
            color: #fff
        }

        .stat {
            border-left: 5px solid #1b7a5a
        }

        .table thead th {
            background: #1b7a5a;
            color: #fff;
            white-space: nowrap
        }

        .btn-primary {
            --bs-btn-bg: #1b7a5a;
            --bs-btn-border-color: #1b7a5a;
            --bs-btn-hover-bg: #145a43;
            --bs-btn-hover-border-color: #145a43;
            --bs-btn-active-bg: #0e4e37;
            --bs-btn-active-border-color: #0e4e37;
            background-color: #1b7a5a !important;
            border-color: #1b7a5a !important;
            color: #fff !important;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: #145a43 !important;
            border-color: #145a43 !important;
            color: #fff !important;
        }

        .btn-outline-primary {
            --bs-btn-color: #1b7a5a;
            --bs-btn-border-color: #1b7a5a;
            --bs-btn-hover-bg: #1b7a5a;
            --bs-btn-hover-border-color: #1b7a5a;
            --bs-btn-active-bg: #1b7a5a;
            --bs-btn-active-border-color: #1b7a5a;
            color: #1b7a5a !important;
            border-color: #1b7a5a !important;
        }

        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary.active {
            background-color: #1b7a5a !important;
            border-color: #1b7a5a !important;
            color: #fff !important;
        }

        .text-primary {
            color: #1b7a5a !important;
        }

        .bg-primary, .text-bg-primary {
            background-color: #1b7a5a !important;
            color: #fff !important;
        }

        .border-primary {
            border-color: #1b7a5a !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: #1b7a5a;
            box-shadow: 0 0 0 0.2rem rgba(27, 122, 90, 0.2);
        }

        .page-item.active .page-link {
            background-color: #1b7a5a;
            border-color: #1b7a5a;
        }

        .page-link {
            color: #1b7a5a;
        }

        .form-control,
        .form-select {
            border-radius: 9px
        }

        .small-muted {
            font-size: .8rem;
            color: #6c757d
        }

        .sidebar-scroll {
            height: calc(100vh - 150px);
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 4px;
        }

        .sidebar-menu-parent {
            color: #d1fae5;
            text-decoration: none;
            border-radius: 9px;
            padding: .62rem .72rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            transition: background .18s ease, color .18s ease;
        }

        .sidebar-menu-parent:hover {
            background: rgba(255, 255, 255, .15);
            color: #fff;
        }

        .sidebar-menu-parent.parent-active {
            background: rgba(255, 255, 255, .14);
            color: #fff;
            font-weight: 600;
        }

        .submenu-caret {
            font-size: .75rem;
            transition: transform .25s ease;
            opacity: .85;
        }

        .sidebar-menu-parent[aria-expanded="true"] .submenu-caret {
            transform: rotate(180deg);
        }

        .submenu-container {
            border-left: 2px solid rgba(255, 255, 255, .22);
            margin-left: 1.15rem;
            padding-left: .5rem;
            display: grid;
            gap: 2px;
            margin-top: 3px;
            margin-bottom: 5px;
        }

        .nav-sub {
            font-size: .84rem;
            padding: .46rem .68rem !important;
            border-radius: 8px;
            color: #d1fae5;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all .16s ease;
        }

        .nav-sub:hover {
            background: rgba(255, 255, 255, .16);
            color: #fff;
            padding-left: .85rem !important;
        }

        .nav-sub.active {
            background: rgba(255, 255, 255, .26) !important;
            color: #fff !important;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .14);
            padding-left: .85rem !important;
        }

        .mobile-nav {
            display: none
        }

        .btn {
            transition: transform .15s ease, box-shadow .15s ease
        }

        .btn:active {
            transform: scale(.97)
        }

        .sidebar a {
            transition: background .18s ease, transform .18s ease
        }

        .sidebar a:active {
            transform: translateX(2px)
        }

        .nav-sub:active {
            transform: translateX(3px)
        }

        @media(max-width:991.98px) {
            .sidebar {
                position: relative;
                height: auto;
                min-height: auto
            }

            .sidebar-scroll {
                height: auto
            }

            .desktop-sidebar {
                display: none
            }

            .mobile-nav {
                display: block
            }

            .main-content {
                width: 100%
            }

            .topbar {
                position: sticky
            }

            .p-4 {
                padding: 1rem !important
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <aside class="col-lg-2 col-md-3 sidebar p-3 desktop-sidebar">
                <div class="d-flex align-items-center gap-2 mb-4"><img src="{{ asset('images/logo-rsma.png') }}" style="width:58px;height:auto">
                    <div class="brand">RS Mutiara Aini<br><small class="fw-normal">Operasi</small></div>
                </div>
                <div class="sidebar-scroll">
                    <nav class="d-grid gap-1">
                        @if(auth()->user()->canAccess('dashboard.view'))
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                        @endif

                        @if(auth()->user()->canAccess('schedule.view'))
                        @php($schedActive = request()->is('admin/schedules*'))
                        <div class="sidebar-dropdown">
                            <a class="sidebar-menu-parent {{ $schedActive ? 'parent-active' : '' }}" data-bs-toggle="collapse" href="#menuSchedules" role="button" aria-expanded="{{ $schedActive ? 'true' : 'false' }}">
                                <span><i class="bi bi-calendar3 me-2"></i>Jadwal Operasi</span>
                                <i class="bi bi-chevron-down submenu-caret"></i>
                            </a>
                            <div class="collapse {{ $schedActive ? 'show' : '' }}" id="menuSchedules">
                                <div class="submenu-container">
                                    <a class="nav-sub {{ request()->routeIs('admin.schedules.create') || request()->routeIs('admin.schedules.index') ? 'active' : '' }}" href="{{ route('admin.schedules.create') }}"><i class="bi bi-calendar-plus me-2"></i>Tambah & Kalender</a>
                                    @if(auth()->user()->canAccess('schedule.manage'))
                                    <a class="nav-sub {{ request()->routeIs('admin.schedules.manage') || request()->routeIs('admin.schedules.edit') ? 'active' : '' }}" href="{{ route('admin.schedules.manage') }}"><i class="bi bi-list-check me-2"></i>Manage Jadwal</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->canAccess('master.manage'))
                        @php($masterActive = request()->is('admin/masters*'))
                        <div class="sidebar-dropdown">
                            <a class="sidebar-menu-parent {{ $masterActive ? 'parent-active' : '' }}" data-bs-toggle="collapse" href="#menuMaster" role="button" aria-expanded="{{ $masterActive ? 'true' : 'false' }}">
                                <span><i class="bi bi-database me-2"></i>Data Master</span>
                                <i class="bi bi-chevron-down submenu-caret"></i>
                            </a>
                            <div class="collapse {{ $masterActive ? 'show' : '' }}" id="menuMaster">
                                <div class="submenu-container">
                                    <a class="nav-sub {{ request()->is('admin/masters/patients*') ? 'active' : '' }}" href="{{ route('admin.masters.index','patients') }}"><i class="bi bi-person-vcard me-2"></i>Pasien</a>
                                    <a class="nav-sub {{ request()->is('admin/masters/operators*') ? 'active' : '' }}" href="{{ route('admin.masters.index','operators') }}"><i class="bi bi-person-badge me-2"></i>Operator</a>
                                    <a class="nav-sub {{ request()->is('admin/masters/anesthesias*') ? 'active' : '' }}" href="{{ route('admin.masters.index','anesthesias') }}"><i class="bi bi-heart-pulse me-2"></i>Anestesi</a>
                                    <a class="nav-sub {{ request()->is('admin/masters/teams*') ? 'active' : '' }}" href="{{ route('admin.masters.index','teams') }}"><i class="bi bi-people me-2"></i>Tim Bedah</a>
                                    <a class="nav-sub {{ request()->is('admin/masters/professions*') ? 'active' : '' }}" href="{{ route('admin.masters.index','professions') }}"><i class="bi bi-briefcase me-2"></i>Data Profesi</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->canAccess('shift.manage'))
                        @php($shiftsActive = request()->is('admin/shifts*'))
                        <div class="sidebar-dropdown">
                            <a class="sidebar-menu-parent {{ $shiftsActive ? 'parent-active' : '' }}" data-bs-toggle="collapse" href="#menuShifts" role="button" aria-expanded="{{ $shiftsActive ? 'true' : 'false' }}">
                                <span><i class="bi bi-clock-history me-2"></i>Atur Shift</span>
                                <i class="bi bi-chevron-down submenu-caret"></i>
                            </a>
                            <div class="collapse {{ $shiftsActive ? 'show' : '' }}" id="menuShifts">
                                <div class="submenu-container">
                                    <a class="nav-sub {{ request()->routeIs('admin.shifts.index') ? 'active' : '' }}" href="{{ route('admin.shifts.index') }}"><i class="bi bi-calendar-event me-2"></i>Jadwal Shift</a>
                                    <a class="nav-sub {{ request()->routeIs('admin.shifts.manage') ? 'active' : '' }}" href="{{ route('admin.shifts.manage') }}"><i class="bi bi-sliders me-2"></i>Manage Shift</a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->canAccess('user.manage') || auth()->user()->canAccess('audit.view') || auth()->user()->canAccess('backup.manage'))
                        @php($userSysActive = request()->is('admin/users*') || request()->is('admin/activity-logs*') || request()->is('admin/backups*') || request()->is('admin/greetings*'))
                        <div class="sidebar-dropdown">
                            <a class="sidebar-menu-parent {{ $userSysActive ? 'parent-active' : '' }}" data-bs-toggle="collapse" href="#menuUserSystem" role="button" aria-expanded="{{ $userSysActive ? 'true' : 'false' }}">
                                <span><i class="bi bi-shield-lock me-2"></i>User & IT Sistem</span>
                                <i class="bi bi-chevron-down submenu-caret"></i>
                            </a>
                            <div class="collapse {{ $userSysActive ? 'show' : '' }}" id="menuUserSystem">
                                <div class="submenu-container">
                                    @if(auth()->user()->canAccess('user.manage'))
                                    <a class="nav-sub {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>User & Akses</a>
                                    @endif
                                    <a class="nav-sub" href="{{ route('admin.display') }}" target="_blank"><i class="bi bi-tv me-2"></i>Display TV</a>
                                    @if(auth()->user()->canAccess('audit.view'))
                                    <a class="nav-sub {{ request()->routeIs('admin.activity_logs.*') ? 'active' : '' }}" href="{{ route('admin.activity_logs.index') }}"><i class="bi bi-shield-check me-2"></i>Rekap Aktivitas IT</a>
                                    @endif
                                    @if(auth()->user()->canAccess('backup.manage'))
                                    <a class="nav-sub {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}" href="{{ route('admin.backups.index') }}"><i class="bi bi-database-check me-2"></i>Backup & Restore</a>
                                    @endif
                                    @if(auth()->user()->canAccess('user.manage'))
                                    <a class="nav-sub {{ request()->routeIs('admin.greetings.*') ? 'active' : '' }}" href="{{ route('admin.greetings.index') }}"><i class="bi bi-chat-square-heart me-2"></i>Pengaturan Salam</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @else
                            @if(auth()->user()->canAccess('display.view'))
                            <a href="{{ route('admin.display') }}" target="_blank"><i class="bi bi-tv me-2"></i>Display TV</a>
                            @endif
                        @endif

                        <a href="{{ route('admin.profile.edit') }}" class="{{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"><i class="bi bi-person-gear me-2"></i>Profil Saya</a>
                    </nav>
                </div>
                <div class="mt-3 small" style="color:#d1fae5;opacity:.85">Login: <b>{{ auth()->user()->username }}</b><br>Laravel 12</div>
            </aside>
            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
                <div class="offcanvas-header text-white" style="background:linear-gradient(180deg, #1b7a5a, #0e4e37)">
                    <h5 class="mb-0">RS Mutiara Aini</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <nav class="d-grid gap-2">
                        @if(auth()->user()->canAccess('dashboard.view'))
                        <a class="btn btn-outline-success text-start {{ request()->routeIs('admin.dashboard') ? 'active text-white' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                        @endif

                        @if(auth()->user()->canAccess('schedule.view'))
                        @php($mSchedActive = request()->is('admin/schedules*'))
                        <div>
                            <button class="btn btn-outline-success text-start w-100 d-flex justify-content-between align-items-center {{ $mSchedActive ? 'active text-white' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuSchedules" aria-expanded="{{ $mSchedActive ? 'true' : 'false' }}">
                                <span><i class="bi bi-calendar3 me-2"></i>Jadwal Operasi</span>
                                <i class="bi bi-chevron-down small"></i>
                            </button>
                            <div class="collapse {{ $mSchedActive ? 'show' : '' }} ps-3 mt-1 d-grid gap-1" id="mobileMenuSchedules">
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->routeIs('admin.schedules.create') || request()->routeIs('admin.schedules.index') ? 'active text-white' : '' }}" href="{{ route('admin.schedules.create') }}"><i class="bi bi-calendar-plus me-2"></i>Tambah & Kalender</a>
                                @if(auth()->user()->canAccess('schedule.manage'))
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->routeIs('admin.schedules.manage') ? 'active text-white' : '' }}" href="{{ route('admin.schedules.manage') }}"><i class="bi bi-list-check me-2"></i>Manage Jadwal</a>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->canAccess('master.manage'))
                        @php($mMasterActive = request()->is('admin/masters*'))
                        <div>
                            <button class="btn btn-outline-success text-start w-100 d-flex justify-content-between align-items-center {{ $mMasterActive ? 'active text-white' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuMaster" aria-expanded="{{ $mMasterActive ? 'true' : 'false' }}">
                                <span><i class="bi bi-database me-2"></i>Data Master</span>
                                <i class="bi bi-chevron-down small"></i>
                            </button>
                            <div class="collapse {{ $mMasterActive ? 'show' : '' }} ps-3 mt-1 d-grid gap-1" id="mobileMenuMaster">
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->is('admin/masters/patients*') ? 'active text-white' : '' }}" href="{{ route('admin.masters.index','patients') }}"><i class="bi bi-person-vcard me-2"></i>Pasien</a>
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->is('admin/masters/operators*') ? 'active text-white' : '' }}" href="{{ route('admin.masters.index','operators') }}"><i class="bi bi-person-badge me-2"></i>Operator</a>
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->is('admin/masters/anesthesias*') ? 'active text-white' : '' }}" href="{{ route('admin.masters.index','anesthesias') }}"><i class="bi bi-heart-pulse me-2"></i>Anestesi</a>
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->is('admin/masters/teams*') ? 'active text-white' : '' }}" href="{{ route('admin.masters.index','teams') }}"><i class="bi bi-people me-2"></i>Tim Bedah</a>
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->is('admin/masters/professions*') ? 'active text-white' : '' }}" href="{{ route('admin.masters.index','professions') }}"><i class="bi bi-briefcase me-2"></i>Data Profesi</a>
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->canAccess('shift.manage'))
                        @php($mShiftActive = request()->is('admin/shifts*'))
                        <div>
                            <button class="btn btn-outline-success text-start w-100 d-flex justify-content-between align-items-center {{ $mShiftActive ? 'active text-white' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuShifts" aria-expanded="{{ $mShiftActive ? 'true' : 'false' }}">
                                <span><i class="bi bi-clock-history me-2"></i>Atur Shift</span>
                                <i class="bi bi-chevron-down small"></i>
                            </button>
                            <div class="collapse {{ $mShiftActive ? 'show' : '' }} ps-3 mt-1 d-grid gap-1" id="mobileMenuShifts">
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->routeIs('admin.shifts.index') ? 'active text-white' : '' }}" href="{{ route('admin.shifts.index') }}"><i class="bi bi-calendar-event me-2"></i>Jadwal Shift</a>
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->routeIs('admin.shifts.manage') ? 'active text-white' : '' }}" href="{{ route('admin.shifts.manage') }}"><i class="bi bi-sliders me-2"></i>Manage Shift</a>
                            </div>
                        </div>
                        @endif

                        @if(auth()->user()->canAccess('user.manage') || auth()->user()->canAccess('audit.view') || auth()->user()->canAccess('backup.manage'))
                        @php($mUserActive = request()->is('admin/users*') || request()->is('admin/activity-logs*') || request()->is('admin/backups*') || request()->is('admin/greetings*'))
                        <div>
                            <button class="btn btn-outline-success text-start w-100 d-flex justify-content-between align-items-center {{ $mUserActive ? 'active text-white' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenuUserSystem" aria-expanded="{{ $mUserActive ? 'true' : 'false' }}">
                                <span><i class="bi bi-shield-lock me-2"></i>User & IT Sistem</span>
                                <i class="bi bi-chevron-down small"></i>
                            </button>
                            <div class="collapse {{ $mUserActive ? 'show' : '' }} ps-3 mt-1 d-grid gap-1" id="mobileMenuUserSystem">
                                @if(auth()->user()->canAccess('user.manage'))
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->routeIs('admin.users.*') ? 'active text-white' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i>User & Akses</a>
                                @endif
                                <a class="btn btn-sm btn-outline-secondary text-start" target="_blank" href="{{ route('admin.display') }}"><i class="bi bi-tv me-2"></i>Display TV</a>
                                @if(auth()->user()->canAccess('audit.view'))
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->routeIs('admin.activity_logs.*') ? 'active text-white' : '' }}" href="{{ route('admin.activity_logs.index') }}"><i class="bi bi-shield-check me-2"></i>Rekap Aktivitas IT</a>
                                @endif
                                @if(auth()->user()->canAccess('backup.manage'))
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->routeIs('admin.backups.*') ? 'active text-white' : '' }}" href="{{ route('admin.backups.index') }}"><i class="bi bi-database-check me-2"></i>Backup & Restore</a>
                                @endif
                                @if(auth()->user()->canAccess('user.manage'))
                                <a class="btn btn-sm btn-outline-secondary text-start {{ request()->routeIs('admin.greetings.*') ? 'active text-white' : '' }}" href="{{ route('admin.greetings.index') }}"><i class="bi bi-chat-square-heart me-2"></i>Pengaturan Salam</a>
                                @endif
                            </div>
                        </div>
                        @else
                            @if(auth()->user()->canAccess('display.view'))
                            <a class="btn btn-outline-dark text-start" target="_blank" href="{{ route('admin.display') }}"><i class="bi bi-tv me-2"></i>Display TV</a>
                            @endif
                        @endif

                        <a class="btn btn-outline-success text-start {{ request()->routeIs('admin.profile.*') ? 'active text-white' : '' }}" href="{{ route('admin.profile.edit') }}"><i class="bi bi-person-gear me-2"></i>Profil Saya</a>
                    </nav>
                </div>
            </div>
            <main class="col-lg-10 col-md-9 col-12 p-0 main-content">
                <header class="topbar px-3 px-lg-4 py-2 d-flex justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2"><button class="btn btn-outline-success d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"><i class="bi bi-list"></i></button>
                        <div>
                            <h5 class="mb-0">@yield('title','Dashboard')</h5><small class="text-muted">Sistem Jadwal Operasi</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.profile.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark rounded-pill border px-2 py-1 bg-white" title="Buka Profil Saya" style="max-width:240px"><span class="rounded-circle border d-inline-flex align-items-center justify-content-center overflow-hidden bg-light flex-shrink-0" style="width:36px;height:36px">@if(auth()->user()->profile_photo && file_exists(public_path(auth()->user()->profile_photo)))<img src="{{ asset(auth()->user()->profile_photo) }}?v={{ @filemtime(public_path(auth()->user()->profile_photo)) }}" alt="Foto profil" style="width:100%;height:100%;object-fit:cover">@else<i class="bi bi-person-fill fs-5 text-secondary"></i>@endif</span><span class="d-none d-sm-block text-truncate" style="max-width:145px"><strong class="d-block small text-truncate">{{ auth()->user()->name }}</strong><small class="text-muted">{{ '@' . auth()->user()->username }}</small></span><i class="bi bi-chevron-down small text-secondary"></i></a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-secondary btn-sm" title="Keluar"><i class="bi bi-box-arrow-right"></i><span class="d-none d-md-inline"> Keluar</span></button></form>
                    </div>
                </header>
                <div class="p-4">@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif @if($errors->any())<div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>@endif @yield('content')</div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function(e) {
            const el = e.target.closest('.sidebar a, .topbar button, .btn');
            if (!el) return;
            el.animate([{
                transform: 'scale(1)'
            }, {
                transform: 'scale(.96)'
            }, {
                transform: 'scale(1)'
            }], {
                duration: 180,
                easing: 'ease-out'
            });
        });
    </script>
    @stack('scripts')
    @php($activeGreeting = !session()->has('greeting_popup_shown') ? \App\Models\Greeting::currentlyActive()->orderBy('start_time')->first() : null)
    @if($activeGreeting)
    @php(session()->put('greeting_popup_shown', true))
    <div class="modal fade" id="loginGreeting" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden">
                <div class="p-4 text-white" style="background:linear-gradient(135deg,#1b7a5a,#2bb184)">
                    <div class="fs-1 mb-2">☀️</div>
                    <h3 class="fw-bold mb-1">{{ $activeGreeting->title }}</h3>
                    <div class="opacity-75">RS Mutiara Aini</div>
                </div>
                <div class="modal-body p-4 fs-5">{{ $activeGreeting->message }}
                    <div class="small text-muted mt-3">Selamat bekerja dan utamakan keselamatan pasien.</div>
                </div>
                <div class="modal-footer border-0 pt-0"><button class="btn btn-rs px-4" data-bs-dismiss="modal">Terima Kasih</button></div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.getElementById('loginGreeting');
            if (el && window.bootstrap) new bootstrap.Modal(el).show();
        });
    </script>
    @endif
</body>

</html>
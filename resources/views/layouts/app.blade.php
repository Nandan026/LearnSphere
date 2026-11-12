@php
    $user = auth()->user();
    $user?->loadMissing('roles');
    $initials = collect(explode(' ', (string) $user?->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'LearnSphere'))</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta20/dist/css/tabler.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/learnsphere.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
<div class="ls-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>
<div class="ls-shell">
    <aside class="ls-sidebar" id="appSidebar">
        <a href="{{ route('dashboard') }}" class="ls-brand-link">
            <span class="ls-brand-mark"><i class="bi bi-mortarboard-fill"></i></span>
            <span>
                <span class="ls-brand-text d-block">LearnSphere</span>
                <span class="ls-brand-sub">Learning workspace</span>
            </span>
        </a>

        <nav class="nav flex-column gap-1 pb-4 mt-2">
            <div class="ls-nav-section">Overview</div>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i><span class="ls-nav-label">Dashboard</span>
            </a>
            <a class="nav-link {{ request()->routeIs('catalogue.*') ? 'active' : '' }}" href="{{ route('catalogue.index') }}">
                <i class="bi bi-grid-1x2"></i><span class="ls-nav-label">Catalogue</span>
            </a>

            @if ($user?->hasAnyRole(['learner', 'super-admin', 'instructor']))
                <div class="ls-nav-section">Learning</div>
                <a class="nav-link {{ request()->routeIs('dashboard') && $user->hasRole('learner') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-play-circle"></i><span class="ls-nav-label">My learning</span>
                </a>
                <a class="nav-link {{ request()->routeIs('learner.paths.*') ? 'active' : '' }}" href="{{ route('learner.paths.index') }}">
                    <i class="bi bi-signpost-split"></i><span class="ls-nav-label">Learning paths</span>
                </a>
                <a class="nav-link {{ request()->routeIs('learner.certificates.*') ? 'active' : '' }}" href="{{ route('learner.certificates.index') }}">
                    <i class="bi bi-award"></i><span class="ls-nav-label">Certificates</span>
                </a>
                <a class="nav-link {{ request()->routeIs('learner.orders.*') ? 'active' : '' }}" href="{{ route('learner.orders.index') }}">
                    <i class="bi bi-receipt"></i><span class="ls-nav-label">Orders</span>
                </a>
                <a class="nav-link {{ request()->routeIs('learner.notifications.*') ? 'active' : '' }}" href="{{ route('learner.notifications.index') }}">
                    <i class="bi bi-bell"></i><span class="ls-nav-label">Notifications</span>
                </a>
            @endif

            @if ($user?->hasAnyRole(['instructor', 'super-admin']))
                <div class="ls-nav-section">Instructor</div>
                <a class="nav-link {{ request()->routeIs('instructor.courses.*') ? 'active' : '' }}" href="{{ route('instructor.courses.index') }}">
                    <i class="bi bi-journal-bookmark"></i><span class="ls-nav-label">My courses</span>
                </a>
                <a class="nav-link {{ request()->routeIs('instructor.discussions.*') ? 'active' : '' }}" href="{{ route('instructor.discussions.index') }}">
                    <i class="bi bi-chat-dots"></i><span class="ls-nav-label">Discussions</span>
                </a>
            @endif

            @if ($user?->hasRole('super-admin'))
                <div class="ls-nav-section">Admin</div>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i><span class="ls-nav-label">Users</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-tags"></i><span class="ls-nav-label">Categories</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.moderation.*') ? 'active' : '' }}" href="{{ route('admin.moderation.index') }}">
                    <i class="bi bi-shield-check"></i><span class="ls-nav-label">Moderation</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                    <i class="bi bi-bar-chart"></i><span class="ls-nav-label">Reports</span>
                </a>
                <a class="nav-link {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}" href="{{ route('admin.audit.index') }}">
                    <i class="bi bi-clock-history"></i><span class="ls-nav-label">Audit log</span>
                </a>
            @endif

            @if ($user?->hasRole('content-manager'))
                <div class="ls-nav-section">Content</div>
                <a class="nav-link {{ request()->routeIs('content.moderation.*') ? 'active' : '' }}" href="{{ route('content.moderation.index') }}">
                    <i class="bi bi-check2-square"></i><span class="ls-nav-label">Publish courses</span>
                </a>
            @endif

            @if ($user?->hasRole('support-staff'))
                <div class="ls-nav-section">Support</div>
                <a class="nav-link {{ request()->routeIs('support.*') ? 'active' : '' }}" href="{{ route('support.users.index') }}">
                    <i class="bi bi-headset"></i><span class="ls-nav-label">Support desk</span>
                </a>
            @endif

            <div class="ls-nav-section">Account</div>
            <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                <i class="bi bi-person-circle"></i><span class="ls-nav-label">Profile</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="px-2">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                    <i class="bi bi-box-arrow-right"></i><span class="ls-nav-label">Sign out</span>
                </button>
            </form>
        </nav>
    </aside>

    <div class="ls-main">
        <header class="ls-topbar px-3 py-2">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm d-lg-none" onclick="toggleSidebar()" aria-label="Open menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm d-none d-lg-inline-flex" id="sidebarCollapseBtn" data-bs-toggle="tooltip" title="Collapse sidebar">
                        <i class="bi bi-layout-sidebar-inset"></i>
                    </button>
                    <div class="d-none d-md-block">
                        <div class="small text-muted">@yield('breadcrumb', 'LearnSphere')</div>
                        <div class="fw-semibold">@yield('page_title', trim($__env->yieldContent('title')))</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <form class="ls-quicksearch d-none d-md-block" action="{{ route('catalogue.index') }}" method="GET" role="search">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                            <input type="search" name="q" class="form-control" placeholder="Search courses…" value="{{ request('q') }}" data-bs-toggle="tooltip" title="Search the catalogue">
                        </div>
                    </form>
                    <a href="{{ route('learner.notifications.index') }}" class="btn btn-outline-secondary btn-sm position-relative" data-bs-toggle="tooltip" title="Notifications">
                        <i class="bi bi-bell"></i>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                        <span class="avatar-chip">{{ strtoupper($initials ?: 'U') }}</span>
                        <span class="d-none d-sm-block small text-end ls-user-meta">
                            <strong class="d-block">{{ $user?->name }}</strong>
                            <span class="text-muted">{{ $user?->roles->pluck('name')->join(', ') }}</span>
                        </span>
                    </a>
                </div>
            </div>
        </header>

        <main class="ls-content fade-up">
            @include('partials.flash')
            @hasSection('page_header')
                @yield('page_header')
            @endif
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/learnsphere.js') }}"></script>
@stack('scripts')
</body>
</html>

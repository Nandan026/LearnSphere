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
<body class="guest-shell">
    <nav class="guest-nav navbar navbar-dark navbar-expand-lg sticky-top">
        <div class="container py-1">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="ls-brand-mark" style="width:34px;height:34px;font-size:1rem;"><i class="bi bi-mortarboard-fill"></i></span>
                <span class="fw-bold">LearnSphere</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a class="nav-link text-white-50 d-none d-sm-inline" href="{{ route('certificates.verify') }}">Verify certificate</a>
                @guest
                    <a class="btn btn-sm btn-outline-light" href="{{ route('login') }}">Sign in</a>
                    <a class="btn btn-sm btn-ls-primary" href="{{ route('register') }}">Register</a>
                @else
                    <a class="btn btn-sm btn-ls-primary" href="{{ route('dashboard') }}">Dashboard</a>
                @endguest
            </div>
        </div>
    </nav>

    <main class="py-4 fade-up">
        <div class="container">
            @include('partials.flash')
            @yield('content')
        </div>
    </main>

    <footer class="guest-footer py-3">
        <div class="container d-flex flex-wrap justify-content-between gap-2">
            <span>© {{ date('Y') }} LearnSphere</span>
            <span>Portfolio LMS · simulated payments only</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/learnsphere.js') }}"></script>
    @stack('scripts')
</body>
</html>

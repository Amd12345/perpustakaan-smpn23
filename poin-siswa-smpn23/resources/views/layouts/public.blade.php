@php
    $schoolName = \App\Models\Setting::getValue('school_name', 'SMP Negeri 23 Kota Tangerang Selatan');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#0e2436">
    <title>@yield('title', 'Dashboard Publik') | {{ $schoolName }}</title>

    <link rel="preload" href="{{ asset('fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('vendor/bs/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <style>
        .public-top { background: var(--brand-900); color: #fff; }
        .public-wrap { max-width: 1120px; margin: 0 auto; padding: 0 16px; }
        .public-head { display: flex; align-items: center; gap: 12px; padding: 16px 0; }
        .public-name { font-size: 15px; font-weight: 700; line-height: 1.3; }
        .public-sub { font-size: 12px; color: rgba(233,238,245,.66); }
        .public-cta { margin-left: auto; }
        .public-cta .btn { --bs-btn-bg: rgba(255,255,255,.12); --bs-btn-border-color: rgba(255,255,255,.2); --bs-btn-color: #fff; --bs-btn-hover-bg: rgba(255,255,255,.2); --bs-btn-hover-color: #fff; }
        @media (min-width: 768px) { .public-wrap { padding: 0 24px; } }
    </style>
</head>
<body>
    <header class="public-top">
        <div class="public-wrap public-head">
            <span class="rail-mark"><i class="bi bi-journal-check" aria-hidden="true"></i></span>
            <div>
                <div class="public-name">{{ $schoolName }}</div>
                <div class="public-sub">Sistem Poin Pelanggaran &amp; Prestasi</div>
            </div>
            <div class="public-cta">
                <a href="{{ route('login') }}" class="btn btn-sm">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Masuk
                </a>
            </div>
        </div>
    </header>

    <main class="content mx-auto" style="max-width:1120px">
        @yield('content')
    </main>

    <footer class="pb-4 text-center muted" style="font-size:12.5px">
        &copy; {{ date('Y') }} {{ $schoolName }}
    </footer>

    <script src="{{ asset('vendor/bs/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('vendor/chart/chart.umd.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
    @yield('scripts')
</body>
</html>

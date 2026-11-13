@php
    $schoolName = \App\Models\Setting::getValue('school_name', 'SMP Negeri 23 Kota Tangerang Selatan');
    $schoolShort = \Illuminate\Support\Str::limit($schoolName, 34);
    $user = auth()->user();
    $initials = $user
        ? \Illuminate\Support\Str::of($user->name)->explode(' ')->filter()->take(2)->map(fn ($w) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($w, 0, 1)))->implode('')
        : '?';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#0e2436">
    <title>@yield('title', 'Dashboard') | Sistem Poin {{ $schoolShort }}</title>

    <link rel="preload" href="{{ asset('fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('vendor/bs/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a class="skip-link" href="#main">Lewati ke konten</a>

    <aside class="rail" aria-label="Navigasi utama">
        @include('partials.sidebar')
    </aside>

    <div class="offcanvas offcanvas-start drawer" tabindex="-1" id="navDrawer" aria-label="Menu navigasi">
        <div class="d-flex justify-content-end p-2 pb-0">
            <button type="button" class="icon-btn" data-bs-dismiss="offcanvas" aria-label="Tutup menu"
                style="color:#e8eef5">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>
        @include('partials.sidebar', ['inDrawer' => true])
    </div>

    <div class="shell">
        <header class="appbar d-lg-none">
            <button class="icon-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#navDrawer"
                aria-controls="navDrawer" aria-label="Buka menu">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
            <div class="min-w-0 flex-grow-1">
                <span class="appbar-title">{{ $schoolShort }}</span>
                <span class="appbar-foot">Poin pelanggaran &amp; prestasi</span>
            </div>
            <button class="appbar-user" type="button" data-bs-toggle="offcanvas" data-bs-target="#navDrawer"
                aria-label="Akun {{ $user?->name ?? 'pengguna' }}">
                <span class="avatar" aria-hidden="true">{{ $initials }}</span>
                <i class="bi bi-chevron-down" aria-hidden="true" style="font-size:12px;color:var(--ink-3)"></i>
            </button>
        </header>

        <main id="main" class="content">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>

    <nav class="bottomnav d-lg-none" aria-label="Navigasi cepat">
        @php
            $canRecord = $user && $user->hasAnyRole(['super_admin', 'admin', 'guru']);
        @endphp
        <a class="bottomnav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"
            @if(request()->routeIs('dashboard')) aria-current="page" @endif>
            <i class="bi bi-grid-1x2" aria-hidden="true"></i>
            <span>Dashboard</span>
        </a>
        @if(Route::has('students.index'))
            <a class="bottomnav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}"
                @if(request()->routeIs('students.*')) aria-current="page" @endif>
                <i class="bi bi-people" aria-hidden="true"></i>
                <span>Siswa</span>
            </a>
        @endif
        @if($canRecord && Route::has('violations.create'))
            <a class="bottomnav-link {{ request()->routeIs('violations.*') && ! request()->routeIs('violations.create') ? 'active' : '' }}"
                href="{{ route('violations.create') }}">
                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                <span>Catat</span>
            </a>
        @elseif(Route::has('violations.index'))
            <a class="bottomnav-link {{ request()->routeIs('violations.*') ? 'active' : '' }}" href="{{ route('violations.index') }}"
                @if(request()->routeIs('violations.*')) aria-current="page" @endif>
                <i class="bi bi-exclamation-octagon" aria-hidden="true"></i>
                <span>Pelanggaran</span>
            </a>
        @endif
        @if(Route::has('achievements.index'))
            <a class="bottomnav-link {{ request()->routeIs('achievements.*') ? 'active' : '' }}" href="{{ route('achievements.index') }}"
                @if(request()->routeIs('achievements.*')) aria-current="page" @endif>
                <i class="bi bi-award" aria-hidden="true"></i>
                <span>Prestasi</span>
            </a>
        @endif
        <button class="bottomnav-link border-0 bg-transparent {{ request()->routeIs('point-transactions.*', 'monthly-reports.*', 'settings.*', 'users.*', 'improvements.*', 'conversions.*', 'notifications.*', 'activity-logs.*', 'classes.*', 'teachers.*', 'parents.*', 'violation-categories.*', 'achievement-categories.*', 'point-rules.*', 'academic-years.*') ? 'active' : '' }}"
            type="button" data-bs-toggle="offcanvas" data-bs-target="#navDrawer" aria-controls="navDrawer">
            <i class="bi bi-list" aria-hidden="true"></i>
            <span>Menu</span>
        </button>
    </nav>

    <script src="{{ asset('vendor/bs/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('vendor/swal/sweetalert2.all.min.js') }}" defer></script>
    <script src="{{ asset('vendor/chart/chart.umd.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
    @yield('scripts')
</body>
</html>

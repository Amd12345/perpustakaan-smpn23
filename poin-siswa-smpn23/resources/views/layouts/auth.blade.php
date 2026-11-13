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
    <title>@yield('title', 'Masuk') | Sistem Poin {{ $schoolName }}</title>

    <link rel="preload" href="{{ asset('fonts/plus-jakarta-sans-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('vendor/bs/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <style>
        .auth-page { min-height: 100vh; display: flex; flex-direction: column; background: var(--paper); }
        .auth-top { background: var(--brand-900); color: #fff; padding: 22px 20px 26px; }
        .auth-top .rail-mark { width: 38px; height: 38px; font-size: 19px; }
        .auth-school { font-size: 15px; font-weight: 700; line-height: 1.3; }
        .auth-tagline { font-size: 12px; color: rgba(233,238,245,.66); }
        .auth-main { flex: 1 1 auto; padding: 0 16px 32px; margin-top: -14px; }
        .auth-card { width: 100%; max-width: 420px; margin: 0 auto; background: var(--surface); border: 1px solid var(--line); border-radius: var(--r-xl); padding: 22px; box-shadow: 0 12px 30px -22px rgba(16,36,54,.35); }
        .auth-title { font-size: 20px; font-weight: 800; margin: 0 0 2px; }
        .auth-sub { font-size: 13.5px; color: var(--ink-3); margin: 0 0 18px; }
        .auth-foot { font-size: 12px; color: var(--ink-3); text-align: center; margin: 18px 0 0; }
        @media (min-width: 992px) {
            .auth-page { flex-direction: row; }
            .auth-aside { flex: 0 0 44%; background: var(--brand-900); color: #fff; padding: 44px 44px 48px; display: flex; flex-direction: column; gap: 32px; }
            .auth-top { background: transparent; padding: 0; }
            .auth-hero { margin-top: auto; }
            .auth-main { flex: 1 1 auto; display: flex; flex-direction: column; align-items: center; justify-content: center; background: var(--surface); padding: 40px; margin-top: 0; gap: 16px; }
            .auth-card { border: 0; box-shadow: none; padding: 0; max-width: 380px; width: 100%; }
            .auth-foot { text-align: center; margin: 0; max-width: 380px; }
        }
    </style>
</head>
<body>
    <div class="auth-page">
        <aside class="auth-aside d-none d-lg-flex">
            <div class="auth-top d-flex align-items-center gap-3">
                <div class="rail-mark"><i class="bi bi-journal-check" aria-hidden="true"></i></div>
                <div>
                    <div class="auth-school">{{ $schoolName }}</div>
                    <div class="auth-tagline">Sistem Poin Pelanggaran &amp; Prestasi</div>
                </div>
            </div>
            <div class="auth-hero">
                <p style="font-size:26px;font-weight:800;line-height:1.3;letter-spacing:-.03em;max-width:22ch;margin:0 0 12px">
                    Catatan poin yang rapi, dari kelas sampai laporan bulanan.
                </p>
                <p style="font-size:14px;color:rgba(233,238,245,.7);max-width:38ch;margin:0">
                    Guru mencatat pelanggaran dan prestasi, sistem menjumlahkan saldo dan mengirim kabar ke orang tua.
                </p>
            </div>
        </aside>

        <div class="auth-main flex-column justify-content-start justify-content-lg-center d-flex">
            <div class="auth-top d-lg-none d-flex align-items-center gap-3">
                <div class="rail-mark"><i class="bi bi-journal-check" aria-hidden="true"></i></div>
                <div>
                    <div class="auth-school">{{ $schoolName }}</div>
                    <div class="auth-tagline">Sistem Poin Pelanggaran &amp; Prestasi</div>
                </div>
            </div>

            <div class="auth-card">
                @yield('content')
            </div>

            <p class="auth-foot">&copy; {{ date('Y') }} {{ $schoolName }}</p>
        </div>
    </div>

    <script src="{{ asset('vendor/bs/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('vendor/swal/sweetalert2.all.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
    @yield('scripts')
</body>
</html>

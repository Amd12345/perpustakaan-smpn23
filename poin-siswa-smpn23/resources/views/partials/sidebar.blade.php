@php
    $user = auth()->user();
    $roleLabel = match (true) {
        $user?->hasRole('super_admin') => 'Super Admin',
        $user?->hasRole('admin') => 'Admin',
        $user?->hasRole('kepala_sekolah') => 'Kepala Sekolah',
        $user?->hasRole('guru') => 'Guru',
        default => $user?->getRoleNames()->first() ?? 'Pengguna',
    };
    $initials = $user
        ? \Illuminate\Support\Str::of($user->name)->explode(' ')->filter()->take(2)->map(fn ($w) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($w, 0, 1)))->implode('')
        : '?';
    $inDrawer = $inDrawer ?? false;
    $isAdmin = $user && $user->hasAnyRole(['super_admin', 'admin']);
    $canRecord = $user && $user->hasAnyRole(['super_admin', 'admin', 'guru']);
@endphp

<div class="rail-inner">
    <a class="rail-brand" href="{{ route('dashboard') }}">
        <span class="rail-mark"><i class="bi bi-journal-check" aria-hidden="true"></i></span>
        <span class="rail-brand-text">
            <span class="rail-brand-name">{{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 23 Kota Tangerang Selatan') }}</span>
            <span class="rail-brand-sub">Poin pelanggaran &amp; prestasi</span>
        </span>
    </a>

    <nav class="rail-nav" aria-label="Menu aplikasi">
        <a class="rail-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"
            @if(request()->routeIs('dashboard')) aria-current="page" @endif>
            <i class="bi bi-grid-1x2" aria-hidden="true"></i> <span>Dashboard</span>
        </a>

        <div class="rail-group">Data siswa</div>
        @if(Route::has('students.index'))
            <a class="rail-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                <i class="bi bi-people" aria-hidden="true"></i> <span>Data Siswa</span>
            </a>
        @endif
        @if($isAdmin)
            @if(Route::has('classes.index'))
                <a class="rail-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}">
                    <i class="bi bi-diagram-3" aria-hidden="true"></i> <span>Data Kelas</span>
                </a>
            @endif
            @if(Route::has('teachers.index'))
                <a class="rail-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}" href="{{ route('teachers.index') }}">
                    <i class="bi bi-person-badge" aria-hidden="true"></i> <span>Data Guru</span>
                </a>
            @endif
            @if(Route::has('parents.index'))
                <a class="rail-link {{ request()->routeIs('parents.*') ? 'active' : '' }}" href="{{ route('parents.index') }}">
                    <i class="bi bi-person-lines-fill" aria-hidden="true"></i> <span>Data Orang Tua</span>
                </a>
            @endif
        @endif

        @if($user)
            <div class="rail-group">Pencatatan</div>
            @if(Route::has('violations.index'))
                <a class="rail-link {{ request()->routeIs('violations.*') ? 'active' : '' }}" href="{{ route('violations.index') }}">
                    <i class="bi bi-exclamation-octagon" aria-hidden="true"></i> <span>Data Pelanggaran</span>
                </a>
            @endif
            @if(Route::has('achievements.index'))
                <a class="rail-link {{ request()->routeIs('achievements.*') ? 'active' : '' }}" href="{{ route('achievements.index') }}">
                    <i class="bi bi-award" aria-hidden="true"></i> <span>Data Prestasi</span>
                </a>
            @endif
            @if($canRecord)
                @if(Route::has('improvements.index'))
                    <a class="rail-link {{ request()->routeIs('improvements.*') ? 'active' : '' }}" href="{{ route('improvements.index') }}">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> <span>Pengurangan Poin</span>
                    </a>
                @endif
                @if(Route::has('conversions.index'))
                    <a class="rail-link {{ request()->routeIs('conversions.*') ? 'active' : '' }}" href="{{ route('conversions.index') }}">
                        <i class="bi bi-arrow-left-right" aria-hidden="true"></i> <span>Konversi Poin</span>
                    </a>
                @endif
            @endif
            @if(Route::has('point-transactions.index'))
                <a class="rail-link {{ request()->routeIs('point-transactions.*') ? 'active' : '' }}" href="{{ route('point-transactions.index') }}">
                    <i class="bi bi-clock-history" aria-hidden="true"></i> <span>Riwayat Poin</span>
                </a>
            @endif
        @endif

        @if($isAdmin)
            <div class="rail-group">Referensi</div>
            @if(Route::has('violation-categories.index'))
                <a class="rail-link {{ request()->routeIs('violation-categories.*') ? 'active' : '' }}" href="{{ route('violation-categories.index') }}">
                    <i class="bi bi-list-ul" aria-hidden="true"></i> <span>Kategori Pelanggaran</span>
                </a>
            @endif
            @if(Route::has('achievement-categories.index'))
                <a class="rail-link {{ request()->routeIs('achievement-categories.*') ? 'active' : '' }}" href="{{ route('achievement-categories.index') }}">
                    <i class="bi bi-trophy" aria-hidden="true"></i> <span>Kategori Prestasi</span>
                </a>
            @endif
            @if(Route::has('point-rules.index'))
                <a class="rail-link {{ request()->routeIs('point-rules.*') ? 'active' : '' }}" href="{{ route('point-rules.index') }}">
                    <i class="bi bi-sliders" aria-hidden="true"></i> <span>Aturan Poin</span>
                </a>
            @endif
            @if(Route::has('academic-years.index'))
                <a class="rail-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}" href="{{ route('academic-years.index') }}">
                    <i class="bi bi-calendar3" aria-hidden="true"></i> <span>Tahun Ajaran</span>
                </a>
            @endif
        @endif

        @if($user && $user->hasAnyRole(['super_admin', 'admin', 'kepala_sekolah']) && Route::has('monthly-reports.index'))
            <div class="rail-group">Laporan</div>
            <a class="rail-link {{ request()->routeIs('monthly-reports.*') ? 'active' : '' }}" href="{{ route('monthly-reports.index') }}">
                <i class="bi bi-file-earmark-text" aria-hidden="true"></i> <span>Laporan Bulanan</span>
            </a>
        @endif

        @if($isAdmin)
            <a class="rail-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}" href="{{ route('activity-logs.index') }}">
                <i class="bi bi-journal-text" aria-hidden="true"></i> <span>Log Aktivitas</span>
            </a>
            @if(Route::has('notifications.index'))
                <a class="rail-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                    <i class="bi bi-whatsapp" aria-hidden="true"></i> <span>Notifikasi WhatsApp</span>
                </a>
            @endif
        @endif

        @if($user && $user->hasRole('super_admin'))
            <div class="rail-group">Sistem</div>
            @if(Route::has('users.index'))
                <a class="rail-link {{ request()->routeIs('users.*') && request()->route('role') === 'admin' ? 'active' : '' }}" href="{{ route('users.index', 'admin') }}">
                    <i class="bi bi-person-gear" aria-hidden="true"></i> <span>Manajemen Admin</span>
                </a>
                <a class="rail-link {{ request()->routeIs('users.*') && request()->route('role') === 'kepala_sekolah' ? 'active' : '' }}" href="{{ route('users.index', 'kepala_sekolah') }}">
                    <i class="bi bi-briefcase" aria-hidden="true"></i> <span>Manajemen Kepala Sekolah</span>
                </a>
                <a class="rail-link {{ request()->routeIs('users.*') && request()->route('role') === 'guru' ? 'active' : '' }}" href="{{ route('users.index', 'guru') }}">
                    <i class="bi bi-mortarboard" aria-hidden="true"></i> <span>Manajemen Guru</span>
                </a>
            @endif
            @if(Route::has('settings.index'))
                <a class="rail-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                    <i class="bi bi-gear" aria-hidden="true"></i> <span>Pengaturan Sistem</span>
                </a>
            @endif
        @endif
    </nav>

    <div class="rail-foot">
        <div class="rail-user">
            <span class="avatar" aria-hidden="true">{{ $initials }}</span>
            <span class="min-w-0">
                <span class="rail-user-name d-block">{{ $user?->name ?? 'Tamu' }}</span>
                <span class="rail-user-role d-block">{{ $roleLabel }}</span>
            </span>
        </div>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rail-logout" type="submit">
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Keluar
                </button>
            </form>
        @endauth
    </div>
</div>

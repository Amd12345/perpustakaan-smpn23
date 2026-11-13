@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-page-head title="Dashboard" :subtitle="'Tahun ajaran '.($year?->name ?? 'belum diatur')">
        <a href="{{ route('public.dashboard') }}" class="btn btn-outline-secondary d-none d-lg-inline-flex">
            <i class="bi bi-globe2" aria-hidden="true"></i> Dashboard publik
        </a>
    </x-page-head>

    <div class="quick-actions">
        @if(auth()->user()?->hasAnyRole(['super_admin', 'admin', 'guru']))
            <a href="{{ route('violations.create') }}" class="quick-action danger">
                <i class="bi bi-exclamation-octagon" aria-hidden="true"></i> Catat pelanggaran
            </a>
            <a href="{{ route('achievements.create') }}" class="quick-action">
                <i class="bi bi-award" aria-hidden="true"></i> Catat prestasi
            </a>
        @endif
        <a href="{{ route('students.index') }}" class="quick-action">
            <i class="bi bi-search" aria-hidden="true"></i> Cari siswa
        </a>
        <a href="{{ route('point-transactions.index') }}" class="quick-action">
            <i class="bi bi-clock-history" aria-hidden="true"></i> Riwayat poin
        </a>
    </div>

    <div class="stat-grid">
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Siswa aktif</span>
                <span class="stat-icon tint-brand"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value">{{ number_format($totalStudents) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Pelanggaran</span>
                <span class="stat-icon tint-danger"><i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value">{{ number_format($totalViolations) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Prestasi</span>
                <span class="stat-icon tint-success"><i class="bi bi-trophy-fill" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value">{{ number_format($totalAchievements) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Poin pelanggaran</span>
                <span class="stat-icon tint-accent"><i class="bi bi-arrow-down-circle-fill" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value text-pelanggaran">{{ number_format($violationPoints) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Poin prestasi</span>
                <span class="stat-icon tint-neutral"><i class="bi bi-star-fill" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value text-prestasi">{{ number_format($achievementPoints) }}</div>
        </div>
    </div>

    <div class="grid-3 section-gap grid-charts">
        <div class="card grid-wide chart-card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-bar-chart" aria-hidden="true"></i> Pelanggaran &amp; prestasi per bulan</h2>
                <div class="chart-box is-bar">
                    <canvas id="chartMonthly" role="img" aria-label="Grafik jumlah pelanggaran dan prestasi per bulan"></canvas>
                </div>
            </div>
        </div>
        <div class="card chart-card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-pie-chart" aria-hidden="true"></i> Pelanggaran per kategori</h2>
                <div class="chart-box is-doughnut">
                    <canvas id="chartCategories" role="img" aria-label="Grafik pelanggaran per kategori"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-3 section-gap">
        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-trophy" aria-hidden="true"></i> Peringkat prestasi</h2>
                <ol class="list-plain">
                    @forelse($topAchievers as $item)
                        <li class="rank-row">
                            <span class="rank-no" aria-hidden="true">{{ $loop->iteration }}</span>
                            <span class="list-row-main">
                                <span class="list-row-title d-block">{{ $item['student']->name }}</span>
                                <span class="list-row-sub">{{ $item['student']->class?->name ?? '-' }}</span>
                            </span>
                            <span class="list-row-end text-prestasi">{{ number_format($item['total']) }}</span>
                        </li>
                    @empty
                        <li class="list-row-sub py-2">Belum ada data.</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i> Poin pelanggaran tertinggi</h2>
                <ol class="list-plain">
                    @forelse($topViolators as $item)
                        <li class="rank-row">
                            <span class="rank-no" aria-hidden="true">{{ $loop->iteration }}</span>
                            <span class="list-row-main">
                                <span class="list-row-title d-block">{{ $item['student']->name }}</span>
                                <span class="list-row-sub">{{ $item['student']->class?->name ?? '-' }}</span>
                            </span>
                            <span class="list-row-end text-pelanggaran">{{ number_format($item['total']) }}</span>
                        </li>
                    @empty
                        <li class="list-row-sub py-2">Belum ada data.</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-arrow-down-circle" aria-hidden="true"></i> Pengurangan terakhir</h2>
                <ul class="list-plain">
                    @forelse($reductions as $tx)
                        <li class="list-row">
                            <span class="list-row-main">
                                <span class="list-row-title d-block">{{ $tx->student?->name ?? '-' }}</span>
                                <span class="list-row-sub">{{ $tx->transaction_date?->format('d M Y') }}</span>
                            </span>
                            <span class="list-row-end">{{ $tx->points }}</span>
                        </li>
                    @empty
                        <li class="list-row-sub py-2">Belum ada pengurangan.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @if(! auth()->user()?->hasRole('guru'))
        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-clock-history" aria-hidden="true"></i> Aktivitas terakhir</h2>
                <ul class="list-plain">
                    @forelse($activityLogs as $log)
                        <li class="list-row">
                            <span class="list-row-main">
                                <span class="list-row-title d-block">{{ $log->description ?? $log->action }}</span>
                                <span class="list-row-sub">{{ $log->user?->name ?? 'Sistem' }}</span>
                            </span>
                            <span class="list-row-end list-row-sub">{{ $log->created_at?->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-row-sub py-2">Belum ada aktivitas.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const sharedOptions = {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle', padding: 14 } } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef2f7' } } }
            };

            // Potong bulan kosong di ujung supaya grafik tidak menyisakan ruang mati.
            const monthly = @json($chart);
            const hasData = (i) => (monthly.violations[i] || 0) > 0 || (monthly.achievements[i] || 0) > 0;
            let start = 0;
            let end = monthly.labels.length - 1;
            while (start < end && !hasData(start)) start += 1;
            while (end > start && !hasData(end)) end -= 1;

            new Chart(document.getElementById('chartMonthly'), {
                type: 'bar',
                data: {
                    labels: monthly.labels.slice(start, end + 1),
                    datasets: [
                        { label: 'Pelanggaran', data: monthly.violations.slice(start, end + 1), backgroundColor: '#9f3129', borderRadius: 4, maxBarThickness: 22 },
                        { label: 'Prestasi', data: monthly.achievements.slice(start, end + 1), backgroundColor: '#176146', borderRadius: 4, maxBarThickness: 22 }
                    ]
                },
                options: sharedOptions
            });

            const categories = @json($categoryChart);

            new Chart(document.getElementById('chartCategories'), {
                type: 'doughnut',
                data: {
                    labels: categories.labels.map((label, index) => label + ' (' + categories.data[index] + ')'),
                    datasets: [{
                        data: categories.data,
                        backgroundColor: ['#9f3129', '#b4522c', '#9a5b00', '#22608f', '#176146', '#5d6f81'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    cutout: '62%',
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle', padding: 12 } } }
                }
            });

            // Pastikan kanvas menyesuaikan diri setelah tata letak selesai.
            window.requestAnimationFrame(function () {
                window.dispatchEvent(new Event('resize'));
            });
        });
    </script>
@endsection

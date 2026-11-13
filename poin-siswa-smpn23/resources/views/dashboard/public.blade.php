@extends('layouts.public')

@section('title', 'Dashboard Publik')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Ringkasan sekolah</h1>
            <p class="page-sub">{{ $schoolName }} &middot; {{ $schoolCity }}</p>
        </div>
    </div>

    <div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr))">
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Siswa aktif</span>
                <span class="stat-icon tint-brand"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value">{{ number_format($totalStudents) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Laporan pelanggaran</span>
                <span class="stat-icon tint-danger"><i class="bi bi-exclamation-octagon-fill" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value">{{ number_format($totalViolations) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Laporan prestasi</span>
                <span class="stat-icon tint-success"><i class="bi bi-trophy-fill" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value">{{ number_format($totalAchievements) }}</div>
        </div>
    </div>

    <div class="grid-3 section-gap grid-charts">
        <div class="card grid-wide chart-card">
            <div class="card-body">
                <h2 class="card-section-title">Perkembangan per bulan</h2>
                <div class="chart-box is-bar">
                    <canvas id="chartPublic" role="img" aria-label="Grafik perkembangan pelanggaran dan prestasi per bulan"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-section-title">Statistik per kelas</h2>
                @if($classes->count())
                    <div class="table-responsive">
                        <table class="table table-stack">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th class="text-end">Pelanggaran</th>
                                    <th class="text-end">Prestasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classes as $class)
                                    <tr>
                                        <td data-label="Kelas" class="cell-main">{{ $class->name }}</td>
                                        <td data-label="Pelanggaran" class="text-end text-pelanggaran num">{{ number_format($class->violations_count) }}</td>
                                        <td data-label="Prestasi" class="text-end text-prestasi num">{{ number_format($class->achievements_count) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-empty-state icon="building" title="Belum ada data kelas" text="Data kelas muncul setelah admin mengisi tahun ajaran." />
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="card-section-title">Prestasi siswa</h2>
            @if($achievements->count())
                <div class="row g-3">
                    @foreach($achievements as $item)
                        <div class="col-md-4 col-6">
                            <article class="h-100">
                                @if($item->documentation_path)
                                    <img src="{{ asset('storage/'.$item->documentation_path) }}" width="400" height="160"
                                        style="height:150px;width:100%;object-fit:cover;border-radius:10px" loading="lazy"
                                        alt="Dokumentasi prestasi {{ $item->student->name }}">
                                @else
                                    <div class="d-flex align-items-center justify-content-center"
                                        style="height:150px;border-radius:10px;background:var(--success-bg);color:var(--success)">
                                        <i class="bi bi-trophy" style="font-size:28px" aria-hidden="true"></i>
                                    </div>
                                @endif
                                <div class="mt-2 fw-semibold" style="font-size:14px">{{ $item->student->name }}</div>
                                <div class="list-row-sub">{{ $item->student->class?->name }} &middot; {{ $item->category?->name }}</div>
                                <div class="mt-1 d-flex align-items-center gap-2">
                                    <span class="tag tag-success">+{{ $item->point_amount }} poin</span>
                                    <span class="list-row-sub">{{ $item->achievement_date?->format('d M Y') }}</span>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
                {{ $achievements->links() }}
            @else
                <x-empty-state icon="award" title="Belum ada prestasi dipublikasikan" text="Prestasi yang dicatat guru akan tampil di sini." />
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const monthly = @json($chart);
            const hasData = (i) => (monthly.violations[i] || 0) > 0 || (monthly.achievements[i] || 0) > 0;
            let start = 0;
            let end = monthly.labels.length - 1;
            while (start < end && !hasData(start)) start += 1;
            while (end > start && !hasData(end)) end -= 1;

            new Chart(document.getElementById('chartPublic'), {
                type: 'line',
                data: {
                    labels: monthly.labels.slice(start, end + 1),
                    datasets: [
                        { label: 'Pelanggaran', data: monthly.violations.slice(start, end + 1), borderColor: '#9f3129', backgroundColor: 'rgba(159,49,41,.10)', tension: .35, fill: true, borderWidth: 2, pointRadius: 3 },
                        { label: 'Prestasi', data: monthly.achievements.slice(start, end + 1), borderColor: '#176146', backgroundColor: 'rgba(23,97,70,.10)', tension: .35, fill: true, borderWidth: 2, pointRadius: 3 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle', padding: 14 } } },
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eef2f7' } } }
                }
            });

            window.requestAnimationFrame(function () {
                window.dispatchEvent(new Event('resize'));
            });
        });
    </script>
@endsection

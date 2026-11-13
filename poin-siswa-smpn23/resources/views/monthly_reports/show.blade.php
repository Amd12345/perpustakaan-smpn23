@extends('layouts.app')

@section('title', 'Laporan '.App\Services\MonthlyReportService::monthName($report->month).' '.$report->year)

@section('content')
    <x-page-head title="Laporan {{ App\Services\MonthlyReportService::monthName($report->month) }} {{ $report->year }}"
        :subtitle="'Tahun ajaran '.($report->academicYear?->name ?? '-')"
        :back="route('monthly-reports.index')" back-label="Laporan bulanan">
        @can('manage-reports')
            <a href="{{ route('monthly-reports.revise', $report) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pencil-square" aria-hidden="true"></i> Revisi
            </a>
            @if($report->status !== 'final')
                <form method="POST" action="{{ route('monthly-reports.finalize', $report) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" data-confirm="Finalkan laporan ini?">
                        <i class="bi bi-check2-circle" aria-hidden="true"></i> Finalkan
                    </button>
                </form>
            @endif
        @endcan
        <a href="{{ route('monthly-reports.pdf', $report) }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">
            <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> PDF
        </a>
        <a href="{{ route('monthly-reports.excel', $report) }}" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-excel" aria-hidden="true"></i> Excel
        </a>
    </x-page-head>

    @php
        $totals = $report->snapshot['totals'] ?? [];
        $previous = $report->previous_month_totals ?? [];
    @endphp

    <div class="stat-grid">
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Pelanggaran</span>
                <span class="stat-icon tint-danger"><i class="bi bi-exclamation-octagon" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value text-pelanggaran">{{ number_format((int) ($totals['violations'] ?? 0)) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Prestasi</span>
                <span class="stat-icon tint-success"><i class="bi bi-trophy" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value text-prestasi">{{ number_format((int) ($totals['achievements'] ?? 0)) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Poin pelanggaran</span>
                <span class="stat-icon tint-accent"><i class="bi bi-arrow-down-circle" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value text-pelanggaran">{{ number_format((int) ($totals['violation_points'] ?? 0)) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Poin prestasi</span>
                <span class="stat-icon tint-neutral"><i class="bi bi-star" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value text-prestasi">{{ number_format((int) ($totals['achievement_points'] ?? 0)) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Pengurangan</span>
                <span class="stat-icon tint-brand"><i class="bi bi-dash-circle" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value">{{ number_format((int) ($totals['reductions'] ?? 0)) }}</div>
        </div>
        <div class="stat">
            <div class="stat-top">
                <span class="stat-label">Status</span>
                <span class="stat-icon tint-neutral"><i class="bi bi-patch-check" aria-hidden="true"></i></span>
            </div>
            <div class="stat-value">
                <span class="tag {{ $report->status === 'final' ? 'tag-success' : 'tag-accent' }}">{{ ucfirst($report->status) }}</span>
            </div>
        </div>
    </div>

    <div class="card section-gap">
        <div class="card-body">
            <h2 class="card-section-title"><i class="bi bi-arrow-left-right" aria-hidden="true"></i> Perbandingan bulan sebelumnya</h2>
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Metrik</th>
                            <th class="text-end">Bulan ini</th>
                            <th class="text-end">{{ $previous['label'] ?? 'Bulan sebelumnya' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="cell-main">Total pelanggaran</td>
                            <td class="text-end num" data-label="Bulan ini">{{ $totals['violations'] ?? 0 }}</td>
                            <td class="text-end num" data-label="{{ $previous['label'] ?? 'Bulan sebelumnya' }}">{{ $previous['violations'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="cell-main">Total prestasi</td>
                            <td class="text-end num" data-label="Bulan ini">{{ $totals['achievements'] ?? 0 }}</td>
                            <td class="text-end num" data-label="{{ $previous['label'] ?? 'Bulan sebelumnya' }}">{{ $previous['achievements'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="cell-main">Poin pelanggaran</td>
                            <td class="text-end text-pelanggaran num" data-label="Bulan ini">{{ $totals['violation_points'] ?? 0 }}</td>
                            <td class="text-end num" data-label="{{ $previous['label'] ?? 'Bulan sebelumnya' }}">{{ $previous['violation_points'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="cell-main">Poin prestasi</td>
                            <td class="text-end text-prestasi num" data-label="Bulan ini">{{ $totals['achievement_points'] ?? 0 }}</td>
                            <td class="text-end num" data-label="{{ $previous['label'] ?? 'Bulan sebelumnya' }}">{{ $previous['achievement_points'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="cell-main">Pengurangan poin</td>
                            <td class="text-end num" data-label="Bulan ini">{{ $totals['reductions'] ?? 0 }}</td>
                            <td class="text-end num" data-label="{{ $previous['label'] ?? 'Bulan sebelumnya' }}">{{ $previous['reductions'] ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid-2 section-gap">
        <div class="stack-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-section-title"><i class="bi bi-people" aria-hidden="true"></i> Rekap per kelas</h2>
                    <div class="table-responsive">
                        <table class="table table-hover table-stack align-middle">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th class="text-end">Pelanggaran</th>
                                    <th class="text-end">Prestasi</th>
                                    <th class="text-end">Poin pelanggaran</th>
                                    <th class="text-end">Poin prestasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report->snapshot['per_class'] ?? [] as $row)
                                    <tr>
                                        <td class="cell-main">{{ $row['class'] }}</td>
                                        <td class="text-end num" data-label="Pelanggaran">{{ $row['violations'] }}</td>
                                        <td class="text-end num" data-label="Prestasi">{{ $row['achievements'] }}</td>
                                        <td class="text-end text-pelanggaran num" data-label="Poin pelanggaran">{{ $row['violation_points'] }}</td>
                                        <td class="text-end text-prestasi num" data-label="Poin prestasi">{{ $row['achievement_points'] }}</td>
                                    </tr>
                                @empty
                                    <tr class="row-empty">
                                        <td colspan="5" class="cell-empty">
                                            <x-empty-state icon="inbox" title="Belum ada rekap kelas" text="Rekap muncul setelah laporan dibuat." compact />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-section-title"><i class="bi bi-bar-chart" aria-hidden="true"></i> Kategori pelanggaran terbanyak</h2>
                    <ol class="list-plain">
                        @forelse($report->snapshot['top_violation_categories'] ?? [] as $row)
                            <li class="rank-row">
                                <span class="rank-no" aria-hidden="true">{{ $loop->iteration }}</span>
                                <span class="list-row-main">
                                    <span class="list-row-title d-block">{{ $row['category'] }}</span>
                                </span>
                                <span class="list-row-end"><span class="tag tag-danger">{{ $row['total'] }}</span></span>
                            </li>
                        @empty
                            <li class="list-row-sub py-2">Belum ada data.</li>
                        @endforelse
                    </ol>
                </div>
            </div>
        </div>

        <div class="stack-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-section-title"><i class="bi bi-person-lines-fill" aria-hidden="true"></i> Rekap per siswa</h2>
                    <div class="table-responsive">
                        <table class="table table-hover table-stack align-middle">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th class="text-end">Poin pelanggaran</th>
                                    <th class="text-end">Poin prestasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report->snapshot['per_student'] ?? [] as $row)
                                    <tr>
                                        <td class="cell-main">{{ $row['name'] }}</td>
                                        <td data-label="Kelas">{{ $row['class'] }}</td>
                                        <td class="text-end text-pelanggaran num" data-label="Poin pelanggaran">{{ $row['violation_points'] }}</td>
                                        <td class="text-end text-prestasi num" data-label="Poin prestasi">{{ $row['achievement_points'] }}</td>
                                    </tr>
                                @empty
                                    <tr class="row-empty">
                                        <td colspan="4" class="cell-empty">
                                            <x-empty-state icon="people" title="Belum ada rekap siswa" text="Rekap muncul setelah laporan dibuat." compact />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-section-title"><i class="bi bi-award" aria-hidden="true"></i> Prestasi bulan ini</h2>
                    <ul class="list-plain">
                        @forelse($report->snapshot['achievements'] ?? [] as $row)
                            <li class="list-row">
                                <span class="list-row-main">
                                    <span class="list-row-title d-block">{{ $row['name'] }}</span>
                                    <span class="list-row-sub">{{ $row['class'] }}</span>
                                </span>
                                <span class="list-row-end"><span class="tag tag-neutral">{{ $row['category'] }}</span></span>
                            </li>
                        @empty
                            <li class="list-row-sub py-2">Belum ada prestasi.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="card-section-title"><i class="bi bi-clock-history" aria-hidden="true"></i> Riwayat revisi</h2>
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Pengubah</th>
                            <th>Waktu</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report->revisions as $revision)
                            <tr>
                                <td class="cell-main">{{ $revision->user?->name ?? '-' }}</td>
                                <td class="num" data-label="Waktu">{{ $revision->created_at->format('d M Y H:i') }}</td>
                                <td data-label="Alasan">{{ $revision->change_reason }}</td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="3" class="cell-empty">
                                    <x-empty-state icon="clock-history" title="Belum ada revisi" text="Riwayat revisi tercatat setiap kali laporan diubah." compact />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

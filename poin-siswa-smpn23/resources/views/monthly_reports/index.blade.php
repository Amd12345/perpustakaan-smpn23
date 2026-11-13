@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
    <x-page-head title="Laporan bulanan" :subtitle="'Rekap pelanggaran, prestasi, dan pengurangan poin tahun '.$year">
        <form method="GET" class="d-flex gap-2">
            <label class="visually-hidden" for="f-year">Tahun laporan</label>
            <select name="year" id="f-year" class="form-select form-select-sm">
                @foreach($availableYears as $item)
                    <option value="{{ $item }}" @selected($year == $item)>{{ $item }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary" type="submit">
                <i class="bi bi-funnel" aria-hidden="true"></i> Terapkan
            </button>
        </form>
    </x-page-head>

    @can('manage-reports')
        <div class="card section-gap">
            <div class="card-body">
                <h2 class="card-section-title"><i class="bi bi-calendar-plus" aria-hidden="true"></i> Buat laporan baru</h2>
                <form method="POST" action="{{ route('monthly-reports.generate') }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-6 col-md-4">
                        <label class="form-label" for="g-year">Tahun</label>
                        <input type="number" id="g-year" name="year" class="form-control" value="{{ $year }}" required>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label" for="g-month">Bulan</label>
                        <select name="month" id="g-month" class="form-select" required>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $index => $name)
                                <option value="{{ $index + 1 }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-primary w-100" data-confirm="Buat laporan untuk periode ini?">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i> Buat laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-stack align-middle">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th class="text-end">Pelanggaran</th>
                            <th class="text-end">Prestasi</th>
                            <th class="text-end">Poin pelanggaran</th>
                            <th class="text-end">Poin prestasi</th>
                            <th class="text-end">Pengurangan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td class="cell-main">
                                    <a href="{{ route('monthly-reports.show', $report) }}">{{ \App\Services\MonthlyReportService::monthName($report->month) }} {{ $report->year }}</a>
                                </td>
                                <td class="text-end num" data-label="Pelanggaran">{{ $report->total_violations }}</td>
                                <td class="text-end num" data-label="Prestasi">{{ $report->total_achievements }}</td>
                                <td class="text-end text-pelanggaran num" data-label="Poin pelanggaran">{{ $report->total_violation_points }}</td>
                                <td class="text-end text-prestasi num" data-label="Poin prestasi">{{ $report->total_achievement_points }}</td>
                                <td class="text-end num" data-label="Pengurangan">{{ $report->total_reductions }}</td>
                                <td data-label="Status">
                                    <span class="tag {{ $report->status === 'final' ? 'tag-success' : ($report->status === 'revisi' ? 'tag-accent' : 'tag-neutral') }}">{{ ucfirst($report->status) }}</span>
                                </td>
                                <td class="cell-actions" data-label="Aksi">
                                    <a href="{{ route('monthly-reports.show', $report) }}" class="btn-icon"
                                        aria-label="Lihat laporan {{ \App\Services\MonthlyReportService::monthName($report->month) }} {{ $report->year }}">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('monthly-reports.pdf', $report) }}" class="btn-icon" target="_blank" rel="noopener"
                                        aria-label="Unduh PDF laporan {{ \App\Services\MonthlyReportService::monthName($report->month) }} {{ $report->year }}">
                                        <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('monthly-reports.excel', $report) }}" class="btn-icon"
                                        aria-label="Unduh Excel laporan {{ \App\Services\MonthlyReportService::monthName($report->month) }} {{ $report->year }}">
                                        <i class="bi bi-file-earmark-excel" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="row-empty">
                                <td colspan="8" class="cell-empty">
                                    @can('manage-reports')
                                        <x-empty-state icon="file-earmark-bar-graph" title="Belum ada laporan"
                                            text="Pilih tahun lain di atas, atau buat laporan untuk bulan yang sudah selesai." />
                                    @else
                                        <x-empty-state icon="file-earmark-bar-graph" title="Belum ada laporan"
                                            text="Pilih tahun lain di atas untuk melihat laporan yang sudah dibuat." />
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $reports->links() }}
        </div>
    </div>
@endsection

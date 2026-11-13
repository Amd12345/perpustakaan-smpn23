@extends('layouts.app')

@section('title', 'Revisi Laporan')

@section('content')
    <x-page-head title="Revisi laporan {{ App\Services\MonthlyReportService::monthName($report->month) }} {{ $report->year }}"
        subtitle="Nilai sebelumnya tersimpan di riwayat revisi."
        :back="route('monthly-reports.show', $report)" back-label="Kembali ke laporan">
    </x-page-head>

    <div class="card">
        <div class="card-body">
            <h2 class="card-section-title"><i class="bi bi-pencil-square" aria-hidden="true"></i> Angka laporan</h2>
            <form method="POST" action="{{ route('monthly-reports.revise.store', $report) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label" for="total_violations">Total pelanggaran</label>
                        <input type="number" id="total_violations" name="total_violations"
                            class="form-control num @error('total_violations') is-invalid @enderror"
                            min="0" value="{{ old('total_violations', $report->total_violations) }}" required>
                        @error('total_violations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label" for="total_achievements">Total prestasi</label>
                        <input type="number" id="total_achievements" name="total_achievements"
                            class="form-control num @error('total_achievements') is-invalid @enderror"
                            min="0" value="{{ old('total_achievements', $report->total_achievements) }}" required>
                        @error('total_achievements')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label" for="total_violation_points">Total poin pelanggaran</label>
                        <input type="number" id="total_violation_points" name="total_violation_points"
                            class="form-control num @error('total_violation_points') is-invalid @enderror"
                            min="0" value="{{ old('total_violation_points', $report->total_violation_points) }}" required>
                        @error('total_violation_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label" for="total_achievement_points">Total poin prestasi</label>
                        <input type="number" id="total_achievement_points" name="total_achievement_points"
                            class="form-control num @error('total_achievement_points') is-invalid @enderror"
                            min="0" value="{{ old('total_achievement_points', $report->total_achievement_points) }}" required>
                        @error('total_achievement_points')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label class="form-label" for="total_reductions">Total pengurangan poin</label>
                        <input type="number" id="total_reductions" name="total_reductions"
                            class="form-control num @error('total_reductions') is-invalid @enderror"
                            min="0" value="{{ old('total_reductions', $report->total_reductions) }}" required>
                        @error('total_reductions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="change_reason">Alasan perubahan</label>
                        <textarea id="change_reason" name="change_reason" rows="3" required
                            class="form-control @error('change_reason') is-invalid @enderror"
                            placeholder="Jelaskan alasan revisi">{{ old('change_reason') }}</textarea>
                        @error('change_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('monthly-reports.show', $report) }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary" data-confirm="Simpan revisi laporan ini?">
                        <i class="bi bi-save" aria-hidden="true"></i> Simpan revisi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

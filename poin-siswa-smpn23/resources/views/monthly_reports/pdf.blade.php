<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 16px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; }
        h5 { margin: 16px 0 6px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #999; padding: 5px 7px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; }
        .stats td { text-align: right; }
        .footer { margin-top: 24px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ \App\Models\Setting::getValue('school_name', 'Sekolah') }}</h2>
        <p>{{ \App\Models\Setting::getValue('school_address', '') }}</p>
        <p>Laporan Bulanan Poin Pelanggaran dan Prestasi Siswa</p>
        <p><strong>Periode: {{ \App\Services\MonthlyReportService::monthName($report->month) }} {{ $report->year }}</strong> ({{ $report->academicYear?->name }})</p>
    </div>

    @php
        $totals = $report->snapshot['totals'] ?? [];
        $previous = $report->previous_month_totals ?? [];
    @endphp

    <h5>Ringkasan</h5>
    <table class="stats">
        <tr><th>Total Pelanggaran</th><td>{{ $totals['violations'] ?? 0 }}</td><th>Total Prestasi</th><td>{{ $totals['achievements'] ?? 0 }}</td></tr>
        <tr><th>Poin Pelanggaran</th><td>{{ $totals['violation_points'] ?? 0 }}</td><th>Poin Prestasi</th><td>{{ $totals['achievement_points'] ?? 0 }}</td></tr>
        <tr><th>Pengurangan Poin</th><td>{{ $totals['reductions'] ?? 0 }}</td><th>Status</th><td>{{ ucfirst($report->status) }}</td></tr>
    </table>

    <h5>Perbandingan Bulan Sebelumnya</h5>
    <table>
        <tr><th>Metrik</th><th>Bulan Ini</th><th>{{ $previous['label'] ?? '-' }}</th></tr>
        <tr><td>Total Pelanggaran</td><td>{{ $totals['violations'] ?? 0 }}</td><td>{{ $previous['violations'] ?? 0 }}</td></tr>
        <tr><td>Total Prestasi</td><td>{{ $totals['achievements'] ?? 0 }}</td><td>{{ $previous['achievements'] ?? 0 }}</td></tr>
        <tr><td>Poin Pelanggaran</td><td>{{ $totals['violation_points'] ?? 0 }}</td><td>{{ $previous['violation_points'] ?? 0 }}</td></tr>
        <tr><td>Poin Prestasi</td><td>{{ $totals['achievement_points'] ?? 0 }}</td><td>{{ $previous['achievement_points'] ?? 0 }}</td></tr>
        <tr><td>Pengurangan Poin</td><td>{{ $totals['reductions'] ?? 0 }}</td><td>{{ $previous['reductions'] ?? 0 }}</td></tr>
    </table>

    <h5>Rekap per Kelas</h5>
    <table>
        <tr><th>Kelas</th><th>Pelanggaran</th><th>Prestasi</th><th>Poin Pelanggaran</th><th>Poin Prestasi</th></tr>
        @foreach($report->snapshot['per_class'] ?? [] as $row)
            <tr><td>{{ $row['class'] }}</td><td>{{ $row['violations'] }}</td><td>{{ $row['achievements'] }}</td><td>{{ $row['violation_points'] }}</td><td>{{ $row['achievement_points'] }}</td></tr>
        @endforeach
    </table>

    <h5>Kategori Pelanggaran Terbanyak</h5>
    <table>
        <tr><th>Kategori</th><th>Jumlah</th></tr>
        @foreach($report->snapshot['top_violation_categories'] ?? [] as $row)
            <tr><td>{{ $row['category'] }}</td><td>{{ $row['total'] }}</td></tr>
        @endforeach
    </table>

    <h5>Prestasi</h5>
    <table>
        <tr><th>Siswa</th><th>Kelas</th><th>Kategori</th><th>Tanggal</th></tr>
        @foreach($report->snapshot['achievements'] ?? [] as $row)
            <tr><td>{{ $row['name'] }}</td><td>{{ $row['class'] }}</td><td>{{ $row['category'] }}</td><td>{{ $row['date'] }}</td></tr>
        @endforeach
    </table>

    <div class="footer">
        Dicetak: {{ now()->format('d M Y H:i') }}<br>
        {{ \App\Models\Setting::getValue('school_name', 'Sekolah') }}
    </div>
</body>
</html>

<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\Export;

use App\Models\MonthlyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportSummarySheet implements Export, FromCollection, WithHeadings, WithTitle {
    public function __construct(private MonthlyReport $report)
    {
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        $t = $this->report->snapshot['totals'] ?? [];
        $p = $this->report->previous_month_totals ?? [];

        return collect([
            ['Total Pelanggaran', $t['violations'] ?? 0, $p['violations'] ?? 0],
            ['Total Prestasi', $t['achievements'] ?? 0, $p['achievements'] ?? 0],
            ['Total Poin Pelanggaran', $t['violation_points'] ?? 0, $p['violation_points'] ?? 0],
            ['Total Poin Prestasi', $t['achievement_points'] ?? 0, $p['achievement_points'] ?? 0],
            ['Total Pengurangan Poin', $t['reductions'] ?? 0, $p['reductions'] ?? 0],
        ]);
    }

    public function headings(): array
    {
        $previousLabel = $this->report->previous_month_totals['label'] ?? 'Bulan Sebelumnya';

        return ['Metrik', 'Bulan Ini', $previousLabel];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }
}

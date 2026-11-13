<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\Export;

use App\Models\MonthlyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportStudentSheet implements Export, FromCollection, WithHeadings, WithTitle {
    public function __construct(private MonthlyReport $report)
    {
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        return collect($this->report->snapshot['per_student'] ?? [])->map(fn ($row) => [
            $row['name'],
            $row['class'],
            $row['violations'],
            $row['achievements'],
            $row['violation_points'],
            $row['achievement_points'],
        ]);
    }

    public function headings(): array
    {
        return ['Nama Siswa', 'Kelas', 'Pelanggaran', 'Prestasi', 'Poin Pelanggaran', 'Poin Prestasi'];
    }

    public function title(): string
    {
        return 'Per Siswa';
    }
}

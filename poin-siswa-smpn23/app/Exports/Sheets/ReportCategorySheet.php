<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\Export;

use App\Models\MonthlyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportCategorySheet implements Export, FromCollection, WithHeadings, WithTitle {
    public function __construct(private MonthlyReport $report)
    {
    }

    public function collection(): \Illuminate\Support\Enumerable
    {
        $categories = collect($this->report->snapshot['top_violation_categories'] ?? [])
            ->map(fn ($row) => [$row['category'], $row['total']]);

        $rows = collect();
        $rows->push(['KATEGORI PELANGGARAN TERBANYAK', 'JUMLAH']);
        foreach ($categories as $category) {
            $rows->push($category);
        }
        $rows->push(['', '']);
        $rows->push(['PRESTASI', 'TINGKAT', 'Tanggal']);
        foreach ($this->report->snapshot['achievements'] ?? [] as $item) {
            $rows->push([$item['category'] ?? '', $item['level'] ?? '', $item['date'] ?? '']);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Kategori', 'Jumlah/Tingkat', 'Tanggal'];
    }

    public function title(): string
    {
        return 'Kategori & Prestasi';
    }
}

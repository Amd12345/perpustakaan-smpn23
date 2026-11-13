<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;

use App\Exports\Sheets\ReportCategorySheet;
use App\Exports\Sheets\ReportClassSheet;
use App\Exports\Sheets\ReportStudentSheet;
use App\Exports\Sheets\ReportSummarySheet;
use App\Models\MonthlyReport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthlyReportExport implements Export, WithMultipleSheets {
    public function __construct(private MonthlyReport $report)
    {
    }

    public function sheets(): array
    {
        return [
            new ReportSummarySheet($this->report),
            new ReportClassSheet($this->report),
            new ReportStudentSheet($this->report),
            new ReportCategorySheet($this->report),
        ];
    }
}

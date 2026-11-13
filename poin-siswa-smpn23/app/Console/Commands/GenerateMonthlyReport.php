<?php

namespace App\Console\Commands;

use App\Services\MonthlyReportService;
use Illuminate\Console\Command;

class GenerateMonthlyReport extends Command
{
    protected $signature = 'report:generate-monthly {year?} {month?}';

    protected $description = 'Buat laporan bulanan untuk tahun dan bulan tertentu';

    public function handle(): int
    {
        $year = (int) ($this->argument('year') ?: now()->year);
        $month = (int) ($this->argument('month') ?: (now()->isLastOfMonth() ? now()->month : now()->subMonth()->month));

        try {
            $report = MonthlyReportService::generate($year, $month);
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Laporan bulanan '.MonthlyReportService::monthName($month).' '.$year.' berhasil dibuat.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyReportExport;
use App\Models\AcademicYear;
use App\Models\MonthlyReport;
use App\Models\MonthlyReportRevision;
use App\Services\ActivityLogService;
use App\Services\MonthlyReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyReportController extends Controller
{
    public function editRevise(MonthlyReport $report)
    {
        return view('monthly_reports.revise', compact('report'));
    }
    public function index(Request $request)
    {
        $year = (int) $request->input('year', now()->year);

        $reports = MonthlyReport::query()
            ->with('academicYear')
            ->where('year', $year)
            ->orderByDesc('month')
            ->paginate(12)
            ->withQueryString();

        return view('monthly_reports.index', [
            'reports' => $reports,
            'year' => $year,
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'availableYears' => \App\Models\MonthlyReport::query()
                ->select('year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year')
                ->merge(range(now()->year - 2, now()->year + 1))
                ->unique()
                ->sortDesc()
                ->values(),
        ]);
    }

    public function show(MonthlyReport $report)
    {
        $report->load(['academicYear', 'generatedBy', 'finalizedBy', 'revisions.user']);

        return view('monthly_reports.show', compact('report'));
    }

    public function downloadPdf(MonthlyReport $report)
    {
        $report->load('academicYear');

        return Pdf::loadView('monthly_reports.pdf', compact('report'))
            ->download('laporan-'.$report->month.'-'.$report->year.'.pdf');
    }

    public function downloadExcel(MonthlyReport $report)
    {
        return Excel::download(
            new MonthlyReportExport($report),
            'laporan-'.$report->month.'-'.$report->year.'.xlsx',
        );
    }

    public function revise(Request $request, MonthlyReport $report)
    {
        $data = $request->validate([
            'change_reason' => ['required', 'string'],
            'total_violations' => ['required', 'integer', 'min:0'],
            'total_achievements' => ['required', 'integer', 'min:0'],
            'total_violation_points' => ['required', 'integer', 'min:0'],
            'total_achievement_points' => ['required', 'integer', 'min:0'],
            'total_reductions' => ['required', 'integer', 'min:0'],
        ]);

        $before = $report->snapshot;
        $after = $before;
        $after['totals'] = [
            'violations' => $data['total_violations'],
            'achievements' => $data['total_achievements'],
            'violation_points' => $data['total_violation_points'],
            'achievement_points' => $data['total_achievement_points'],
            'reductions' => $data['total_reductions'],
        ];

        DB::transaction(function () use ($report, $before, $after, $data) {
            $report->update([
                'status' => 'revisi',
                'snapshot' => $after,
                'total_violations' => $data['total_violations'],
                'total_achievements' => $data['total_achievements'],
                'total_violation_points' => $data['total_violation_points'],
                'total_achievement_points' => $data['total_achievement_points'],
                'total_reductions' => $data['total_reductions'],
            ]);

            MonthlyReportRevision::create([
                'monthly_report_id' => $report->id,
                'user_id' => auth()->id(),
                'change_reason' => $data['change_reason'],
                'before_snapshot' => $before,
                'after_snapshot' => $after,
            ]);

            ActivityLogService::log(
                'revise_monthly_report',
                $report,
                'Revisi laporan '.MonthlyReportService::monthName($report->month).' '.$report->year,
                ['reason' => $data['change_reason']],
            );
        });

        return back()->with('success', 'Laporan direvisi dan riwayat perubahan disimpan.');
    }

    public function finalize(Request $request, MonthlyReport $report)
    {
        $report->update([
            'status' => 'final',
            'finalized_by' => auth()->id(),
            'finalized_at' => now(),
        ]);

        ActivityLogService::log('finalize_monthly_report', $report, 'Finalisasi laporan '.MonthlyReportService::monthName($report->month).' '.$report->year);

        return back()->with('success', 'Laporan difinalkan.');
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        try {
            MonthlyReportService::generate($data['year'], $data['month']);
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('monthly-reports.index', ['year' => $data['year']])
            ->with('success', 'Laporan bulan '.MonthlyReportService::monthName($data['month']).' '.$data['year'].' berhasil dibuat.');
    }
}

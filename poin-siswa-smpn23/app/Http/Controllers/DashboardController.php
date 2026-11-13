<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Achievement;
use App\Models\Kelas;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\Violation;

class DashboardController extends Controller
{
    public function internal()
    {
        $year = AcademicYear::where('is_active', true)->first();
        $yearId = $year?->id;

        $totalStudents = Student::where('status', 'aktif')
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->count();

        $totalViolations = Violation::where('status', 'aktif')
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->count();

        $totalAchievements = Achievement::where('is_public', true)
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->count();

        $violationPoints = (int) PointTransaction::where('balance_type', 'pelanggaran')
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->sum('points');

        $achievementPoints = (int) PointTransaction::where('balance_type', 'prestasi')
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->sum('points');

        $months = $this->lastMonths(12);

        $violationRows = Violation::where('status', 'aktif')
            ->selectRaw("DATE_FORMAT(incident_date, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $achievementRows = Achievement::where('is_public', true)
            ->selectRaw("DATE_FORMAT(achievement_date, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $chart = [
            'labels' => $months->pluck('label')->all(),
            'violations' => $months->map(fn ($month) => (int) ($violationRows[$month['key']] ?? 0))->all(),
            'achievements' => $months->map(fn ($month) => (int) ($achievementRows[$month['key']] ?? 0))->all(),
        ];

        $categoryRows = Violation::where('status', 'aktif')
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->join('violation_categories', 'violations.violation_category_id', '=', 'violation_categories.id')
            ->selectRaw('violation_categories.name as category, COUNT(*) as total')
            ->groupBy('violation_categories.name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $categoryChart = [
            'labels' => $categoryRows->pluck('category')->all(),
            'data' => $categoryRows->pluck('total')->all(),
        ];

        $topAchievers = PointTransaction::where('balance_type', 'prestasi')
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->selectRaw('student_id, SUM(points) as total')
            ->groupBy('student_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'student' => Student::with('class')->find($row->student_id),
                'total' => (int) $row->total,
            ])
            ->filter(fn ($item) => $item['student']);

        $topViolators = PointTransaction::where('balance_type', 'pelanggaran')
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->selectRaw('student_id, SUM(points) as total')
            ->groupBy('student_id')
            ->havingRaw('SUM(points) > 0')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'student' => Student::with('class')->find($row->student_id),
                'total' => (int) $row->total,
            ])
            ->filter(fn ($item) => $item['student']);

        $reductions = PointTransaction::where('balance_type', 'pelanggaran')
            ->whereIn('type', ['pengurangan', 'konversi'])
            ->when($yearId, fn ($query) => $query->where('academic_year_id', $yearId))
            ->with(['student.class', 'source'])
            ->orderByDesc('transaction_date')
            ->limit(10)
            ->get();

        $activityLogs = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('dashboard.internal', compact(
            'year',
            'totalStudents',
            'totalViolations',
            'totalAchievements',
            'violationPoints',
            'achievementPoints',
            'chart',
            'categoryChart',
            'topAchievers',
            'topViolators',
            'reductions',
            'activityLogs',
        ));
    }

    private function lastMonths(int $count): \Illuminate\Support\Collection
    {
        \Carbon\Carbon::setLocale('id');

        $months = collect();

        for ($i = $count - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'key' => $date->format('Y-m'),
                'label' => $date->translatedFormat('M'),
            ]);
        }

        return $months;
    }
}

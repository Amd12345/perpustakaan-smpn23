<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Kelas;
use App\Models\MonthlyReport;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\Violation;
use App\Models\ViolationCategory;
use Illuminate\Support\Facades\DB;

class MonthlyReportService
{
    public static function generate(int $year, int $month, ?int $academicYearId = null): MonthlyReport
    {
        $academicYear = $academicYearId
            ? AcademicYear::find($academicYearId)
            : AcademicYear::where('is_active', true)->first();

        if (! $academicYear) {
            throw new \RuntimeException('Tahun ajaran aktif tidak ditemukan.');
        }

        $snapshot = self::buildSnapshot($year, $month);

        $report = MonthlyReport::updateOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'month' => $month,
                'year' => $year,
            ],
            [
                'status' => 'final',
                'snapshot' => $snapshot,
                'total_violations' => $snapshot['totals']['violations'],
                'total_achievements' => $snapshot['totals']['achievements'],
                'total_violation_points' => $snapshot['totals']['violation_points'],
                'total_achievement_points' => $snapshot['totals']['achievement_points'],
                'total_reductions' => $snapshot['totals']['reductions'],
                'previous_month_totals' => $snapshot['previous_month'],
                'generated_by' => auth()->id(),
                'generated_at' => now(),
            ],
        );

        ActivityLogService::log(
            'generate_monthly_report',
            $report,
            'Laporan bulanan '.self::monthName($month).' '.$year.' dibuat',
        );

        return $report;
    }

    public static function buildSnapshot(int $year, int $month): array
    {
        $violationQuery = Violation::query()
            ->whereYear('incident_date', $year)
            ->whereMonth('incident_date', $month)
            ->where('status', 'aktif');

        $achievementQuery = Achievement::query()
            ->whereYear('achievement_date', $year)
            ->whereMonth('achievement_date', $month);

        $violationsCount = (clone $violationQuery)->count();
        $violationsPoints = (int) (clone $violationQuery)->sum('point_amount');
        $achievementsCount = (clone $achievementQuery)->count();
        $achievementsPoints = (int) (clone $achievementQuery)->sum('point_amount');

        $reductions = (int) PointTransaction::query()
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('balance_type', 'pelanggaran')
            ->whereIn('type', ['pengurangan', 'konversi'])
            ->sum('points');
        $reductions = abs($reductions);

        $perClassViolations = DB::table('violations')
            ->join('students', 'violations.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereYear('violations.incident_date', $year)
            ->whereMonth('violations.incident_date', $month)
            ->where('violations.status', 'aktif')
            ->selectRaw('classes.name as class_name, COUNT(*) as violations, COALESCE(SUM(violations.point_amount),0) as violation_points')
            ->groupBy('classes.name')
            ->get()
            ->keyBy('class_name');

        $perClassAchievements = DB::table('achievements')
            ->join('students', 'achievements.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereYear('achievements.achievement_date', $year)
            ->whereMonth('achievements.achievement_date', $month)
            ->selectRaw('classes.name as class_name, COUNT(*) as achievements, COALESCE(SUM(achievements.point_amount),0) as achievement_points')
            ->groupBy('classes.name')
            ->get()
            ->keyBy('class_name');

        $perClass = collect($perClassViolations->keys()->merge($perClassAchievements->keys())->unique())
            ->map(function ($className) use ($perClassViolations, $perClassAchievements) {
                $v = $perClassViolations->get($className);
                $a = $perClassAchievements->get($className);

                return [
                    'class' => $className,
                    'violations' => (int) ($v->violations ?? 0),
                    'achievements' => (int) ($a->achievements ?? 0),
                    'violation_points' => (int) ($v->violation_points ?? 0),
                    'achievement_points' => (int) ($a->achievement_points ?? 0),
                ];
            })
            ->values()
            ->all();

        $perStudentViolations = DB::table('violations')
            ->join('students', 'violations.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereYear('violations.incident_date', $year)
            ->whereMonth('violations.incident_date', $month)
            ->where('violations.status', 'aktif')
            ->selectRaw('students.id as student_id, students.name as student_name, classes.name as class_name, COUNT(*) as violations, COALESCE(SUM(violations.point_amount),0) as violation_points')
            ->groupBy('students.id', 'students.name', 'classes.name')
            ->get()
            ->keyBy('student_id');

        $perStudentAchievements = DB::table('achievements')
            ->join('students', 'achievements.student_id', '=', 'students.id')
            ->join('classes', 'students.class_id', '=', 'classes.id')
            ->whereYear('achievements.achievement_date', $year)
            ->whereMonth('achievements.achievement_date', $month)
            ->selectRaw('students.id as student_id, students.name as student_name, classes.name as class_name, COUNT(*) as achievements, COALESCE(SUM(achievements.point_amount),0) as achievement_points')
            ->groupBy('students.id', 'students.name', 'classes.name')
            ->get()
            ->keyBy('student_id');

        $perStudent = collect($perStudentViolations->keys()->merge($perStudentAchievements->keys())->unique())
            ->map(function ($studentId) use ($perStudentViolations, $perStudentAchievements) {
                $v = $perStudentViolations->get($studentId);
                $a = $perStudentAchievements->get($studentId);
                $row = $v ?: $a;

                return [
                    'student_id' => (int) $studentId,
                    'name' => $row->student_name,
                    'class' => $row->class_name,
                    'violations' => (int) ($v->violations ?? 0),
                    'achievements' => (int) ($a->achievements ?? 0),
                    'violation_points' => (int) ($v->violation_points ?? 0),
                    'achievement_points' => (int) ($a->achievement_points ?? 0),
                ];
            })
            ->sortByDesc('violation_points')
            ->values()
            ->all();

        $topCategories = Violation::query()
            ->join('violation_categories', 'violations.violation_category_id', '=', 'violation_categories.id')
            ->whereYear('violations.incident_date', $year)
            ->whereMonth('violations.incident_date', $month)
            ->where('violations.status', 'aktif')
            ->selectRaw('violation_categories.name as category, COUNT(*) as total')
            ->groupBy('violation_categories.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->all();

        $achievementList = Achievement::query()
            ->with(['student.class', 'category'])
            ->whereYear('achievement_date', $year)
            ->whereMonth('achievement_date', $month)
            ->latest('achievement_date')
            ->limit(50)
            ->get()
            ->map(fn (Achievement $item) => [
                'name' => $item->student?->name,
                'class' => $item->student?->class?->name,
                'category' => $item->category?->name,
                'level' => $item->category?->level,
                'date' => $item->achievement_date?->format('d M Y'),
            ])
            ->all();

        $reductionList = PointTransaction::query()
            ->with('student.class')
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->where('balance_type', 'pelanggaran')
            ->whereIn('type', ['pengurangan', 'konversi'])
            ->latest('transaction_date')
            ->limit(50)
            ->get()
            ->map(fn (PointTransaction $item) => [
                'name' => $item->student?->name,
                'class' => $item->student?->class?->name,
                'points' => abs($item->points),
                'type' => $item->type,
                'date' => $item->transaction_date?->format('d M Y'),
            ])
            ->all();

        $previousMonth = $month === 1 ? 12 : $month - 1;
        $previousYear = $month === 1 ? $year - 1 : $year;

        $previous = MonthlyReport::where('year', $previousYear)
            ->where('month', $previousMonth)
            ->first();

        if ($previous) {
            $previousTotals = [
                'label' => self::monthName($previousMonth).' '.$previousYear,
                'violations' => $previous->total_violations,
                'achievements' => $previous->total_achievements,
                'violation_points' => $previous->total_violation_points,
                'achievement_points' => $previous->total_achievement_points,
                'reductions' => $previous->total_reductions,
            ];
        } else {
            $prevViolations = (int) Violation::whereYear('incident_date', $previousYear)
                ->whereMonth('incident_date', $previousMonth)
                ->where('status', 'aktif')
                ->count();
            $prevAchievements = (int) Achievement::whereYear('achievement_date', $previousYear)
                ->whereMonth('achievement_date', $previousMonth)
                ->count();

            $previousTotals = [
                'label' => self::monthName($previousMonth).' '.$previousYear,
                'violations' => $prevViolations,
                'achievements' => $prevAchievements,
                'violation_points' => 0,
                'achievement_points' => 0,
                'reductions' => 0,
            ];
        }

        return [
            'totals' => [
                'violations' => $violationsCount,
                'achievements' => $achievementsCount,
                'violation_points' => $violationsPoints,
                'achievement_points' => $achievementsPoints,
                'reductions' => $reductions,
            ],
            'previous_month' => $previousTotals,
            'per_class' => $perClass,
            'per_student' => $perStudent,
            'top_violation_categories' => $topCategories,
            'achievements' => $achievementList,
            'reductions' => $reductionList,
        ];
    }

    public static function monthName(int $month): string
    {
        $names = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $names[$month] ?? '';
    }
}

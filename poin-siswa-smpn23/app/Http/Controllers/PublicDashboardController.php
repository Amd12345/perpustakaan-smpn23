<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Kelas;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Violation;

class PublicDashboardController extends Controller
{
    public function index()
    {
        $year = AcademicYear::where('is_active', true)->first();

        $totalStudents = Student::where('status', 'aktif')
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->count();

        $totalViolations = Violation::where('status', 'aktif')
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->count();

        $totalAchievements = Achievement::where('is_public', true)
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->count();

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

        $classes = Kelas::where('academic_year_id', $year?->id)
            ->withCount([
                'violations' => fn ($query) => $query->where('violations.status', 'aktif'),
                'achievements' => fn ($query) => $query->where('achievements.is_public', true),
            ])
            ->orderBy('name')
            ->get();

        $achievements = Achievement::with(['student.class', 'category'])
            ->where('is_public', true)
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->latest('achievement_date')
            ->paginate(9);

        return view('dashboard.public', [
            'schoolName' => Setting::getValue('school_name', 'Sekolah'),
            'schoolCity' => Setting::getValue('school_city', ''),
            'totalStudents' => $totalStudents,
            'totalViolations' => $totalViolations,
            'totalAchievements' => $totalAchievements,
            'chart' => $chart,
            'classes' => $classes,
            'achievements' => $achievements,
        ]);
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

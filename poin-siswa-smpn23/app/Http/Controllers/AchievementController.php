<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use App\Models\PointRule;
use App\Models\Setting;
use App\Models\Student;
use App\Services\ActivityLogService;
use App\Services\PointService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::where('is_active', true)->first();

        $achievements = Achievement::query()
            ->with(['student.class', 'category', 'teacher'])
            ->when($request->filled('academic_year_id'), fn ($query) => $query->where('academic_year_id', $request->input('academic_year_id')))
            ->when(! $request->filled('academic_year_id') && $year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('student', fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('nisn', 'like', '%'.$request->input('search').'%'));
            })
            ->when($request->filled('category_id'), fn ($query) => $query->where('achievement_category_id', $request->input('category_id')))
            ->latest('achievement_date')
            ->paginate(20)
            ->withQueryString();

        return view('achievements.index', [
            'achievements' => $achievements,
            'year' => $year,
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'categories' => AchievementCategory::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $year = AcademicYear::where('is_active', true)->first();

        $students = Student::query()
            ->where('status', 'aktif')
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->orderBy('name')
            ->get();

        $categories = AchievementCategory::query()
            ->where('is_active', true)
            ->with(['pointRules' => fn ($query) => $query->where('academic_year_id', $year?->id)])
            ->orderBy('name')
            ->get();

        $pointMap = $categories->mapWithKeys(fn ($category) => [
            $category->id => $category->pointRules->first()?->point_value ?? 0,
        ]);

        return view('achievements.create', compact('year', 'students', 'categories', 'pointMap'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'achievement_category_id' => ['required', 'exists:achievement_categories,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'achievement_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'documentation' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ]);

        $rule = PointRule::where('ruleable_type', AchievementCategory::class)
            ->where('ruleable_id', $data['achievement_category_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->first();

        if (! $rule) {
            return back()->withInput()->withErrors([
                'achievement_category_id' => 'Aturan poin untuk kategori ini belum diatur pada tahun ajaran terpilih.',
            ]);
        }

        $achievement = DB::transaction(function () use ($data, $rule, $request) {
            $path = $request->file('documentation')?->store('achievements', 'public');

            $achievement = Achievement::create([
                'student_id' => $data['student_id'],
                'achievement_category_id' => $data['achievement_category_id'],
                'academic_year_id' => $data['academic_year_id'],
                'teacher_id' => auth()->user()->teacher_id,
                'achievement_date' => $data['achievement_date'],
                'description' => $data['description'],
                'documentation_path' => $path,
                'point_amount' => $rule->point_value,
                'is_public' => true,
            ]);

            PointService::record([
                'student_id' => $achievement->student_id,
                'academic_year_id' => $achievement->academic_year_id,
                'type' => 'prestasi',
                'balance_type' => 'prestasi',
                'source_type' => Achievement::class,
                'source_id' => $achievement->id,
                'points' => $rule->point_value,
                'description' => 'Prestasi: '.$achievement->category->name,
            ]);

            if ((bool) Setting::getValue('wa_achievement_enabled', true)) {
                WhatsAppService::queueForStudent(
                    $achievement->student,
                    'prestasi',
                    "Selamat! Putra/i Anda {$achievement->student->name} memperoleh prestasi "
                    .$achievement->category->name." (".$rule->point_value." poin).\n\n"
                    .'- '.Setting::getValue('school_name', 'Sekolah'),
                );
            }

            ActivityLogService::log(
                'create_achievement',
                $achievement,
                'Mencatat prestasi '.$achievement->category->name.' untuk '.$achievement->student->name.' ('.$rule->point_value.' poin)',
            );

            return $achievement;
        });

        return redirect()->route('achievements.show', $achievement)
            ->with('success', 'Prestasi dicatat dan poin diperbarui.');
    }

    public function show(Achievement $achievement)
    {
        $achievement->load(['student.class', 'category', 'teacher']);

        return view('achievements.show', compact('achievement'));
    }

    public function edit(Achievement $achievement)
    {
        $year = AcademicYear::find($achievement->academic_year_id);

        $students = Student::query()
            ->where('status', 'aktif')
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->orderBy('name')
            ->get();

        $categories = AchievementCategory::query()
            ->with(['pointRules' => fn ($query) => $query->where('academic_year_id', $achievement->academic_year_id)])
            ->orderBy('name')
            ->get();

        $pointMap = $categories->mapWithKeys(fn ($category) => [
            $category->id => $category->pointRules->first()?->point_value ?? 0,
        ]);

        return view('achievements.edit', compact('achievement', 'year', 'students', 'categories', 'pointMap'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $data = $request->validate([
            'achievement_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'documentation' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $path = $achievement->documentation_path;

        if ($request->hasFile('documentation')) {
            $path = $request->file('documentation')->store('achievements', 'public');
        }

        $achievement->update([
            'achievement_date' => $data['achievement_date'],
            'description' => $data['description'],
            'documentation_path' => $path,
            'is_public' => $request->boolean('is_public'),
        ]);

        ActivityLogService::log('update_achievement', $achievement, 'Memperbarui laporan prestasi untuk '.$achievement->student->name);

        return redirect()->route('achievements.show', $achievement)
            ->with('success', 'Laporan prestasi diperbarui.');
    }

    public function destroy(Achievement $achievement)
    {
        if (PointService::hasTransaction(Achievement::class, $achievement->id, 'prestasi')) {
            return back()->with('error', 'Prestasi sudah masuk ledger dan tidak dapat dihapus.');
        }

        $achievement->delete();

        ActivityLogService::log('delete_achievement', $achievement, 'Menghapus laporan prestasi');

        return back()->with('success', 'Laporan prestasi dihapus.');
    }
}

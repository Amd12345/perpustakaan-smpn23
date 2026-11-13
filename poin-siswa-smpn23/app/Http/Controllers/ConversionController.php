<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Conversion;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\Student;
use App\Services\ActivityLogService;
use App\Services\PointService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversionController extends Controller
{
    public function index()
    {
        $conversions = Conversion::query()
            ->with(['student.class', 'achievement.category', 'teacher'])
            ->latest()
            ->paginate(20);

        return view('conversions.index', compact('conversions'));
    }

    public function create(Request $request)
    {
        $year = AcademicYear::where('is_active', true)->first();

        $students = Student::query()
            ->where('status', 'aktif')
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->orderBy('name')
            ->get();

        $achievements = collect();

        if ($year) {
            $achievements = Achievement::query()
                ->with('category')
                ->where('academic_year_id', $year->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->map(function (Achievement $achievement) {
                    $converted = PointTransaction::where('source_type', Achievement::class)
                        ->where('source_id', $achievement->id)
                        ->where('type', 'konversi_prestasi')
                        ->sum('points');

                    $achievement->remaining_points = max(0, $achievement->point_amount + (int) $converted);

                    return $achievement;
                })
                ->filter(fn (Achievement $achievement) => $achievement->remaining_points > 0)
                ->values();
        }

        return view('conversions.create', [
            'year' => $year,
            'students' => $students,
            'achievements' => $achievements,
            'achievementOptions' => $achievements->map(fn (Achievement $item) => [
                'id' => $item->id,
                'student_id' => $item->student_id,
                'label' => $item->category?->name.' (sisa '.$item->remaining_points.')',
                'remaining' => $item->remaining_points,
            ])->toJson(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'achievement_id' => ['required', 'exists:achievements,id'],
            'conversion_points' => ['required', 'integer', 'min:1'],
        ]);

        $student = Student::with('class')->findOrFail($data['student_id']);
        $achievement = Achievement::with('category')->findOrFail($data['achievement_id']);

        if ($achievement->student_id != $student->id) {
            return back()->withInput()->withErrors(['achievement_id' => 'Prestasi tidak dimiliki siswa ini.']);
        }

        $year = AcademicYear::find($achievement->academic_year_id);
        $ratio = max(1, (int) Setting::getValue('point_conversion_ratio', 1));

        $converted = (int) PointTransaction::where('source_type', Achievement::class)
            ->where('source_id', $achievement->id)
            ->where('type', 'konversi_prestasi')
            ->sum('points');

        $available = max(0, $achievement->point_amount + $converted);

        if ($data['conversion_points'] > $available) {
            return back()->withInput()->withErrors([
                'conversion_points' => "Poin prestasi yang tersedia hanya {$available}.",
            ]);
        }

        $reduction = $data['conversion_points'] * $ratio;
        $violationBalance = PointService::balance($student->id, $year?->id, 'pelanggaran');

        if ($reduction > $violationBalance) {
            return back()->withInput()->withErrors([
                'conversion_points' => "Pengurangan melebihi saldo poin pelanggaran ({$violationBalance} poin).",
            ]);
        }

        $conversion = DB::transaction(function () use ($student, $achievement, $year, $data, $reduction, $ratio) {
            $conversion = Conversion::create([
                'student_id' => $student->id,
                'achievement_id' => $achievement->id,
                'academic_year_id' => $achievement->academic_year_id,
                'teacher_id' => auth()->user()->teacher_id,
                'conversion_points' => $data['conversion_points'],
                'reduction_points' => $reduction,
                'description' => 'Konversi '.$achievement->category->name.' menjadi pengurangan poin',
            ]);

            PointService::record([
                'student_id' => $student->id,
                'academic_year_id' => $achievement->academic_year_id,
                'type' => 'konversi',
                'balance_type' => 'pelanggaran',
                'source_type' => Conversion::class,
                'source_id' => $conversion->id,
                'points' => -$reduction,
                'description' => 'Konversi poin prestasi: '.$achievement->category->name,
            ]);

            PointService::record([
                'student_id' => $student->id,
                'academic_year_id' => $achievement->academic_year_id,
                'type' => 'konversi_prestasi',
                'balance_type' => 'prestasi',
                'source_type' => Conversion::class,
                'source_id' => $conversion->id,
                'points' => -$data['conversion_points'],
                'description' => 'Poin prestasi dikonversi: '.$achievement->category->name,
            ]);

            WhatsAppService::queueForStudent(
                $student,
                'konversi',
                "Yth. Orang Tua/Wali {$student->name},\n\n"
                ."Sebanyak {$data['conversion_points']} poin prestasi ({$achievement->category->name}) "
                ."telah dikonversi menjadi pengurangan {$reduction} poin pelanggaran.\n\n"
                ."- ".Setting::getValue('school_name', 'Sekolah'),
            );

            ActivityLogService::log(
                'create_conversion',
                $conversion,
                "Mengonversi {$data['conversion_points']} poin prestasi menjadi pengurangan {$reduction} poin",
            );

            return $conversion;
        });

        return redirect()->route('conversions.index')
            ->with('success', 'Konversi poin berhasil dicatat.');
    }
}

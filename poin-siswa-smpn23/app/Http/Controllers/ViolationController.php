<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\PointRule;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Violation;
use App\Models\ViolationCategory;
use App\Services\ActivityLogService;
use App\Services\PointService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::where('is_active', true)->first();

        $violations = Violation::query()
            ->with(['student.class', 'category', 'teacher'])
            ->when($request->filled('academic_year_id'), fn ($query) => $query->where('academic_year_id', $request->input('academic_year_id')))
            ->when(! $request->filled('academic_year_id') && $year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->whereHas('student', fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%')
                    ->orWhere('nisn', 'like', '%'.$request->input('search').'%'));
            })
            ->when($request->filled('category_id'), fn ($query) => $query->where('violation_category_id', $request->input('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest('incident_date')
            ->paginate(20)
            ->withQueryString();

        return view('violations.index', [
            'violations' => $violations,
            'year' => $year,
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'categories' => ViolationCategory::orderBy('name')->get(),
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

        $categories = ViolationCategory::query()
            ->where('is_active', true)
            ->with(['pointRules' => fn ($query) => $query->where('academic_year_id', $year?->id)])
            ->orderBy('name')
            ->get();

        $pointMap = $categories->mapWithKeys(fn ($category) => [
            $category->id => $category->pointRules->first()?->point_value ?? 0,
        ]);

        return view('violations.create', compact('year', 'students', 'categories', 'pointMap'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'violation_category_id' => ['required', 'exists:violation_categories,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'incident_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ]);

        $rule = PointRule::where('ruleable_type', ViolationCategory::class)
            ->where('ruleable_id', $data['violation_category_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->first();

        if (! $rule) {
            return back()->withInput()->withErrors([
                'violation_category_id' => 'Aturan poin untuk kategori ini belum diatur pada tahun ajaran terpilih.',
            ]);
        }

        $violation = DB::transaction(function () use ($data, $rule, $request) {
            $path = $request->file('evidence')?->store('violations', 'public');

            $violation = Violation::create([
                'student_id' => $data['student_id'],
                'violation_category_id' => $data['violation_category_id'],
                'academic_year_id' => $data['academic_year_id'],
                'teacher_id' => auth()->user()->teacher_id,
                'incident_date' => $data['incident_date'],
                'description' => $data['description'],
                'evidence_path' => $path,
                'point_amount' => $rule->point_value,
                'status' => 'aktif',
            ]);

            $transaction = PointService::record([
                'student_id' => $violation->student_id,
                'academic_year_id' => $violation->academic_year_id,
                'type' => 'pelanggaran',
                'balance_type' => 'pelanggaran',
                'source_type' => Violation::class,
                'source_id' => $violation->id,
                'points' => $rule->point_value,
                'description' => 'Pelanggaran: '.$violation->category->name,
            ]);

            WhatsAppService::checkViolationThreshold(
                $violation->student,
                $violation->academic_year_id,
                $transaction->balance_before,
                $transaction->balance_after,
            );

            ActivityLogService::log(
                'create_violation',
                $violation,
                'Mencatat pelanggaran '.$violation->category->name.' untuk '.$violation->student->name.' ('.$rule->point_value.' poin)',
            );

            return $violation;
        });

        return redirect()->route('violations.show', $violation)
            ->with('success', 'Pelanggaran dicatat dan poin diperbarui.');
    }

    public function show(Violation $violation)
    {
        $violation->load(['student.class', 'category', 'teacher', 'improvementTask.submissions']);

        return view('violations.show', compact('violation'));
    }

    public function edit(Violation $violation)
    {
        $year = AcademicYear::find($violation->academic_year_id);

        $students = Student::query()
            ->where('status', 'aktif')
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->orderBy('name')
            ->get();

        $categories = ViolationCategory::query()
            ->with(['pointRules' => fn ($query) => $query->where('academic_year_id', $violation->academic_year_id)])
            ->orderBy('name')
            ->get();

        $pointMap = $categories->mapWithKeys(fn ($category) => [
            $category->id => $category->pointRules->first()?->point_value ?? 0,
        ]);

        return view('violations.edit', compact('violation', 'year', 'students', 'categories', 'pointMap'));
    }

    public function update(Request $request, Violation $violation)
    {
        $data = $request->validate([
            'incident_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ]);

        $path = $violation->evidence_path;

        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('violations', 'public');
        }

        $violation->update([
            'incident_date' => $data['incident_date'],
            'description' => $data['description'],
            'evidence_path' => $path,
        ]);

        ActivityLogService::log('update_violation', $violation, 'Memperbarui laporan pelanggaran untuk '.$violation->student->name);

        return redirect()->route('violations.show', $violation)
            ->with('success', 'Laporan pelanggaran diperbarui.');
    }

    public function cancel(Request $request, Violation $violation)
    {
        if ($violation->status !== 'aktif') {
            return back()->with('error', 'Pelanggaran ini sudah dibatalkan.');
        }

        try {
            DB::transaction(function () use ($violation) {
                $before = PointService::balance($violation->student_id, $violation->academic_year_id, 'pelanggaran');

                if ($before < $violation->point_amount) {
                    throw new \RuntimeException('Saldo poin pelanggaran tidak mencukupi untuk pembatalan.');
                }

                PointService::record([
                    'student_id' => $violation->student_id,
                    'academic_year_id' => $violation->academic_year_id,
                    'type' => 'penyesuaian',
                    'balance_type' => 'pelanggaran',
                    'source_type' => Violation::class,
                    'source_id' => $violation->id,
                    'points' => -$violation->point_amount,
                    'description' => 'Pembatalan pelanggaran ID '.$violation->id,
                ]);

                $violation->update(['status' => 'dibatalkan']);
            });
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }

        ActivityLogService::log('cancel_violation', $violation, 'Membatalkan pelanggaran untuk '.$violation->student->name);

        return back()->with('success', 'Pelanggaran dibatalkan dan poin dikembalikan.');
    }

    public function destroy(Violation $violation)
    {
        if (PointService::hasTransaction(Violation::class, $violation->id, 'pelanggaran')) {
            return back()->with('error', 'Pelanggaran sudah masuk ledger dan tidak dapat dihapus. Gunakan pembatalan.');
        }

        $violation->delete();

        ActivityLogService::log('delete_violation', $violation, 'Menghapus laporan pelanggaran');

        return back()->with('success', 'Laporan pelanggaran dihapus.');
    }
}

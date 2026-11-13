<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Student;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use App\Exports\StudentsExport;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::where('is_active', true)->first();

        $students = Student::query()
            ->with('class', 'academicYear')
            ->withSum(['pointTransactions as violation_points' => function ($query) use ($year) {
                $query->where('balance_type', 'pelanggaran')
                    ->when($year, fn ($q) => $q->where('academic_year_id', $year->id));
            }], 'points')
            ->withSum(['pointTransactions as achievement_points' => function ($query) use ($year) {
                $query->where('balance_type', 'prestasi')
                    ->when($year, fn ($q) => $q->where('academic_year_id', $year->id));
            }], 'points')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->input('search').'%')
                        ->orWhere('nisn', 'like', '%'.$request->input('search').'%');
                });
            })
            ->when($request->filled('class_id'), fn ($query) => $query->where('class_id', $request->input('class_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('academic_year_id'), fn ($query) => $query->where('academic_year_id', $request->input('academic_year_id')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('students.index', [
            'students' => $students,
            'classes' => Kelas::when($year, fn ($query) => $query->where('academic_year_id', $year->id))->orderBy('name')->get(),
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'year' => $year,
        ]);
    }

    public function create()
    {
        $year = $this->activeYear();

        return view('students.create', [
            'year' => $year,
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'classes' => Kelas::where('academic_year_id', $year?->id)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $student = DB::transaction(function () use ($data, $request) {
            $student = Student::create($data);
            $this->syncParents($student, $request->input('parents', []));

            return $student;
        });

        ActivityLogService::log('create_student', $student, 'Menambahkan siswa: '.$student->name);

        return redirect()->route('students.show', $student)
            ->with('success', 'Data siswa berhasil disimpan.');
    }

    public function show(Student $student)
    {
        $year = $this->activeYear();

        $student->load(['class', 'academicYear', 'orangTuas']);

        $violations = $student->violations()
            ->with(['category', 'teacher'])
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->orderByDesc('incident_date')
            ->paginate(10, ['*'], 'violations_page');

        $achievements = $student->achievements()
            ->with(['category', 'teacher'])
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->orderByDesc('achievement_date')
            ->paginate(10, ['*'], 'achievements_page');

        $transactions = $student->pointTransactions()
            ->with('source')
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->orderByDesc('transaction_date')
            ->limit(20)
            ->get();

        return view('students.show', compact('student', 'violations', 'achievements', 'transactions', 'year'));
    }

    public function edit(Student $student)
    {
        $year = $this->activeYear();
        $student->load('orangTuas');

        return view('students.edit', [
            'student' => $student,
            'year' => $year,
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'classes' => Kelas::where('academic_year_id', $student->academic_year_id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $data = $this->validateData($request, $student);

        DB::transaction(function () use ($student, $data, $request) {
            $student->update($data);
            $this->syncParents($student, $request->input('parents', []));
        });

        ActivityLogService::log('update_student', $student, 'Memperbarui data siswa: '.$student->name);

        return redirect()->route('students.show', $student)
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->update(['status' => 'nonaktif']);

        ActivityLogService::log('deactivate_student', $student, 'Menonaktifkan siswa: '.$student->name);

        return back()->with('success', 'Siswa berhasil dinonaktifkan.');
    }

    public function importForm()
    {
        return view('students.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));

            ActivityLogService::log('import_students', null, 'Import data siswa dari Excel');

            return redirect()->route('students.index')->with('success', 'Import data siswa selesai.');
        } catch (\Throwable $exception) {
            return back()->withInput()->withErrors(['file' => $exception->getMessage()]);
        }
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'data-siswa-'.now()->format('Ymd').'.xlsx');
    }

    private function validateData(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'nisn' => ['required', 'string', 'max:30', Rule::unique('students', 'nisn')->ignore($student?->id)],
            'name' => ['required', 'string', 'max:255'],
            'class_id' => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif', 'lulus', 'pindah'])],
            'parents' => ['nullable', 'array'],
            'parents.*.name' => ['nullable', 'string', 'max:255'],
            'parents.*.phone' => ['nullable', 'string', 'max:20'],
            'parents.*.relation' => ['nullable', 'string', 'max:50'],
            'parents.*.is_primary' => ['nullable', 'boolean'],
        ]);
    }

    private function syncParents(Student $student, array $parents): void
    {
        $student->orangTuas()->detach();

        $primarySet = false;

        foreach ($parents as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            $phone = trim((string) ($item['phone'] ?? ''));

            if ($name === '' && $phone === '') {
                continue;
            }

            $normalizedPhone = preg_replace('/\D/', '', $phone);

            $parent = OrangTua::firstOrNew(['phone' => $normalizedPhone ?: $phone]);

            $parent->name = $name ?: $parent->name;
            $parent->phone = $normalizedPhone ?: $phone;

            if (! empty($item['address'])) {
                $parent->address = $item['address'];
            }

            $parent->save();

            $isPrimary = ! $primarySet && ! empty($item['is_primary']);
            $student->orangTuas()->attach($parent->id, [
                'relation' => $item['relation'] ?? 'orang tua',
                'is_primary' => $isPrimary,
            ]);

            if ($isPrimary) {
                $primarySet = true;
            }
        }
    }

    private function activeYear(): ?AcademicYear
    {
        return AcademicYear::where('is_active', true)->first();
    }
}

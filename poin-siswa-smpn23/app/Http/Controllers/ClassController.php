<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Kelas;
use App\Models\Teacher;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::find($request->input('academic_year_id'))
            ?: AcademicYear::where('is_active', true)->first();

        $classes = Kelas::query()
            ->with(['academicYear', 'homeroomTeacher'])
            ->withCount('students')
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->orderBy('name')
            ->get();

        return view('classes.index', [
            'classes' => $classes,
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'year' => $year,
        ]);
    }

    public function create()
    {
        return view('classes.create', [
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'teachers' => Teacher::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $class = Kelas::create($data);

        ActivityLogService::log('create_class', $class, 'Menambahkan kelas: '.$class->name);

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil disimpan.');
    }

    public function edit(Kelas $class)
    {
        return view('classes.edit', [
            'class' => $class,
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'teachers' => Teacher::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Kelas $class)
    {
        $data = $this->validateData($request, $class);

        $class->update($data);

        ActivityLogService::log('update_class', $class, 'Memperbarui kelas: '.$class->name);

        return redirect()->route('classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $class)
    {
        try {
            $class->delete();
            ActivityLogService::log('delete_class', $class, 'Menghapus kelas: '.$class->name);

            return back()->with('success', 'Kelas berhasil dihapus.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data terkait.');
        }
    }

    private function validateData(Request $request, ?Kelas $class = null): array
    {
        return $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('classes', 'name')
                    ->where('academic_year_id', $request->input('academic_year_id'))
                    ->ignore($class?->id),
            ],
            'homeroom_teacher_id' => ['nullable', 'exists:teachers,id'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        return view('academic_years.index', [
            'years' => AcademicYear::orderByDesc('start_year')->get(),
        ]);
    }

    public function create()
    {
        return view('academic_years.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $year = AcademicYear::create($data);

        ActivityLogService::log('create_academic_year', $year, 'Menambahkan tahun ajaran: '.$year->name);

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil disimpan.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('academic_years.edit', ['year' => $academicYear]);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $this->validateData($request, $academicYear);

        $academicYear->update($data);

        ActivityLogService::log('update_academic_year', $academicYear, 'Memperbarui tahun ajaran: '.$academicYear->name);

        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function setActive(AcademicYear $academicYear)
    {
        AcademicYear::query()->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        ActivityLogService::log('activate_academic_year', $academicYear, 'Mengaktifkan tahun ajaran: '.$academicYear->name);

        return back()->with('success', 'Tahun ajaran aktif diubah.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return back()->with('error', 'Tahun ajaran aktif tidak dapat dihapus.');
        }

        try {
            $academicYear->delete();
            ActivityLogService::log('delete_academic_year', $academicYear, 'Menghapus tahun ajaran: '.$academicYear->name);

            return back()->with('success', 'Tahun ajaran berhasil dihapus.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Tahun ajaran tidak dapat dihapus karena masih memiliki data terkait.');
        }
    }

    private function validateData(Request $request, ?AcademicYear $year = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:30', 'unique:academic_years,name,'.($year?->id ?? 'NULL')],
            'start_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'end_year' => ['required', 'integer', 'gte:start_year', 'max:2100'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}

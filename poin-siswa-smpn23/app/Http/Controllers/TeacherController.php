<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::query()
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->input('search').'%')
                        ->orWhere('nip', 'like', '%'.$request->input('search').'%')
                        ->orWhere('email', 'like', '%'.$request->input('search').'%');
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'aktif'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    public function pending()
    {
        $teachers = Teacher::query()
            ->where('is_active', false)
            ->whereHas('user', fn ($query) => $query->where('is_active', false))
            ->with('user')
            ->orderBy('created_at')
            ->paginate(20);

        return view('teachers.pending', compact('teachers'));
    }

    public function create()
    {
        return view('teachers.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $teacher = Teacher::create($data);

        ActivityLogService::log('create_teacher', $teacher, 'Menambahkan data guru: '.$teacher->name);

        return redirect()->route('teachers.index')->with('success', 'Data guru berhasil disimpan.');
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', ['teacher' => $teacher->load('user')]);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $this->validateData($request, $teacher);

        $teacher->update($data);

        ActivityLogService::log('update_teacher', $teacher, 'Memperbarui data guru: '.$teacher->name);

        return redirect()->route('teachers.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function approve(Teacher $teacher)
    {
        $teacher->update(['is_active' => true]);

        if ($teacher->user) {
            $teacher->user->update(['is_active' => true]);
        }

        ActivityLogService::log('approve_teacher', $teacher, 'Menyetujui akun guru: '.$teacher->name);

        return back()->with('success', 'Akun guru disetujui dan diaktifkan.');
    }

    public function toggleActive(Teacher $teacher)
    {
        $teacher->update(['is_active' => ! $teacher->is_active]);

        if ($teacher->user) {
            $teacher->user->update(['is_active' => $teacher->is_active]);
        }

        ActivityLogService::log(
            $teacher->is_active ? 'activate_teacher' : 'deactivate_teacher',
            $teacher,
            ($teacher->is_active ? 'Mengaktifkan' : 'Menonaktifkan').' guru: '.$teacher->name,
        );

        return back()->with('success', 'Status guru diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        try {
            $teacher->delete();
            ActivityLogService::log('delete_teacher', $teacher, 'Menghapus data guru: '.$teacher->name);

            return back()->with('success', 'Data guru berhasil dihapus.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Data guru tidak dapat dihapus karena masih memiliki data terkait.');
        }
    }

    private function validateData(Request $request, ?Teacher $teacher = null): array
    {
        return $request->validate([
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('teachers', 'nip')->ignore($teacher?->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('teachers', 'email')->ignore($teacher?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'address' => ['nullable', 'string'],
        ]);
    }
}

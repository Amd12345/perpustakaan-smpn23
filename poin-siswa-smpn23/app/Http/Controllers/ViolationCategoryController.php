<?php

namespace App\Http\Controllers;

use App\Models\ViolationCategory;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ViolationCategoryController extends Controller
{
    public function index()
    {
        return view('violation_categories.index', [
            'categories' => ViolationCategory::orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('violation_categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $category = ViolationCategory::create($data);

        ActivityLogService::log('create_violation_category', $category, 'Menambahkan kategori pelanggaran: '.$category->name);

        return redirect()->route('violation-categories.index')->with('success', 'Kategori pelanggaran disimpan.');
    }

    public function edit(ViolationCategory $violationCategory)
    {
        return view('violation_categories.edit', ['category' => $violationCategory]);
    }

    public function update(Request $request, ViolationCategory $violationCategory)
    {
        $data = $this->validateData($request, $violationCategory);

        $violationCategory->update($data);

        ActivityLogService::log('update_violation_category', $violationCategory, 'Memperbarui kategori pelanggaran: '.$violationCategory->name);

        return redirect()->route('violation-categories.index')->with('success', 'Kategori pelanggaran diperbarui.');
    }

    public function destroy(ViolationCategory $violationCategory)
    {
        try {
            $violationCategory->delete();
            ActivityLogService::log('delete_violation_category', $violationCategory, 'Menghapus kategori pelanggaran: '.$violationCategory->name);

            return back()->with('success', 'Kategori pelanggaran dihapus.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan.');
        }
    }

    private function validateData(Request $request, ?ViolationCategory $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AchievementCategory;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AchievementCategoryController extends Controller
{
    public function index()
    {
        return view('achievement_categories.index', [
            'categories' => AchievementCategory::orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('achievement_categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $category = AchievementCategory::create($data);

        ActivityLogService::log('create_achievement_category', $category, 'Menambahkan kategori prestasi: '.$category->name);

        return redirect()->route('achievement-categories.index')->with('success', 'Kategori prestasi disimpan.');
    }

    public function edit(AchievementCategory $achievementCategory)
    {
        return view('achievement_categories.edit', ['category' => $achievementCategory]);
    }

    public function update(Request $request, AchievementCategory $achievementCategory)
    {
        $data = $this->validateData($request, $achievementCategory);

        $achievementCategory->update($data);

        ActivityLogService::log('update_achievement_category', $achievementCategory, 'Memperbarui kategori prestasi: '.$achievementCategory->name);

        return redirect()->route('achievement-categories.index')->with('success', 'Kategori prestasi diperbarui.');
    }

    public function destroy(AchievementCategory $achievementCategory)
    {
        try {
            $achievementCategory->delete();
            ActivityLogService::log('delete_achievement_category', $achievementCategory, 'Menghapus kategori prestasi: '.$achievementCategory->name);

            return back()->with('success', 'Kategori prestasi dihapus.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan.');
        }
    }

    private function validateData(Request $request, ?AchievementCategory $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', Rule::in(['kelas', 'sekolah', 'kota', 'provinsi', 'nasional', 'internasional'])],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}

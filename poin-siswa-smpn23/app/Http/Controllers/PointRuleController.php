<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AchievementCategory;
use App\Models\PointRule;
use App\Models\ViolationCategory;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PointRuleController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::find($request->input('academic_year_id'))
            ?: AcademicYear::where('is_active', true)->first();

        $violationCategories = ViolationCategory::query()
            ->with(['pointRules' => fn ($query) => $query->where('academic_year_id', $year?->id)])
            ->orderBy('name')
            ->get();

        $achievementCategories = AchievementCategory::query()
            ->with(['pointRules' => fn ($query) => $query->where('academic_year_id', $year?->id)])
            ->orderBy('name')
            ->get();

        return view('point_rules.index', [
            'years' => AcademicYear::orderByDesc('start_year')->get(),
            'year' => $year,
            'violationCategories' => $violationCategories,
            'achievementCategories' => $achievementCategories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $rule = PointRule::updateOrCreate(
            [
                'ruleable_type' => $data['ruleable_type'],
                'ruleable_id' => $data['ruleable_id'],
                'academic_year_id' => $data['academic_year_id'],
            ],
            ['point_value' => $data['point_value'], 'is_active' => $data['is_active'] ?? true],
        );

        ActivityLogService::log('save_point_rule', $rule, 'Menyimpan aturan poin: '.$rule->point_value);

        return back()->with('success', 'Aturan poin disimpan.');
    }

    public function update(Request $request, PointRule $pointRule)
    {
        $data = $request->validate([
            'point_value' => ['required', 'integer', 'min:0', 'max:10000'],
        ]);

        $oldValue = $pointRule->point_value;
        $pointRule->update($data);

        ActivityLogService::log(
            'update_point_rule',
            $pointRule,
            "Mengubah bobot poin dari {$oldValue} menjadi {$pointRule->point_value}",
            ['before' => $oldValue, 'after' => $pointRule->point_value],
        );

        return back()->with('success', 'Aturan poin diperbarui.');
    }

    public function destroy(PointRule $pointRule)
    {
        try {
            $pointRule->delete();
            ActivityLogService::log('delete_point_rule', $pointRule, 'Menghapus aturan poin');

            return back()->with('success', 'Aturan poin dihapus.');
        } catch (\Throwable $exception) {
            return back()->with('error', 'Aturan poin tidak dapat dihapus.');
        }
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'ruleable_type' => ['required', Rule::in([ViolationCategory::class, AchievementCategory::class])],
            'ruleable_id' => ['required', 'integer'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'point_value' => ['required', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'month',
        'year',
        'status',
        'snapshot',
        'total_violations',
        'total_achievements',
        'total_violation_points',
        'total_achievement_points',
        'total_reductions',
        'previous_month_totals',
        'generated_by',
        'generated_at',
        'finalized_by',
        'finalized_at',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'snapshot' => 'array',
        'previous_month_totals' => 'array',
        'total_violations' => 'integer',
        'total_achievements' => 'integer',
        'total_violation_points' => 'integer',
        'total_achievement_points' => 'integer',
        'total_reductions' => 'integer',
        'generated_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function revisions()
    {
        return $this->hasMany(MonthlyReportRevision::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}

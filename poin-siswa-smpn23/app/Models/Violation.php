<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'violation_category_id',
        'academic_year_id',
        'teacher_id',
        'incident_date',
        'description',
        'evidence_path',
        'point_amount',
        'status',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'point_amount' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function category()
    {
        return $this->belongsTo(ViolationCategory::class, 'violation_category_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function improvementTask()
    {
        return $this->hasOne(ImprovementTask::class);
    }

    public function transactions()
    {
        return $this->morphMany(PointTransaction::class, 'source');
    }
}

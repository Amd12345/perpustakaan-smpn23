<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'achievement_category_id',
        'academic_year_id',
        'teacher_id',
        'achievement_date',
        'description',
        'documentation_path',
        'point_amount',
        'is_public',
    ];

    protected $casts = [
        'achievement_date' => 'date',
        'point_amount' => 'integer',
        'is_public' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function category()
    {
        return $this->belongsTo(AchievementCategory::class, 'achievement_category_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function transactions()
    {
        return $this->morphMany(PointTransaction::class, 'source');
    }
}

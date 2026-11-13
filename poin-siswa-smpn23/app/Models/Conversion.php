<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'achievement_id',
        'academic_year_id',
        'teacher_id',
        'conversion_points',
        'reduction_points',
        'description',
    ];

    protected $casts = [
        'conversion_points' => 'integer',
        'reduction_points' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
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

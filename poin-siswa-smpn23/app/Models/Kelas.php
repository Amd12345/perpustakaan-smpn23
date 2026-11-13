<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['academic_year_id', 'name', 'homeroom_teacher_id'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function violations()
    {
        return $this->hasManyThrough(Violation::class, Student::class, 'class_id', 'student_id');
    }

    public function achievements()
    {
        return $this->hasManyThrough(Achievement::class, Student::class, 'class_id', 'student_id');
    }
}

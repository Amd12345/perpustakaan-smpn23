<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_year', 'end_year', 'is_active'];

    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
        'is_active' => 'boolean',
    ];

    public function classes()
    {
        return $this->hasMany(Kelas::class, 'academic_year_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'academic_year_id');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'academic_year_id');
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class, 'academic_year_id');
    }

    public function pointRules()
    {
        return $this->hasMany(PointRule::class, 'academic_year_id');
    }

    public function monthlyReports()
    {
        return $this->hasMany(MonthlyReport::class, 'academic_year_id');
    }
}

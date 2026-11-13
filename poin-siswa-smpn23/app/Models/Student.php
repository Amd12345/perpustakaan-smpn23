<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'academic_year_id',
        'nisn',
        'name',
        'gender',
        'birth_date',
        'address',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function class()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function orangTuas()
    {
        return $this->belongsToMany(OrangTua::class, 'student_parents', 'student_id', 'parent_id')
            ->withPivot(['relation', 'is_primary'])
            ->withTimestamps();
    }

    public function primaryOrangTua()
    {
        return $this->belongsToMany(OrangTua::class, 'student_parents', 'student_id', 'parent_id')
            ->withPivot(['relation', 'is_primary'])
            ->wherePivot('is_primary', true);
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function notifications()
    {
        return $this->hasMany(WhatsAppNotification::class);
    }

    public function getViolationPointsAttribute(): int
    {
        return (int) $this->pointTransactions()
            ->where('balance_type', 'pelanggaran')
            ->sum('points');
    }

    public function getAchievementPointsAttribute(): int
    {
        return (int) $this->pointTransactions()
            ->where('balance_type', 'prestasi')
            ->sum('points');
    }
}

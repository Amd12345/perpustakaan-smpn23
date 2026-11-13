<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['nip', 'name', 'email', 'phone', 'gender', 'address', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function homeRoomClasses()
    {
        return $this->hasMany(Kelas::class, 'homeroom_teacher_id');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'teacher_id');
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class, 'teacher_id');
    }

    public function improvementTasks()
    {
        return $this->hasMany(ImprovementTask::class, 'teacher_id');
    }

    public function improvementSubmissions()
    {
        return $this->hasMany(ImprovementSubmission::class, 'teacher_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = ['name', 'phone', 'address'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_parents', 'parent_id', 'student_id')
            ->withPivot(['relation', 'is_primary'])
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(WhatsAppNotification::class, 'parent_id');
    }
}

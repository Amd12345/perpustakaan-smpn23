<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImprovementTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'violation_id',
        'teacher_id',
        'description',
        'max_point_reduction',
        'due_date',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'max_point_reduction' => 'integer',
        'due_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function violation()
    {
        return $this->belongsTo(Violation::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function submissions()
    {
        return $this->hasMany(ImprovementSubmission::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImprovementSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'improvement_task_id',
        'teacher_id',
        'evidence_path',
        'note',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(ImprovementTask::class, 'improvement_task_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

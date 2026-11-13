<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'type',
        'balance_type',
        'source_type',
        'source_id',
        'points',
        'balance_before',
        'balance_after',
        'user_id',
        'description',
        'transaction_date',
    ];

    protected $casts = [
        'points' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'transaction_date' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

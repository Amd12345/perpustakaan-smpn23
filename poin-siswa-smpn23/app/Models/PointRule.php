<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointRule extends Model
{
    use HasFactory;

    protected $fillable = ['ruleable_type', 'ruleable_id', 'academic_year_id', 'point_value', 'is_active'];

    protected $casts = [
        'ruleable_id' => 'integer',
        'point_value' => 'integer',
        'is_active' => 'boolean',
    ];

    public function ruleable()
    {
        return $this->morphTo();
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}

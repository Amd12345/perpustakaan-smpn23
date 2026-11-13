<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pointRules()
    {
        return $this->morphMany(PointRule::class, 'ruleable');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }
}

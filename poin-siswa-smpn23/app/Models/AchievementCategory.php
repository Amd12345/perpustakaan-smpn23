<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchievementCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'level', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pointRules()
    {
        return $this->morphMany(PointRule::class, 'ruleable');
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }
}

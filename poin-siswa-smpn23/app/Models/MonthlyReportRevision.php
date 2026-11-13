<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReportRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'monthly_report_id',
        'user_id',
        'change_reason',
        'before_snapshot',
        'after_snapshot',
    ];

    protected $casts = [
        'before_snapshot' => 'array',
        'after_snapshot' => 'array',
    ];

    public function report()
    {
        return $this->belongsTo(MonthlyReport::class, 'monthly_report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

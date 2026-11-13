<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'student_id',
        'parent_id',
        'type',
        'message',
        'recipient_phone',
        'status',
        'sent_at',
        'provider_message_id',
        'error',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function parent()
    {
        return $this->belongsTo(OrangTua::class, 'parent_id');
    }
}

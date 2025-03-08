<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';
    
    protected $fillable = [
        'class_session_id',
        'user_id',
        'clock_in',
        'date',
        'status'
    ];

    protected $casts = [
        'clock_in' => 'datetime:H:i',
        'date' => 'date',
        'status' => 'string'
    ];

    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id', 'class_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function siswa()
    {
        return $this->user->siswa();
    }
}

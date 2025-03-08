<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'guru_id';
    
    protected $fillable = [
        'user_id',
        'nip',
        'subject_id',
        'education',
        'hire_date',
        'gender'
    ];

    protected $casts = [
        'hire_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'guru_id', 'guru_id');
    }
}

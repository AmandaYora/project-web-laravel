<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapel';
    protected $primaryKey = 'mapel_id';
    
    protected $fillable = [
        'day',
        'subject_id',
        'jurusan_id',
        'class_id',
        'start_time',
        'end_time',
        'date'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date' => 'date'
    ];

    protected $dates = [
        'start_time',
        'end_time',
        'date'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'jurusan_id');
    }

    public function class()
    {
        return $this->belongsTo(ClassSiswa::class, 'class_id', 'class_id');
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'mapel_id', 'mapel_id');
    }
}

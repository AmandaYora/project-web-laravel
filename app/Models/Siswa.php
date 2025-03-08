<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'siswa_id';
    
    protected $fillable = [
        'user_id',
        'nis',
        'tahun_masuk',
        'class_id',
        'jurusan_id',
        'gender'
    ];

    protected $casts = [
        'tahun_masuk' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(ClassSiswa::class, 'class_id', 'class_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'jurusan_id');
    }
}

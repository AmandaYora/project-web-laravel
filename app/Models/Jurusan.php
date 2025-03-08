<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table = 'jurusan';
    protected $primaryKey = 'jurusan_id';
    
    protected $fillable = [
        'jurusan',
        'description'
    ];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'jurusan_id', 'jurusan_id');
    }

    public function mapels()
    {
        return $this->hasMany(Mapel::class, 'jurusan_id', 'jurusan_id');
    }
}

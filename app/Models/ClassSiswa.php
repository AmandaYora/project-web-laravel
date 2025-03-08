<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSiswa extends Model
{
    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    
    protected $fillable = [
        'class',
        'description'
    ];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'class_id', 'class_id');
    }

    public function mapels()
    {
        return $this->hasMany(Mapel::class, 'class_id', 'class_id');
    }
}

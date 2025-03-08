<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $primaryKey = 'subject_id';
    
    protected $fillable = [
        'subject',
        'description'
    ];

    public function gurus()
    {
        return $this->hasMany(Guru::class, 'subject_id', 'subject_id');
    }

    public function mapels()
    {
        return $this->hasMany(Mapel::class, 'subject_id', 'subject_id');
    }
}

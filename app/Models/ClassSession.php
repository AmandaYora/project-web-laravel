<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    protected $table = 'class_sessions';
    protected $primaryKey = 'class_session_id';
    
    protected $fillable = [
        'mapel_id',
        'guru_id',
        'barcode',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id', 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'guru_id');
    }
}

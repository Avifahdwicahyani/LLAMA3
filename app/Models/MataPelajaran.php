<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $fillable = [
        'nama_mapel',
        'guru_id'
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

     public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function ujians()
    {
        return $this->hasMany(Ujian::class, 'mapel_id');
    }

    public function kelasMapel()
    {
       return $this->belongsToMany(Kelas::class, 'kelas_mapel', 'mapel_id', 'kelas_id');
    }
}

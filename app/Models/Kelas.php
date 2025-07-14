<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = ['nama_kelas', 'tingkat'];

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function jadwalUjian()
    {
        return $this->belongsToMany(Ujian::class, 'jadwal_ujian_kelas', 'kelas_id', 'ujian_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    protected $fillable = [
        'mapel_id',
        'created_by',
        'jadwal',
        'waktu_selesai',
        'name'
    ];

    protected $dates = ['jadwal', 'waktu_selesai'];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'created_by');
    }

    public function soals()
    {
        return $this->hasMany(Soal::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'jadwal_ujian_kelas', 'ujian_id', 'kelas_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = [
        'ujian_id',
        'pertanyaan',
        'jawaban_benar'
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function jawabanSiswas()
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    public function jawaban()
    {
        return $this->hasOne(JawabanSiswa::class)->where('siswa_id', auth()->user()->siswa->id);
    }

}

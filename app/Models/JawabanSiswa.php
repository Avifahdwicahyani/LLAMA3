<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
     protected $fillable = [
        'siswa_id',
        'soal_id',
        'jawaban_dipilih',
        'waktu_dijawab',
        'nilai_llama3',
        'nilai_similarity',
        'percent_text_similarity'
    ];


    protected $casts = [
        'waktu_dijawab' => 'datetime',
        'nilai_llama3' => 'float',
        'nilai_similarity' => 'float',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }
}
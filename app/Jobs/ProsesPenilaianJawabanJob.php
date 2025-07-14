<?php

namespace App\Jobs;

use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\JawabanSiswa;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProsesPenilaianJawabanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $siswaId;
    protected $soalId;
    protected $ujianId;
    protected $jawabanDipilih;

    public function __construct($siswaId, $soalId, $ujianId, $jawabanDipilih)
    {
        $this->siswaId = $siswaId;
        $this->soalId = $soalId;
        $this->ujianId = $ujianId;
        $this->jawabanDipilih = $jawabanDipilih;
    }

    public function handle()
    {
        $soal = Soal::findOrFail($this->soalId);
        $ujian = Ujian::findOrFail($this->ujianId);

        $totalSoal = Soal::where('ujian_id', $ujian->id)->count();
        $skorPerSoal = $totalSoal > 0 ? round(100 / $totalSoal, 2) : 0;

        $prompt = "Soal Esai: {$soal->pertanyaan}\n"
                ."Jawaban Siswa: {$this->jawabanDipilih}\n"
                ."Berdasarkan soal dan jawaban siswa di atas, berikan **nilai** dari **0 hingga {$skorPerSoal}**. Penilaian harus mempertimbangkan:\n"
                ."- **Kesesuaian** jawaban dengan pertanyaan.\n"
                ."- **Kebenaran** informasi atau konsep yang diberikan.\n"
                ."- **Kelengkapan** argumen atau penjelasan (jika relevan).\n"
                ."- **Kejelasan** dan **koherensi** penulisan.\n.\n"
                ."Jawab hanya dengan angka. Contoh: 4.5";

        $response = Http::timeout(120)->post('http://localhost:11434/api/generate', [
            'model' => 'llama3',
            'prompt' => $prompt,
            'stream' => false,
        ]);

        $output = $response->json('response');
        preg_match('/\d+(\.\d+)?/', $output, $matches);
        $nilai = isset($matches[0]) ? floatval($matches[0]) : 0;

        JawabanSiswa::updateOrCreate(
            [
                'siswa_id' => $this->siswaId,
                'soal_id' => $this->soalId,
            ],
            [
                'jawaban_dipilih' => $this->jawabanDipilih,
                'waktu_dijawab' => now(),
                'nilai_llama3' => $nilai,
                'nilai_similarity' => null
            ]
        );
    }
}
<?php

namespace App\Http\Controllers\Guru;

use App\Exports\HasilUjianExport;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\JawabanSiswa;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class UjianController extends Controller
{
     public function index()
    {
        $guru = Guru::where('user_id', auth()->id())->firstOrFail();

        $ujians = Ujian::with('mataPelajaran')
            ->where('created_by', $guru->id)
            ->get();

        return view('guru.ujian.index', compact('ujians'));
    }

    public function create()
    {
        $guru = Guru::where('user_id', auth()->id())->firstOrFail();
        $mapels = MataPelajaran::all();
        $semuaKelas = Kelas::all();

        return view('guru.ujian.create', compact('mapels', 'semuaKelas'));
    }

   public function store(Request $request)
    {
        $guru = Guru::where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'mapel_id' => 'required|exists:mata_pelajarans,id',
            'jadwal' => 'required|date',
            'waktu_selesai' => 'required|date|after:jadwal',
            'name' => 'required',
            'kelas_id' => 'required',
        ]);

        $ujian = Ujian::create([
            'mapel_id' => $request->mapel_id,
            'jadwal' => $request->jadwal,
            'waktu_selesai' => $request->waktu_selesai,
            'created_by' => $guru->id,
            'name' => $request->name
        ]);

        $ujian->kelas()->attach($request->kelas_id);

        return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dibuat.');
    }

    public function show(Ujian $ujian)
    {
        $ujian->load('soals');
        $hasilujian = UjianSiswa::where('ujian_id', $ujian->id)->get();
        return view('guru.ujian.show', compact('ujian', 'hasilujian'));
    }

    public function edit(Ujian $ujian)
    {
        $guru = Guru::where('user_id', auth()->id())->firstOrFail();
        $mapels = MataPelajaran::where('guru_id', $guru->id)->get();
        $semuaKelas = Kelas::all();
        if ($ujian->created_by != $guru->id) {
            return redirect()->route('guru.ujian.index')->with('error', 'Anda tidak diizinkan mengedit ujian ini.');
        }

        return view('guru.ujian.edit', compact('ujian', 'mapels', 'semuaKelas'));
    }

   public function update(Request $request, Ujian $ujian)
    {
        $guru = Guru::where('user_id', auth()->id())->firstOrFail();

        if ($ujian->created_by != $guru->id) {
            return redirect()->route('guru.ujian.index')->with('error', 'Anda tidak diizinkan mengubah ujian ini.');
        }

        $request->validate([
            'mapel_id' => 'required|exists:mata_pelajarans,id',
            'jadwal' => 'required|date',
            'waktu_selesai' => 'required|date|after:jadwal',
            'name' => 'required',
            'kelas_id' => 'required', 
        ]);

        $ujian->update([
            'mapel_id' => $request->mapel_id,
            'jadwal' => $request->jadwal,
            'waktu_selesai' => $request->waktu_selesai,
            'name' => $request->name
        ]);

        $ujian->kelas()->sync($request->kelas_id);

        return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil diperbarui.');
}

    public function destroy(Ujian $ujian)
    {
        $ujian->delete();
        return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dihapus.');
    }

    public function exportHasilUjian(Request $request, $ujianId)
    {
        $ujian = Ujian::findOrFail($ujianId);
        $hasilujian = UjianSiswa::where('ujian_id', $ujianId)
            ->with('siswa.user')
            ->get();
        $kelasIds = KelasMapel::where('mapel_id', $ujian->mapel_id)
            ->pluck('kelas_id');
        $kelas = Kelas::whereIn('id', $kelasIds)->get();

        $kolomNilai = $request->input('nilai', ['nilai_1', 'nilai_2']);
        $format = $request->input('format', 'pdf');

        if ($format === 'excel') {
            return Excel::download(new HasilUjianExport($ujian, $hasilujian, $kelas, $kolomNilai), 'hasil-ujian-'.$ujian->id.'.xlsx');
        }

        $pdf = Pdf::loadView('guru.ujian.hasilnilai', compact('ujian', 'hasilujian', 'kelas', 'kolomNilai'))
            ->setPaper('A4', 'portrait');
        return $pdf->download('hasil-ujian-'.$ujian->id.'.pdf');
    }

    public function showNilaisiswa($id, $ujianid)
    {
       $ujianSiswa = UjianSiswa::with([
            'siswa.user',
            'jawabanSiswa' => function ($q) use ($ujianid) {
                $q->whereHas('soal', function ($query) use ($ujianid) {
                    $query->where('ujian_id', $ujianid);
                });
            },
            'jawabanSiswa.soal'
        ])
        ->where('siswa_id', $id)
        ->where('ujian_id', $ujianid)
        ->firstOrFail();
        
        return view('guru.ujian.nilaisiswa', compact('ujianSiswa'));
    }

     function normalisasiJawaban($text) {
        return preg_replace("/[^\w\s]/u", '', strtolower(trim($text)));
    }

    function textToVector($text) {
        $words = explode(' ', $text);
        $vector = [];
        foreach ($words as $word) {
            if ($word !== '') {
                if (!isset($vector[$word])) {
                    $vector[$word] = 0;
                }
                $vector[$word]++;
            }
        }
        return $vector;
    }

    function cosineSimilarity($vec1, $vec2) {
        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        $allKeys = array_unique(array_merge(array_keys($vec1), array_keys($vec2)));

        foreach ($allKeys as $key) {
            $a = $vec1[$key] ?? 0;
            $b = $vec2[$key] ?? 0;

            $dotProduct += $a * $b;
            $magnitudeA += $a * $a;
            $magnitudeB += $b * $b;
        }

        if ($magnitudeA == 0 || $magnitudeB == 0) return 0;

        return $dotProduct / (sqrt($magnitudeA) * sqrt($magnitudeB));
    }

    public function koreksiUjianSiswa($ujianId)
    {
        $startTime = microtime(true);
        $siswaIds = JawabanSiswa::with('soal')
            ->whereHas('soal', function ($query) use ($ujianId) {
                $query->where('ujian_id', $ujianId);
            })
            ->pluck('siswa_id')
            ->unique();

        foreach ($siswaIds as $siswaId) {
            $check = UjianSiswa::where([
                'ujian_id' => $ujianId,
                'siswa_id' => $siswaId
            ])->first();

            if (!$check) {
                continue;
            }

            if ($check->status != 'selesai' && $check->nilai_1 !== null && $check->nilai_2 !== null) {
                continue;
            }

            $jawabanSiswa = JawabanSiswa::where('siswa_id', $siswaId)
                ->whereHas('soal', function ($query) use ($ujianId) {
                    $query->where('ujian_id', $ujianId);
                })
                ->with('soal')
                ->get();

            $totalNilaiLlama3 = 0;
            $totalNilaiSimilarity = 0;
            $totalSoal = $jawabanSiswa->count();
            $skorPerSoal = $totalSoal > 0 ? round(100 / $totalSoal, 2) : 0;
            $totalPercentTextSimilarity = 0;
            foreach ($jawabanSiswa as $jawaban) {
                $soal = $jawaban->soal;
                $jawabanSiswaText = $this->normalisasiJawaban($jawaban->jawaban_dipilih);
                $jawabanBenar = $this->normalisasiJawaban($soal->jawaban_benar ?? '');

                // Similarity calculation
                $jawabanSiswaText = $this->normalisasiJawaban($jawaban->jawaban_dipilih);
                $jawabanBenar = $this->normalisasiJawaban($soal->jawaban_benar ?? '');

                $vectorSiswa =  $this->textToVector($jawabanSiswaText);
                $vectorBenar =  $this->textToVector($jawabanBenar);

                $cosine =  $this->cosineSimilarity($vectorSiswa, $vectorBenar);

                $percent_text_similarity = round($cosine * 100, 2);
                $totalPercentTextSimilarity += $percent_text_similarity;
                // Kalikan dengan skor per soal
                $nilaiSimilarity = round($cosine * $skorPerSoal, 2);

                // Prompt generation
                $prompt = "Soal Esai:\n{$soal->pertanyaan}\n\n"
                        . "Jawaban Siswa:\n{$jawaban->jawaban_dipilih}\n\n"
                        . "Petunjuk Penilaian:\n"
                        . "- Nilai diberikan dalam ANGKA DESIMAL, contoh: {$skorPerSoal}.0, 75.0, 0.0.\n"
                        . "- Skor maksimal adalah {$skorPerSoal}.\n"
                        . "- Jika jawaban siswa SEPENUHNYA BENAR secara makna dan isi (meskipun tidak identik secara kata per kata), berikan NILAI PENUH yaitu {$skorPerSoal}.\n"
                        . "- Jika jawaban benar SEBAGIAN, berikan skor yang dikurangi secara proporsional.\n"
                        . "- Jika jawaban SALAH TOTAL atau tidak sesuai, beri nilai 0.\n"
                        . "- Abaikan gaya bahasa, typo, dan urutan kalimat, selama makna dan fakta tetap benar.\n"
                        . "- Fokus hanya pada kebenaran FAKTUAL dan KELENGKAPAN isi jawaban.\n\n"
                        . "Perintah:\n"
                        . "Jawab HANYA dengan ANGKA DESIMAL tanpa penjelasan apa pun.\n\n"
                        . "Skor:";

                try {
                    $response = Http::timeout(120)->post('http://localhost:11434/api/generate', [
                        'model' => 'llama3',
                        'prompt' => $prompt,
                        'stream' => false,
                    ]);
                    $output = $response->json('response');
                    preg_match('/\d+(\.\d+)?/', $output, $matches);
                    $nilaiLlama3 = isset($matches[0]) ? floatval($matches[0]) : 0;
                } catch (\Throwable $e) {
                    $nilaiLlama3 = 0; // Log error for debugging if needed
                }

                $jawaban->update([
                    'nilai_llama3' => round($nilaiLlama3,2),
                    'nilai_similarity' => round($nilaiSimilarity,2),
                    'percent_text_similarity' => round($percent_text_similarity, 2)
                ]);

                $totalNilaiLlama3 += $nilaiLlama3;
                $totalNilaiSimilarity += $nilaiSimilarity;
            }
            $presentaseNilai2 = $totalSoal > 0 ? round($totalPercentTextSimilarity / $totalSoal, 2) : 0;
            $endTime = microtime(true);
            $durasiDetik = round($endTime - $startTime); 
            UjianSiswa::updateOrCreate(
                [
                    'ujian_id' => $ujianId,
                    'siswa_id' => $siswaId
                ],
                [
                    'nilai_1' => round($totalNilaiLlama3,2),
                    'nilai_2' => round($totalNilaiSimilarity,2),
                    'presentase_nilai_2' => $presentaseNilai2,
                    'time_koreksi' => $durasiDetik,
                ]
            );
        }

        return response()->json(['success' => true]);
    }

  public function koreksiUjianSiswaPersiswa($ujianId, $siswaId)
{
    try {
        $startTime = microtime(true);
        $ujianSiswa = UjianSiswa::where([
            'ujian_id' => $ujianId,
            'siswa_id' => $siswaId
        ])->first();

        if (!$ujianSiswa) {
            return response()->json(['success' => false, 'message' => 'Data ujian siswa tidak ditemukan'], 404);
        }

        $jawabanSiswa = JawabanSiswa::with('soal')
            ->where('siswa_id', $siswaId)
            ->whereHas('soal', function ($q) use ($ujianId) {
                $q->where('ujian_id', $ujianId);
            })->get();

        $totalSoal = $jawabanSiswa->count();
        if ($totalSoal === 0) {
            return response()->json(['success' => false, 'message' => 'Tidak ada jawaban ditemukan']);
        }

        $skorPerSoal = round(100 / $totalSoal, 2);
        $totalNilaiLlama3 = 0;
        $totalNilaiSimilarity = 0;

        $totalPercentTextSimilarity = 0;

        foreach ($jawabanSiswa as $jawaban) {
            $soal = $jawaban->soal;

            if (!$soal) {
                Log::warning("Soal tidak ditemukan untuk jawaban ID {$jawaban->id}");
                continue;
            }

            $jawabanSiswaText = $this->normalisasiJawaban($jawaban->jawaban_dipilih);
            $jawabanBenar = $this->normalisasiJawaban($soal->jawaban_benar ?? '');

            // Similarity calculation
            $jawabanSiswaText = $this->normalisasiJawaban($jawaban->jawaban_dipilih);
            $jawabanBenar = $this->normalisasiJawaban($soal->jawaban_benar ?? '');

            $vectorSiswa =  $this->textToVector($jawabanSiswaText);
            $vectorBenar =  $this->textToVector($jawabanBenar);

            $cosine =  $this->cosineSimilarity($vectorSiswa, $vectorBenar);

            $percent_text_similarity = round($cosine * 100, 2);
            // Kalikan dengan skor per soal
            $nilaiSimilarity = round($cosine * $skorPerSoal, 2);
            $totalPercentTextSimilarity += $percent_text_similarity;
            // Prompt generation
            $prompt = "Soal Esai:\n{$soal->pertanyaan}\n\n"
                    . "Jawaban Siswa:\n{$jawaban->jawaban_dipilih}\n\n"
                    . "Petunjuk Penilaian:\n"
                    . "- Nilai diberikan dalam ANGKA DESIMAL, contoh: {$skorPerSoal}.0, 75.0, 0.0.\n"
                    . "- Skor maksimal adalah {$skorPerSoal}.\n"
                    . "- Jika jawaban siswa SEPENUHNYA BENAR secara makna dan isi (meskipun tidak identik secara kata per kata), berikan NILAI PENUH yaitu {$skorPerSoal}.\n"
                    . "- Jika jawaban benar SEBAGIAN, berikan skor yang dikurangi secara proporsional.\n"
                    . "- Jika jawaban SALAH TOTAL atau tidak sesuai, beri nilai 0.\n"
                    . "- Abaikan gaya bahasa, typo, dan urutan kalimat, selama makna dan fakta tetap benar.\n"
                    . "- Fokus hanya pada kebenaran FAKTUAL dan KELENGKAPAN isi jawaban.\n\n"
                    . "Perintah:\n"
                    . "Jawab HANYA dengan ANGKA DESIMAL tanpa penjelasan apa pun.\n\n"
                    . "Skor:";

            // Llama3 API call
            try {
                $response = Http::timeout(120)->post('http://localhost:11434/api/generate', [
                    'model' => 'llama3',
                    'prompt' => $prompt,
                    'stream' => false,
                ]);

                $output = $response->json('response') ?? '';
                $outputLines = explode("\n", trim($output));
                preg_match('/\d+(\.\d+)?/', $outputLines[0] ?? '', $matches);
                $nilaiLlama3 = isset($matches[0]) ? floatval($matches[0]) : 0;
            } catch (\Throwable $e) {
                Log::error("Gagal memanggil Llama3 untuk jawaban ID {$jawaban->id}: {$e->getMessage()}");
                $nilaiLlama3 = 0;
            }

            $nilaiLlama3 = isset($matches[0]) ? floatval($matches[0]) : 0;
            $nilaiLlama3 = min($nilaiLlama3, $skorPerSoal);
            $endTime = microtime(true);
            $durasiDetik = round($endTime - $startTime); 

            $jawaban->update([
                    'nilai_llama3' => round($nilaiLlama3, 2),
                    'nilai_similarity' => round($nilaiSimilarity, 2),
                    'percent_text_similarity' => round($percent_text_similarity, 2),
                    'time_koreksi' => $durasiDetik,
                ]);

            $totalNilaiLlama3 += $nilaiLlama3;
            $totalNilaiSimilarity += $nilaiSimilarity;
        }

        $presentaseNilai2 = $totalSoal > 0 ? round($totalPercentTextSimilarity / $totalSoal, 2) : 0;

        // Update nilai ujian siswa
        $ujianSiswa->update([
            'nilai_1' => round($totalNilaiLlama3,2),
            'nilai_2' => round($totalNilaiSimilarity,2),
            'presentase_nilai_2' => $presentaseNilai2,
        ]);

        return response()->json(['success' => true]);
    } catch (\Throwable $e) {
        Log::error("Terjadi kesalahan saat mengoreksi ujian siswa ID {$siswaId} untuk ujian {$ujianId}: {$e->getMessage()}");
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat memproses data. Silakan coba lagi.',
        ], 500);
    }
}
}

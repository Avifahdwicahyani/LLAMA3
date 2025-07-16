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

    public function koreksiUjianSiswa($ujianId)
    {
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

            foreach ($jawabanSiswa as $jawaban) {
                $soal = $jawaban->soal;
                $jawabanSiswaText = trim(strtolower($jawaban->jawaban_dipilih));
                $jawabanBenar = trim(strtolower($soal->jawaban_benar ?? ''));

                $similarityPercentage = 0;
                if (!empty($jawabanBenar)) {
                    similar_text($jawabanSiswaText, $jawabanBenar, $similarityPercentage);
                }
                $nilaiSimilarity = round(($similarityPercentage / 100) * $skorPerSoal, 2);

                $prompt = "Soal Esai: {$soal->pertanyaan}\n"
                    . "Jawaban Siswa: {$jawaban->jawaban_dipilih}\n"
                    . "Berdasarkan soal dan jawaban siswa di atas, berikan nilai objektif dalam bentuk angka bulat dari 0 sampai {$skorPerSoal}.\n"
                    . "Penilaian WAJIB mengikuti pedoman berikut:\n"
                    . "- Jika jawaban siswa 100% benar dan sesuai dengan jawaban yang benar, maka berikan NILAI PENUH yaitu {$skorPerSoal}.\n"
                    . "- Jika jawaban salah total, beri nilai 0.\n"
                    . "- Jika hanya sebagian benar, nilai harus dikurangi secara proporsional.\n"
                    . "- Fokus hanya pada kebenaran dan kelengkapan isi.\n\n"
                    . "Jawab hanya dengan ANGKA DESIMAL. Contoh: 100.0 atau 70.5 atau 0.0.";

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
                    'nilai_llama3' => $nilaiLlama3,
                    'nilai_similarity' => $nilaiSimilarity,
                ]);

                $totalNilaiLlama3 += $nilaiLlama3;
                $totalNilaiSimilarity += $nilaiSimilarity;
            }

            UjianSiswa::updateOrCreate(
                [
                    'ujian_id' => $ujianId,
                    'siswa_id' => $siswaId
                ],
                [
                    'nilai_1' => $totalNilaiLlama3,
                    'nilai_2' => $totalNilaiSimilarity
                ]
            );
        }

        return response()->json(['success' => true]);
    }


  public function koreksiUjianSiswaPersiswa($ujianId, $siswaId)
    {
        $ujianSiswa = UjianSiswa::where([
            'ujian_id' => $ujianId,
            'siswa_id' => $siswaId
        ])->first();

        $jawabanSiswa = JawabanSiswa::with('soal')
            ->where('siswa_id', $siswaId)
            ->whereHas('soal', function ($q) use ($ujianId) {
                $q->where('ujian_id', $ujianId);
            })->get();

        $totalSoal = $jawabanSiswa->count();
        if ($totalSoal === 0) {
            return response()->json(['success' => false, 'message' => 'Tidak ada jawaban']);
        }

        $skorPerSoal = round(100 / $totalSoal, 2);
        $totalNilaiLlama3 = 0;
        $totalNilaiSimilarity = 0;

        foreach ($jawabanSiswa as $jawaban) {
            $soal = $jawaban->soal;
            $jawabanSiswaText = strtolower(trim($jawaban->jawaban_dipilih));
            $jawabanBenar = strtolower(trim($soal->jawaban_benar ?? ''));

            $similarityPercentage = 0;
            if ($jawabanBenar) {
                similar_text($jawabanSiswaText, $jawabanBenar, $similarityPercentage);
            }
            $nilaiSimilarity = round(($similarityPercentage / 100) * $skorPerSoal, 2);

             $prompt = "Soal Esai: {$soal->pertanyaan}\n"
                    . "Jawaban Siswa: {$jawaban->jawaban_dipilih}\n"
                    . "Berdasarkan soal dan jawaban siswa di atas, berikan nilai objektif dalam bentuk angka bulat dari 0 sampai {$skorPerSoal}.\n"
                    . "Penilaian WAJIB mengikuti pedoman berikut:\n"
                    . "- Jika jawaban siswa 100% benar dan sesuai dengan jawaban yang benar, maka berikan NILAI PENUH yaitu {$skorPerSoal}.\n"
                    . "- Jika jawaban salah total, beri nilai 0.\n"
                    . "- Jika hanya sebagian benar, nilai harus dikurangi secara proporsional.\n"
                    . "- Fokus hanya pada kebenaran dan kelengkapan isi.\n\n"
                    . "Jawab hanya dengan ANGKA DESIMAL. Contoh: 100.0 atau 70.5 atau 0.0.";

            try {
                $response = Http::timeout(30)->post('http://localhost:11434/api/generate', [
                    'model' => 'llama3',
                    'prompt' => $prompt,
                    'stream' => false,
                ]);
                $output = $response->json('response') ?? '';
                preg_match('/\d+(\.\d+)?/', $output, $matches);
                $nilaiLlama3 = isset($matches[0]) ? floatval($matches[0]) : 0;
            } catch (\Throwable $e) {
                $nilaiLlama3 = 0;
            }

            $jawaban->update([
                'nilai_llama3' => $nilaiLlama3,
                'nilai_similarity' => $nilaiSimilarity,
            ]);

            $totalNilaiLlama3 += $nilaiLlama3;
            $totalNilaiSimilarity += $nilaiSimilarity;
        }

        $ujianSiswa->update([
            'nilai_1' => $totalNilaiLlama3,
            'nilai_2' => $totalNilaiSimilarity,
        ]);

        return response()->json(['success' => true]);
    }
}

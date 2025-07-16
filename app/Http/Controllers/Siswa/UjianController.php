<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\JawabanSiswa;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UjianController extends Controller
{
    public function index()
    {
        $siswa = Siswa::where('user_id', auth()->id())->firstOrFail();

        $now = \Carbon\Carbon::now('Asia/Jakarta');

         $ujians = Ujian::with(['mataPelajaran', 'kelas'])
            ->whereHas('kelas', function ($query) use ($siswa) {
                $query->where('kelas_id', $siswa->kelas_id);
            })
        ->orderBy('created_at', 'desc') 
        ->get()
        ->map(function ($ujian) use ($now, $siswa) {
            $check = UjianSiswa::where('ujian_id', $ujian->id)
                ->where('siswa_id', $siswa->id)
                ->first();

            if ($check && $check->status === 'selesai') {
                $ujian->status = 'selesai';
            } else {
                $jadwal = \Carbon\Carbon::parse($ujian->jadwal);
                $waktuSelesai = \Carbon\Carbon::parse($ujian->waktu_selesai);

                if ($now->lt($jadwal)) {
                    $ujian->status = 'incoming';
                } elseif ($now->between($jadwal, $waktuSelesai)) {
                    $ujian->status = 'ongoing';
                } else {
                    $ujian->status = 'ended';
                }
            }

             if ($check) {
                $nilai1 = $check->nilai_1 ?? 0;
                $nilai2 = $check->nilai_2 ?? 0;
                $ujian->nilai1 = $nilai1;
                $ujian->nilai2 = $nilai2;
            } else {
                $ujian->nilai1 = null;
                $ujian->nilai2 = null;
            }

            return $ujian;
        });


        return view('siswa.ujian.index', compact('ujians'));
    }

    public function show($id)
    {
        $ujian = Ujian::with(['soals.jawaban' => function($query) {
            $query->where('siswa_id', auth()->user()->siswa->id);
        }])->findOrFail($id);

        $sessionKey = 'ujian_soal_order_' . $id;

        if (!session()->has($sessionKey)) {
            session([$sessionKey => $ujian->soals->pluck('id')->shuffle()->toArray()]);
        }

        $soalOrder = session($sessionKey);

        $ujian->soals = $ujian->soals->sortBy(function ($soal) use ($soalOrder) {
            return array_search($soal->id, $soalOrder);
        })->values();

        $siswa = Siswa::where('user_id', auth()->id())->firstOrFail();

        UjianSiswa::updateOrCreate(
            [
                'ujian_id' => $id,
                'siswa_id' => auth()->user()->siswa->id
            ],
            [
                'status' => 'mengerjakan'
            ]
        );

        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $startTime = \Carbon\Carbon::parse($ujian->jadwal);
        $endTime = \Carbon\Carbon::parse($ujian->waktu_selesai);

        if ($now->lt($startTime)) {
            return redirect()->route('siswa.ujian.index')->with('error', 'Ujian belum dimulai.');
        }

        if ($now->gt($endTime)) {
            return redirect()->route('siswa.ujian.index')->with('error', 'Waktu ujian sudah berakhir.');
        }

        $jumlahSoal = $ujian->soals->count();
        $nilaiPerSoal = 100 / max($jumlahSoal, 1);

        return view('siswa.ujian.show', compact('ujian', 'siswa', 'nilaiPerSoal', 'endTime'));
    }

   public function simpanJawaban(Request $request)
    {
        $request->validate([
            'soal_id' => 'required|exists:soals,id',
            'ujian_id' => 'required|exists:ujians,id',
            'jawaban_dipilih' => 'required|string',
        ]);

        $siswa = Siswa::where('user_id', auth()->id())->firstOrFail();
        $soal = Soal::findOrFail($request->soal_id);
        $ujian = Ujian::findOrFail($request->ujian_id);


        $checkjawaban = JawabanSiswa::where([
            'siswa_id' => $siswa->id,
            'soal_id' => $soal->id,
        ])->first();

        if (
            $checkjawaban &&
            trim(strtolower($request->jawaban_dipilih)) == trim(strtolower($checkjawaban->jawaban_dipilih))
        ) {
            return response()->json(['success' => true]);
        }
       JawabanSiswa::updateOrCreate(
        [
            'siswa_id' => $siswa->id,
            'soal_id' => $soal->id,
        ],
        [
            'jawaban_dipilih' => $request->jawaban_dipilih,
            'waktu_dijawab' => now(),
        ]
    );

        return response()->json(['success' => true]);
    }

   public function selesaikanUjian(Request $request)
    {
        $request->validate([
            'ujian_id' => 'required|integer'
        ]);

        $siswaId = auth()->user()->siswa->id;
        $ujianId = $request->ujian_id;

        UjianSiswa::updateOrCreate(
            [
                'ujian_id' => $ujianId,
                'siswa_id' => $siswaId
            ],
            [
                'status' => 'selesai'
            ]
        );

        return response()->json(['success' => true]);
    }

}
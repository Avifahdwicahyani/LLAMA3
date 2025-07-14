<?php

namespace App\Http\Controllers\Guru;

use App\Exports\HasilUjianExport;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
}

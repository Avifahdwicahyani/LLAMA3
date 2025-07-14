<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
     public function index()
    {
        $mapels = MataPelajaran::with('guru', 'kelas')->get();
        return view('admin.mata_pelajaran.index', compact('mapels'));
    }

    public function create()
    {
        $gurus = Guru::all();
        $kelas = Kelas::all();
        return view('admin.mata_pelajaran.create', compact('gurus', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string',
            'guru_id' => 'required|exists:gurus,id',
            'kelas_id' => 'required|array',
            'kelas_id.*' => 'exists:kelas,id',
        ]);

        $mapel = MataPelajaran::create([
            'nama_mapel' => $request->nama_mapel,
            'guru_id' => $request->guru_id,
        ]);

        $mapel->kelasMapel()->attach($request->kelas_id);

        return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran ditambahkan');
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        $gurus = Guru::all();
        $kelas = Kelas::all();
        $selectedKelas = $mataPelajaran->kelasMapel->pluck('id')->toArray();

        return view('admin.mata_pelajaran.edit', compact('mataPelajaran', 'gurus', 'kelas', 'selectedKelas'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama_mapel' => 'required|string',
            'guru_id' => 'required|exists:gurus,id',
            'kelas_id' => 'required|array',
            'kelas_id.*' => 'exists:kelas,id',
        ]);

        $mataPelajaran->update([
            'nama_mapel' => $request->nama_mapel,
            'guru_id' => $request->guru_id,
        ]);

        $mataPelajaran->kelasMapel()->sync($request->kelas_id);

        return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran diperbarui');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->kelasMapel()->detach();
        $mataPelajaran->delete();

        return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran dihapus');
    }
}
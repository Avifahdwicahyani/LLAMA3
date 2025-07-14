<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Imports\SiswaImport;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function index()
    {
        $guru = Guru::where('user_id', auth()->user()->id)->first();

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        $mataPelajaran = MataPelajaran::with('kelasMapel.siswas.user')
            ->where('guru_id', $guru->id)
            ->get();

        return view('guru.siswa.index', compact('mataPelajaran'));
    }

    public function create()
    {
         $kelas = Kelas::all();
        return view('guru.siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'nis' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'siswa',
        ]);

        Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $request->kelas_id,
                'nis' => $request->nis,
            ]);

        return redirect()->route('guru.siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $kelas = Kelas::all();
        return view('guru.siswa.edit', compact('user', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable',
            'nis' => 'required'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'role' => 'siswa',
        ]);

          Siswa::updateOrCreate(
                ['user_id' => $user->id],
                ['kelas_id' => $request->kelas_id, 'nis' => $request->nis,]
            );

        return redirect()->route('guru.siswa.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        Siswa::where('user_id', $user->id)->delete();
        Guru::where('user_id', $user->id)->delete();

        $user->delete();

        return redirect()->route('guru.siswa.index')->with('success', 'User berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);

        Excel::import(new SiswaImport, $request->file('file'));

        return redirect()->route('guru.siswa.index')->with('success', 'Data siswa berhasil diimport.');
    }
}

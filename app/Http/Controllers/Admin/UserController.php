<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\UsersImport;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
         $kelas = Kelas::all();
        return view('admin.users.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'role' => 'required|in:admin,guru,siswa',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        if ($user->role === 'siswa') {
            Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $request->kelas_id,
                'nis' => $request->nis,
            ]);
        } elseif ($user->role === 'guru') {
            Guru::create([
                'user_id' => $user->id,
                'nip' => $request->nip,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $kelas = Kelas::all();
        return view('admin.users.edit', compact('user', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable',
            'role' => 'required|in:admin,guru,siswa',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'role' => $request->role,
        ]);

        if ($user->role === 'siswa') {
            Siswa::updateOrCreate(
                ['user_id' => $user->id],
                ['kelas_id' => $request->kelas_id,  'nis' => $request->nis]
            );
            Guru::where('user_id', $user->id)->delete();
        } elseif ($user->role === 'guru') {
            Guru::updateOrCreate(['user_id' => $user->id], [  'nip' => $request->nip]);
            Siswa::where('user_id', $user->id)->delete();
        } else {
            Siswa::where('user_id', $user->id)->delete();
            Guru::where('user_id', $user->id)->delete();
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        Siswa::where('user_id', $user->id)->delete();
        Guru::where('user_id', $user->id)->delete();

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);

        Excel::import(new UsersImport, $request->file('file'));

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diimport.');
    }
}
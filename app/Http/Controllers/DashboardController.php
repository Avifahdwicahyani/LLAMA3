<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $totalUsers = User::count();
            $totalSiswa = Siswa::count();
            $siswaTerdaftar = Siswa::whereNotNull('user_id')->count();
            $totalGuru = Guru::whereNotNull('user_id')->count();
            $totalKelas = Kelas::count();
            $totalMapel = MataPelajaran::count();
            $totalBankSoal = Soal::count();
            $ujianBerlangsung = Ujian::where('jadwal', '<=', now())
                                    ->where('waktu_selesai', '>=', now())->count();
            $ujianAkanDatang = Ujian::where('jadwal', '>', now())->count();
            $totalJadwalUjian = Ujian::count();

            $komposisiPengguna = [
                'admin' => User::where('role', 'admin')->count(),
                'guru' => User::where('role', 'guru')->count(),
                'siswa' => User::where('role', 'siswa')->count(),
            ];

            return view('dashboard.admin', compact(
                'totalUsers', 'totalSiswa', 'siswaTerdaftar', 'totalGuru',
                'totalKelas', 'totalMapel', 'totalBankSoal',
                'ujianBerlangsung', 'ujianAkanDatang', 'totalJadwalUjian',
                'komposisiPengguna'
            ));
        }

        if ($user->role === 'guru') {
            $guru = Guru::where('user_id', $user->id)->first();
            $ujianSaya = Ujian::where('created_by', $guru->id)->count();
             $totalSiswa = Siswa::count();
            return view('dashboard.guru', compact('guru', 'ujianSaya', 'totalSiswa'));
        }

        if ($user->role === 'siswa') {
            $siswa = Siswa::where('user_id', $user->id)->first();
            $ujianMendatang = Ujian::where('jadwal', '>', now())->count();
            return view('dashboard.siswa', compact('siswa', 'ujianMendatang'));
        }

        abort(403);
    }

}
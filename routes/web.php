<?php

use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\SiswaController;
use App\Http\Controllers\Guru\UjianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Guru\SoalController;
use App\Http\Controllers\Siswa\UjianController as SiswaUjianController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/user/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/user/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/user/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('admin/users/import', [UserController::class, 'import'])->name('admin.users.import');
    Route::resource('admin/users', UserController::class)->names('admin.users');

    Route::resource('admin/kelas', KelasController::class)->names('admin.kelas');
    Route::resource('admin/mata-pelajaran', MataPelajaranController::class)->names('admin.mata-pelajaran');
});

Route::middleware(['auth', 'role:guru'])->group(function () {
    Route::post('guru/siswa/import', [SiswaController::class, 'import'])->name('guru.siswa.import');
    Route::resource('guru/siswa', SiswaController::class)->names('guru.siswa');
      Route::get('guru/ujian/koreksi/{ujianid}/{siswaid}', [UjianController::class, 'koreksiUjianSiswaPersiswa'])->name('guru.ujian.show.koreksiUjianSiswaPersiswa');
   Route::get('guru/ujian/koreksi/{ujianid}', [UjianController::class, 'koreksiUjianSiswa'])->name('guru.ujian.show.koreksi');
     Route::get('guru/ujian-nilai-siswa/{id}/{ujianid}', [UjianController::class, 'showNilaisiswa'])->name('guru.ujian.show.nilaisiswa');
    Route::get('guru/ujian/export/{id}', [UjianController::class, 'exportHasilUjian'])->name('guru.ujian.exportHasilUjian');
    Route::resource('guru/ujian', UjianController::class)->names('guru.ujian');
    Route::post('guru/soal/import', [SoalController::class, 'import'])->name('admin.soal.import');
    Route::resource('guru/soal', SoalController::class)->names('guru.soal');
});

Route::middleware(['auth', 'role:siswa'])->group(function () {
   Route::resource('siswa/ujian', SiswaUjianController::class)->names('siswa.ujian');
    Route::post('siswa/ujian/simpan-jawaban', [SiswaUjianController::class, 'simpanJawaban'])->name('siswa.ujian.simpanJawaban');
    Route::post('siswa/ujian/selesai', [SiswaUjianController::class, 'selesaikanUjian'])->name('siswa.ujian.selesai');
});
require __DIR__.'/auth.php';

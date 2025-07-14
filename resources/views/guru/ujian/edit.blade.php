@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card card-body">
    <h4 class="card-title">Edit Jadwal Ujian</h4>

    <form method="POST" action="{{ route('guru.ujian.update', $ujian->id) }}">
        @csrf @method('PUT')
       <div class="mb-3">
            <label for="mapel_id" class="form-label">Mata Pelajaran</label>
            <select name="mapel_id" class="form-control" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach($mapels as $mapel)
                    <option value="{{ $mapel->id }}" {{ $ujian->mapel_id == $mapel->id ? 'selected' : '' }}>
                        {{ $mapel->nama_mapel }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $ujian->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Jadwal Mulai</label>
            <input type="datetime-local" name="jadwal" class="form-control" value="{{ \Carbon\Carbon::parse($ujian->jadwal)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Waktu Selesai</label>
            <input type="datetime-local" name="waktu_selesai" class="form-control" value="{{ \Carbon\Carbon::parse($ujian->waktu_selesai)->format('Y-m-d\TH:i') }}" required>
        </div>

       <div class="mb-3">
    <label class="form-label">Pilih Kelas</label>
    @foreach ($semuaKelas as $kelas)
        <div class="form-check">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="kelas_id[]" 
                id="kelas_{{ $kelas->id }}" 
                value="{{ $kelas->id }}"
                {{ $ujian->kelas->contains($kelas->id) ? 'checked' : '' }}> 
            <label class="form-check-label" for="kelas_{{ $kelas->id }}">
                {{ $kelas->nama_kelas }}
            </label>
        </div>
    @endforeach
</div>


         <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
</div>
@endsection

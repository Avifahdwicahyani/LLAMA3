@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
   <h4 class="card-title">Tambah Mata Pelajaran</h4>

   <form method="POST" action="{{ route('admin.mata-pelajaran.store') }}">
        @csrf
      <div class="mb-2">
            <label>Nama Mapel</label>
            <input type="text" name="nama_mapel" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Guru</label>
            <select name="guru_id" class="form-control" required>
                @foreach($gurus as $guru)
                    <option value="{{ $guru->id }}">{{ $guru->user->name }}</option>
                @endforeach
            </select>
        </div>

        @php
            $kelasGrouped = $kelas->groupBy('tingkat');
        @endphp

        <div class="mb-3">
            <label for="kelas_id">Pilih Kelas</label>

            @foreach($kelasGrouped as $tingkat => $groupedKelas)
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <div class="w-100 fw-bold mb-1">Tingkat {{ $tingkat }}</div>
                    @foreach($groupedKelas as $k)
                        <div class="form-check me-2" style="margin-right: 10px;">
                            <input class="form-check-input" type="checkbox" name="kelas_id[]" id="kelas_{{ $k->id }}" value="{{ $k->id }}"
                                {{ in_array($k->id, old('kelas_id', $selectedKelas ?? [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="kelas_{{ $k->id }}">
                                {{ $k->nama_kelas }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
   </div>
@endsection

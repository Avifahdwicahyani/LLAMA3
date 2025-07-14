@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
          <div class="d-flex justify-content-between">
              <h4 class="card-title">Data Siswa</h4>
              <div>
                    <a href="{{ route('guru.siswa.create') }}" class="btn btn-primary mb-3">Tambah Siswa</a>
                     <button class="btn btn-success mb-3" data-toggle="modal" data-target="#importModal">
                Import Siswa
              </button>
              </div>
          </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered zero-configuration">
            <thead>
                <tr>
                    <th>Nama</th>
                     <th>NIS</th>
                    <th>Kelas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($mataPelajaran->count())
                    @foreach($mataPelajaran as $mapel)
                        @foreach($mapel->kelasMapel as $kelas)
                            @foreach($kelas->siswas as $item)
                                <tr>
                                    <td>{{ $item->user?->name }}</td>
                                       <td>{{ $item->nis }}</td>
                                    <td>{{ $kelas->nama_kelas }}</td>
                                    <td>
                                        <a href="{{ route('guru.siswa.edit', $item->user?->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                        <form action="{{ route('guru.siswa.destroy', $item->user?->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-nama="{{ $item->user?->name }}">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
   </div>

   <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('guru.siswa.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Siswa</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File (xlsx/csv)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
                    </div>
                    <div class="alert alert-info">
                        <strong>Format file (header):</strong><br>
                        <code>name, email, password, kelas_id, nis</code><br>
                        <small class="mb-2"><em>Kolom <code>password</code> boleh dikosongkan jika tidak ingin mengubah password lama.</em></small> <br/>
                        <span>Contoh File Excel Download <a href="{{ asset('contoh_siswa.csv')}}">Disini</a> </span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

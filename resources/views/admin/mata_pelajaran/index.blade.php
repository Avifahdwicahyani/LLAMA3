@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
          <div class="d-flex justify-content-between">
              <h4 class="card-title">Data Mata Pelajaran</h4>
              <a href="{{ route('admin.mata-pelajaran.create') }}" class="btn btn-primary mb-3">Tambah Mata Pelajaran</a>
          </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered zero-configuration">
            <thead>
            <tr>
                <th>Nama Mapel</th>
                <th>Guru</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($mapels as $item)
            <tr>
                <td>{{ $item->nama_mapel }}</td>
                <td>{{ $item->guru->user->name ?? '-' }}</td>
                <td>
                    @if ($item->kelasMapel->isNotEmpty())
                        {{ $item->kelasMapel->pluck('nama_kelas')->join(', ') }}
                    @else
                        -
                    @endif
                </td>
               <td>
                    <a href="{{ route('admin.mata-pelajaran.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('admin.mata-pelajaran.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-nama="{{ $item->nama_mapel }}">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
        </table>
    </div>
</div>
   </div>
@endsection

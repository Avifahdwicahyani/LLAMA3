@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
          <div class="d-flex justify-content-between">
              <h4 class="card-title">Data Kelas</h4>
              <a href="{{ route('admin.kelas.create') }}" class="btn btn-primary mb-3">Tambah Kelas</a>
          </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered zero-configuration">
            <thead>
                <tr>
                    <th>Id Kelas</th>
            <th>Nama Kelas</th>
            <th>Aksi</th>
        </tr>
            </thead>
            <tbody>
               @foreach($kelas as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->nama_kelas }}</td>
           <td>
    <a href="{{ route('admin.kelas.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>

    <form action="{{ route('admin.kelas.destroy', $item->id) }}" method="POST" class="d-inline form-delete">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-sm btn-danger btn-delete" data-nama="{{ $item->nama_kelas }}">
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

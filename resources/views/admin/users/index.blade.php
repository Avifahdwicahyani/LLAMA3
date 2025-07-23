@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
          <div class="d-flex justify-content-between">
              <h4 class="card-title">Data User</h4>
             <div>
                 <a href="{{ route('s.create') }}" class="btn btn-primary mb-3">Tambah User</a>
              <button class="btn btn-success mb-3" data-toggle="modal" data-target="#importModal">
                Import User
              </button>
             </div>
          </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered zero-configuration">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                   <td>
    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>

    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline form-delete">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-sm btn-danger btn-delete" data-nama="{{ $user->name }}">
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


     <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data User</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                                                        </button>
                    </div>
                                                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File (xlsx/csv)</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
                        </div>
                        <div class="alert alert-info">
                            <span class="mb-2">Format: name, email, password, role, kelas_id, nis, nip</span> </br>
                              <span>Contoh File Excel Download <a href="{{ asset('contoh_users.csv')}}">Disini</a> </span>
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

@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
   <h4 class="card-title">Tambah Kelas</h4>

   <form method="POST" action="{{ route('admin.kelas.store') }}">
        @csrf
      <div class="mb-3">
          <label>Nama Kelas:</label>
        <input type="text" name="nama_kelas" class="form-control" required>
      </div>
        <div class="mb-3">
          <label>Tingkat:</label>
        <input type="number" name="tingkat" class="form-control" required>
      </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
   </div>
@endsection

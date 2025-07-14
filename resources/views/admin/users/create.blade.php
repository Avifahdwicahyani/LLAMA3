@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
   <h4 class="card-title">Tambah User</h4>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" id="roleSelect" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
                <option value="siswa">Siswa</option>
            </select>
        </div>

         <div class="mb-3">
            <label>NIP</label>
            <input type="number" name="nip" id="nip" class="form-control" style="display: none;">
        </div>

         <div class="mb-3">
            <label>NIS</label>
            <input type="number" name="nis" id="nis" class="form-control" style="display: none;">
        </div>

        <div class="mb-3" id="kelasGroup" style="display: none;">
            <label>Kelas</label>
            <select name="kelas_id" class="form-control">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
   </div>

<script>
    document.getElementById('roleSelect').addEventListener('change', function () {
        let role = this.value;
        document.getElementById('kelasGroup').style.display = (role === 'siswa') ? 'block' : 'none';
         document.getElementById('nis').style.display = (role === 'siswa') ? 'block' : 'none';
          document.getElementById('nip').style.display = (role === 'guru') ? 'block' : 'none';
    });
</script>
@endsection

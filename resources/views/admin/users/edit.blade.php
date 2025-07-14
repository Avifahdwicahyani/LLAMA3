@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card card-body">
    <h4 class="card-title">Edit User</h4>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>

        <div class="mb-3">
            <label>Password (biarkan kosong jika tidak diubah)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" id="roleSelect" required>
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ $user->role == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="siswa" {{ $user->role == 'siswa' ? 'selected' : '' }}>Siswa</option>
            </select>
        </div>

          <div class="mb-3" style="display: {{ $user->role == 'guru' ? 'block' : 'none' }};">
            <label>NIP</label>
            <input type="number" name="nip" id="nip" value="{{ $user->guru?->nip }}"  class="form-control">
        </div>

         <div class="mb-3" style="display: {{ $user->role == 'siswa' ? 'block' : 'none' }};">
            <label>NIS</label>
            <input type="number" name="nis" id="nis" value="{{ $user->siswa?->nis }}"  class="form-control">
        </div>

        <div class="mb-3" id="kelasGroup" style="display: {{ $user->role == 'siswa' ? 'block' : 'none' }};">
            <label>Kelas</label>
            <select name="kelas_id" class="form-control">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}"
                        @if(optional($user->siswa)->kelas_id == $k->id) selected @endif>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
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

@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <div class="card card-body">
    <h4 class="card-title">Edit Siswa</h4>

    <form action="{{ route('guru.siswa.update', $user->id) }}" method="POST">
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
            <label>NIS</label>
            <input type="number" name="nis" value="{{ $user->siswa?->nis }}" class="form-control">
        </div>

         <div class="mb-3" id="kelasGroup">
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
@endsection

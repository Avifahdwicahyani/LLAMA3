@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
          <div class="d-flex justify-content-between">
              <h4 class="card-title">Data Jadwal Ujian</h4>
              <a href="{{ route('guru.ujian.create') }}" class="btn btn-primary mb-3">Tambah Jadwal Ujian</a>
          </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered zero-configuration">
           <thead>
            <tr>
                <th>ID</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Nama Ujian</th>
                <th>Jadwal</th>
                <th>Waktu Selesai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ujians as $ujian)
                <tr>
                    <td>{{ $ujian->id }}</td>
                    <td>{{ $ujian->mataPelajaran->nama_mapel ?? '-' }}</td>
                    <td>{{ $ujian->kelas->pluck('nama_kelas')->implode(', ') }}</td>
                    <td>{{ $ujian->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($ujian->jadwal)->format('d F Y H:i:s') }}</td>
                    <td>{{ \Carbon\Carbon::parse($ujian->waktu_selesai)->format('d F Y H:i:s') }}</td>
                    <td>
                        <a href="{{ route('guru.ujian.show', $ujian->id) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('guru.ujian.edit', $ujian->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <a href="#" class="btn btn-sm btn-success" id="koreksiUjian" data-id="{{ $ujian->id }}">Koreksi Ujian</a>
                        <form action="{{ route('guru.ujian.destroy', $ujian->id) }}" method="POST" class="d-inline form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-nama="{{ $ujian->mataPelajaran->nama_mapel }}">
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

   <script>
document.getElementById('koreksiUjian').addEventListener('click', function(event) {
    event.preventDefault(); // Mencegah default action
    const ujianId = this.getAttribute('data-id');

    Swal.fire({
        title: 'Loading...',
        text: 'Sedang memproses, harap tunggu...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const url = "{{ route('guru.ujian.show.koreksi', ':id') }}".replace(':id', ujianId);

    fetch(url)
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan!'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan!'
            });
        });
});
</script>
@endsection

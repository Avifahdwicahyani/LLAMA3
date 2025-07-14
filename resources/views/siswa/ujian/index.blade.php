@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">Daftar Ujian</h5>
            <div id="current-time" class="text-white"></div>
        </div>
        <div class="card-body">
            @if($ujians->isEmpty())
                <div class="alert alert-info">Belum ada ujian yang tersedia untuk kelas Anda.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-bordered zero-configuration">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu Selesai</th>
                                <th>Nama Ujian</th>
                                <th>Nilai 1</th>
                                <th>Nilai 2</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ujians as $index => $ujian)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $ujian->mataPelajaran->nama_mapel ?? '-' }}</td>
                                    <td>{{ $ujian->Guru->user->name ?? 'Tidak diketahui' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ujian->jadwal)->translatedFormat('l, d M Y H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ujian->waktu_selesai)->translatedFormat('l, d M Y H:i') }}</td>
                                      <td>{{ $ujian->name }}</td>
                                     <td>{{ $ujian->nilai1 ?? 0 }}</td>
                                      <td>{{ $ujian->nilai2 ?? 0 }}</td>
                                      <td>
                                        @if($ujian->status == 'incoming')
                                            <span class="badge bg-warning text-dark">Coming Soon</span>
                                        @elseif($ujian->status == 'ongoing')
                                            <span class="badge bg-info text-white">Sedang Berlangsung</span>
                                        @elseif($ujian->status == 'selesai')
                                            <span class="badge bg-success text-white">Selesai</span>
                                          @else
                                            <span class="badge bg-danger text-white">Waktu Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ujian->status == 'ongoing')
                                            <a href="{{ route('siswa.ujian.show', $ujian->id) }}" class="btn btn-sm btn-info">Mengerjakan</a>
                                        @else
                                            <a href="#" class="btn btn-sm btn-secondary disabled" onclick="return false;" style="pointer-events: none;">Mengerjakan</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();

        const hari = now.toLocaleDateString('id-ID', { weekday: 'long' });
        const tanggal = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        document.getElementById('current-time').textContent = `${hari}, ${tanggal} - ${jam}`;
    }

    setInterval(updateTime, 1000);
    updateTime();
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Mata Pelajaran : {{ $ujian->mataPelajaran->nama_mapel ?? '-' }}</h5>
            <p class="card-text mb-0">Jadwal : {{ \Carbon\Carbon::parse($ujian->jadwal)->format('d F Y H:i:s') }}</p>
            <p class="card-text mt-0">Waktu Selesai : {{ \Carbon\Carbon::parse($ujian->waktu_selesai)->format('d F Y H:i:s') }}</p>
        </div>
    </div>

    <div class="card card-body">
<ul class="nav nav-tabs mb-3" id="ujianTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="soal-tab" data-toggle="tab" href="#soal" role="tab" aria-controls="soal" aria-selected="true">Daftar Soal</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="false">Hasil Ujian</a>
        </li>
    </ul>


    <div class="tab-content" id="ujianTabContent">
        <!-- Tab Soal -->
        <div class="tab-pane fade show active" id="soal" role="tabpanel" aria-labelledby="soal-tab">
          
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">Data Soal</h4>
                            <div>
                                 <a href="{{ route('guru.soal.create') }}" class="btn btn-primary mb-3">Tambah Soal</a>
                                <button class="btn btn-success mb-3" data-toggle="modal" data-target="#importModal">
                                    Import Soal
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                             <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Pertanyaan</th>
                                        <th>Jawaban Benar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                      @if ($ujian->soals->count() > 0)
                                    @foreach ($ujian->soals as $soal)
                                        <tr>
                                            <td>{{ $soal->id }}</td>
                                            <td>{{ $soal->pertanyaan }}</td>
                                            <td>{{ $soal->jawaban_benar }}</td>
                                             <td>
                                                <a href="{{ route('guru.soal.edit', $soal->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                                <form action="{{ route('guru.soal.destroy', $soal->id) }}" method="POST" class="d-inline form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-nama="{{ $soal->pertanyaan }}">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @else
               <tr>
    <td colspan="4" class="text-center">
        Tidak ada soal untuk ujian ini.
    </td>
</tr>
            @endif
                                </tbody>
                            </table>
                        </div>
            
        </div>

        <!-- Tab Info -->
        <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">Hasil Ujian Siswa</h4>
                   <!-- Tombol Export yang memunculkan modal -->
                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#exportModal">
                        Export Hasil Ujian
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form id="exportForm" method="GET">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title" id="exportModalLabel">Pilih Nilai untuk Export</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                            <p>Centang nilai yang ingin dimasukkan ke hasil export:</p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="nilai[]" value="nilai_1" id="nilai1" checked>
                                <label class="form-check-label" for="nilai1">
                                Nilai dari LLaMA3
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="nilai[]" value="nilai_2" id="nilai2" checked>
                                <label class="form-check-label" for="nilai2">
                                Nilai dari Text Similarity
                                </label>
                            </div>
                            <hr>
                            <p>Pilih format file:</p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" value="pdf" id="formatPdf" checked>
                                <label class="form-check-label" for="formatPdf">
                                PDF
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" value="excel" id="formatExcel">
                                <label class="form-check-label" for="formatExcel">
                                Excel
                                </label>
                            </div>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Download</button>
                            </div>
                        </div>
                        </form>
                    </div>
                    </div>

                </div>
                  <div class="table-responsive">
                             <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Siswa</th>
                                          <th>Nis</th>
                                        <th>Status</th>
                                        <th>Nilai 1</th>
                                        <th>Nilai 2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hasilujian as $key => $siswas)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $siswas->siswa?->user?->name }}</td>
                                              <td>{{ $siswas->siswa?->nis }}</td>
                                            <td>
                                                 @if($siswas->status == 'mengerjakan')
                                                    <span class="badge bg-warning text-dark">Sedang Mengerjakan</span>
                                                @else
                                                    <span class="badge bg-success text-white">Selesai</span>
                                                @endif
                                            </td>
                                            <td>{{ $siswas->nilai_1 ?? 0 }}</td>
                                            <td>{{ $siswas->nilai_2 ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

        </div>
    </div>
 </div>

    <div class="text-right mt-4">
        <a href="{{ route('guru.ujian.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.soal.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Soal</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File (xlsx/csv)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
                    </div>
                    <div class="alert alert-info">
                       <span class="mb-2"> Format: ujian id, pertanyaan, jawaban benar</span><br/>
                        <span>Contoh File Excel Download <a href="{{ asset('contoh_soal.csv')}}">Disini</a> </span>
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

<script>
document.getElementById('exportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const params = new URLSearchParams(new FormData(form)).toString();
    window.location.href = "{{ route('guru.ujian.exportHasilUjian', $ujian->id) }}" + '?' + params;
});
</script>
@endsection

@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
          <div class="d-flex justify-content-between">
              <h4 class="card-title">Data Ujian Siswa - {{$ujianSiswa->siswa?->user?->name}}</h4>
          </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered zero-configuration">
           <thead>
                <tr>
                    <th>No</th>
                    <th>Soal</th>
                    <th>Jawaban Guru</th>
                    <th>Jawaban Dipilih</th>
                    <th>Nilai LLAMA3</th>
                    <th>Nilai Similarity</th>
                    <th>Presentase Similarity</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ujianSiswa->jawabanSiswa as $jawaban)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{!! $jawaban->soal->pertanyaan ?? '-' !!}</td>
                     <td>{{ $jawaban->soal->jawaban_benar ?? '-' }}</td>
                    <td>{{ $jawaban->jawaban_dipilih ?? '-' }}</td>
                    <td>{{ $jawaban->nilai_llama3 ?? '0' }}</td>
                    <td>{{ $jawaban->nilai_similarity ?? '0' }}</td>
                    <td>{{ $jawaban->percent_text_similarity ?? '0' }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada jawaban untuk ujian ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
   </div>
@endsection

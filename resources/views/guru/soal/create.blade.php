@extends('layouts.app')

@section('content')
   <div class="container-fluid mt-3">
    <div class="card card-body">
   <h4 class="card-title">Tambah Soal Ujian</h4>

   <form method="POST" action="{{ route('guru.soal.store') }}">
        @csrf
       <div class="mb-3">
                <label for="ujian_id" class="form-label">Ujian</label>
                <select class="form-control" id="ujian_id" name="ujian_id" required>
                    @foreach ($ujians as $ujian)
                        <option value="{{ $ujian->id }}">{{ $ujian->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="pertanyaan" class="form-label">Pertanyaan</label>
                <textarea class="form-control summernote" id="pertanyaan" name="pertanyaan" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="jawaban_benar" class="form-label">Jawaban Benar</label>
                <textarea class="form-control" id="jawaban_benar" name="jawaban_benar" rows="3"></textarea>
            </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
   </div>
@endsection

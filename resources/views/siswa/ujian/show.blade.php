@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12 col-md-8">
   <div class="card shadow">
        <div class="card-header bg-primary text-white">
           <div class="d-flex justify-content-between">
                <h5 class="text-white">{{ $ujian->name }}</h5>
                <div id="timer" class="float-end text-white fw-bold"></div>
           </div>
        </div>
        <div class="card-body">
            <form id="soal-form">
                <input type="hidden" name="ujian_id" value="{{ $ujian->id }}">
                <div id="soal-container">
                    @foreach($ujian->soals as $index => $soal)
                        <div class="soal-item" data-soal="{{ $soal->id }}" data-index="{{ $index }}" style="display: {{ $index == 0 ? 'block' : 'none' }};">
                            <p><strong>Soal {{ $index + 1 }}</strong> <br/> {!! $soal->pertanyaan !!}</p>
                            <div class="mb-3">
                                <label for="jawaban_{{ $soal->id }}" class="form-label">Jawaban Anda:</label>
                                <textarea
                                    class="form-control"
                                    id="jawaban_{{ $soal->id }}"
                                    rows="5"
                                >{{ $soal->jawaban->jawaban_dipilih ?? '' }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="prevSoal()">Previous</button>
                    <button type="button" id="next-btn" class="btn btn-secondary" onclick="nextSoal()">Next</button>
                </div>
            </form>
        </div>
    </div>
        </div>
        <div class="col-12 col-md-4">
             <div class="card shadow">
        <div class="card-header bg-primary text-white">
           <div class="d-flex justify-content-between">
                <h5 class="text-white">LIST SOAL</h5>
           </div>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-2" id="nomor-soal-nav">
                    @foreach($ujian->soals as $index => $soal)
                        @php
                            $jawabanAda = !empty($soal->jawaban->jawaban_dipilih);
                        @endphp
                     <button
                        type="button"
                        class="btn nomor-soal-btn {{ $jawabanAda ? 'btn-secondary' : 'btn-outline-secondary' }}"
                        onclick="goToSoal({{ $index }})"
                        id="nomor-btn-{{ $soal->id }}"
                        style="width: 40px; height: 40px; margin: 5px; display: flex; align-items: center; justify-content: center;"
                    >
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </div>


    </div>

</div>

<script>
     const ujianId = {{ $ujian->id }};
    let currentIndex = 0;
    const soalItems = document.querySelectorAll('.soal-item');
    const nextBtn = document.getElementById('next-btn');

    function showSoal(index) {
        soalItems.forEach((el, i) => el.style.display = i === index ? 'block' : 'none');

        if (index === soalItems.length - 1) {
            nextBtn.innerText = "Selesaikan";
        } else {
            nextBtn.innerText = "Next";
        }
    }

    function getCurrentSoalId() {
        const currentSoal = document.querySelector(`.soal-item[data-index="${currentIndex}"]`);
        if (!currentSoal) return null;

        const soalId = currentSoal.getAttribute('data-soal');
        return soalId;
    }

    function goToSoal(index) {
        currentIndex = index;
        showSoal(index);
    }

    function nextSoal() {
        const soalId = getCurrentSoalId();
        submitJawaban(soalId);

        if (currentIndex < soalItems.length - 1) {
            currentIndex++;
            showSoal(currentIndex);
        } else {
            Swal.fire({
                title: 'Selesaikan Ujian?',
                text: "Anda yakin ingin menyelesaikan ujian ini sekarang?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
               if (result.isConfirmed) {
                    fetch("{{ route('siswa.ujian.selesai') }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ujian_id: ujianId })
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ujian Selesai',
                                text: 'Jawaban Anda telah disimpan.',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            setTimeout(() => {
                                window.location.href = "{{ route('siswa.ujian.index') }}";
                            }, 2000);
                        }
                    });
                }
            });
        }
    }

    function prevSoal() {
        if (currentIndex > 0) {
            const soalId = getCurrentSoalId();
            submitJawaban(soalId);

            currentIndex--;
            showSoal(currentIndex);
        }
    }

    function submitJawaban(soalId) {
        const textarea = document.getElementById(`jawaban_${soalId}`);
        const jawaban = textarea.value;

        fetch("{{ route('siswa.ujian.simpanJawaban') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                soal_id: soalId,
                ujian_id: "{{ $ujian->id }}",
                jawaban_dipilih: jawaban
            })
        }).then(res => res.json())
       .then(data => {
        const btn = document.getElementById(`nomor-btn-${soalId}`);
        if (jawaban.trim() !== "") {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        } else {
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-outline-secondary');
        }
    });
    }

    const endTime = new Date("{{ $endTime->format('Y-m-d H:i:s') }}").getTime();
    const timerEl = document.getElementById("timer");

    const countdown = setInterval(() => {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance <= 0) {
            clearInterval(countdown);
            Swal.fire({
                icon: 'warning',
                title: 'Waktu Habis!',
                text: 'Ujian diselesaikan otomatis.',
                showConfirmButton: false,
                timer: 2000
            });

            fetch("{{ route('siswa.ujian.selesai') }}", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ujian_id: ujianId })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    setTimeout(() => {
                        window.location.href = "{{ route('siswa.ujian.index') }}";
                    }, 2000);
                }
            });
        } else {
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            const formatTime = (time) => String(time).padStart(2, '0');

            timerEl.innerText = `Sisa waktu: ${formatTime(hours)}:${formatTime(minutes)}:${formatTime(seconds)}`;
        }
    }, 1000);
</script>

@endsection

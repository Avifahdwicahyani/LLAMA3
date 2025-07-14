@extends('layouts.app')

@section('content')
<style>
    .dashboard-box {
        border-radius: 5px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
        color: #fff;
        overflow: hidden;
        position: relative;
    }

    .dashboard-box:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
    }

    .dashboard-box .icon {
        font-size: 2.5rem;
        opacity: 0.3;
        position: absolute;
        top: 1rem;
        right: 1rem;
    }

    .dashboard-box .content {
        position: relative;
        z-index: 2;
    }

    .dashboard-box .title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .dashboard-box .value {
        font-size: 1.75rem;
        font-weight: bold;
    }

    @media (max-width: 767px) {
        .dashboard-box {
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .dashboard-box .icon {
            font-size: 2rem;
        }

        .dashboard-box .value {
            font-size: 1.5rem;
        }
    }
</style>

<div class="container-fluid mt-4">
   <div class="row">
    <x-dashboard-box title="Total Pengguna" :value="$totalUsers" icon="bi-people" color="primary" />
    <x-dashboard-box title="Total Siswa" :value="$totalSiswa" icon="bi-person-lines-fill" color="success" />
    <x-dashboard-box title="Siswa Terdaftar" :value="$siswaTerdaftar" icon="bi-person-check-fill" color="info" />
    <x-dashboard-box title="Total Pengajar" :value="$totalGuru" icon="bi-person-badge-fill" color="warning" />
    <x-dashboard-box title="Total Kelas" :value="$totalKelas" icon="bi-building" color="secondary" />
    <x-dashboard-box title="Total Mapel" :value="$totalMapel" icon="bi-book-half" color="danger" />
    <x-dashboard-box title="Total Bank Soal" :value="$totalBankSoal" icon="bi-journal-text" color="dark" />
    <x-dashboard-box title="Ujian Berlangsung" :value="$ujianBerlangsung" icon="bi-clock-history" color="primary" />
    <x-dashboard-box title="Ujian Akan Datang" :value="$ujianAkanDatang" icon="bi-calendar-event" color="info" />
    <x-dashboard-box title="Total Jadwal Ujian" :value="$totalJadwalUjian" icon="bi-calendar-check" color="success" />
</div>


  <div class="row">
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">

            <h5>Komposisi Pengguna</h5>
        <div class="mt-2">
            <div style="width: 200px; height: 200px; margin: auto;">
                <canvas id="komposisiChart"></canvas>
            </div>
        </div>
            </div>
    </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('komposisiChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Admin', 'Guru', 'Siswa'],
            datasets: [{
                label: 'Komposisi Pengguna',
                data: [{{ $komposisiPengguna['admin'] }}, {{ $komposisiPengguna['guru'] }}, {{ $komposisiPengguna['siswa'] }}],
                backgroundColor: ['#007bff', '#28a745', '#ffc107'],
            }]
        },
         options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endsection

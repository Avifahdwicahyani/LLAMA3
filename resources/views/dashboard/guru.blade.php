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
    <h4>Selamat datang, {{ auth()->user()->name }}!</h4>
    <p>Anda login sebagai <strong>Guru</strong>.</p>

    <div class="row">
         <x-dashboard-box title="Total Ujian yang Anda Buat" :value="$ujianSaya" icon="bi-clock-history" color="primary" />
         <x-dashboard-box title="Total Siswa" :value="$totalSiswa" icon="bi-person-lines-fill" color="success" />
    </div>
</div>
@endsection

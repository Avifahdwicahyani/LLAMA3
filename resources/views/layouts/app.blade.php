<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>SMAN 4 PAMEKASAN - SISTEM UJIAN ONLINE</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <!-- Pignose Calender -->
    <link href="{{ asset('plugins/pg-calendar/css/pignose.calendar.min.css') }}" rel="stylesheet">
    <!-- Chartist -->
    <link rel="stylesheet" href="{{ asset('plugins/chartist/css/chartist.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/chartist-plugin-tooltips/css/chartist-plugin-tooltip.css') }}">
    <!-- Custom Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
       <link href="{{ asset('plugins/tables/css/datatable/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

       @yield('css')
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header" style="background-color: #003692;">
            <div class="brand-logo">
                <a href="{{ url('/dashboard')}}">
                    <b class="logo-abbr"><img src="{{ asset('images/logologin.png') }}" alt=""> </b>
                    <span class="logo-compact"><img src="{{ asset('images/logologin.png') }}" alt=""></span>
                    <span class="brand-title ">
                        <img src="{{ asset('images/logologin.png') }}" alt="" style="width: 200px;">
                    </span>
                </a>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content clearfix">

                <div class="nav-control">
                    <div class="hamburger">
                        <span class="toggle-icon"><i class="icon-menu"></i></span>
                    </div>
                </div>

                <div class="header-right">
                    <ul class="clearfix">
                       <li class="icons dropdown">
                            <div class="user-img c-pointer position-relative"   data-toggle="dropdown">
                                <span class="activity active"></span>
                                <img src="{{ auth()->user()->profile_photo_path ? asset(auth()->user()->profile_photo_path) : 'images/user/1.png' }}" height="40" width="40" alt="">
                            </div>
                            <div class="drop-down dropdown-profile animated fadeIn dropdown-menu">
                                <div class="dropdown-content-body">
                                    <ul>
                                        <li>
                                            <a href="{{ route('profile.edit') }}"><i class="icon-user"></i> <span>Profile</span></a>
                                        </li>
                                        <hr class="my-2">
                                      <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" style="background: none; border: none; padding: 0; color: inherit; cursor: pointer;">
                                                    <i class="icon-key"></i> <span>Logout</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
       @include('layouts.sidebar')
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="min-height: 100vh;">
            @yield('content')
        </div>
        <!--**********************************
            Content body end
        ***********************************-->


        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>SMAN 4 PAMEKASAN - SISTEM UJIAN ONLINE</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <script src="{{ asset('plugins/common/common.min.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/gleek.js') }}"></script>
    <script src="{{ asset('js/styleSwitcher.js') }}"></script>

    <!-- Chartjs -->
    <script src="{{ asset('plugins/chart.js/Chart.bundle.min.js') }}"></script>
    <!-- Circle progress -->
    <script src="{{ asset('plugins/circle-progress/circle-progress.min.js') }}"></script>
    <!-- Datamap -->
    <script src="{{ asset('plugins/d3v3/index.js') }}"></script>
    <script src="{{ asset('plugins/topojson/topojson.min.js') }}"></script>
    <!-- Morrisjs -->
    <script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('plugins/morris/morris.min.js') }}"></script>
    <!-- Pignose Calender -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/pg-calendar/js/pignose.calendar.min.js') }}"></script>
    <!-- ChartistJS -->
    <script src="{{ asset('plugins/chartist/js/chartist.min.js') }}"></script>
    <script src="{{ asset('plugins/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js') }}"></script>


    <script src="{{ asset('plugins/tables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/tables/js/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/tables/js/datatable-init/datatable-basic.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <link href="{{ asset('plugins/summernote/dist/summernote.css') }}" rel="stylesheet">
     <script src="{{ asset('plugins/summernote/dist/summernote.min.js') }}"></script>
    <script src="{{ asset('plugins/summernote/dist/summernote-init.js') }}"></script>
    @yield('js')

    <script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            const nama = this.getAttribute('data-nama');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `Kelas "${nama}" akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error("{{ $error }}", "Error", {
                closeButton: true,
                progressBar: true,
                timeOut: 5000
            });
        @endforeach
    @endif

    @if (session('success'))
        toastr.success("{{ session('success') }}", "Success", {
            closeButton: true,
            progressBar: true,
            timeOut: 5000
        });
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}", "Error", {
            closeButton: true,
            progressBar: true,
            timeOut: 5000
        });
    @endif

    document.querySelectorAll('.btn-koreksi').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); 
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: 'Ujian siswa telah dinilai.',
                        }).then(() => {
                            location.reload();
                        });
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
    });

    document.querySelectorAll('.btn-koreksi-siswa').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); 
            const ujianId = this.getAttribute('data-id');
             const siswaId = this.getAttribute('data-siswa');

            Swal.fire({
                title: 'Loading...',
                text: 'Sedang memproses, harap tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const url = "{{ route('guru.ujian.show.koreksiUjianSiswaPersiswa', [':ujianid', ':siswaid']) }}"
            .replace(':ujianid', ujianId)
            .replace(':siswaid', siswaId);
            fetch(url)
                .then(response => {
                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: 'Ujian siswa telah dinilai.',
                        }).then(() => {
                            location.reload();
                        });
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
    });
</script>
</body>

</html>

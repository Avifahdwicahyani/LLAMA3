 <div class="nk-sidebar">
            <div class="nk-nav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label">Dashboard</li>
                    <li>
                         <a href="{{ url('/dashboard') }}" aria-expanded="false">
                            <i class="icon-home menu-icon"></i><span class="nav-text">Dashboard</span>
                        </a>
                    </li>

                    @if(auth()->user()->role == 'admin')
                    <li class="nav-label">Admin</li>

                    <li>
                        <a href="{{ route('admin.users.index') }}" aria-expanded="false">
                            <i class="icon-people menu-icon"></i> <span class="nav-text">User</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.kelas.index') }}" aria-expanded="false">
                            <i class="icon-graduation menu-icon"></i> <span class="nav-text">Kelas</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.mata-pelajaran.index') }}" aria-expanded="false">
                            <i class="icon-notebook menu-icon"></i> <span class="nav-text">Mata Pelajaran</span>
                        </a>
                    </li>

                    @elseif(auth()->user()->role == 'guru')
                    <li class="nav-label">Guru</li>

                    <li>
                        <a href="{{ route('guru.siswa.index') }}" aria-expanded="false">
                            <i class="icon-graduation menu-icon"></i> <span class="nav-text">Siswa</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('guru.ujian.index') }}" aria-expanded="false">
                            <i class="fa fa-calendar-alt menu-icon"></i> <span class="nav-text">Jadwal Ujian</span>
                        </a>
                    </li>

                    {{-- <li>
                        <a href="{{ route('guru.soal.index') }}" aria-expanded="false">
                            <i class="icon-notebook menu-icon"></i> <span class="nav-text">Soal Ujian</span>
                        </a>
                    </li> --}}
                    @else
                    <li class="nav-label">Siswa</li>
                    <li>
                        <a href="{{ route('siswa.ujian.index') }}" aria-expanded="false">
                            <i class="fa fa-calendar-alt menu-icon"></i> <span class="nav-text">Jadwal Ujian</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

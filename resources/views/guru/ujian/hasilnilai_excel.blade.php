<table>
    <tr>
        <td><b>Tanggal</b></td>
        <td>: {{ \Carbon\Carbon::parse($ujian->tanggal)->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td><b>Mata Pelajaran</b></td>
        <td>: {{ $ujian->mataPelajaran->nama_mapel ?? '-' }}</td>
    </tr>
    <tr>
        <td><b>Kelas</b></td>
        <td>: {{ $ujian->kelas->pluck('nama_kelas')->implode(', ') }}</td>
    </tr>
    <tr>
        <td><b>Pengajar</b></td>
        <td>: {{ $ujian->guru->user->name ?? '-' }}</td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Siswa</th>
            <th>NIS</th>
            @if(in_array('nilai_1', $kolomNilai))
                <th>Nilai LLAMA3</th>
            @endif
            @if(in_array('nilai_2', $kolomNilai))
                <th>Nilai Text Similarity</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($hasilujian->groupBy(fn($item) => $item->siswa?->kelas?->nama_kelas) as $namaKelas => $siswaGroup)
            @foreach ($siswaGroup as $key => $siswas)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $siswas->siswa?->user?->name }}</td>
                    <td>{{ $siswas->siswa?->nis }}</td>
                    @if(in_array('nilai_1', $kolomNilai))
                        <td>{{ $siswas->nilai_1 ?? 0 }}</td>
                    @endif
                    @if(in_array('nilai_2', $kolomNilai))
                        <td>{{ $siswas->nilai_2 ?? 0 }}</td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
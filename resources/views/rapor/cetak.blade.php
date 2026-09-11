<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rapor - {{ $siswa->nama_lengkap }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2,
        .header h3 {
            margin: 0;
            padding: 2px;
        }

        .identitas {
            width: 100%;
            margin-bottom: 20px;
        }

        .identitas td {
            padding: 3px 0;
        }

        .table-nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table-nilai th,
        .table-nilai td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .table-nilai th {
            background-color: #f2f2f2;
        }

        .text-left {
            text-align: left !important;
        }

        .ttd {
            width: 100%;
            margin-top: 50px;
        }

        .ttd td {
            width: 50%;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>LAPORAN HASIL BELAJAR SISWA</h2>
        <h3>SMK NEGERI 1 SIMPANG EMPAT</h3>
    </div>

    <table class="identitas">
        <tr>
            <td width="20%">Nama Siswa</td>
            <td width="2%">:</td>
            <td width="48%"><strong>{{ $siswa->nama_lengkap }}</strong></td>
            <td width="15%">Kelas</td>
            <td width="2%">:</td>
            <td width="13%">{{ $siswa->rombel->nama_rombel ?? '-' }}</td>
        </tr>
        <tr>
            <td>NISN / NIS</td>
            <td>:</td>
            <td>{{ $siswa->nisn }} / {{ $siswa->nis ?? '-' }}</td>
            <td>Semester</td>
            <td>:</td>
            <td>{{ $siswa->rombel->tahunAjaran->semester ?? '-' }}</td>
        </tr>
        <tr>
            <td>Kompetensi Keahlian</td>
            <td>:</td>
            <td>{{ $siswa->jurusan->nama_jurusan ?? '-' }}</td>
            <td>Tahun Ajaran</td>
            <td>:</td>
            <td>{{ $siswa->rombel->tahunAjaran->tahun ?? '-' }}</td>
        </tr>
    </table>

    <table class="table-nilai">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Mata Pelajaran</th>
                <th width="15%">Nilai Akhir</th>
                <th width="35%">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswa->nilais as $index => $nilai)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $nilai->mataPelajaran->nama_mapel ?? '-' }}</td>
                <td><strong>{{ $nilai->nilai_akhir }}</strong></td>
                <td>
                    @if($nilai->nilai_akhir >= 90) A (Sangat Baik)
                    @elseif($nilai->nilai_akhir >= 80) B (Baik)
                    @elseif($nilai->nilai_akhir >= 70) C (Cukup)
                    @else D (Kurang)
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td>
                Mengetahui,<br>
                Orang Tua/Wali
                <br><br><br><br><br>
                ( ..................................... )
            </td>
            <td>
                Banjarmasin, {{ date('d F Y') }}<br>
                Wali Kelas
                <br><br><br><br><br>
                <strong>{{ $siswa->rombel->waliKelas->nama_lengkap ?? '.....................................' }}</strong>
            </td>
        </tr>
    </table>

</body>

</html>
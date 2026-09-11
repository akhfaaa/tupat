<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Absensi;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiSiswaController extends Controller
{
    public function index()
    {
        // Cari profil siswa berdasarkan user_id
        $siswa = Siswa::where('user_id', Auth::id())->firstOrFail();

        // Ambil riwayat absensi beserta relasi jurnal dan mata pelajaran
        $riwayatAbsensi = Absensi::with(['jurnal.mataPelajaran', 'jurnal.guru'])
            ->where('siswa_id', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung statistik kehadiran
        $statistik = [
            'hadir' => $riwayatAbsensi->where('status', 'Hadir')->count(),
            'sakit' => $riwayatAbsensi->where('status', 'Sakit')->count(),
            'izin' => $riwayatAbsensi->where('status', 'Izin')->count(),
            'alpa' => $riwayatAbsensi->where('status', 'Alpa')->count(),
            'total' => $riwayatAbsensi->count(),
        ];

        return Inertia::render('Siswa/Absensi/Index', [
            'riwayat' => $riwayatAbsensi,
            'statistik' => $statistik,
        ]);
    }
}

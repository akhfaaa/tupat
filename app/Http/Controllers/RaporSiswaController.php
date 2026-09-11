<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan ini

class RaporSiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::with(['rombel.tahunAjaran', 'rombel.waliKelas', 'jurusan', 'nilais.mataPelajaran'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$siswa) {
            abort(403, 'Profil siswa tidak ditemukan.');
        }

        return Inertia::render('Siswa/Rapor/Index', ['siswa' => $siswa]);
    }

    // Fungsi baru untuk PDF
    public function cetakPdf()
    {
        $siswa = Siswa::with(['rombel.tahunAjaran', 'rombel.waliKelas', 'jurusan', 'nilais.mataPelajaran'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Mengarahkan data ke file Blade (bukan Vue, karena PDF butuh HTML murni)
        $pdf = Pdf::loadView('rapor.cetak', compact('siswa'));

        // Atur ukuran kertas ke A4 (Portrait)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Rapor_' . $siswa->nisn . '_' . $siswa->nama_lengkap . '.pdf');
    }
}

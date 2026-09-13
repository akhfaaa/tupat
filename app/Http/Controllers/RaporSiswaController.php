<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan ini
use Illuminate\Support\Facades\DB;

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

        $components = DB::table('report_components')
            ->join('mata_pelajarans', 'mata_pelajarans.id', '=', 'report_components.mata_pelajaran_id')
            ->where('report_components.siswa_id', $siswa->id)
            ->where('report_components.tahun_ajaran_id', $siswa->rombel?->tahun_ajaran_id)
            ->select('report_components.*', 'mata_pelajarans.nama_mapel', 'mata_pelajarans.kelompok')
            ->orderBy('mata_pelajarans.nama_mapel')
            ->get()
            ->groupBy('mata_pelajaran_id');

        return Inertia::render('Siswa/Rapor/Index', [
            'siswa' => $siswa,
            'reportComponents' => $components,
        ]);
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

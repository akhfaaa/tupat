<?php

namespace App\Http\Controllers;

use App\Models\Rombel;
use App\Models\MataPelajaran;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function index()
    {
        // Mengambil daftar kelas dan mata pelajaran aktif
        return Inertia::render('Guru/Penilaian/Index', [
            'rombels' => Rombel::with('tahunAjaran')->orderBy('nama_rombel', 'asc')->get(),
            'mapels' => MataPelajaran::orderBy('nama_mapel', 'asc')->get(),
        ]);
    }
}

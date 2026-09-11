<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaporSiswaController extends Controller
{
    public function index()
    {
        // Cari data profil siswa yang terhubung dengan akun user saat ini
        $siswa = Siswa::with(['rombel.tahunAjaran', 'rombel.waliKelas', 'jurusan', 'nilais.mataPelajaran'])
            ->where('user_id', Auth::id())
            ->first();

        // Jika user ini belum punya profil siswa yang valid, tolak akses
        if (!$siswa) {
            abort(403, 'Profil siswa tidak ditemukan atau belum ditautkan.');
        }

        return Inertia::render('Siswa/Rapor/Index', [
            'siswa' => $siswa
        ]);
    }
}

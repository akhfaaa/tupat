<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\Pkl;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $stats = [];

        // Hanya muat data statistik berat jika user adalah Admin atau TU
        if ($user->hasRole('super-admin') || $user->hasRole('tu')) {
            $stats = [
                'total_siswa' => Siswa::count(),
                'total_guru' => Guru::count(),
                'total_kelas' => Rombel::count(),
                'siswa_pkl_aktif' => Pkl::where('status', 'Aktif')->count(),
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\Pkl;
use App\Models\Jurnal;
use App\Models\Absensi;
use App\Models\PklLogbook;
use App\Models\JobPosting;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $roles = $user->getRoleNames()->values()->all();
        $primaryRole = $roles[0] ?? 'user';
        $children = $user->hasSystemRole('orang-tua')
            ? $user->children()->with(['rombel', 'jurusan'])->get()
            : [];
        $stats = match (true) {
            $user->hasAnySystemRole(['super-admin', 'tu']) => [
                'total_siswa' => Siswa::count(),
                'total_guru' => Guru::count(),
                'total_kelas' => Rombel::count(),
                'siswa_pkl_aktif' => Pkl::where('status', 'Aktif')->count(),
            ],
            $user->hasAnySystemRole(['guru', 'wali-kelas']) => [
                'jurnal_bulan_ini' => Jurnal::where('guru_id', optional($user->guru)->id)
                    ->whereMonth('tanggal', now()->month)
                    ->whereYear('tanggal', now()->year)
                    ->count(),
                'presensi_bulan_ini' => Absensi::whereHas('jurnal', fn ($query) => $query
                    ->where('guru_id', optional($user->guru)->id)
                    ->whereMonth('tanggal', now()->month))
                    ->count(),
            ],
            $user->hasAnySystemRole(['hubin', 'mentor-industri']) => [
                'pkl_aktif' => Pkl::where('status', 'Aktif')->count(),
                'logbook_menunggu' => PklLogbook::where('status_verifikasi', 'menunggu')->count(),
            ],
            $user->hasSystemRole('bkk') => [
                'lowongan_aktif' => JobPosting::where('status', 'aktif')->count(),
            ],
            default => [],
        };

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'primaryRole' => $primaryRole,
            'roles' => $roles,
            'children' => $children,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ParentDashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        return Inertia::render('Parent/Index', [
            'children' => $user->children()->with(['rombel', 'jurusan'])->get()->map(function ($student) {
                $attendance = DB::table('absensis')
                    ->where('siswa_id', $student->id)
                    ->selectRaw("SUM(status = 'Hadir') as hadir, SUM(status = 'Sakit') as sakit, SUM(status = 'Izin') as izin, SUM(status = 'Alpa') as alpa")
                    ->first();

                return [
                    'id' => $student->id,
                    'nama_lengkap' => $student->nama_lengkap,
                    'nisn' => $student->nisn,
                    'rombel' => $student->rombel?->nama_rombel,
                    'jurusan' => $student->jurusan?->nama_jurusan,
                    'attendance' => [
                        'hadir' => (int) ($attendance->hadir ?? 0),
                        'sakit' => (int) ($attendance->sakit ?? 0),
                        'izin' => (int) ($attendance->izin ?? 0),
                        'alpa' => (int) ($attendance->alpa ?? 0),
                    ],
                    'grades_count' => $student->nilais()->count(),
                ];
            })->values(),
        ]);
    }
}
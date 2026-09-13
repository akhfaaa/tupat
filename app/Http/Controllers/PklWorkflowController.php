<?php

namespace App\Http\Controllers;

use App\Models\Pkl;
use App\Models\PklLogbook;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PklWorkflowController extends Controller
{
    public function studentIndex()
    {
        $siswa = Siswa::where('user_id', Auth::id())->first();
        abort_unless($siswa, 403, 'Profil siswa belum terhubung ke akun ini.');

        return Inertia::render('Siswa/Pkl/Index', [
            'pkls' => Pkl::with(['mitraDudi', 'guru', 'logbooks'])
                ->where('siswa_id', $siswa->id)
                ->latest()
                ->get(),
        ]);
    }

    public function storeLogbook(Request $request, Pkl $pkl)
    {
        $this->ensureStudentOwnsPkl($pkl);

        $validated = $request->validate([
            'tanggal' => [
                'required',
                'date',
                'after_or_equal:' . $pkl->tanggal_mulai,
                'before_or_equal:' . $pkl->tanggal_selesai,
                Rule::unique('pkl_logbooks', 'tanggal')->where('pkl_id', $pkl->id),
            ],
            'kegiatan' => 'required|string',
            'hasil' => 'nullable|string',
        ]);

        $pkl->logbooks()->create($validated);

        return redirect()->back()->with('success', 'Logbook berhasil dikirim untuk verifikasi.');
    }

    public function verifyLogbook(Request $request, PklLogbook $logbook)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole(['super-admin', 'hubin', 'mentor-industri']), 403);

        $validated = $request->validate([
            'status_verifikasi' => 'required|in:disetujui,ditolak',
            'catatan_verifikasi' => 'nullable|string',
        ]);

        $logbook->update([
            ...$validated,
            'verified_by' => $user->getKey(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Logbook berhasil diverifikasi.');
    }

    public function assess(Request $request, Pkl $pkl)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole(['super-admin', 'hubin', 'mentor-industri']), 403);

        $validated = $request->validate([
            'technical_score' => 'required|integer|min:0|max:100',
            'discipline_score' => 'required|integer|min:0|max:100',
            'communication_score' => 'required|integer|min:0|max:100',
            'teamwork_score' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $pkl->assessment()->updateOrCreate(
            ['pkl_id' => $pkl->id],
            [...$validated, 'assessed_by' => $user->getKey(), 'assessed_at' => now()]
        );

        return redirect()->back()->with('success', 'Penilaian industri berhasil disimpan.');
    }

    private function ensureStudentOwnsPkl(Pkl $pkl): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(
            $user->hasSystemRole('siswa') && $pkl->siswa?->user_id === $user->getKey(),
            403
        );
    }
}
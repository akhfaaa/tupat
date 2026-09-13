<?php

namespace App\Http\Controllers;

use App\Models\Pkl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ExtendedModuleController extends Controller
{
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole([
            'super-admin', 'tu', 'guru', 'guru-bk', 'wali-kelas', 'hubin', 'mentor-industri', 'bkk',
        ]), 403);

        return Inertia::render('Modules/Index', [
            'schemes' => DB::table('ukk_schemes')->latest()->get(),
            'disciplineRecords' => DB::table('discipline_records')
                ->join('siswas', 'siswas.id', '=', 'discipline_records.siswa_id')
                ->select('discipline_records.*', 'siswas.nama_lengkap')
                ->latest('discipline_records.occurred_at')
                ->limit(20)
                ->get(),
            'inventory' => DB::table('inventory_items')->orderBy('name')->get(),
            'loans' => DB::table('inventory_loans')
                ->join('inventory_items', 'inventory_items.id', '=', 'inventory_loans.inventory_item_id')
                ->join('users', 'users.id', '=', 'inventory_loans.borrower_id')
                ->select('inventory_loans.*', 'inventory_items.name as item_name', 'users.name as borrower_name')
                ->where('inventory_loans.status', '!=', 'dikembalikan')
                ->latest('inventory_loans.borrowed_at')
                ->limit(30)
                ->get(),
            'jobs' => DB::table('job_postings')->where('status', 'aktif')->latest()->get(),
            'alumni' => DB::table('alumni_profiles')->latest()->limit(20)->get(),
            'students' => DB::table('siswas')->orderBy('nama_lengkap')->get(['id', 'nama_lengkap', 'nisn']),
            'subjects' => DB::table('mata_pelajarans')->orderBy('nama_mapel')->get(['id', 'nama_mapel']),
            'academicPeriods' => DB::table('tahun_ajarans')->orderByDesc('is_active')->orderByDesc('tahun')->get(['id', 'tahun', 'semester', 'is_active']),
        ]);
    }

    public function storeUkkAssessment(Request $request)
    {
        $data = $request->validate([
            'scheme_id' => 'required|exists:ukk_schemes,id',
            'siswa_id' => 'required|exists:siswas,id',
            'score' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:belum_dinilai,kompeten,belum_kompeten',
            'notes' => 'nullable|string',
        ]);

        DB::table('ukk_assessments')->updateOrInsert(
            ['scheme_id' => $data['scheme_id'], 'siswa_id' => $data['siswa_id']],
            [...$data, 'assessor_id' => Auth::id(), 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->back()->with('success', 'Penilaian UKK tersimpan.');
    }

    public function storeUkkScheme(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole(['super-admin', 'tu', 'hubin']), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'certification_body' => 'nullable|string|max:255',
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        DB::table('ukk_schemes')->insert([
            ...$data,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Skema UKK berhasil dibuat.');
    }

    public function storeJobPosting(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole(['super-admin', 'tu', 'hubin', 'bkk']), 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'closing_date' => 'nullable|date',
            'status' => 'required|in:draft,aktif,ditutup',
        ]);

        DB::table('job_postings')->insert([
            ...$data,
            'posted_by' => $user->getKey(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Lowongan berhasil dipublikasikan.');
    }

    public function storeDisciplineRecord(Request $request)
    {
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'type' => 'required|in:pelanggaran,prestasi',
            'points' => 'required|integer|min:-1000|max:1000',
            'category' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'occurred_at' => 'required|date',
        ]);

        DB::table('discipline_records')->insert([...$data, 'recorded_by' => Auth::id(), 'created_at' => now(), 'updated_at' => now()]);

        return redirect()->back()->with('success', 'Catatan kedisiplinan tersimpan.');
    }

    public function storePklAttendance(Request $request, Pkl $pkl)
    {
        abort_unless($pkl->siswa?->user_id === Auth::id(), 403);

        $data = $request->validate([
            'attendance_date' => [
                'required',
                'date',
                Rule::unique('pkl_attendances', 'attendance_date')->where('pkl_id', $pkl->id),
            ],
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy_meters' => 'nullable|numeric|min:0',
        ]);

        DB::table('pkl_attendances')->insert([
            ...$data,
            'pkl_id' => $pkl->id,
            'status' => 'menunggu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Presensi lokasi PKL dikirim.');
    }

    public function storeInventoryLoan(Request $request)
    {
        $data = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
            'borrowed_at' => 'required|date',
            'due_at' => 'nullable|date|after_or_equal:borrowed_at',
        ]);

        DB::transaction(function () use ($data) {
            $item = DB::table('inventory_items')->where('id', $data['inventory_item_id'])->lockForUpdate()->first();
            abort_unless($item && $item->quantity >= $data['quantity'], 422, 'Stok alat tidak mencukupi.');

            DB::table('inventory_items')->where('id', $item->id)->decrement('quantity', $data['quantity']);
            DB::table('inventory_loans')->insert([
                ...$data,
                'borrower_id' => Auth::id(),
                'status' => 'dipinjam',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Peminjaman alat tercatat.');
    }

    public function storeInventoryItem(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole(['super-admin', 'tu']), 403);

        $data = $request->validate([
            'code' => 'required|string|max:100|unique:inventory_items,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:30',
            'condition' => 'required|in:baik,perlu_perbaikan,rusak',
        ]);

        DB::table('inventory_items')->insert([...$data, 'created_at' => now(), 'updated_at' => now()]);

        return redirect()->back()->with('success', 'Alat berhasil ditambahkan.');
    }

    public function returnInventoryLoan(int $loan)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole(['super-admin', 'tu', 'guru']), 403);

        DB::transaction(function () use ($loan) {
            $record = DB::table('inventory_loans')->where('id', $loan)->lockForUpdate()->first();
            abort_unless($record && $record->status !== 'dikembalikan', 404);

            DB::table('inventory_loans')->where('id', $record->id)->update([
                'status' => 'dikembalikan',
                'returned_at' => now()->toDateString(),
                'updated_at' => now(),
            ]);
            DB::table('inventory_items')->where('id', $record->inventory_item_id)->increment('quantity', $record->quantity);
        });

        return redirect()->back()->with('success', 'Peminjaman dikembalikan dan stok diperbarui.');
    }

    public function storeReportComponent(Request $request)
    {
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'component' => 'required|in:formatif,sumatif,praktik,projek_p5,pkl',
            'score' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        DB::table('report_components')->updateOrInsert(
            collect($data)->only(['siswa_id', 'mata_pelajaran_id', 'tahun_ajaran_id', 'component'])->all(),
            [...$data, 'updated_at' => now(), 'created_at' => now()]
        );

        return redirect()->back()->with('success', 'Komponen rapor tersimpan.');
    }

    public function storeTracerStudy(Request $request)
    {
        $data = $request->validate([
            'alumni_profile_id' => 'required|exists:alumni_profiles,id',
            'status' => 'required|in:bekerja,melanjutkan,wirausaha,belum_terlacak',
            'institution' => 'nullable|string|max:255',
            'reported_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::table('tracer_studies')->insert([...$data, 'created_at' => now(), 'updated_at' => now()]);

        return redirect()->back()->with('success', 'Tracer study tersimpan.');
    }

    public function exportDapodik()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole(['super-admin', 'tu']), 403);

        return response()->streamDownload(function () {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['nisn', 'nis', 'nama_lengkap', 'jenis_kelamin', 'jurusan', 'rombel']);

            DB::table('siswas')
                ->leftJoin('jurusans', 'jurusans.id', '=', 'siswas.jurusan_id')
                ->leftJoin('rombels', 'rombels.id', '=', 'siswas.rombel_id')
                ->select('siswas.nisn', 'siswas.nis', 'siswas.nama_lengkap', 'siswas.jenis_kelamin', 'jurusans.nama_jurusan', 'rombels.nama_rombel')
                ->orderBy('siswas.nama_lengkap')
                ->each(function ($student) use ($output) {
                    fputcsv($output, (array) $student);
                });

            fclose($output);
        }, 'dapodik-siswa.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function importDapodik(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasAnySystemRole(['super-admin', 'tu']), 403);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = fgetcsv($handle);
        $required = ['nisn', 'nis', 'nama_lengkap', 'jenis_kelamin'];

        abort_unless($header && count(array_diff($required, $header)) === 0, 422, 'Header CSV Dapodik tidak sesuai.');

        $indexes = array_flip($header);
        $updated = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $nisn = trim($row[$indexes['nisn']] ?? '');
            if ($nisn === '') {
                $skipped++;
                continue;
            }

            $student = DB::table('siswas')->where('nisn', $nisn)->first();
            if (! $student) {
                $skipped++;
                continue;
            }

            DB::table('siswas')->where('id', $student->id)->update([
                'nis' => trim($row[$indexes['nis']] ?? $student->nis),
                'nama_lengkap' => trim($row[$indexes['nama_lengkap']] ?? $student->nama_lengkap),
                'jenis_kelamin' => trim($row[$indexes['jenis_kelamin']] ?? $student->jenis_kelamin),
                'updated_at' => now(),
            ]);
            $updated++;
        }

        fclose($handle);

        DB::table('dapodik_sync_logs')->insert([
            'user_id' => $user->getKey(),
            'direction' => 'import',
            'file_name' => $request->file('file')->getClientOriginalName(),
            'status' => 'berhasil',
            'summary' => json_encode(['updated' => $updated, 'skipped' => $skipped]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', "Import selesai: {$updated} diperbarui, {$skipped} dilewati.");
    }
}
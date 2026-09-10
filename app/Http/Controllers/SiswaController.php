<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\Rombel;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Wajib di-import untuk transaksi database

class SiswaController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Siswa/Index', [
            'siswas' => Siswa::with(['jurusan', 'rombel', 'user'])->latest()->get(),
            'jurusans' => Jurusan::orderBy('nama_jurusan', 'asc')->get(),
            'rombels' => Rombel::with('tahunAjaran')->orderBy('nama_rombel', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:siswas,nisn',
            'nis' => 'nullable|string|max:20|unique:siswas,nis',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'nullable|string|max:20',
            'jurusan_id' => 'required|exists:jurusans,id',
            'rombel_id' => 'nullable|exists:rombels,id',
        ]);

        // Gunakan DB Transaction agar jika gagal satu, batal semua
        DB::transaction(function () use ($validated) {
            // 1. Buat Akun Login Siswa
            $user = User::create([
                'name' => $validated['nama_lengkap'],
                'email' => $validated['nisn'] . '@smk.com', // Email dummy menggunakan NISN
                'password' => Hash::make($validated['nisn']), // Password bawaan adalah NISN
            ]);

            // 2. Beri Role 'siswa'
            $user->assignRole('siswa');

            // 3. Simpan Data Profil Siswa
            $validated['user_id'] = $user->id;
            Siswa::create($validated);
        });

        return redirect()->back();
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:siswas,nisn,' . $siswa->id,
            'nis' => 'nullable|string|max:20|unique:siswas,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'nullable|string|max:20',
            'jurusan_id' => 'required|exists:jurusans,id',
            'rombel_id' => 'nullable|exists:rombels,id',
        ]);

        DB::transaction(function () use ($validated, $siswa) {
            // Update nama di tabel users juga
            if ($siswa->user) {
                $siswa->user->update(['name' => $validated['nama_lengkap']]);
            }
            $siswa->update($validated);
        });

        return redirect()->back();
    }

    public function destroy(Siswa $siswa)
    {
        DB::transaction(function () use ($siswa) {
            $user = $siswa->user;
            $siswa->delete(); // Hapus profil siswa
            if ($user) {
                $user->delete(); // Hapus akun login-nya sekalian
            }
        });

        return redirect()->back();
    }
}

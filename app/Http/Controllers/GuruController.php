<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Guru/Index', [
            'gurus' => Guru::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:30|unique:gurus,nip',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'nullable|string|max:20',
        ]);

        Guru::create($validated);
        return redirect()->back();
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $guru->update($validated);
        return redirect()->back();
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->back();
    }
}

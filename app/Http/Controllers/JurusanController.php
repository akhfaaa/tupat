<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Inertia\Inertia;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        return Inertia::render('Master/Jurusan/Index', [
            'jurusans' => Jurusan::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_jurusan' => 'required|string|max:10|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        Jurusan::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $validated = $request->validate([
            'kode_jurusan' => 'required|string|max:10|unique:jurusans,kode_jurusan,' . $jurusan->id,
            'nama_jurusan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $jurusan->update($validated);

        return redirect()->back();
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return redirect()->back();
    }
}

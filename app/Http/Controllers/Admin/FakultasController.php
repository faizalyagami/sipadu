<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::with('prodis')->get();
        $jenjang = ['S1', 'S2', 'S3', 'Profesi'];
        return view('admin.fakultas.index', compact('fakultas', 'jenjang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_fakultas' => 'required|string|max:255'
        ]);

        Fakultas::create([
            'nama_fakultas' => $request->nama_fakultas
        ]);

        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil ditambahkan');
    }

    public function storeProdi(Request $request, Fakultas $fakultas)
    {
        $request->validate([
            'nama_prodi' => 'required|string|max:255',
            'jenjang' => 'required|in:S1,S2,S3,Profesi'
        ]);

        $fakultas->prodis()->create($request->only('nama_prodi', 'jenjang'));

        return redirect()->route('admin.fakultas.index')->with('success', 'Program Studi berhasil ditambahkan');
    }

    public function update(Request $request, Fakultas $fakultas)
    {
        $request->validate([
            'nama_fakultas' => 'required|string|max:255'
        ]);

        $fakultas->update($request->only('nama_fakultas'));

        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil diupdate');
    }

    public function destroy(Fakultas $fakultas)
    {
        $fakultas->delete();
        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil dihapus');
    }

    public function updateProdi(Request $request, Prodi $prodi)
    {
        $request->validate([
            'nama_prodi' => 'required|string|max:255',
            'jenjang' => 'required|in:S1,S2,S3,Profesi'
        ]);

        $prodi->update($request->only('nama_prodi', 'jenjang'));

        return redirect()->route('admin.fakultas.index')->with('success', 'Program Studi berhasil diupdate');
    }

    public function destroyProdi(Prodi $prodi)
    {
        $prodi->delete();
        return redirect()->route('admin.fakultas.index')->with('success', 'Program Studi berhasil dihapus');
    }

    public function getProdiByFakultas($fakultasId)
    {
        $prodis = Prodi::where('fakultas_id', $fakultasId)->get();
        return response()->json($prodis);
    }
}
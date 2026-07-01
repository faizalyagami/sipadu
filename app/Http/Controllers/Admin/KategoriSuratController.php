<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriSurat;
use Illuminate\Http\Request;

class KategoriSuratController extends Controller
{
    public function index()
    {
        $kategoris = KategoriSurat::orderBy('nama_kategori')->get();
        return view('admin.kategori_surat.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategori_surat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_surats',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        KategoriSurat::create($request->all());
        return redirect()->route('admin.kategori-surat.index')->with('success', 'Kategori surat berhasil ditambahkan.');
    }

    public function edit(KategoriSurat $kategoriSurat)
    {
        return view('admin.kategori_surat.edit', compact('kategoriSurat'));
    }

    public function update(Request $request, KategoriSurat $kategoriSurat)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_surats,nama_kategori,' . $kategoriSurat->id,
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'warna' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $kategoriSurat->update($request->all());
        return redirect()->route('admin.kategori-surat.index')->with('success', 'Kategori surat berhasil diperbarui.');
    }

    public function destroy(KategoriSurat $kategoriSurat)
    {
        if ($kategoriSurat->jenisSurats()->count() > 0) {
            return redirect()->route('admin.kategori-surat.index')->with('error', 'Tidak dapat menghapus kategori yang memiliki jenis surat.');
        }

        $kategoriSurat->delete();
        return redirect()->route('admin.kategori-surat.index')->with('success', 'Kategori surat berhasil dihapus.');
    }

    public function toggleStatus(KategoriSurat $kategoriSurat)
    {
        $kategoriSurat->is_active = !$kategoriSurat->is_active;
        $kategoriSurat->save();

        return response()->json([
            'success' => true,
            'is_active' => $kategoriSurat->is_active
        ]);
    }
}

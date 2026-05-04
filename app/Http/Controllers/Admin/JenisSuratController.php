<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JenisSuratController extends Controller
{
    public function index()
    {
        $jenisSurats = JenisSurat::all();
        return view('admin.jenis_surat.index', compact('jenisSurats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'kategori' => 'required|string'
        ]);

        JenisSurat::create([
            'nama_surat' => $request->nama_surat,
            'kategori_surat' => $request->kategori,
            'is_active' => true
        ]);

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil ditambahkan');
    }

    public function update(Request $request, JenisSurat $jenisSurat)
    {
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'kategori' => 'required|string'
        ]);

        $jenisSurat->update([
            'nama_surat' => $request->nama_surat,
            'kategori_surat' => $request->kategori
        ]);

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil diupdate');
    }

    public function updateTemplate(Request $request, JenisSurat $jenisSurat)
    {
        $request->validate([
            'template_content' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            if ($jenisSurat->logo_path && Storage::disk('public')->exists($jenisSurat->logo_path)) {
                Storage::disk('public')->delete($jenisSurat->logo_path);
            }
            $logoPath = $request->file('logo')->store('uploads/logo', 'public');
            $jenisSurat->logo_path = $logoPath;
        }

        if ($request->hasFile('kop_surat')) {
            if ($jenisSurat->kop_surat_path && Storage::disk('public')->exists($jenisSurat->kop_surat_path)) {
                Storage::disk('public')->delete($jenisSurat->kop_surat_path);
            }
            $kopPath = $request->file('kop_surat')->store('uploads/kop_surat', 'public');
            $jenisSurat->kop_surat_path = $kopPath;
        }

        $jenisSurat->template_content = $request->template_content;
        $jenisSurat->save();

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Template surat berhasil disimpan');
    }

    public function destroy(JenisSurat $jenisSurat)
    {
        if ($jenisSurat->logo_path && Storage::disk('public')->exists($jenisSurat->logo_path)) {
            Storage::disk('public')->delete($jenisSurat->logo_path);
        }
        
        if ($jenisSurat->kop_surat_path && Storage::disk('public')->exists($jenisSurat->kop_surat_path)) {
            Storage::disk('public')->delete($jenisSurat->kop_surat_path);
        }
        
        $jenisSurat->delete();
        
        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil dihapus');
    }

    public function getSuratJson($id)
    {
        $surat = Surat::with(['mahasiswa', 'jenisSurat', 'approvedBy'])->findOrFail($id);
        
        return response()->json([
            'id' => $surat->id,
            'mahasiswa' => $surat->mahasiswa->nama_lengkap ?? '-',
            'npm' => $surat->mahasiswa->npm ?? '-',
            'jenis_surat' => $surat->jenisSurat->nama_surat ?? '-',
            'keperluan' => $surat->keperluan,
            'status' => $surat->status == 'approved' ? 'disetujui' : 'ditolak',
            'created_at' => $surat->created_at->format('d/m/Y H:i'),
            'approved_at' => $surat->approved_at ? $surat->approved_at->format('d/m/Y H:i') : null,
            'alasan_reject' => $surat->alasan_reject,
            'content' => $surat->content
        ]);
    }
}
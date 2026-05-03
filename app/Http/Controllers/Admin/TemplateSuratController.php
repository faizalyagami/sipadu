<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index(JenisSurat $jenisSurat)
    {
        $templates = $jenisSurat->templates()->orderBy('version', 'desc')->get();
        return view('admin.jenis_surat.templates.index', compact('jenisSurat', 'templates'));
    }

    public function create(JenisSurat $jenisSurat)
    {
        return view('admin.jenis_surat.templates.create', compact('jenisSurat'));
    }

    public function store(Request $request, JenisSurat $jenisSurat)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'template_html' => 'required|string',
            'variables' => 'nullable|array',
            'keterangan' => 'nullable|string'
        ]);

        // Nonaktifkan template lama
        TemplateSurat::where('jenis_surat_id', $jenisSurat->id)
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        $latestVersion = TemplateSurat::where('jenis_surat_id', $jenisSurat->id)->max('version') ?? 0;

        $template = $jenisSurat->templates()->create([
            'nama_template' => $request->nama_template,
            'template_html' => $request->template_html,
            'variables' => $request->variables,
            'status' => 'active',
            'version' => $latestVersion + 1,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('admin.jenis-surat.templates.index', $jenisSurat)
            ->with('success', 'Template surat berhasil ditambahkan');
    }

    public function edit(JenisSurat $jenisSurat, TemplateSurat $template)
    {
        return view('admin.jenis_surat.templates.edit', compact('jenisSurat', 'template'));
    }

    public function update(Request $request, JenisSurat $jenisSurat, TemplateSurat $template)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'template_html' => 'required|string',
            'variables' => 'nullable|array',
            'keterangan' => 'nullable|string'
        ]);

        $template->update($request->only('nama_template', 'template_html', 'variables', 'keterangan'));

        return redirect()->route('admin.jenis-surat.templates.index', $jenisSurat)
            ->with('success', 'Template surat berhasil diupdate');
    }

    public function destroy(JenisSurat $jenisSurat, TemplateSurat $template)
    {
        $template->delete();
        return redirect()->route('admin.jenis-surat.templates.index', $jenisSurat)
            ->with('success', 'Template surat berhasil dihapus');
    }

    public function preview(Request $request, JenisSurat $jenisSurat, TemplateSurat $template)
    {
        // Data contoh untuk preview
        $sampleData = [
            'nama_mahasiswa' => 'Ahmad Fauzi',
            'npm' => '202410001',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2000-01-15',
            'alamat' => 'Jl. Contoh No. 123 Bandung',
            'fakultas' => 'Fakultas Teknik',
            'prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'ipk' => '3.75',
            'tanggal_surat' => date('d F Y'),
            'keperluan' => $request->keperluan ?? 'Contoh Keperluan Surat'
        ];

        $rendered = $template->render($sampleData);
        
        return response()->json([
            'html' => $rendered,
            'variables' => $template->getAvailableVariables()
        ]);
    }

    public function setActive(JenisSurat $jenisSurat, TemplateSurat $template)
    {
        // Nonaktifkan semua template
        TemplateSurat::where('jenis_surat_id', $jenisSurat->id)
            ->update(['status' => 'inactive']);
        
        // Aktifkan template yang dipilih
        $template->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Template aktif berhasil diubah');
    }
}
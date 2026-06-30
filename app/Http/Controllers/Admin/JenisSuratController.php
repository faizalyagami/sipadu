<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\KategoriSurat;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JenisSuratController extends Controller
{

    private function migrateTemplate(string $template): string
    {
        // Ubah <img src="...storage/uploads/kop_surat/..."> menjadi {kop_surat}
        $template = preg_replace(
            '/<img[^>]*src=["\'][^"\']*(?:storage|public|uploads)\/kop_surat[^"\']*["\'][^>]*>/i',
            '{kop_surat}',
            $template
        );

        // Ubah <img src="http://.../storage/uploads/kop_surat/..."> menjadi {kop_surat}
        $template = preg_replace(
            '/<img[^>]*src=["\'](?:https?:\/\/[^"\']*)\/storage\/uploads\/kop_surat[^"\']*["\'][^>]*>/i',
            '{kop_surat}',
            $template
        );

        // Hapus div kosong yang berisi hanya placeholder
        $template = preg_replace(
            '/<div[^>]*>\s*\{kop_surat\}\s*<\/div>/i',
            '{kop_surat}',
            $template
        );

        return $template;
    }

    public function index()
    {
        $jenisSurats = JenisSurat::with('kategoriSurat')->get();
        $kategoris = KategoriSurat::active()->get();

        // Migrasi template saat index dibuka
        foreach ($jenisSurats as $jenis) {
            if ($jenis->template_content && strpos($jenis->template_content, 'kop_surat') !== false) {
                $migrated = $this->migrateTemplate($jenis->template_content);
                if ($migrated !== $jenis->template_content) {
                    $jenis->template_content = $migrated;
                    $jenis->save();
                    Log::info('Template migrated for jenis surat ID: ' . $jenis->id);
                }
            }
        }

        return view('admin.jenis_surat.index', compact('jenisSurats', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'kategori_surat_id' => 'required|exists:kategori_surats,id'
        ]);

        JenisSurat::create([
            'nama_surat' => $request->nama_surat,
            'kategori_surat_id' => $request->kategori_surat_id,
            'is_active' => true
        ]);

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil ditambahkan');
    }

    public function update(Request $request, JenisSurat $jenisSurat)
    {
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'kategori_surat_id' => 'required|exists:kategori_surats,id'
        ]);

        $jenisSurat->update([
            'nama_surat' => $request->nama_surat,
            'kategori_surat_id' => $request->kategori_surat_id
        ]);

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Jenis surat berhasil diupdate');
    }

    public function updateTemplate(Request $request, JenisSurat $jenisSurat)
    {
        $request->validate([
            'template_content' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle logo
        if ($request->hasFile('logo')) {
            if ($jenisSurat->logo_path && Storage::disk('public')->exists($jenisSurat->logo_path)) {
                Storage::disk('public')->delete($jenisSurat->logo_path);
            }
            $path = $request->file('logo')->store('uploads/logo', 'public');
            $jenisSurat->logo_path = $path;
        } elseif ($request->input('remove_logo') == '1') {
            if ($jenisSurat->logo_path && Storage::disk('public')->exists($jenisSurat->logo_path)) {
                Storage::disk('public')->delete($jenisSurat->logo_path);
            }
            $jenisSurat->logo_path = null;
        }

        // Handle kop surat
        if ($request->hasFile('kop_surat')) {
            if ($jenisSurat->kop_surat_path && Storage::disk('public')->exists($jenisSurat->kop_surat_path)) {
                Storage::disk('public')->delete($jenisSurat->kop_surat_path);
            }
            $path = $request->file('kop_surat')->store('uploads/kop_surat', 'public');
            $jenisSurat->kop_surat_path = $path;
        } elseif ($request->input('remove_kop') == '1') {
            if ($jenisSurat->kop_surat_path && Storage::disk('public')->exists($jenisSurat->kop_surat_path)) {
                Storage::disk('public')->delete($jenisSurat->kop_surat_path);
            }
            $jenisSurat->kop_surat_path = null;
        }

        $jenisSurat->template_content = $request->template_content;
        $jenisSurat->save();

        return redirect()->route('admin.jenis-surat.index')
            ->with('success', 'Template surat berhasil disimpan');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $file = $request->file('logo');
        $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/logo', $filename, 'public');

        return response()->json([
            'success' => true,
            'url' => Storage::url($path),
            'path' => $path
        ]);
    }

    public function uploadKop(Request $request)
    {
        $request->validate([
            'kop_surat' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $file = $request->file('kop_surat');
        $filename = 'kop_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/kop_surat', $filename, 'public');

        return response()->json([
            'success' => true,
            'url' => Storage::url($path),
            'path' => $path
        ]);
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

    public function exportPdf($id)
    {
        try {
            $surat = Surat::with(['mahasiswa', 'jenisSurat'])->findOrFail($id);

            // Generate HTML surat
            $html = $surat->generateSuratHtml();

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Times New Roman',
                'logErrors' => true
            ]);

            return $pdf->stream('surat_' . ($surat->nomor_surat ?? $surat->id) . '.pdf');
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFormFields($id)
    {
        try {
            $jenisSurat = JenisSurat::with('kategoriSurat')->findOrFail($id);

            // Ambil fields dari database atau generate default
            $fields = $jenisSurat->getFormFields();

            return response()->json([
                'success' => true,
                'fields' => $fields,
                'kategori' => $jenisSurat->kategoriSurat->nama_kategori ?? '',
                'jenis_surat' => $jenisSurat->nama_surat,
                'jenis_surat_id' => $jenisSurat->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat form: ' . $e->getMessage()
            ], 500);
        }
    }
}

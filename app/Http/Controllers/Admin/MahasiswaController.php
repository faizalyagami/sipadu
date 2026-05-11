<?php
// app/Http/Controllers/Admin/MahasiswaController.php
namespace App\Http\Controllers\Admin;

use App\Exports\MahasiswaExport;
use App\Exports\MahasiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MahasiswaImport;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use App\Models\ImportProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswas = Mahasiswa::with(['user', 'prodi.fakultas'])->latest()->paginate(25);
        $fakultas = Fakultas::all();
        $allProdis = Prodi::all();
        
        return view('admin.mahasiswa.index', compact('mahasiswas', 'fakultas', 'allProdis'));
    }

    public function create()
    {
        $fakultas = Fakultas::with('prodis')->get();
        $allProdis = Prodi::with('fakultas')->get();
        
        return view('admin.mahasiswa.create', compact('fakultas', 'allProdis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'npm' => 'required|unique:mahasiswas',
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:users',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'no_hp' => 'nullable|string|max:15',
            'fakultas_id' => 'required|exists:fakultas,id',
            'prodi_id' => 'required|exists:prodis,id',
            'tanggal_masuk' => 'required|date',
            'status_mahasiswa' => 'required|in:Aktif,Lulus,Cuti',
            'ipk' => 'nullable|numeric|min:0|max:4'
        ]);

        DB::beginTransaction();
        try {
            // Buat user account
            $password = 'password123';
            $user = User::create([
                'name' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($password),
                'role' => 'mahasiswa'
            ]);

            // Buat data mahasiswa
            Mahasiswa::create([
                'user_id' => $user->id,
                'prodi_id' => $request->prodi_id,
                'npm' => $request->npm,
                'nama_lengkap' => $request->nama_lengkap,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_mahasiswa' => $request->status_mahasiswa,
                'ipk' => $request->ipk
            ]);

            DB::commit();
            return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.mahasiswa.index')->with('error', 'Gagal menambahkan: ' . $e->getMessage());
        }
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $fakultas = Fakultas::with('prodis')->get();
        $prodis = Prodi::with('fakultas')->get();
        
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'fakultas', 'prodis'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'dosen_wali_nik' => 'nullable|string',
            'dosen_wali' => 'nullable|string',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'no_hp' => 'nullable|string|max:15',
            'prodi_id' => 'required|exists:prodis,id',
            'tanggal_masuk' => 'required|date',
            'status_mahasiswa' => 'required|in:Aktif,Lulus,Cuti',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'jenis_kelamin' => 'required|in:L,P',
            'sks_tempuh' => 'nullable|integer|min:0|max:200',
        ]);

        DB::beginTransaction();
        try {
            $mahasiswa->update([
                'nama_lengkap' => $request->nama_lengkap,
                'dosen_wali_nik' => $request->dosen_wali_nik,
                'dosen_wali' => $request->dosen_wali,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                'prodi_id' => $request->prodi_id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_mahasiswa' => $request->status_mahasiswa,
                'ipk' => $request->ipk,
                'jenis_kelamin' => $request->jenis_kelamin,
                'sks_tempuh' => $request->sks_tempuh,
            ]);
            
            $mahasiswa->user->update(['name' => $request->nama_lengkap]);
            
            DB::commit();
            return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.mahasiswa.index')->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $mahasiswa = Mahasiswa::with(['user', 'prodi.fakultas'])->findOrFail($id);
        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        DB::beginTransaction();
        try {
            $user = $mahasiswa->user;
            $mahasiswa->delete();
            $user->delete();
            DB::commit();
            return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.mahasiswa.index')->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    public function export(Request $request, $status = 'semua')
    {
        $fileName = 'data_mahasiswa_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new MahasiswaExport($status), $fileName);
    }

    public function exportTemplate()
    {
        $fileName = 'template_import_mahasiswa.xlsx';
        return Excel::download(new MahasiswaTemplateExport(), $fileName);
    }

    /**
     * Import data mahasiswa dengan progress bar
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // Max 10MB
        ]);

        try {
            // Generate batch ID
            $batchId = (string) Str::uuid();
            
            // Simpan file sementara
            $file = $request->file('file');
            $filePath = $file->storeAs('temp_imports', $batchId . '_' . $file->getClientOriginalName());
            
            // Buat record progress
            $progress = ImportProgress::create([
                'batch_id' => $batchId,
                'filename' => $file->getClientOriginalName(),
                'total_rows' => 0, // Akan diupdate nanti
                'processed_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
                'status' => 'processing'
            ]);
            
            // Dispatch job ke queue
            dispatch(new \App\Jobs\ProcessMahasiswaImport($filePath, $batchId));
            
            return response()->json([
                'success' => true,
                'batch_id' => $batchId,
                'message' => 'Import dimulai, silahkan pantau progress di modal'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai import: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cek progress import
     */
    public function checkProgress($batchId)
    {
        $progress = ImportProgress::where('batch_id', $batchId)->first();
        
        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Progress tidak ditemukan'
            ], 404);
        }
        
        $percentage = 0;
        if ($progress->total_rows > 0) {
            $percentage = round(($progress->processed_rows / $progress->total_rows) * 100);
        }
        
        // Ambil errors (limit 100 untuk response)
        $errors = $progress->errors ?? [];
        if (is_array($errors) && count($errors) > 100) {
            $errors = array_slice($errors, 0, 100);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'batch_id' => $progress->batch_id,
                'filename' => $progress->filename,
                'total_rows' => $progress->total_rows,
                'processed_rows' => $progress->processed_rows,
                'success_rows' => $progress->success_rows,
                'failed_rows' => $progress->failed_rows,
                'percentage' => $percentage,
                'status' => $progress->status,
                'errors' => $errors,
                'created_at' => $progress->created_at->diffForHumans()
            ]
        ]);
    }
}
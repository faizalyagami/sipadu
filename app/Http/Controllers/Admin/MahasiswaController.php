<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MahasiswaExport;
use App\Exports\MahasiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MahasiswaImport;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswas = Mahasiswa::with(['user', 'prodi.fakultas'])->get();
        $fakultas = Fakultas::with('prodis')->get();
        $allProdis = Prodi::with('fakultas')->get();
        
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
            'no_hp' => 'required',
            'fakultas_id' => 'required|exists:fakultas,id',
            'prodi_id' => 'required|exists:prodis,id',
            'tanggal_masuk' => 'required|date',
            'status_mahasiswa' => 'required|in:Aktif,Lulus,Cuti',
            'ipk' => 'nullable|numeric|min:0|max:4'
        ]);

        // Buat user account
        $password = Str::random(8);
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

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan');
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
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'no_hp' => 'required',
            'prodi_id' => 'required|exists:prodis,id',
            'tanggal_masuk' => 'required|date',
            'status_mahasiswa' => 'required|in:Aktif,Lulus,Cuti',
            'ipk' => 'nullable|numeric|min:0|max:4'
        ]);

        $mahasiswa->update($request->except('npm'));
        
        // Update user name
        $mahasiswa->user->update(['name' => $request->nama_lengkap]);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diupdate');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $user = $mahasiswa->user;
        $mahasiswa->delete();
        $user->delete();
        
        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus');
    }

    public function export(Request $request)
    {
        $status = $request->get('status', 'semua');
        $fileName = 'data_mahasiswa_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(new MahasiswaExport($status), $fileName);
    }

    /**
     * Export template Excel untuk import
     */
    public function exportTemplate()
    {
        $fileName = 'template_import_mahasiswa.xlsx';
        
        return Excel::download(new MahasiswaTemplateExport(), $fileName);
    }

    /**
     * Import data mahasiswa dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MahasiswaImport, $request->file('file'));
            return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('admin.mahasiswa.index')->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}
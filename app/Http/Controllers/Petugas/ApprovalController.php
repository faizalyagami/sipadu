<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $surats = Surat::with(['mahasiswa', 'jenisSurat'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('petugas.approval.index', compact('surats'));
    }

    public function approve(Surat $surat)
    {
        $surat->approve(auth()->id());
        
        return redirect()->route('petugas.approval.index')->with('success', 'Surat berhasil disetujui');
    }

    public function reject(Request $request, Surat $surat)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        $surat->update([
            'status' => 'rejected',
            'content' => $surat->content . "\n\nAlasan ditolak: " . $request->reason
        ]);

        return redirect()->route('petugas.approval.index')->with('success', 'Surat ditolak');
    }

    public function history()
    {
        $surats = Surat::with(['mahasiswa', 'jenisSurat'])
            ->where('status', '!=', 'pending')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        
        return view('petugas.approval.history', compact('surats'));
    }
}
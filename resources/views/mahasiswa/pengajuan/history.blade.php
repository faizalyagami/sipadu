@extends('layouts.app')

@section('title', 'Riwayat Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Riwayat Pengajuan Surat</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Surat</th>
                            <th>Keperluan</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surats as $index => $surat)
                        <tr>
                            <td>{{ $surats->firstItem() + $index }}</td>
                            <td>{{ $surat->jenisSurat->nama_surat }}</td>
                            <td>{{ Str::limit($surat->keperluan, 50) }}</td>
                            <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($surat->status == 'pending')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($surat->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info">Detail</a>
                                @if($surat->status == 'approved')
                                    <a href="{{ route('mahasiswa.pengajuan.download', $surat->id) }}" class="btn btn-sm btn-success">Download</a>
                                @endif
                            </td>
                        </table>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada pengajuan surat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $surats->links() }}
        </div>
    </div>
</div>
@endsection
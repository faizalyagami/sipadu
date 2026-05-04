@extends('layouts.app')

@section('title', 'Approve Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Surat Menunggu Persetujuan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Mahasiswa</th>
                            <th>Jenis Surat</th>
                            <th>Keperluan</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surats as $index => $surat)
                        <tr>
                            <td>{{ $surats->firstItem() + $index }}</td>
                            <td>{{ $surat->mahasiswa->nama_lengkap }}<br><small>{{ $surat->mahasiswa->npm }}</small></td>
                            <td>{{ $surat->jenisSurat->nama_surat }}</td>
                            <td>{{ Str::limit($surat->keperluan, 50) }}</td>
                            <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success" onclick="openApproveModal({{ $surat->id }})">Setujui</button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $surat->id }}">Tolak</button>
                            </td>
                        </tr>
                        
                        <!-- Modal Tolak -->
                        <div class="modal fade" id="rejectModal{{ $surat->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('petugas.approval.reject', $surat->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Alasan Penolakan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <textarea name="alasan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada surat yang menunggu persetujuan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $surats->links() }}
        </div>
    </div>
</div>


<div class="modal fade" id="approveConfirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-check-circle me-2"></i>Konfirmasi Persetujuan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-shield-check fs-1 text-success"></i>
                </div>
                <p>Surat akan disetujui dan ditandatangani secara elektronik oleh:</p>
                <ul>
                    <li><strong>Nama</strong> : Dr. Oki Mardiawan, M.Psi., Psikolog.</li>
                    <li><strong>NIP</strong> : D.07.0.464</li>
                    <li><strong>Jabatan</strong> : Wakil Dekan Bidang Pembelajaran dan Kemahasiswaan</li>
                </ul>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tanda tangan elektronik akan otomatis ditambahkan ke surat.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmApproveBtn">Ya, Setujui</button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedSuratId = null;
    
    function openApproveModal(id) {
        selectedSuratId = id;
        $('#approveConfirmModal').modal('show');
    }
    
    $('#confirmApproveBtn').click(function() {
        if (selectedSuratId) {
            window.location.href = `/petugas/approval/${selectedSuratId}/approve`;
        }
    });
</script>
@endsection
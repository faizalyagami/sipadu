{{-- resources/views/admin/mahasiswa/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Mahasiswa')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%); border-radius: 15px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="text-white mb-1">Detail Mahasiswa</h3>
                            <p class="text-white-50 mb-0">Informasi lengkap data mahasiswa</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-white rounded-circle p-3 d-inline-block">
                                <i class="bi bi-person-badge fs-1 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Informasi Pribadi
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="120" class="fw-semibold">NPM</td>
                            <td>: {{ $mahasiswa->npm }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Nama Lengkap</td>
                            <td>: {{ $mahasiswa->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Tempat, Tgl Lahir</td>
                            <td>: {{ $mahasiswa->tempat_lahir }}, {{ $mahasiswa->tanggal_lahir ? date('d F Y', strtotime($mahasiswa->tanggal_lahir)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Jenis Kelamin</td>
                            <td>: {{ $mahasiswa->jenis_kelamin == 'L' ? 'Laki-laki' : ($mahasiswa->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Alamat</td>
                            <td>: {{ $mahasiswa->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">No HP</td>
                            <td>: {{ $mahasiswa->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Email</td>
                            <td>: {{ $mahasiswa->user->email ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0">
                        <i class="bi bi-mortarboard text-primary me-2"></i>Informasi Akademik
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="120" class="fw-semibold">Fakultas</td>
                            <td>: {{ $mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Program Studi</td>
                            <td>: {{ $mahasiswa->prodi->nama_prodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Jenjang</td>
                            <td>: {{ $mahasiswa->prodi->jenjang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Status</td>
                            <td>: 
                                @if($mahasiswa->status_mahasiswa == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif($mahasiswa->status_mahasiswa == 'Cuti')
                                    <span class="badge bg-warning">Cuti</span>
                                @elseif($mahasiswa->status_mahasiswa == 'Lulus')
                                    <span class="badge bg-info">Lulus</span>
                                @else
                                    <span class="badge bg-secondary">{{ $mahasiswa->status_mahasiswa }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Tanggal Masuk</td>
                            <td>: {{ $mahasiswa->tanggal_masuk ? date('d F Y', strtotime($mahasiswa->tanggal_masuk)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">IPK</td>
                            <td>: {{ $mahasiswa->ipk ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">SKS Tempuh</td>
                            <td>: {{ $mahasiswa->sks_tempuh ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0">
                        <i class="bi bi-person-check text-primary me-2"></i>Informasi Dosen Wali
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="120" class="fw-semibold">Nama Dosen Wali</td>
                            <td>: {{ $mahasiswa->dosen_wali ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">NIK Dosen Wali</td>
                            <td>: {{ $mahasiswa->dosen_wali_nik ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Surat Section -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0">
                        <i class="bi bi-file-text text-primary me-2"></i>Riwayat Surat
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Surat</th>
                                    <th>Keperluan</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Status</th>
                                    <th>Tanggal Diproses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mahasiswa->surats as $index => $surat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $surat->jenisSurat->nama_surat ?? '-' }}</td>
                                    <td>{{ Str::limit($surat->keperluan, 50) }}</td>
                                    <td>{{ $surat->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($surat->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($surat->status == 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-danger">Ditolak</span>
                                        @endif
                                    </td>
                                    <td>{{ $surat->approved_at ? $surat->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                                        Belum ada riwayat surat
                                    <tr>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div>
                    <a href="{{ route('admin.mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Data
                    </a>
                    <button class="btn btn-danger delete-mahasiswa" data-id="{{ $mahasiswa->id }}">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Delete mahasiswa
    $('.delete-mahasiswa').click(function() {
        let id = $(this).data('id');
        confirmDelete(`/admin/mahasiswa/${id}`, 'Yakin hapus mahasiswa ini?');
    });
</script>
@endpush
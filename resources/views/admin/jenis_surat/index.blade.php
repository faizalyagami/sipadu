@extends('layouts.app')

@section('title', 'Jenis Surat')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4"
                        style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%); border-radius: 16px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-white mb-1 fw-bold">
                                    <i class="bi bi-file-text me-2"></i>Jenis Surat
                                </h3>
                                <p class="text-white-50 mb-0">Kelola jenis dan template surat</p>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-file-earmark me-1"></i>
                                    {{ $jenisSurats->count() }} Jenis Surat
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Section -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0">Daftar Jenis Surat</h5>
                <p class="text-muted small mb-0">Total {{ $jenisSurats->count() }} jenis surat</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJenisSuratModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Jenis Surat
            </button>
        </div>

        <!-- Tabel Jenis Surat -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="jenisSuratTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama Surat</th>
                                <th>Kategori</th>
                                <th>Variabel</th>
                                <th class="text-center">Template</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jenisSurats as $index => $jenis)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-text text-primary"></i>
                                            <strong>{{ $jenis->nama_surat }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($jenis->kategoriSurat)
                                            <span class="badge"
                                                style="background-color: {{ $jenis->kategoriSurat->warna ?? '#6f42c1' }};">
                                                {{ $jenis->kategoriSurat->nama_kategori }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $vars = $jenis->getVariableFields();
                                        @endphp
                                        @if (count($vars) > 0)
                                            <button class="btn btn-sm btn-info"
                                                onclick="showVariables({{ $jenis->id }})">
                                                {{ count($vars) }} Variabel
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($jenis->template_content)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Ada
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle me-1"></i>Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $jenis->is_active ? 'success' : 'danger' }}">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>
                                            {{ $jenis->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-center flex-wrap">
                                            <button class="btn btn-sm btn-outline-info edit-template"
                                                data-id="{{ $jenis->id }}" data-nama="{{ $jenis->nama_surat }}"
                                                data-template="{{ $jenis->template_content }}"
                                                data-logo="{{ $jenis->logo_path }}"
                                                data-kop="{{ $jenis->kop_surat_path }}" title="Edit Template">
                                                <i class="bi bi-file-text"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning edit-jenis"
                                                data-id="{{ $jenis->id }}" data-nama="{{ $jenis->nama_surat }}"
                                                data-kategori="{{ $jenis->kategori_surat_id }}" title="Edit Jenis Surat">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('admin.jenis-surat.destroy', $jenis->id) }}"
                                                method="POST" class="d-inline delete-form"
                                                onsubmit="return confirmDeleteJenisSurat(this, '{{ $jenis->nama_surat }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Jenis Surat">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">Belum ada jenis surat</p>
                                        <p class="text-muted small">Klik tombol "Tambah Jenis Surat" untuk menambahkan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL TAMBAH JENIS SURAT -->
    <!-- ============================================ -->
    <div class="modal fade" id="addJenisSuratModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.jenis-surat.store') }}" method="POST" id="formAddJenisSurat">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Jenis Surat
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nama_surat" class="form-control"
                                placeholder="Contoh: Surat Keterangan Aktif Kuliah" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_surat_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active"
                                    value="1" checked>
                                <label class="form-check-label fw-bold" for="add_is_active">
                                    Aktifkan Jenis Surat
                                </label>
                            </div>
                            <small class="text-muted">Nonaktifkan untuk menyembunyikan jenis surat</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL EDIT JENIS SURAT -->
    <!-- ============================================ -->
    <div class="modal fade" id="editJenisSuratModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editJenisSuratForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>Edit Jenis Surat
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nama_surat" id="edit_nama_surat" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori_surat_id" id="edit_kategori_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active"
                                    value="1">
                                <label class="form-check-label fw-bold" for="edit_is_active">
                                    Aktifkan Jenis Surat
                                </label>
                            </div>
                            <small class="text-muted">Nonaktifkan untuk menyembunyikan jenis surat</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL EDIT TEMPLATE (Fullscreen) -->
    <!-- ============================================ -->
    <div class="modal fade" id="editTemplateModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <form id="editTemplateForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header" style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                        <h5 class="modal-title text-white">
                            <i class="bi bi-file-text me-2"></i>
                            Edit Template Surat: <span id="template_nama_surat" class="fw-bold"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="row g-0 h-100">
                            <!-- Sidebar Kiri -->
                            <div class="col-md-2 bg-light p-3"
                                style="border-right: 1px solid #dee2e6; height: calc(100vh - 130px); overflow-y: auto;">

                                <!-- Upload Logo -->
                                <div class="mb-4">
                                    <label class="fw-bold mb-2"><i class="bi bi-image"></i> Logo Universitas</label>
                                    <div class="border rounded p-2 text-center bg-white">
                                        <div id="logoPreview" class="mb-2">
                                            <img id="logoPreviewImg" src=""
                                                style="max-width: 80px; max-height: 80px; display: none;">
                                            <div id="logoPlaceholder" class="text-muted">
                                                <i class="bi bi-building fs-1"></i>
                                                <p class="small mb-0">Belum ada logo</p>
                                            </div>
                                        </div>
                                        <input type="file" name="logo" id="logoInput"
                                            class="form-control form-control-sm" accept="image/*">
                                        <small class="text-muted">PNG, JPG (Max 2MB)</small>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100"
                                            id="btnRemoveLogo" style="display: none;">
                                            <i class="bi bi-trash"></i> Hapus Logo
                                        </button>
                                    </div>
                                </div>

                                <!-- Upload Kop Surat -->
                                <div class="mb-4">
                                    <label class="fw-bold mb-2"><i class="bi bi-file-image"></i> Gambar Kop Surat</label>
                                    <div class="border rounded p-2 text-center bg-white">
                                        <div id="kopPreview" class="mb-2">
                                            <img id="kopPreviewImg" src=""
                                                style="max-width: 100%; max-height: 100px; display: none;">
                                            <div id="kopPlaceholder" class="text-muted">
                                                <i class="bi bi-card-image fs-1"></i>
                                                <p class="small mb-0">Belum ada kop</p>
                                            </div>
                                        </div>
                                        <input type="file" name="kop_surat" id="kopInput"
                                            class="form-control form-control-sm" accept="image/*">
                                        <div class="mt-2" id="kopSizeControl" style="display: none;">
                                            <label class="form-label small">Ukuran Gambar Kop</label>
                                            <input type="range" id="kopWidthSlider" class="form-range" min="30"
                                                max="100" value="80">
                                            <div class="d-flex justify-content-between small text-muted">
                                                <span>Kecil</span>
                                                <span>Normal</span>
                                                <span>Besar</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100"
                                            id="btnRemoveKop" style="display: none;">
                                            <i class="bi bi-trash"></i> Hapus Kop Surat
                                        </button>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm btn-primary w-100 mb-3" id="btnInsertKop">
                                    <i class="bi bi-image"></i> Sisipkan Kop Surat
                                </button>

                                <input type="hidden" name="logo_path" id="logo_path">
                                <input type="hidden" name="kop_surat_path" id="kop_surat_path">

                                <hr>

                                <!-- Variabel -->
                                <h6 class="fw-bold mb-2"><i class="bi bi-tags"></i> Variabel Tersedia</h6>
                                <p class="text-muted small">Klik untuk menyisipkan</p>

                                <div class="mb-3">
                                    <label class="fw-bold small text-primary">Data Mahasiswa</label>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{nama_mahasiswa}">
                                        <code>{nama_mahasiswa}</code> - Nama Lengkap
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{npm}">
                                        <code>{npm}</code> - NPM
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{alamat}">
                                        <code>{alamat}</code> - Alamat
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small text-danger">Data Orangtua Mahasiswa</label>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{nama_orangtua}">
                                        <code>{nama_orangtua}</code> - Nama Orangtua
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{nrp_nik_nip}">
                                        <code>{nrp_nik_nip}</code> - NRP/NIK/NIP
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{pangkat_orangtua}">
                                        <code>{pangkat_orangtua}</code> - Pangkat/Golongan
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{instansi_orangtua}">
                                        <code>{instansi_orangtua}</code> - Instansi
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{alamat_kantor}">
                                        <code>{alamat_kantor}</code> - Alamat Kantor
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small text-success">Data Akademik</label>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{fakultas}">
                                        <code>{fakultas}</code> - Fakultas
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{prodi}">
                                        <code>{prodi}</code> - Program Studi
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{semester}">
                                        <code>{semester}</code> - Semester
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small text-warning">Data Surat</label>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{nomor_surat}">
                                        <code>{nomor_surat}</code> - Nomor Surat
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{tanggal_surat}">
                                        <code>{tanggal_surat}</code> - Tanggal Surat
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{perihal}">
                                        <code>{perihal}</code> - Perihal
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold small text-danger">Data Tanda Tangan</label>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{dekan}">
                                        <code>{dekan}</code> - Nama Dekan
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable"
                                        data-var="{nip_dekan}">
                                        <code>{nip_dekan}</code> - NIP Dekan
                                    </button>
                                </div>

                                <hr>

                                <!-- Format Paragraf -->
                                <h6 class="fw-bold mb-2">
                                    <i class="bi bi-text-paragraph"></i> Format Paragraf
                                </h6>
                                <div class="card mb-3">
                                    <div class="card-body p-2">
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Left Margin (px)</label>
                                            <input type="number" id="leftMargin" class="form-control form-control-sm"
                                                value="0">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Left Indent (px)</label>
                                            <input type="number" id="leftIndent" class="form-control form-control-sm"
                                                value="0">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">First Line Indent (px)</label>
                                            <input type="number" id="firstLineIndent"
                                                class="form-control form-control-sm" value="48">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-bold">Line Spacing</label>
                                            <select id="lineSpacing" class="form-select form-select-sm">
                                                <option value="1">1.0</option>
                                                <option value="1.15">1.15</option>
                                                <option value="1.5" selected>1.5</option>
                                                <option value="2">2.0</option>
                                                <option value="2.5">2.5</option>
                                                <option value="3">3.0</option>
                                            </select>
                                        </div>
                                        <div class="d-grid gap-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-primary" id="applyMarginBtn">
                                                <i class="bi bi-layout-text-window"></i> Terapkan Margin ke Template
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                id="formatSuratResmi">
                                                <i class="bi bi-file-earmark-text"></i> Format Surat Resmi
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success"
                                                id="applyParagraphStyle">
                                                <i class="bi bi-check-circle"></i> Terapkan ke Paragraf
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Editor Area -->
                            <div class="col-md-10 p-0 d-flex flex-column">
                                <textarea id="templateEditor" name="template_content" style="width:100%; height:600px;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="bi bi-x-circle me-1"></i> Batal</button>
                        <button type="button" class="btn btn-info" id="previewBtn"><i class="bi bi-eye me-1"></i>
                            Preview</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan
                            Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MODAL PREVIEW -->
    <!-- ============================================ -->
    <div class="modal fade" id="previewModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #6f42c1, #8b5cf6);">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-eye me-2"></i>Preview Template Surat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background:#e5e5e5; overflow:auto;">
                    <div id="previewPaper"
                        style="width:210mm; min-height:297mm; margin:0 auto; background:white; padding:0; box-shadow:0 0 10px rgba(0,0,0,0.1);">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="printPreviewBtn"><i
                            class="bi bi-printer me-1"></i> Print</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table> :not(caption)>*>* {
            vertical-align: middle;
        }

        .cke_top {
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding: 8px !important;
        }

        .cke_editable {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 12pt !important;
            padding: 20mm !important;
            background: white !important;
            min-height: 500px;
        }

        .insert-variable {
            text-align: left;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .insert-variable:hover {
            background-color: #6f42c1 !important;
            color: white !important;
            border-color: #6f42c1 !important;
        }

        .insert-variable code {
            background: #f8f9fa;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 11px;
        }

        .insert-variable:hover code {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        #leftMargin,
        #leftIndent,
        #firstLineIndent,
        #lineSpacing {
            font-size: 12px;
        }

        .card .form-label.small {
            margin-bottom: 3px;
        }

        .card .btn-sm {
            font-size: 12px;
        }

        /* Preview paper - TANPA padding agar full */
        #previewPaper {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
        }

        #previewPaper table {
            border-collapse: collapse;
            width: 100%;
        }

        #previewPaper table td {
            padding: 4px 0;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #previewPaper,
            #previewPaper * {
                visibility: visible;
            }

            #previewPaper {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="//cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
            let editor = null;
            let currentJenisId = null;
            let currentKopUrl = null;

            // ============================================
            // DELETE JENIS SURAT - Confirm Function
            // ============================================
            window.confirmDeleteJenisSurat = function(form, nama) {
                Swal.fire({
                    title: 'Apakah anda yakin?',
                    html: `Jenis surat <strong>"${nama}"</strong> akan dihapus permanen!<br>
                       <small class="text-danger">Data template surat juga akan terhapus.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            };

            // ============================================
            // INIT DATATABLE
            // ============================================
            if (document.getElementById('jenisSuratTable')) {
                try {
                    if ($.fn.DataTable.isDataTable('#jenisSuratTable')) {
                        $('#jenisSuratTable').DataTable().destroy();
                    }
                    $('#jenisSuratTable').DataTable({
                        responsive: true,
                        language: {
                            "sProcessing": "Sedang memproses...",
                            "sLengthMenu": "Tampilkan _MENU_ data",
                            "sZeroRecords": "Tidak ada data yang ditemukan",
                            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                            "sInfoFiltered": "(difilter dari _MAX_ total data)",
                            "sSearch": "Cari:",
                            "sEmptyTable": "Tidak ada data tersedia",
                            "oPaginate": {
                                "sFirst": "Pertama",
                                "sPrevious": "Sebelumnya",
                                "sNext": "Selanjutnya",
                                "sLast": "Terakhir"
                            }
                        },
                        pageLength: 10,
                        columnDefs: [{
                            orderable: false,
                            targets: [3, 4, 5, 6]
                        }],
                        order: [
                            [0, 'asc']
                        ]
                    });
                } catch (e) {
                    console.log('DataTable error:', e);
                }
            }

            // ============================================
            // EDIT JENIS SURAT
            // ============================================
            document.querySelectorAll('.edit-jenis').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nama = this.dataset.nama;
                    const kategoriId = this.dataset.kategori;

                    const editNamaSurat = document.getElementById('edit_nama_surat');
                    const editKategoriId = document.getElementById('edit_kategori_id');
                    const editIsActive = document.getElementById('edit_is_active');

                    if (editNamaSurat) editNamaSurat.value = nama;
                    if (editKategoriId) editKategoriId.value = kategoriId;
                    if (editIsActive) editIsActive.checked = true;

                    const editForm = document.getElementById('editJenisSuratForm');
                    if (editForm) {
                        editForm.action = `/admin/jenis-surat/${id}`;
                    }

                    $('#editJenisSuratModal').modal('show');
                });
            });

            // ============================================
            // FUNGSI: Ambil Format dari Form
            // ============================================
            function getFormatFromForm() {
                return {
                    left_margin: parseInt(document.getElementById('leftMargin')?.value) || 0,
                    left_indent: parseInt(document.getElementById('leftIndent')?.value) || 0,
                    first_line_indent: parseInt(document.getElementById('firstLineIndent')?.value) || 48,
                    line_spacing: parseFloat(document.getElementById('lineSpacing')?.value) || 1.5,
                };
            }

            // ============================================
            // EDIT TEMPLATE - Event Delegation
            // ============================================
            $(document).on('click', '.edit-template', function(e) {
                e.preventDefault();

                currentJenisId = this.dataset.id;
                const nama = this.dataset.nama;
                const template = this.dataset.template || getDefaultTemplate();
                const logoPath = this.dataset.logo;
                const kopPath = this.dataset.kop;

                // Clean template untuk editor
                let cleanTemplate = template;

                // Reset form
                const logoPathInput = document.getElementById('logo_path');
                const kopPathInput = document.getElementById('kop_surat_path');
                const logoInput = document.getElementById('logoInput');
                const kopInput = document.getElementById('kopInput');
                const btnRemoveLogo = document.getElementById('btnRemoveLogo');
                const btnRemoveKop = document.getElementById('btnRemoveKop');
                const kopSizeControl = document.getElementById('kopSizeControl');

                if (logoPathInput) logoPathInput.value = '';
                if (kopPathInput) kopPathInput.value = '';
                if (logoInput) logoInput.value = '';
                if (kopInput) kopInput.value = '';
                if (btnRemoveLogo) btnRemoveLogo.style.display = 'none';
                if (btnRemoveKop) btnRemoveKop.style.display = 'none';
                if (kopSizeControl) kopSizeControl.style.display = 'none';

                // Set preview logo
                const logoPreviewImg = document.getElementById('logoPreviewImg');
                const logoPlaceholder = document.getElementById('logoPlaceholder');

                if (logoPath && logoPath !== 'null' && logoPath !== '') {
                    const logoUrl = logoPath.startsWith('http') ? logoPath : '/storage/' + logoPath;
                    if (logoPreviewImg) {
                        logoPreviewImg.src = logoUrl;
                        logoPreviewImg.style.display = 'block';
                    }
                    if (logoPlaceholder) logoPlaceholder.style.display = 'none';
                    if (logoPathInput) logoPathInput.value = logoPath;
                    if (btnRemoveLogo) btnRemoveLogo.style.display = 'block';
                } else {
                    if (logoPreviewImg) logoPreviewImg.style.display = 'none';
                    if (logoPlaceholder) logoPlaceholder.style.display = 'block';
                    if (btnRemoveLogo) btnRemoveLogo.style.display = 'none';
                }

                // Set preview kop
                const kopPreviewImg = document.getElementById('kopPreviewImg');
                const kopPlaceholder = document.getElementById('kopPlaceholder');

                if (kopPath && kopPath !== 'null' && kopPath !== '') {
                    currentKopUrl = kopPath.startsWith('http') ? kopPath : '/storage/' + kopPath;
                    if (kopPreviewImg) {
                        kopPreviewImg.src = currentKopUrl;
                        kopPreviewImg.style.display = 'block';
                    }
                    if (kopPlaceholder) kopPlaceholder.style.display = 'none';
                    if (kopSizeControl) kopSizeControl.style.display = 'block';
                    if (kopPathInput) kopPathInput.value = kopPath;
                    if (btnRemoveKop) btnRemoveKop.style.display = 'block';
                } else {
                    currentKopUrl = null;
                    if (kopPreviewImg) kopPreviewImg.style.display = 'none';
                    if (kopPlaceholder) kopPlaceholder.style.display = 'block';
                    if (kopSizeControl) kopSizeControl.style.display = 'none';
                    if (btnRemoveKop) btnRemoveKop.style.display = 'none';
                }

                const templateNama = document.getElementById('template_nama_surat');
                if (templateNama) templateNama.textContent = nama;

                const editForm = document.getElementById('editTemplateForm');
                if (editForm) {
                    editForm.action = `/admin/jenis-surat/${currentJenisId}/template`;
                }

                // Destroy existing CKEditor
                if (CKEDITOR.instances.templateEditor) {
                    CKEDITOR.instances.templateEditor.destroy();
                }

                // Initialize CKEditor
                initCKEditor(cleanTemplate);

                $('#editTemplateModal').modal('show');
            });

            // ============================================
            // REMOVE LOGO
            // ============================================
            const btnRemoveLogo = document.getElementById('btnRemoveLogo');
            if (btnRemoveLogo) {
                btnRemoveLogo.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Hapus Logo?',
                        text: 'Logo akan dihapus dari template',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const logoPreviewImg = document.getElementById('logoPreviewImg');
                            const logoPlaceholder = document.getElementById('logoPlaceholder');
                            const logoPathInput = document.getElementById('logo_path');
                            const btnRemoveLogo = document.getElementById('btnRemoveLogo');

                            if (logoPreviewImg) logoPreviewImg.style.display = 'none';
                            if (logoPlaceholder) logoPlaceholder.style.display = 'block';
                            if (logoPathInput) logoPathInput.value = '';
                            if (btnRemoveLogo) btnRemoveLogo.style.display = 'none';
                            Swal.fire('Terhapus!', 'Logo telah dihapus', 'success');
                        }
                    });
                });
            }

            // ============================================
            // REMOVE KOP
            // ============================================
            const btnRemoveKop = document.getElementById('btnRemoveKop');
            if (btnRemoveKop) {
                btnRemoveKop.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Hapus Kop Surat?',
                        text: 'Kop surat akan dihapus dari template',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const kopPreviewImg = document.getElementById('kopPreviewImg');
                            const kopPlaceholder = document.getElementById('kopPlaceholder');
                            const kopPathInput = document.getElementById('kop_surat_path');
                            const kopSizeControl = document.getElementById('kopSizeControl');
                            const btnRemoveKop = document.getElementById('btnRemoveKop');

                            if (kopPreviewImg) kopPreviewImg.style.display = 'none';
                            if (kopPlaceholder) kopPlaceholder.style.display = 'block';
                            if (kopPathInput) kopPathInput.value = '';
                            if (kopSizeControl) kopSizeControl.style.display = 'none';
                            if (btnRemoveKop) btnRemoveKop.style.display = 'none';
                            currentKopUrl = null;
                            Swal.fire('Terhapus!', 'Kop surat telah dihapus', 'success');
                        }
                    });
                });
            }

            // ============================================
            // INSERT KOP - Sisipkan {kop_surat}
            // ============================================
            const btnInsertKop = document.getElementById('btnInsertKop');
            if (btnInsertKop) {
                btnInsertKop.addEventListener('click', function() {
                    if (!editor) {
                        Swal.fire('Error', 'Editor belum siap', 'error');
                        return;
                    }

                    editor.insertText('{kop_surat}');
                    editor.focus();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Placeholder {kop_surat} berhasil disisipkan',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            }

            // ============================================
            // UPLOAD LOGO
            // ============================================
            const logoInput = document.getElementById('logoInput');
            if (logoInput) {
                logoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
                        this.value = '';
                        return;
                    }

                    const formData = new FormData();
                    formData.append('logo', file);
                    formData.append('_token', csrfToken);

                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('/admin/upload-logo', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                const logoPreviewImg = document.getElementById('logoPreviewImg');
                                const logoPlaceholder = document.getElementById('logoPlaceholder');
                                const logoPathInput = document.getElementById('logo_path');
                                const btnRemoveLogo = document.getElementById('btnRemoveLogo');

                                if (logoPreviewImg) {
                                    logoPreviewImg.src = data.url;
                                    logoPreviewImg.style.display = 'block';
                                }
                                if (logoPlaceholder) logoPlaceholder.style.display = 'none';
                                if (logoPathInput) logoPathInput.value = data.path;
                                if (btnRemoveLogo) btnRemoveLogo.style.display = 'block';
                                Swal.fire('Sukses', 'Logo berhasil diupload', 'success');
                            }
                        })
                        .catch(() => {
                            Swal.close();
                            Swal.fire('Error', 'Gagal upload logo', 'error');
                            document.getElementById('logoInput').value = '';
                        });
                });
            }

            // ============================================
            // UPLOAD KOP
            // ============================================
            const kopInput = document.getElementById('kopInput');
            if (kopInput) {
                kopInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
                        this.value = '';
                        return;
                    }

                    const formData = new FormData();
                    formData.append('kop_surat', file);
                    formData.append('_token', csrfToken);

                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('/admin/upload-kop', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                currentKopUrl = data.url;
                                const kopPreviewImg = document.getElementById('kopPreviewImg');
                                const kopPlaceholder = document.getElementById('kopPlaceholder');
                                const kopSizeControl = document.getElementById('kopSizeControl');
                                const kopPathInput = document.getElementById('kop_surat_path');
                                const btnRemoveKop = document.getElementById('btnRemoveKop');

                                if (kopPreviewImg) {
                                    kopPreviewImg.src = currentKopUrl;
                                    kopPreviewImg.style.display = 'block';
                                }
                                if (kopPlaceholder) kopPlaceholder.style.display = 'none';
                                if (kopSizeControl) kopSizeControl.style.display = 'block';
                                if (kopPathInput) kopPathInput.value = data.path;
                                if (btnRemoveKop) btnRemoveKop.style.display = 'block';
                                Swal.fire('Sukses', 'Kop surat berhasil diupload', 'success');
                            }
                        })
                        .catch(() => {
                            Swal.close();
                            Swal.fire('Error', 'Gagal upload kop surat', 'error');
                            document.getElementById('kopInput').value = '';
                        });
                });
            }

            // ============================================
            // KOP WIDTH SLIDER
            // ============================================
            const kopWidthSlider = document.getElementById('kopWidthSlider');
            if (kopWidthSlider) {
                kopWidthSlider.addEventListener('input', function() {
                    const width = this.value;
                    const kopPreviewImg = document.getElementById('kopPreviewImg');
                    if (kopPreviewImg) {
                        kopPreviewImg.style.width = width + '%';
                    }
                });
            }

            // ============================================
            // INSERT VARIABLE
            // ============================================
            document.querySelectorAll('.insert-variable').forEach(button => {
                button.addEventListener('click', function() {
                    const variable = this.dataset.var;
                    if (editor) {
                        editor.insertText(variable);
                        editor.focus();
                    }
                });
            });

            // ============================================
            // FORMAT PRESET - Surat Resmi
            // ============================================
            const formatSuratResmi = document.getElementById('formatSuratResmi');
            if (formatSuratResmi) {
                formatSuratResmi.addEventListener('click', function() {
                    document.getElementById('leftMargin').value = 0;
                    document.getElementById('leftIndent').value = 0;
                    document.getElementById('firstLineIndent').value = 48;
                    document.getElementById('lineSpacing').value = 1.5;

                    Swal.fire({
                        icon: 'success',
                        title: 'Preset diterapkan',
                        text: 'Format surat resmi berhasil dimuat',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            }

            // ============================================
            // APPLY PARAGRAPH STYLE
            // ============================================
            const applyParagraphStyle = document.getElementById('applyParagraphStyle');
            if (applyParagraphStyle) {
                applyParagraphStyle.addEventListener('click', function() {
                    if (!editor) {
                        Swal.fire('Error', 'Editor belum siap', 'error');
                        return;
                    }

                    const leftMargin = document.getElementById('leftMargin');
                    const leftIndent = document.getElementById('leftIndent');
                    const firstLineIndent = document.getElementById('firstLineIndent');
                    const lineSpacing = document.getElementById('lineSpacing');

                    const margin = leftMargin ? parseInt(leftMargin.value) || 0 : 0;
                    const indent = leftIndent ? parseInt(leftIndent.value) || 0 : 0;
                    const firstIndent = firstLineIndent ? parseInt(firstLineIndent.value) || 0 : 48;
                    const spacing = lineSpacing ? parseFloat(lineSpacing.value) || 1.5 : 1.5;

                    editor.focus();

                    const selection = editor.getSelection();
                    if (!selection) {
                        Swal.fire('Info', 'Pilih paragraf terlebih dahulu', 'info');
                        return;
                    }

                    let element = selection.getStartElement();
                    if (!element) {
                        Swal.fire('Info', 'Letakkan cursor di dalam paragraf', 'info');
                        return;
                    }

                    let paragraph = element.getAscendant('p', true);
                    if (!paragraph) {
                        paragraph = element.getAscendant('div', true);
                        if (!paragraph) {
                            Swal.fire('Info', 'Letakkan cursor pada paragraf yang ingin diformat', 'info');
                            return;
                        }
                    }

                    // ============================================
                    // TERAPKAN STYLE KE CKEDITOR
                    // ============================================
                    paragraph.setStyle('margin-left', margin + 'px');
                    paragraph.setStyle('padding-left', indent + 'px');
                    paragraph.setStyle('text-indent', firstIndent + 'px');
                    paragraph.setStyle('line-height', spacing);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Format paragraf berhasil diterapkan ke CKEditor',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            }
            // ============================================
            // TERAPKAN MARGIN KE TEMPLATE
            // ============================================
            document.getElementById('applyMarginBtn')?.addEventListener('click', function() {
                if (!editor) {
                    Swal.fire('Error', 'Editor belum siap', 'error');
                    return;
                }

                const marginTop = document.getElementById('marginTop')?.value || 20;
                const marginBottom = document.getElementById('marginBottom')?.value || 20;
                const marginLeft = document.getElementById('marginLeft')?.value || 20;
                const marginRight = document.getElementById('marginRight')?.value || 20;

                // Ambil semua konten editor
                let content = editor.getData();

                // Cek apakah sudah ada wrapper dengan class 'surat-wrapper'
                const wrapperRegex = /<div class="surat-wrapper"[^>]*>([\s\S]*?)<\/div>/;
                const match = content.match(wrapperRegex);

                if (match) {
                    // Update wrapper yang sudah ada
                    const newWrapper =
                        `<div class="surat-wrapper" style="padding: ${marginTop}mm ${marginRight}mm ${marginBottom}mm ${marginLeft}mm; max-width: 210mm; margin: 0 auto;">${match[1]}</div>`;
                    content = content.replace(wrapperRegex, newWrapper);
                } else {
                    // Bungkus dengan wrapper baru
                    content =
                        `<div class="surat-wrapper" style="padding: ${marginTop}mm ${marginRight}mm ${marginBottom}mm ${marginLeft}mm; max-width: 210mm; margin: 0 auto;">${content}</div>`;
                }

                // Set ke editor
                editor.setData(content);

                Swal.fire({
                    icon: 'success',
                    title: 'Margin Diterapkan',
                    text: `Margin: Top ${marginTop}mm, Bottom ${marginBottom}mm, Left ${marginLeft}mm, Right ${marginRight}mm`,
                    timer: 2000,
                    showConfirmButton: false
                });
            });
            // ============================================
            // PREVIEW
            // ============================================
            const previewBtn = document.getElementById('previewBtn');
            if (previewBtn) {
                previewBtn.addEventListener('click', function() {
                    if (!editor) {
                        Swal.fire('Error', 'Editor belum siap', 'error');
                        return;
                    }

                    let content = editor.getData();

                    // ============================================
                    // GANTI VARIABEL DENGAN DATA PREVIEW
                    // ============================================
                    const previewData = {
                        nama_mahasiswa: 'Nuni Lestari',
                        npm: '10050022094',
                        alamat: 'Jl. Dederuk No. 21, Bandung',
                        fakultas: 'Psikologi',
                        prodi: 'Psikologi S1',
                        semester: 'VIII',
                        nomor_surat: '083/M.10/Dek.Psi-k/IV/2026',
                        tanggal_surat: '29 April 2026',
                        perihal: 'SURAT KETERANGAN AKTIF KULIAH',
                        dekan: 'Dr. Oki Mardiawan, M.Psi., Psikolog.',
                        nip_dekan: 'D.07.0.464',
                        nama_orangtua: 'H. M. Nurdin',
                        nrp_nik_nip: '32104330107920069',
                        pangkat_orangtua: 'Golongan VII',
                        instansi_orangtua: 'TNI',
                        alamat_kantor: 'Bandung',
                    };

                    // Ganti {kop_surat} dengan preview placeholder
                    const hasKopSurat = content.includes('{kop_surat}');
                    if (hasKopSurat) {
                        content = content.replace(
                            /\{kop_surat\}/g,
                            '<div style="text-align:center; padding:15px 0; background:#f5f0ff; border:2px dashed #6f42c1; margin:0; font-size:16pt; font-weight:bold; color:#6f42c1; width:100%;">[KOP SURAT - PREVIEW]</div>'
                        );
                    }

                    // Replace semua variabel
                    for (const key in previewData) {
                        const regex = new RegExp(`\\{${key}\\}`, 'g');
                        content = content.replace(regex, previewData[key]);
                    }

                    // ============================================
                    // TAMPILKAN DI PREVIEW PAPER
                    // ============================================
                    const previewHtml = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Preview Surat</title>
            <style>
                @page { size: A4; margin: 0; }
                body { font-family: "Times New Roman", Times, serif; font-size: 12pt; background: white; margin: 0; padding: 0; }
                .kop-surat { margin: 0; padding: 0; width: 100%; }
                .kop-surat img { width: 100%; max-width: 100%; height: auto; display: block; margin: 0; padding: 0; }
            </style>
        </head>
        <body>
            <div class="kop-surat">
                ${hasKopSurat ? previewData.kop_surat : ''}
            </div>
            ${content}
        </body>
        </html>
        `;

                    document.getElementById('previewPaper').innerHTML = previewHtml;
                    $('#previewModal').modal('show');
                });
            }

            // ============================================
            // PRINT PREVIEW
            // ============================================
            const printPreviewBtn = document.getElementById('printPreviewBtn');
            if (printPreviewBtn) {
                printPreviewBtn.addEventListener('click', function() {
                    const previewPaper = document.getElementById('previewPaper');
                    if (!previewPaper) return;

                    const printContent = previewPaper.innerHTML;
                    const win = window.open('', '_blank');
                    if (win) {
                        win.document.write(`<html><head><title>Print Surat</title>
                            <style>
                                @page { size: A4; margin: 15mm; }
                                body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.6; }
                                table { border-collapse: collapse; width: 100%; }
                                table td { padding: 4px 0; }
                            </style>
                        </head><body>${printContent}</body></html>`);
                        win.document.close();
                        win.print();
                    }
                });
            }

            // ============================================
            // CKEDITOR INIT
            // ============================================
            function initCKEditor(content) {
                const templateEditor = document.getElementById('templateEditor');
                if (!templateEditor) return;

                if (typeof CKEDITOR === 'undefined') {
                    console.error('CKEditor tidak ditemukan!');
                    templateEditor.innerHTML = `
                        <textarea class="form-control" style="height:600px; font-family: 'Times New Roman', Times, serif;">${content || getDefaultTemplate()}</textarea>
                    `;
                    return;
                }

                if (CKEDITOR.instances.templateEditor) {
                    CKEDITOR.instances.templateEditor.destroy();
                }

                CKEDITOR.replace('templateEditor', {
                    toolbar: [{
                            name: 'document',
                            items: ['Source', '-', 'Save', 'NewPage', 'Print', '-', 'Templates']
                        },
                        {
                            name: 'clipboard',
                            items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo',
                                'Redo'
                            ]
                        },
                        {
                            name: 'editing',
                            items: ['Find', 'Replace', '-', 'SelectAll', '-', 'SpellChecker', 'Scayt']
                        },
                        {
                            name: 'forms',
                            items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select',
                                'Button', 'ImageButton', 'HiddenField'
                            ]
                        },
                        '/',
                        {
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript',
                                '-', 'RemoveFormat'
                            ]
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-',
                                'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter',
                                'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'
                            ]
                        },
                        {
                            name: 'links',
                            items: ['Link', 'Unlink', 'Anchor']
                        },
                        {
                            name: 'insert',
                            items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley',
                                'SpecialChar', 'PageBreak', 'Iframe'
                            ]
                        },
                        '/',
                        {
                            name: 'styles',
                            items: ['Styles', 'Format', 'Font', 'FontSize']
                        },
                        {
                            name: 'colors',
                            items: ['TextColor', 'BGColor']
                        },
                        {
                            name: 'tools',
                            items: ['Maximize', 'ShowBlocks']
                        }
                    ],
                    height: 'calc(100vh - 200px)',
                    width: '100%',
                    language: 'id',
                    enterMode: CKEDITOR.ENTER_P,
                    shiftEnterMode: CKEDITOR.ENTER_BR,
                    filebrowserImageUploadUrl: '{{ route('upload.image') }}',
                    filebrowserUploadUrl: '{{ route('upload.image') }}',
                    allowedContent: true,
                    extraAllowedContent: '*[*]'
                });

                CKEDITOR.instances.templateEditor.on('instanceReady', function() {
                    editor = CKEDITOR.instances.templateEditor;
                    editor.setData(content || getDefaultTemplate());
                });

                CKEDITOR.instances.templateEditor.on('error', function() {
                    templateEditor.innerHTML = `
                        <textarea class="form-control" style="height:600px; font-family: 'Times New Roman', Times, serif;">${content || getDefaultTemplate()}</textarea>
                    `;
                });
            }

            // ============================================
            // DEFAULT TEMPLATE
            // ============================================
            function getDefaultTemplate() {
                return `<div style="font-family:'Times New Roman', Times, serif; font-size:12pt;">
                    <div style="text-align:center; margin-bottom:20px;">
                        <img src="{kop_surat}" style="width:80%; max-width:100%; height:auto;">
                    </div>
                    <div style="text-align:center; margin:20px 0;">
                        <strong style="font-size:14pt;">SURAT KETERANGAN AKTIF KULIAH</strong><br>
                        <strong>Nomor : {nomor_surat}</strong>
                    </div>
                    <div style="text-align:justify;">
                        <p>Yang bertanda tangan di bawah ini:</p>
                        <table style="width:100%; border:none;">
                            <tr><td style="width:120px;">Nama</td><td>: {dekan}</td></tr>
                            <tr><td>NIP</td><td>: {nip_dekan}</td></tr>
                            <tr><td>Jabatan</td><td>: Dekan Fakultas</td></tr>
                        </table>
                        <p>Menerangkan bahwa mahasiswa:</p>
                        <table style="width:100%; border:none;">
                            <tr><td style="width:120px;">Nama</td><td>: {nama_mahasiswa}</td></tr>
                            <tr><td>NPM</td><td>: {npm}</td></tr>
                            <tr><td>Fakultas</td><td>: {fakultas}</td></tr>
                            <tr><td>Program Studi</td><td>: {prodi}</td></tr>
                            <tr><td>Semester</td><td>: {semester}</td></tr>
                            <tr><td>Alamat</td><td>: {alamat}</td></tr>
                        </table>
                        <p>Adalah benar-benar mahasiswa aktif Universitas pada semester yang tertera.</p>
                        <p>Surat keterangan ini dibuat untuk memenuhi persyaratan administrasi.</p>
                        <p>Demikian surat ini dibuat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.</p>
                    </div>
                    <div style="margin-top:50px; text-align:right;">
                        <p>Bandung, {tanggal_surat}</p>
                        <p>Dekan Fakultas,</p>
                        <br><br>
                        <p><strong><u>{dekan}</u></strong></p>
                        <p>{nip_dekan}</p>
                    </div>
                </div>`;
            }

            // ============================================
            // AUTO CLOSE MODAL & RESET FORM
            // ============================================
            document.querySelectorAll(
                    '#addJenisSuratModal, #editJenisSuratModal, #editTemplateModal, #previewModal')
                .forEach(modal => {
                    modal.addEventListener('hidden.bs.modal', function() {
                        if (this.id === 'addJenisSuratModal') {
                            const form = this.querySelector('form');
                            if (form) form.reset();
                        }
                        if (this.id === 'editTemplateModal') {
                            if (CKEDITOR.instances.templateEditor) {
                                CKEDITOR.instances.templateEditor.destroy();
                                editor = null;
                            }
                        }
                    });
                });

            // ============================================
            // VALIDASI FORM TAMBAH
            // ============================================
            const formAddJenisSurat = document.getElementById('formAddJenisSurat');
            if (formAddJenisSurat) {
                formAddJenisSurat.addEventListener('submit', function(e) {
                    const namaInput = this.querySelector('input[name="nama_surat"]');
                    const kategoriSelect = this.querySelector('select[name="kategori_surat_id"]');

                    if (!namaInput || !namaInput.value.trim()) {
                        e.preventDefault();
                        Swal.fire('Error', 'Nama surat harus diisi', 'error');
                        if (namaInput) namaInput.focus();
                        return false;
                    }

                    if (!kategoriSelect || !kategoriSelect.value) {
                        e.preventDefault();
                        Swal.fire('Error', 'Kategori harus dipilih', 'error');
                        if (kategoriSelect) kategoriSelect.focus();
                        return false;
                    }

                    return true;
                });
            }

            // ============================================
            // CLOSE MODAL BUTTON - Fix untuk modal yang tidak tertutup
            // ============================================
            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (modal) {
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }
                });
            });
        });
    </script>
@endpush

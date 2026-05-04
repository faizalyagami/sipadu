@extends('layouts.app')

@section('title', 'Jenis Surat')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Jenis Surat</h4>
            <p class="text-muted mb-0">Kelola jenis dan template surat</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJenisSuratModal">
            <i class="bi bi-plus-circle me-2"></i>Tambah Jenis Surat
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="jenisSuratTable">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Surat</th>
                            <th>Kategori</th>
                            <th width="100">Template</th>
                            <th width="100">Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisSurats as $index => $jenis)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $jenis->nama_surat }}</td>
                            <td>{{ $jenis->kategori_surat }}</td>
                            <td class="text-center">
                                @if($jenis->template_content)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Ada</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Belum</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $jenis->is_active ? 'success' : 'danger' }}">
                                    {{ $jenis->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info edit-template" 
                                        data-id="{{ $jenis->id }}" 
                                        data-nama="{{ $jenis->nama_surat }}" 
                                        data-template="{{ $jenis->template_content }}"
                                        data-logo="{{ $jenis->logo_path }}"
                                        data-kop="{{ $jenis->kop_surat_path }}">
                                    <i class="bi bi-file-text"></i> Template
                                </button>
                                <button class="btn btn-sm btn-outline-warning edit-jenis" 
                                        data-id="{{ $jenis->id }}" 
                                        data-nama="{{ $jenis->nama_surat }}" 
                                        data-kategori="{{ $jenis->kategori_surat }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-jenis" data-id="{{ $jenis->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <p class="text-muted mb-0">Belum ada jenis surat</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jenis Surat -->
<div class="modal fade" id="addJenisSuratModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.jenis-surat.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Jenis Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Surat <span class="text-danger">*</span></label>
                        <input type="text" name="nama_surat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Surat Izin">📋 Surat Izin</option>
                            <option value="Surat Keterangan">📄 Surat Keterangan</option>
                            <option value="Surat Pengajuan">📝 Surat Pengajuan</option>
                            <option value="Surat Rekomendasi">⭐ Surat Rekomendasi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Jenis Surat -->
<div class="modal fade" id="editJenisSuratModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editJenisSuratForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Jenis Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Surat</label>
                        <input type="text" name="nama_surat" id="edit_nama_surat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" id="edit_kategori" class="form-select" required>
                            <option value="Surat Izin">📋 Surat Izin</option>
                            <option value="Surat Keterangan">📄 Surat Keterangan</option>
                            <option value="Surat Pengajuan">📝 Surat Pengajuan</option>
                            <option value="Surat Rekomendasi">⭐ Surat Rekomendasi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Template (Fullscreen seperti Microsoft Word) -->
<div class="modal fade" id="editTemplateModal" tabindex="-1" data-bs-backdrop="static">
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
                        <div class="col-md-2 bg-light p-3" style="border-right: 1px solid #dee2e6; height: calc(100vh - 130px); overflow-y: auto;">
                            
                            <!-- Upload Logo -->
                            <div class="mb-4">
                                <label class="fw-bold mb-2"><i class="bi bi-image"></i> Logo</label>
                                <div class="border rounded p-2 text-center bg-white">
                                    <div id="logoPreview" class="mb-2">
                                        <img id="logoPreviewImg" src="" style="max-width: 80px; max-height: 80px; display: none;">
                                        <div id="logoPlaceholder" class="text-muted">
                                            <i class="bi bi-building fs-1"></i>
                                            <p class="small mb-0">Belum ada logo</p>
                                        </div>
                                    </div>
                                    <input type="file" name="logo" id="logoInput" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                            
                            <!-- Upload Kop Surat -->
                            <div class="mb-4">
                                <label class="fw-bold mb-2"><i class="bi bi-file-image"></i> Kop Surat</label>
                                <div class="border rounded p-2 text-center bg-white">
                                    <div id="kopPreview" class="mb-2">
                                        <img id="kopPreviewImg" src="" style="max-width: 100%; max-height: 100px; display: none;">
                                        <div id="kopPlaceholder" class="text-muted">
                                            <i class="bi bi-card-image fs-1"></i>
                                            <p class="small mb-0">Belum ada kop</p>
                                        </div>
                                    </div>
                                    <input type="file" name="kop_surat" id="kopInput" class="form-control form-control-sm" accept="image/*">
                                    <div class="mt-2 d-none" id="kopSizeControl">
                                        <label class="form-label small">Ukuran Gambar Kop</label>
                                        <input type="range" id="kopWidthSlider" class="form-range" min="30" max="100" value="80">
                                        <div class="d-flex justify-content-between">
                                            <small>Kecil</small>
                                            <small>Normal</small>
                                            <small>Besar</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Variabel -->
                            <h6 class="fw-bold mb-2"><i class="bi bi-tags"></i> Variabel</h6>
                            <div class="alert alert-info small py-1">Klik untuk menyisipkan</div>
                            
                            <div class="mb-2">
                                <label class="fw-bold small">Mahasiswa</label>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nama_mahasiswa}"><code>{nama_mahasiswa}</code></button>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{npm}"><code>{npm}</code></button>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{alamat}"><code>{alamat}</code></button>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold small">Akademik</label>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{fakultas}"><code>{fakultas}</code></button>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{prodi}"><code>{prodi}</code></button>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{semester}"><code>{semester}</code></button>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold small">Surat</label>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nomor_surat}"><code>{nomor_surat}</code></button>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{tanggal_surat}"><code>{tanggal_surat}</code></button>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{perihal}"><code>{perihal}</code></button>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold small">Tanda Tangan</label>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{dekan}"><code>{dekan}</code></button>
                                <button class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nip_dekan}"><code>{nip_dekan}</code></button>
                            </div>
                        </div>
                        
                        <!-- Editor Area -->
                        <div class="col-md-10 p-0 d-flex flex-column">
                            <!-- Toolbar Lengkap seperti MS Word -->
                            <div class="btn-toolbar p-2 border-bottom flex-wrap" style="background: #f8f9fa;">
                                <!-- Clipboard -->
                                <div class="btn-group me-2 mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-undo" title="Undo"><i class="bi bi-arrow-return-left"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-redo" title="Redo"><i class="bi bi-arrow-return-right"></i></button>
                                </div>
                                
                                <!-- Font Style -->
                                <div class="btn-group me-2 mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-bold" title="Tebal (Ctrl+B)"><i class="bi bi-type-bold"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-italic" title="Miring (Ctrl+I)"><i class="bi bi-type-italic"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-underline" title="Garis Bawah (Ctrl+U)"><i class="bi bi-type-underline"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-strikethrough" title="Coret"><i class="bi bi-type-strikethrough"></i></button>
                                </div>
                                
                                <!-- Font Size -->
                                <div class="btn-group me-2 mb-1">
                                    <select id="fontSizeSelect" class="form-select form-select-sm" style="width: 70px;">
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12" selected>12</option>
                                        <option value="14">14</option>
                                        <option value="16">16</option>
                                        <option value="18">18</option>
                                        <option value="20">20</option>
                                        <option value="22">22</option>
                                        <option value="24">24</option>
                                        <option value="26">26</option>
                                        <option value="28">28</option>
                                        <option value="36">36</option>
                                        <option value="48">48</option>
                                        <option value="72">72</option>
                                    </select>
                                </div>
                                
                                <!-- Font Family -->
                                <div class="btn-group me-2 mb-1">
                                    <select id="fontFamilySelect" class="form-select form-select-sm" style="width: 150px;">
                                        <option value="'Times New Roman', Times, serif" selected>Times New Roman</option>
                                        <option value="Arial, sans-serif">Arial</option>
                                        <option value="'Courier New', monospace">Courier New</option>
                                        <option value="Georgia, serif">Georgia</option>
                                        <option value="Verdana, sans-serif">Verdana</option>
                                        <option value="'Segoe UI', Tahoma, Geneva">Segoe UI</option>
                                        <option value="'Calibri', sans-serif">Calibri</option>
                                        <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                                    </select>
                                </div>
                                
                                <!-- Alignment -->
                                <div class="btn-group me-2 mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-align-left" title="Rata Kiri"><i class="bi bi-text-left"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-align-center" title="Rata Tengah"><i class="bi bi-text-center"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-align-right" title="Rata Kanan"><i class="bi bi-text-right"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-align-justify" title="Rata Kiri-Kanan"><i class="bi bi-text-paragraph"></i></button>
                                </div>
                                
                                <!-- List -->
                                <div class="btn-group me-2 mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-bullet-list" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-numbered-list" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                </div>
                                
                                <!-- Indent -->
                                <div class="btn-group me-2 mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-indent" title="Indent"><i class="bi bi-text-indent-left"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-outdent" title="Outdent"><i class="bi bi-text-indent-right"></i></button>
                                </div>
                                
                                <!-- Insert -->
                                <div class="btn-group me-2 mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-insert-logo" title="Insert Logo"><i class="bi bi-image"></i> Logo</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-insert-kop" title="Insert Kop Surat"><i class="bi bi-file-image"></i> Kop</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-insert-table" title="Insert Table"><i class="bi bi-table"></i> Table</button>
                                </div>
                                
                                <!-- Tools -->
                                <div class="btn-group mb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-fullscreen" title="Fullscreen"><i class="bi bi-arrows-fullscreen"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-print-editor" title="Print"><i class="bi bi-printer"></i></button>
                                </div>
                            </div>
                            
                            <!-- Editor -->
                            <div id="templateEditor" style="flex:1; overflow:auto; padding:20px; background:white; font-family:'Times New Roman', Times, serif; font-size:12pt;"></div>
                            <textarea name="template_content" id="template_content" style="display:none;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Batal</button>
                    <button type="button" class="btn btn-info" id="previewBtn"><i class="bi bi-eye"></i> Preview</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content border-0">
            
            <!-- Header -->
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-eye me-2"></i>Preview Template Surat
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-0" style="background:#dcdcdc; overflow:auto;">
                
                <div class="preview-wrapper">
                    <div id="previewPaper" class="preview-paper"></div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 bg-white shadow-sm">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>

                <button type="button" class="btn btn-primary" id="printPreviewBtn">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .ck-editor__editable_inline {
        min-height: 600px;
        font-family: 'Times New Roman', Times, serif;
    }
    .btn-group .btn-sm {
        padding: 4px 8px;
    }
    .form-select-sm {
        font-size: 12px;
        padding: 4px 8px;
    }
    .a4-preview {
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.5;
    }
    .a4-preview table {
        width: 100%;
        border-collapse: collapse;
    }
    .a4-preview td {
        padding: 4px 0;
    }

    .ck-editor__editable {
    min-height: 1100px !important;
    width: 210mm !important;
    margin: auto !important;
    background: white !important;
    padding: 20mm !important;
    box-shadow: 0 0 15px rgba(0,0,0,0.15);
    border: 1px solid #ddd;
    font-family: 'Times New Roman', serif;
    font-size: 14pt;
    }

    .ck-content img {
        max-width: 100%;
        cursor: move;
        resize: both;
        overflow: auto;
    }
    #previewContent {
    background:#d9d9d9;
    height:100vh;
    overflow:auto;
    padding:40px;
    }

    /* Kertas A4 */
    .preview-paper {
        width: 210mm;
        min-height: 297mm;
        background: white;
        padding: 20mm;
        box-shadow: 0 0 20px rgba(0,0,0,0.15);
        border-radius: 4px;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.6;
        position: relative;
        overflow: hidden;
    }

    .preview-paper img {
        max-width: 100%;
        height: auto;
        display: block;
    }

    .preview-paper table {
        width: 100%;
        border-collapse: collapse;
    }

    .preview-paper td,
    .preview-paper th {
        border: 1px solid #000;
        padding: 6px;
    }

    /* Fullscreen modal lebih rapi */
    #previewModal .modal-body {
        height: calc(100vh - 130px);
    }

    /* Print khusus */
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
            width: 210mm;
            min-height: 297mm;
            margin: 0;
            padding: 15mm;
            box-shadow: none;
            border-radius: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let editor;
    let currentKopWidth = 80;
    let currentKopUrl = null;
    
    $(document).ready(function() {
        // DataTable
        if ($('#jenisSuratTable').length) {
            if ($.fn.DataTable.isDataTable('#jenisSuratTable')) $('#jenisSuratTable').DataTable().destroy();
            $('#jenisSuratTable').DataTable({ 
                responsive: true, 
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' }, 
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: [3, 4, 5] }]
            });
        }
        
        // Edit Jenis Surat
        $('.edit-jenis').click(function() {
            $('#edit_nama_surat').val($(this).data('nama'));
            $('#edit_kategori').val($(this).data('kategori'));
            $('#editJenisSuratForm').attr('action', `/admin/jenis-surat/${$(this).data('id')}`);
            $('#editJenisSuratModal').modal('show');
        });
        
        // Edit Template
        $('.edit-template').click(function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            let template = $(this).data('template') || getDefaultTemplate();
            let logoPath = $(this).data('logo');
            let kopPath = $(this).data('kop');
            
            // Set preview logo
            if (logoPath) {
                let logoUrl = logoPath.startsWith('/') ? logoPath : '/' + logoPath;
                $('#logoPreviewImg').attr('src', logoUrl).show();
                $('#logoPlaceholder').hide();
            } else {
                $('#logoPreviewImg').hide();
                $('#logoPlaceholder').show();
            }
            
            // Set preview kop
            if (kopPath) {
                currentKopUrl = kopPath.startsWith('/') ? kopPath : '/' + kopPath;
                $('#kopPreviewImg').attr('src', currentKopUrl).show();
                $('#kopPlaceholder').hide();
                $('#kopSizeControl').removeClass('d-none');
            } else {
                currentKopUrl = null;
                $('#kopPreviewImg').hide();
                $('#kopPlaceholder').show();
                $('#kopSizeControl').addClass('d-none');
            }
            
            $('#template_nama_surat').text(nama);
            $('#editTemplateForm').attr('action', `/admin/jenis-surat/${id}/template`);
            
            if (editor) {
                editor.destroy().then(() => initEditor(template));
            } else {
                initEditor(template);
            }
            
            $('#editTemplateModal').modal('show');
        });
        
        $('.delete-jenis').click(function() {
            confirmDelete(`/admin/jenis-surat/${$(this).data('id')}`, 'Yakin hapus jenis surat ini?');
        });
        
        // Preview Logo
        $('#logoInput').on('change', function(e) {
            let file = e.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#logoPreviewImg').attr('src', e.target.result).show();
                    $('#logoPlaceholder').hide();
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Preview Kop
        $('#kopInput').on('change', function(e) {
            let file = e.target.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    currentKopUrl = e.target.result;
                    $('#kopPreviewImg').attr('src', currentKopUrl).show();
                    $('#kopPlaceholder').hide();
                    $('#kopSizeControl').removeClass('d-none');
                    $('#kopPreviewImg').css('width', currentKopWidth + '%');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Kop Width Slider
        $('#kopWidthSlider').on('input', function () {

            currentKopWidth = $(this).val();

            $('#kopPreviewImg').css({
                width: currentKopWidth + '%',
                maxWidth: '100%'
            });

            if (editor) {

                let html = editor.getData();

                html = html.replace(
                    /(<img[^>]*alt="Kop Surat"[^>]*style="[^"]*width:)([^;]+)(;[^"]*")/g,
                    `$1${currentKopWidth}%$3`
                );

                editor.setData(html);
            }
        });
        
        // Insert Variable
        $('.insert-variable').click(function() {
            let variable = $(this).data('var');
            if (editor && variable) {
                editor.model.change(writer => {
                    writer.insertText(variable, editor.model.document.selection.getFirstPosition());
                });
            }
        });
        
        // Insert Logo
        $('#btn-insert-logo').click(function() {
            let logoUrl = $('#logoPreviewImg').attr('src');
            if (logoUrl && logoUrl !== '#') {
                let html = `<div style="text-align:center; margin:10px 0;"><img src="${logoUrl}" style="max-width:100px; height:auto;"></div>`;
                insertHtmlToEditor(html);
            } else {
                Swal.fire('Info', 'Silakan upload logo terlebih dahulu', 'info');
            }
        });
        
        // Insert Kop
        $('#btn-insert-kop').click(function () {

            if (!currentKopUrl || !editor) return;

            const width = $('#kopWidth').val();
            const height = $('#kopHeight').val();

            const html = `
                <div style="text-align:center; margin-bottom:20px;">
                    <img src="${currentKopUrl}"
                        style="
                            width:${width}%;
                            height:${height}px;
                            object-fit:contain;
                            display:block;
                            margin:auto;
                            resize:both;
                            overflow:auto;
                            cursor:move;
                        ">
                </div>
            `;

            editor.model.change(() => {

                const viewFragment = editor.data.processor.toView(html);
                const modelFragment = editor.data.toModel(viewFragment);

                editor.model.insertContent(modelFragment);
            });
        });
        
        // Insert Table
        $('#btn-insert-table').click(function() {
            Swal.fire({
                title: 'Insert Table',
                html: `
                    <div class="row">
                        <div class="col-6"><label>Baris:</label><input id="table-rows" class="form-control" value="3" type="number" min="1" max="10"></div>
                        <div class="col-6"><label>Kolom:</label><input id="table-cols" class="form-control" value="3" type="number" min="1" max="10"></div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Insert',
                preConfirm: () => {
                    return { rows: document.getElementById('table-rows').value, cols: document.getElementById('table-cols').value };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let rows = parseInt(result.value.rows);
                    let cols = parseInt(result.value.cols);
                    let tableHtml = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%; margin:10px 0;">';
                    for (let i = 0; i < rows; i++) {
                        tableHtml += '<tr>';
                        for (let j = 0; j < cols; j++) {
                            tableHtml += '<td style="border:1px solid #ddd; padding:8px;">&nbsp;</td>';
                        }
                        tableHtml += '</tr>';
                    }
                    tableHtml += '</table>';
                    insertHtmlToEditor(tableHtml);
                }
            });
        });
        
        // Toolbar Functions
        function insertHtmlToEditor(html) {
            if (editor) {
                editor.model.change(writer => {
                    const viewFragment = editor.data.processor.toView(html);
                    const modelFragment = editor.data.toModel(viewFragment);
                    editor.model.insertContent(modelFragment);
                });
            }
        }
        
        // Editor commands
        $('#btn-bold').click(() => editor && editor.execute('bold'));
        $('#btn-italic').click(() => editor && editor.execute('italic'));
        $('#btn-underline').click(() => editor && editor.execute('underline'));
        $('#btn-strikethrough').click(() => editor && editor.execute('strikethrough'));
        $('#btn-align-left').click(() => editor && editor.execute('alignment', { value: 'left' }));
        $('#btn-align-center').click(() => editor && editor.execute('alignment', { value: 'center' }));
        $('#btn-align-right').click(() => editor && editor.execute('alignment', { value: 'right' }));
        $('#btn-align-justify').click(() => editor && editor.execute('alignment', { value: 'justify' }));
        $('#btn-bullet-list').click(() => editor && editor.execute('bulletedList'));
        $('#btn-numbered-list').click(() => editor && editor.execute('numberedList'));
        $('#btn-indent').click(() => editor && editor.execute('indentList'));
        $('#btn-outdent').click(() => editor && editor.execute('outdentList'));
        $('#btn-undo').click(() => editor && editor.execute('undo'));
        $('#btn-redo').click(() => editor && editor.execute('redo'));
        
        // Font Size
        $('#fontSizeSelect').change(function() {
            if (editor) editor.execute('fontSize', { value: $(this).val() + 'pt' });
        });
        
        // Font Family
        $('#fontFamilySelect').change(function() {
            if (editor) editor.execute('fontFamily', { value: $(this).val() });
        });
        
        // Fullscreen
        $('#btn-fullscreen').click(function() {
            let elem = document.querySelector('.ck-editor__editable');
            if (!document.fullscreenElement) elem.requestFullscreen();
            else document.exitFullscreen();
        });
        
        // Print Editor
        $('#btn-print-editor').click(function() {
            let content = editor.getData();
            let win = window.open('', '_blank');
            win.document.write(`<html><head><title>Print Template</title><style>body{font-family:'Times New Roman',serif;padding:20mm;}</style></head><body>${content}</body></html>`);
            win.document.close();
            win.print();
        });
        
        // Preview
        $('#previewBtn').click(function() {
            if (!editor) return;

            let content = editor.getData();

            let previewData = {
                nama_mahasiswa: 'Nuni Lestari',
                npm: '10050022094',
                tempat_lahir: 'Bandung',
                tanggal_lahir: '23 Desember 2000',
                alamat: 'Jl. Dederuk No. 21, Bandung',
                fakultas: 'Psikologi',
                prodi: 'Psikologi S1',
                jenjang: 'S1',
                ipk: '3.75',
                semester: 'VIII',
                tahun_akademik: '2025/2026',
                nomor_surat: '083/M.10/Dek.Psi-k/IV/2026',
                tanggal_surat: '29 April 2026',
                keperluan: 'persyaratan administrasi',
                perihal: 'SURAT KETERANGAN AKTIF KULIAH',
                dekan: 'Dr. Oki Mardiawan, M.Psi., Psikolog.',
                nip_dekan: 'D.07.0.464',
                nama_ortu: 'Iwan Ridwan',
                nik_ortu: '3205152111070961',
                pangkat_ortu: 'Golongan IV-B',
                instansi_ortu: 'SDN Sukamukti 4',
                alamat_kantor_ortu: 'Jalan Lapang Trikarya, Kec. Sukawening, Kab. Garut'
            };

            let html = content;

            for (let key in previewData) {
                html = html.replace(new RegExp(`\\{${key}\\}`, 'g'), previewData[key]);
            }

            $('#previewPaper').html(html);
            $('#previewModal').modal('show');
        });
        
        // Print Preview
        $('#printPreviewBtn').click(function() {
            let printContent = $('#previewPaper').html();

            let printWindow = window.open('', '_blank');

            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Surat</title>
                    <style>
                        @page {
                            size: A4;
                            margin: 15mm;
                        }

                        body {
                            margin: 0;
                            padding: 0;
                            font-family: 'Times New Roman', Times, serif;
                            background: white;
                        }

                        .paper {
                            width: 210mm;
                            min-height: 297mm;
                            margin: auto;
                            padding: 15mm;
                            box-sizing: border-box;
                        }

                        img {
                            max-width: 100%;
                            height: auto;
                        }

                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }

                        td, th {
                            border: 1px solid #000;
                            padding: 6px;
                        }
                    </style>
                </head>
                <body>
                    <div class="paper">
                        ${printContent}
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();

            setTimeout(() => {
                printWindow.print();
            }, 500);
        });
        
        $('#editTemplateForm').on('submit', function(e) {
            if (editor) $('#template_content').val(editor.getData());
        });
    });
    
    function initEditor(content) {

        if (editor) {
            editor.destroy();
        }

        ClassicEditor.create(document.querySelector('#templateEditor'), {

            toolbar: {
                items: [
                    'undo','redo',
                    '|',
                    'heading',
                    '|',
                    'bold','italic','underline','strikethrough',
                    '|',
                    'fontFamily','fontSize',
                    '|',
                    'alignment',
                    '|',
                    'bulletedList','numberedList',
                    '|',
                    'outdent','indent',
                    '|',
                    'insertTable',
                    'blockQuote',
                    'link'
                ]
            },

            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            },

            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            }
        })

        .then(newEditor => {

            editor = newEditor;

            editor.setData(content || '');

            const editable = editor.ui.view.editable.element;

            editable.style.minHeight = '1100px';
            editable.style.width = '210mm';
            editable.style.margin = '20px auto';
            editable.style.padding = '20mm';
            editable.style.background = '#fff';
            editable.style.boxShadow = '0 0 15px rgba(0,0,0,0.2)';
            editable.style.border = '1px solid #ddd';
        })

        .catch(error => console.error(error));
    }
    
    function getDefaultTemplate() {
        return `<div style="font-family:'Times New Roman', Times, serif;">
            <div style="text-align:center; margin-bottom:15px;">
                <img src="{kop_surat}" style="width:80%; max-width:100%; height:auto;">
            </div>
            <div style="text-align:center; margin:20px 0;">
                <strong style="font-size:14pt;">SURAT KETERANGAN AKTIF KULIAH</strong><br>
                <strong>Nomor : {nomor_surat}</strong>
            </div>
            <div style="text-align:justify;">
                <p>Assalamu'alaikum wr. wb.</p>
                <p>Yang bertanda tangan dibawah ini:</p>
                <p>Nama : {dekan}<br>N.I.K. : {nip_dekan}<br>Jabatan : Wakil Dekan</p>
                <p>Menyatakan bahwa:</p>
                <p>Nama : {nama_mahasiswa}<br>NPM : {npm}<br>Fakultas : {fakultas}<br>Semester : {semester}</p>
                <p>Adalah benar mahasiswa aktif Universitas Islam Bandung.</p>
                <p>Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
                <p>Wassalamu'alaikum wr. wb.</p>
            </div>
            <div style="margin-top:50px; text-align:right;">
                <p>Bandung, {tanggal_surat}</p>
                <p>Wakil Dekan Bidang Pembelajaran dan Kemahasiswaan,</p>
                <br><br>
                <p><strong><u>{dekan}</u></strong></p>
                <p>{nip_dekan}</p>
            </div>
        </div>`;
    }
</script>
@endpush
@endsection
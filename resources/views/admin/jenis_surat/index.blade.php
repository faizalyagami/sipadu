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
                            <td>{{ $jenis->kategoriSurat->nama_kategori ?? 'Tidak ada kategori' }}</td>
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
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Jenis Surat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Surat <span class="text-danger">*</span></label>
                        <input type="text" name="nama_surat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_surat_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
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
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Jenis Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Surat</label>
                        <input type="text" name="nama_surat" id="edit_nama_surat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori_surat_id" id="edit_kategori_id" class="form-select" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Template (Fullscreen) -->
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
                                <label class="fw-bold mb-2"><i class="bi bi-image"></i> Logo Universitas</label>
                                <div class="border rounded p-2 text-center bg-white">
                                    <div id="logoPreview" class="mb-2">
                                        <img id="logoPreviewImg" src="" style="max-width: 80px; max-height: 80px; display: none;">
                                        <div id="logoPlaceholder" class="text-muted">
                                            <i class="bi bi-building fs-1"></i>
                                            <p class="small mb-0">Belum ada logo</p>
                                        </div>
                                    </div>
                                    <input type="file" name="logo" id="logoInput" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted">PNG, JPG (Max 2MB)</small>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100" id="btnRemoveLogo" style="display: none;">
                                        <i class="bi bi-trash"></i> Hapus Logo
                                    </button>
                                </div>
                            </div>

                            <!-- Upload Kop Surat -->
                            <div class="mb-4">
                                <label class="fw-bold mb-2"><i class="bi bi-file-image"></i> Gambar Kop Surat</label>
                                <div class="border rounded p-2 text-center bg-white">
                                    <div id="kopPreview" class="mb-2">
                                        <img id="kopPreviewImg" src="" style="max-width: 100%; max-height: 100px; display: none;">
                                        <div id="kopPlaceholder" class="text-muted">
                                            <i class="bi bi-card-image fs-1"></i>
                                            <p class="small mb-0">Belum ada kop</p>
                                        </div>
                                    </div>
                                    <input type="file" name="kop_surat" id="kopInput" class="form-control form-control-sm" accept="image/*">
                                    <div class="mt-2" id="kopSizeControl" style="display: none;">
                                        <label class="form-label small">Ukuran Gambar Kop</label>
                                        <input type="range" id="kopWidthSlider" class="form-range" min="30" max="100" value="80">
                                        <div class="d-flex justify-content-between small text-muted">
                                            <span>Kecil</span>
                                            <span>Normal</span>
                                            <span>Besar</span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100" id="btnRemoveKop" style="display: none;">
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
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nama_mahasiswa}">
                                    <code>{nama_mahasiswa}</code> - Nama Lengkap
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{npm}">
                                    <code>{npm}</code> - NPM
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{alamat}">
                                    <code>{alamat}</code> - Alamat
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold small text-success">Data Akademik</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{fakultas}">
                                    <code>{fakultas}</code> - Fakultas
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{prodi}">
                                    <code>{prodi}</code> - Program Studi
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{semester}">
                                    <code>{semester}</code> - Semester
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-warning">Data Surat</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nomor_surat}">
                                    <code>{nomor_surat}</code> - Nomor Surat
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{tanggal_surat}">
                                    <code>{tanggal_surat}</code> - Tanggal Surat
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{perihal}">
                                    <code>{perihal}</code> - Perihal
                                </button>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold small text-danger">Data Tanda Tangan</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{dekan}">
                                    <code>{dekan}</code> - Nama Dekan
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nip_dekan}">
                                    <code>{nip_dekan}</code> - NIP Dekan
                                </button>
                            </div>
                        </div>

                        <!-- Editor Area -->
                        <div class="col-md-10 p-0 d-flex flex-column">
                            <textarea id="templateEditor" name="template_content" style="width:100%; height:600px;"></textarea>
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
<div class="modal fade" id="previewModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Preview Template Surat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#e5e5e5; overflow:auto;">
                <div id="previewPaper" style="width:210mm; min-height:297mm; margin:0 auto; background:white; padding:20mm; box-shadow:0 0 10px rgba(0,0,0,0.1);"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="printPreviewBtn"><i class="bi bi-printer"></i> Print</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    
    .btn-sm {
        margin: 2px;
    }
    
    /* CKEditor Styling */
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
    
    /* Variable buttons */
    .insert-variable {
        text-align: left;
        font-size: 12px;
    }
    
    .insert-variable code {
        background: #f8f9fa;
        padding: 2px 4px;
        border-radius: 4px;
        font-size: 11px;
    }
    
    /* Preview paper */
    @media print {
        body * {
            visibility: hidden;
        }
        #previewPaper, #previewPaper * {
            visibility: visible;
        }
        #previewPaper {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 15mm;
        }
    }
</style>
@endpush

@push('scripts')
<script src="//cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
let editor = null;
let currentJenisId = null;
let currentKopUrl = null;

$(document).ready(function() {
    // DataTable initialization with error handling
    if ($('#jenisSuratTable').length) {
        try {
            if ($.fn.DataTable.isDataTable('#jenisSuratTable')) {
                $('#jenisSuratTable').DataTable().destroy();
            }
            $('#jenisSuratTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                pageLength: 10,
                columnDefs: [{
                    orderable: false,
                    targets: [3, 4, 5]
                }]
            });
        } catch(e) {
            console.log('DataTable error:', e);
        }
    }
    
    // Edit Jenis Surat
    $('.edit-jenis').click(function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        let kategori_id = $(this).data('kategori');
        
        $('#edit_nama_surat').val($(this).data('nama'));
        $('#edit_kategori').val($(this).data('kategori'));
        $('#editJenisSuratForm').attr('action', `/admin/jenis-surat/${$(this).data('id')}`);
        $('#editJenisSuratModal').modal('show');
    });
    
    // Edit Template
    $(document).on('click', '.edit-template', function() {
        currentJenisId = $(this).data('id');
        let nama = $(this).data('nama');
        let template = $(this).data('template') || getDefaultTemplate();
        let logoPath = $(this).data('logo');
        let kopPath = $(this).data('kop');
        
        // Reset form
        $('#logo_path').val('');
        $('#kop_surat_path').val('');
        $('#logoInput').val('');
        $('#kopInput').val('');
        $('#btnRemoveLogo').hide();
        $('#btnRemoveKop').hide();
        $('#kopSizeControl').hide();
        
        // Set preview logo
        if (logoPath && logoPath !== 'null' && logoPath !== '') {
            let logoUrl = logoPath.startsWith('http') ? logoPath : '/storage/' + logoPath;
            $('#logoPreviewImg').attr('src', logoUrl).show();
            $('#logoPlaceholder').hide();
            $('#logo_path').val(logoPath);
            $('#btnRemoveLogo').show();
        } else {
            $('#logoPreviewImg').hide();
            $('#logoPlaceholder').show();
            $('#btnRemoveLogo').hide();
        }
        
        // Set preview kop
        if (kopPath && kopPath !== 'null' && kopPath !== '') {
            currentKopUrl = kopPath.startsWith('http') ? kopPath : '/storage/' + kopPath;
            $('#kopPreviewImg').attr('src', currentKopUrl).show();
            $('#kopPlaceholder').hide();
            $('#kopSizeControl').show();
            $('#kop_surat_path').val(kopPath);
            $('#btnRemoveKop').show();
        } else {
            currentKopUrl = null;
            $('#kopPreviewImg').hide();
            $('#kopPlaceholder').show();
            $('#kopSizeControl').hide();
            $('#btnRemoveKop').hide();
        }
        
        $('#template_nama_surat').text(nama);
        $('#editTemplateForm').attr('action', `/admin/jenis-surat/${currentJenisId}/template`);
        
        // Destroy existing CKEditor
        if (CKEDITOR.instances.templateEditor) {
            CKEDITOR.instances.templateEditor.destroy();
        }
        
        // Initialize CKEditor
        initCKEditor(template);
        
        $('#editTemplateModal').modal('show');
    });
    
    // Remove Logo
    $('#btnRemoveLogo').click(function() {
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
                $('#logoPreviewImg').hide();
                $('#logoPlaceholder').show();
                $('#logo_path').val('');
                $('#btnRemoveLogo').hide();
                Swal.fire('Terhapus!', 'Logo telah dihapus', 'success');
            }
        });
    });
    
    // Remove Kop
    $('#btnRemoveKop').click(function() {
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
                $('#kopPreviewImg').hide();
                $('#kopPlaceholder').show();
                $('#kop_surat_path').val('');
                $('#kopSizeControl').hide();
                $('#btnRemoveKop').hide();
                currentKopUrl = null;
                Swal.fire('Terhapus!', 'Kop surat telah dihapus', 'success');
            }
        });
    });
    
    // Insert Kop
    $('#btnInsertKop').click(function() {
        if (!editor) {
            Swal.fire('Error', 'Editor belum siap', 'error');
            return;
        }
        
        let kopUrl = $('#kopPreviewImg').attr('src');
        let kopWidth = $('#kopWidthSlider').val() || 80;
        
        if (kopUrl && kopUrl !== '#') {
            let html = `<div style="text-align:center; margin-bottom:20px;">
                <img src="${kopUrl}" style="width:${kopWidth}%; max-width:100%; height:auto;">
            </div>`;
            editor.insertHtml(html);
            editor.focus();
            Swal.fire('Sukses', 'Kop surat berhasil disisipkan', 'success');
        } else {
            Swal.fire('Info', 'Silakan upload kop surat terlebih dahulu', 'info');
        }
    });
    
    // Upload Logo
    $('#logoInput').on('change', function(e) {
        let file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
                $(this).val('');
                return;
            }
            
            let formData = new FormData();
            formData.append('logo', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            Swal.fire({ title: 'Uploading...', text: 'Mohon tunggu', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            $.ajax({
                url: '/admin/upload-logo',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        $('#logoPreviewImg').attr('src', response.url).show();
                        $('#logoPlaceholder').hide();
                        $('#logo_path').val(response.path);
                        $('#btnRemoveLogo').show();
                        Swal.fire('Sukses', 'Logo berhasil diupload', 'success');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Gagal upload logo', 'error');
                    $('#logoInput').val('');
                }
            });
        }
    });
    
    // Upload Kop
    $('#kopInput').on('change', function(e) {
        let file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
                $(this).val('');
                return;
            }
            
            let formData = new FormData();
            formData.append('kop_surat', file);
            formData.append('_token', '{{ csrf_token() }}');
            
            Swal.fire({ title: 'Uploading...', text: 'Mohon tunggu', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            $.ajax({
                url: '/admin/upload-kop',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        currentKopUrl = response.url;
                        $('#kopPreviewImg').attr('src', currentKopUrl).show();
                        $('#kopPlaceholder').hide();
                        $('#kopSizeControl').show();
                        $('#kop_surat_path').val(response.path);
                        $('#btnRemoveKop').show();
                        Swal.fire('Sukses', 'Kop surat berhasil diupload', 'success');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Error', 'Gagal upload kop surat', 'error');
                    $('#kopInput').val('');
                }
            });
        }
    });
    
    // Kop Width Slider
    $('#kopWidthSlider').on('input', function() {
        let width = $(this).val();
        $('#kopPreviewImg').css('width', width + '%');
    });
    
    // Insert Variable
    $(document).on('click', '.insert-variable', function() {
        let variable = $(this).data('var');
        if (editor) {
            editor.insertText(variable);
            editor.focus();
        }
    });
    
    // Preview
    $('#previewBtn').click(function() {
        if (!editor) return;
        let content = editor.getData();
        let previewData = {
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
            nip_dekan: 'D.07.0.464'
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
        let win = window.open('', '_blank');
        win.document.write(`<html><head><title>Print Surat</title>
            <style>@page{size:A4;margin:15mm;}body{font-family:'Times New Roman',serif;}</style>
            </head><body>${printContent}</body></html>`);
        win.document.close();
        win.print();
    });
    
    // Delete Jenis Surat
    $('.delete-jenis').click(function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Jenis Surat?',
            text: 'Data yang terkait dengan surat ini juga akan terhapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/jenis-surat/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() {
                        Swal.fire('Terhapus!', 'Jenis surat berhasil dihapus', 'success');
                        location.reload();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal menghapus jenis surat', 'error');
                    }
                });
            }
        });
    });
});

function initCKEditor(content) {
    if (typeof CKEDITOR === 'undefined') {
        console.error('CKEditor tidak ditemukan!');
        return;
    }
    
    CKEDITOR.replace('templateEditor', {
        toolbar: [
            { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Print', '-', 'Templates'] },
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'SpellChecker', 'Scayt'] },
            { name: 'forms', items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'] },
            '/',
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },
            { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
            { name: 'insert', items: ['Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe'] },
            '/',
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
        ],
        height: 'calc(100vh - 200px)',
        width: '100%',
        language: 'id',
        enterMode: CKEDITOR.ENTER_P,
        shiftEnterMode: CKEDITOR.ENTER_BR,
        filebrowserImageUploadUrl: '{{ route("upload.image") }}',
        filebrowserUploadUrl: '{{ route("upload.image") }}',
        allowedContent: true,
        extraAllowedContent: '*[*]'
    });
    
    CKEDITOR.instances.templateEditor.on('instanceReady', function() {
        editor = CKEDITOR.instances.templateEditor;
        editor.setData(content || getDefaultTemplate());
    });
}

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
</script>
@endpush
@endsection
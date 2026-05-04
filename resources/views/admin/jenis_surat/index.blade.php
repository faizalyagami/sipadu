{{-- resources/views/admin/jenis_surat/index.blade.php --}}
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
                                    <div class="mt-2 d-none" id="kopSizeControl">
                                        <label class="form-label small">Ukuran Gambar Kop</label>
                                        <input type="range" id="kopWidthSlider" class="form-range" min="30" max="100" value="80">
                                        <div class="d-flex justify-content-between">
                                            <small>Kecil</small>
                                            <small>Normal</small>
                                            <small>Besar</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100" id="btnRemoveKop" style="display: none;">
                                        <i class="bi bi-trash"></i> Hapus Kop Surat
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-primary w-100" id="btnInsertKop">
                                    <i class="bi bi-image"></i> Sisipkan Kop Surat ke Template
                                </button>
                            </div>

                            <input type="hidden" name="logo_path" id="logo_path">
                            <input type="hidden" name="kop_surat_path" id="kop_surat_path">
                            <input type="hidden" name="remove_logo" id="remove_logo" value="0">
                            <input type="hidden" name="remove_kop" id="remove_kop" value="0">

                            <hr>

                            <!-- Variabel -->
                            <h6 class="fw-bold mb-2"><i class="bi bi-tags"></i> Variabel yang Tersedia</h6>
                            <div class="alert alert-info small py-1">Klik untuk menyisipkan</div>

                            <div class="mb-2">
                                <label class="fw-bold small">Data Mahasiswa</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nama_mahasiswa}"><code>{nama_mahasiswa}</code> - Nama Lengkap</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{npm}"><code>{npm}</code> - NPM</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{alamat}"><code>{alamat}</code> - Alamat</button>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold small">Data Akademik</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{fakultas}"><code>{fakultas}</code> - Fakultas</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{prodi}"><code>{prodi}</code> - Program Studi</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{semester}"><code>{semester}</code> - Semester</button>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold small">Data Surat</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nomor_surat}"><code>{nomor_surat}</code> - Nomor Surat</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{tanggal_surat}"><code>{tanggal_surat}</code> - Tanggal Surat</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{perihal}"><code>{perihal}</code> - Perihal</button>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold small">Data Tanda Tangan</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{dekan}"><code>{dekan}</code> - Nama Dekan</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-1 insert-variable" data-var="{nip_dekan}"><code>{nip_dekan}</code> - NIP Dekan</button>
                            </div>
                        </div>

                        <!-- Editor Area -->
                        <div class="col-md-10 p-0 d-flex flex-column">
                            <!-- CKEditor 4 -->
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
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Preview Template Surat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#e5e5e5; overflow:auto;">
                <div id="previewPaper" style="width:210mm; min-height:297mm; margin:0 auto; background:white; padding:20mm; box-shadow:0 0 10px rgba(0,0,0,0.1);"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="printPreviewBtn">Print</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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

    .cke_toolbar_group {
        margin: 0 2px !important;
    }

    .cke_button {
        margin: 2px !important;
    }
</style>
@endpush

@push('scripts')
<script src="//cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let editor;
    let currentKopWidth = 80;
    let currentKopUrl = null;
    let currentJenisId = null;

    $(document).ready(function() {
        // DataTable
        if ($('#jenisSuratTable').length) {
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
        }

        // Edit Jenis Surat
        $('.edit-jenis').click(function() {
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
            $('#remove_logo').val('0');
            $('#remove_kop').val('0');
            $('#btnRemoveLogo').hide();
            $('#btnRemoveKop').hide();

            // Set preview logo dari database
            if (logoPath && logoPath !== 'null') {
                let logoUrl = logoPath.startsWith('http') ? logoPath : '/storage/' + logoPath;
                $('#logoPreviewImg').attr('src', logoUrl).show();
                $('#logoPlaceholder').hide();
                $('#logo_path').val(logoPath);
                $('#btnRemoveLogo').show();
            } else {
                $('#logoPreviewImg').hide();
                $('#logoPlaceholder').show();
                $('#logo_path').val('');
                $('#btnRemoveLogo').hide();
            }

            // Set preview kop dari database
            if (kopPath && kopPath !== 'null') {
                currentKopUrl = kopPath.startsWith('http') ? kopPath : '/storage/' + kopPath;
                $('#kopPreviewImg').attr('src', currentKopUrl).show();
                $('#kopPlaceholder').hide();
                $('#kopSizeControl').removeClass('d-none');
                $('#kop_surat_path').val(kopPath);
                $('#btnRemoveKop').show();
            } else {
                currentKopUrl = null;
                $('#kopPreviewImg').hide();
                $('#kopPlaceholder').show();
                $('#kopSizeControl').addClass('d-none');
                $('#kop_surat_path').val('');
                $('#btnRemoveKop').hide();
            }

            $('#template_nama_surat').text(nama);
            $('#editTemplateForm').attr('action', `/admin/jenis-surat/${currentJenisId}/template`);

            // Destroy existing CKEditor if any
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
                    $('#remove_logo').val('1');
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
                    $('#kopSizeControl').addClass('d-none');
                    $('#remove_kop').val('1');
                    $('#btnRemoveKop').hide();
                    currentKopUrl = null;
                    Swal.fire('Terhapus!', 'Kop surat telah dihapus', 'success');
                }
            });
        });

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

        function initCKEditor(content) {
            if (typeof CKEDITOR === 'undefined') {
                console.error('CKEditor tidak ditemukan!');
                return;
            }

            CKEDITOR.replace('templateEditor', {
                toolbarGroups: [{
                        name: 'document',
                        groups: ['mode', 'document', 'doctools']
                    },
                    {
                        name: 'clipboard',
                        groups: ['clipboard', 'undo']
                    },
                    {
                        name: 'editing',
                        groups: ['find', 'selection', 'spellchecker', 'editing']
                    },
                    {
                        name: 'forms',
                        groups: ['forms']
                    },
                    '/',
                    {
                        name: 'basicstyles',
                        groups: ['basicstyles', 'cleanup']
                    },
                    {
                        name: 'paragraph',
                        groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
                    },
                    {
                        name: 'links',
                        groups: ['links']
                    },
                    {
                        name: 'insert',
                        groups: ['insert']
                    },
                    '/',
                    {
                        name: 'styles',
                        groups: ['styles']
                    },
                    {
                        name: 'colors',
                        groups: ['colors']
                    },
                    {
                        name: 'tools',
                        groups: ['tools']
                    },
                    {
                        name: 'others',
                        groups: ['others']
                    },
                    {
                        name: 'about',
                        groups: ['about']
                    }
                ],
                removeButtons: 'Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField',
                font_names: 'Arial;Times New Roman;Calibri;Georgia;Verdana;Courier New;Tahoma;Trebuchet MS;Comic Sans MS;Impact;',
                fontSize_sizes: '8/8pt;9/9pt;10/10pt;11/11pt;12/12pt;14/14pt;16/16pt;18/18pt;20/20pt;22/22pt;24/24pt;26/26pt;28/28pt;36/36pt;48/48pt;72/72pt',
                height: 500,
                width: '100%',
                language: 'id',
                enterMode: CKEDITOR.ENTER_P,
                shiftEnterMode: CKEDITOR.ENTER_BR,
                filebrowserImageUploadUrl: '{{ route("upload.image") }}',
                filebrowserUploadUrl: '{{ route("upload.image") }}',
                extraPlugins: 'justify,font,colorbutton,panelbutton,richcombo',
                allowedContent: true,
                extraAllowedContent: '*[*]',
                startupFocus: true
            });

            CKEDITOR.instances.templateEditor.on('instanceReady', function() {
                editor = CKEDITOR.instances.templateEditor;
                editor.setData(content || '');

                var editable = editor.editable();
                editable.setStyles({
                    'font-family': 'Times New Roman, Times, serif',
                    'font-size': '12pt',
                    'line-height': '1.5',
                    'padding': '20mm',
                    'width': '210mm',
                    'min-height': '297mm',
                    'background': 'white',
                    'margin': '0 auto',
                    'box-shadow': '0 0 10px rgba(0,0,0,0.1)'
                });
            });
        }

        // Insert Variable
        $('.insert-variable').click(function() {
            let variable = $(this).data('var');
            if (editor) {
                editor.insertText(variable);
                editor.focus();
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

                Swal.fire({
                    title: 'Uploading...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

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
                            $('#remove_logo').val('0');
                            $('#btnRemoveLogo').show();
                            Swal.fire('Sukses', 'Logo berhasil diupload', 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let message = xhr.responseJSON?.message || 'Gagal upload logo';
                        Swal.fire('Error', message, 'error');
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

                Swal.fire({
                    title: 'Uploading...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

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
                            $('#kopSizeControl').removeClass('d-none');
                            $('#kop_surat_path').val(response.path);
                            $('#remove_kop').val('0');
                            $('#btnRemoveKop').show();
                            Swal.fire('Sukses', 'Kop surat berhasil diupload', 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let message = xhr.responseJSON?.message || 'Gagal upload kop surat';
                        Swal.fire('Error', message, 'error');
                        $('#kopInput').val('');
                    }
                });
            }
        });

        // Kop Width Slider
        $('#kopWidthSlider').on('input', function() {
            currentKopWidth = $(this).val();
            $('#kopPreviewImg').css('width', currentKopWidth + '%');
        });

        // Preview Template
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
            win.document.write(`<html><head><title>Print Surat</title><style>@page{size:A4;margin:15mm;}body{font-family:'Times New Roman',serif;}</style></head><body>${printContent}</body></html>`);
            win.document.close();
            win.print();
        });

        // Submit form
        $('#editTemplateForm').on('submit', function(e) {
            if (editor) {
                let content = editor.getData();
                $('#template_content').val(content);
            }

            // Tambahkan hidden fields untuk remove flags
            if ($('#remove_logo').val() === '1') {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'remove_logo',
                    value: '1'
                }).appendTo('#editTemplateForm');
            }
            if ($('#remove_kop').val() === '1') {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'remove_kop',
                    value: '1'
                }).appendTo('#editTemplateForm');
            }
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
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
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
                <p>Yang bertanda tangan dibawah ini:</p>
                <p>Nama : {dekan}<br>NIP : {nip_dekan}<br>Jabatan : Dekan</p>
                <p>Menyatakan bahwa:</p>
                <p>Nama : {nama_mahasiswa}<br>NPM : {npm}<br>Fakultas : {fakultas}<br>Program Studi : {prodi}<br>Semester : {semester}</p>
                <p>Adalah benar mahasiswa aktif Universitas.</p>
                <p>Demikian surat ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
            </div>
            <div style="margin-top:50px; text-align:right;">
                <p>Bandung, {tanggal_surat}</p>
                <p>Dekan,</p>
                <br><br>
                <p><strong><u>{dekan}</u></strong></p>
                <p>{nip_dekan}</p>
            </div>
        </div>`;
    }
</script>
@endpush
@endsection
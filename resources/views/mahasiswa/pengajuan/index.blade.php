@extends('layouts.app')

@section('title', 'Buat Surat Baru')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Header -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4"
                        style="background: linear-gradient(135deg, #6f42c1 0%, #8b5cf6 100%); border-radius: 16px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-1 fw-bold">
                                    <i class="bi bi-send me-2"></i>Form Pengajuan Surat
                                </h4>
                                <p class="text-white-50 mb-0">Ajukan permohonan surat dengan mudah dan cepat</p>
                            </div>
                            <div class="text-center">
                                <div class="bg-white rounded-circle p-3 d-inline-block">
                                    <i class="bi bi-file-text fs-1 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="card shadow-sm">
                    <div class="card-header bg-transparent border-0 pt-4">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-pencil-square me-2 text-primary"></i>Isi Data Pengajuan
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('mahasiswa.pengajuan.store') }}" method="POST" enctype="multipart/form-data"
                            id="formPengajuan">
                            @csrf

                            <!-- Jenis Surat -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Jenis Surat <span class="text-danger">*</span>
                                </label>
                                <select name="jenis_surat_id" id="jenis_surat_id" class="form-select form-select-lg"
                                    required>
                                    <option value="">-- Pilih Jenis Surat --</option>
                                    @foreach ($jenisSurats as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            data-kategori="{{ $jenis->kategoriSurat->nama_kategori ?? '' }}">
                                            {{ $jenis->nama_surat }}
                                            <small
                                                class="text-muted">({{ $jenis->kategoriSurat->nama_kategori ?? 'Tanpa Kategori' }})</small>
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih jenis surat yang ingin Anda ajukan</small>
                            </div>

                            <!-- Keperluan -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Keperluan <span class="text-danger">*</span>
                                </label>
                                <textarea name="keperluan" id="keperluan" class="form-control" rows="3" required
                                    placeholder="Jelaskan keperluan pengajuan surat dengan jelas..."></textarea>
                                <small class="text-muted">Deskripsikan tujuan dan keperluan pengajuan surat</small>
                            </div>

                            <!-- Dynamic Fields Container -->
                            <div id="dynamicFieldsContainer" style="display: none;">
                                <hr>
                                <div class="mb-3">
                                    <h6 class="fw-bold">
                                        <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                                        Data Tambahan
                                    </h6>
                                    <p class="text-muted small" id="jenisSuratInfo">Silakan lengkapi data tambahan sesuai
                                        jenis surat yang dipilih</p>
                                </div>
                                <div id="dynamicFields"></div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnSubmit">
                                    <i class="bi bi-send me-2"></i>Ajukan Surat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft-primary rounded-circle p-2 me-3">
                                        <i class="bi bi-clock-history text-primary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Proses Persetujuan</small>
                                        <span class="fw-semibold">1-3 Hari Kerja</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft-success rounded-circle p-2 me-3">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Status Surat</small>
                                        <span class="fw-semibold">Akan diproses petugas</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft-warning rounded-circle p-2 me-3">
                                        <i class="bi bi-download text-warning"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Download</small>
                                        <span class="fw-semibold">PDF setelah disetujui</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .bg-soft-primary {
                background: rgba(111, 66, 193, 0.1);
            }

            .bg-soft-success {
                background: rgba(40, 167, 69, 0.1);
            }

            .bg-soft-warning {
                background: rgba(255, 193, 7, 0.1);
            }

            .form-select-lg {
                font-size: 1rem;
                padding: 0.6rem 1rem;
            }

            .dynamic-field {
                animation: fadeInUp 0.3s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #6f42c1;
                box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.1);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                // ============================================
                // 1. JENIS SURAT CHANGE - Load Dynamic Fields
                // ============================================
                $('#jenis_surat_id').change(function() {
                    let jenisSuratId = $(this).val();

                    if (!jenisSuratId) {
                        $('#dynamicFieldsContainer').hide();
                        $('#dynamicFields').empty();
                        return;
                    }

                    // Tampilkan loading
                    $('#dynamicFields').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat form...</p>
            </div>
        `);
                    $('#dynamicFieldsContainer').show();

                    // Get jenis surat info
                    let selectedOption = $(this).find('option:selected');
                    let kategori = selectedOption.data('kategori') || '';
                    let namaSurat = selectedOption.text().trim();

                    $('#jenisSuratInfo').text(`Jenis Surat: ${namaSurat} | Kategori: ${kategori}`);

                    // AJAX request
                    $.ajax({
                        url: `/mahasiswa/pengajuan/get-fields/${jenisSuratId}`,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                renderDynamicFields(response.fields);
                            } else {
                                $('#dynamicFields').html(`
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Gagal memuat form: ${response.message || 'Terjadi kesalahan'}
                        </div>
                    `);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            $('#dynamicFields').html(`
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Terjadi kesalahan saat memuat form. Silakan refresh halaman.
                    </div>
                `);
                        }
                    });
                });

                // ============================================
                // 2. RENDER DYNAMIC FIELDS
                // ============================================
                function renderDynamicFields(fields) {
                    if (!fields || Object.keys(fields).length === 0) {
                        $('#dynamicFields').html(`
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Tidak ada data tambahan yang diperlukan untuk jenis surat ini.
                </div>
            `);
                        return;
                    }

                    let html = '';
                    let fileCount = 0;

                    for (let key in fields) {
                        let field = fields[key];
                        let required = field.required ? '<span class="text-danger">*</span>' : '';
                        let requiredAttr = field.required ? 'required' : '';

                        // Conditional field (untuk tipe pengajuan)
                        let conditionalAttr = '';
                        if (field.conditional_on) {
                            conditionalAttr =
                                `data-conditional-on="${field.conditional_on}" data-conditional-value="${field.conditional_value}"`;
                        }

                        html += `<div class="mb-3 dynamic-field" data-field-key="${key}" ${conditionalAttr}>`;
                        html += `<label class="form-label fw-bold">${field.label} ${required}</label>`;

                        switch (field.type) {
                            case 'textarea':
                                html += `<textarea name="${key}" class="form-control" rows="3" 
                              placeholder="${field.placeholder || ''}" 
                              ${requiredAttr}></textarea>`;
                                break;

                            case 'select':
                                html += `<select name="${key}" class="form-select" ${requiredAttr}>`;
                                html += `<option value="">Pilih ${field.label}</option>`;
                                if (field.options) {
                                    for (let val in field.options) {
                                        html += `<option value="${val}">${field.options[val]}</option>`;
                                    }
                                }
                                html += `</select>`;
                                break;

                            case 'file':
                                fileCount++;
                                html += `<input type="file" name="${key}" class="form-control" 
                              accept="${field.accept || '*/*'}" 
                              ${requiredAttr}>`;
                                html += `<small class="text-muted d-block mt-1">
                                <i class="bi bi-info-circle"></i> 
                                ${field.help_text || 'Format: PDF, JPG, PNG (Max 2MB)'}
                             </small>`;
                                break;

                            case 'date':
                                html += `<input type="date" name="${key}" class="form-control" ${requiredAttr}>`;
                                break;

                            case 'number':
                                html += `<input type="number" name="${key}" class="form-control" 
                              placeholder="${field.placeholder || ''}" 
                              ${requiredAttr}>`;
                                break;

                            case 'radio':
                                if (field.options) {
                                    for (let val in field.options) {
                                        html += `<div class="form-check">
                                <input class="form-check-input" type="radio" name="${key}" value="${val}" id="${key}_${val}" ${requiredAttr}>
                                <label class="form-check-label" for="${key}_${val}">${field.options[val]}</label>
                            </div>`;
                                    }
                                }
                                break;

                            default: // text
                                html += `<input type="text" name="${key}" class="form-control" 
                              placeholder="${field.placeholder || ''}" 
                              ${requiredAttr}>`;
                        }

                        if (field.help_text && field.type !== 'file') {
                            html += `<small class="text-muted d-block mt-1">
                            <i class="bi bi-info-circle"></i> ${field.help_text}
                         </small>`;
                        }

                        html += `</div>`;
                    }

                    // Tambahkan info file jika ada
                    if (fileCount > 0) {
                        html += `
                <div class="alert alert-info mt-2">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Info Upload File:</strong> Pastikan file yang diupload sesuai dengan ketentuan yang berlaku.
                    <ul class="mb-0 mt-1">
                        <li>Format file: PDF, JPG, JPEG, PNG</li>
                        <li>Ukuran maksimal: 2MB per file</li>
                    </ul>
                </div>
            `;
                    }

                    $('#dynamicFields').html(html);

                    // Handle conditional fields (misal: tipe_pengajuan)
                    handleConditionalFields();

                    // Trigger change untuk initial state
                    $('input[name="tipe_pengajuan"]:checked').trigger('change');
                }

                // ============================================
                // 3. HANDLE CONDITIONAL FIELDS
                // ============================================
                function handleConditionalFields() {
                    // Radio buttons untuk tipe pengajuan
                    $('input[name="tipe_pengajuan"]').off('change').on('change', function() {
                        let value = $(this).val();
                        let fieldKey = $(this).attr('name');

                        // Tampilkan/sembunyikan field yang bergantung
                        $('.dynamic-field[data-conditional-on="' + fieldKey + '"]').each(function() {
                            let conditionalValue = $(this).data('conditional-value');
                            if (value === conditionalValue) {
                                $(this).show();
                                $(this).find('input, select, textarea').prop('required', true);
                            } else {
                                $(this).hide();
                                $(this).find('input, select, textarea').prop('required', false);
                            }
                        });
                    });
                }

                // ============================================
                // 4. PREVIEW FILE UPLOAD
                // ============================================
                $(document).on('change', 'input[type="file"]', function(e) {
                    let file = e.target.files[0];
                    let fieldName = $(this).attr('name');

                    if (file) {
                        // Cek ukuran file
                        if (file.size > 2 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Ukuran File Terlalu Besar',
                                text: 'Ukuran file maksimal 2MB',
                                confirmButtonColor: '#6f42c1'
                            });
                            $(this).val('');
                            return;
                        }

                        // Tampilkan nama file
                        let validExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                        let fileExt = file.name.split('.').pop().toLowerCase();

                        if (!validExtensions.includes(fileExt)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Format File Tidak Didukung',
                                text: 'Format file yang didukung: PDF, JPG, JPEG, PNG',
                                confirmButtonColor: '#6f42c1'
                            });
                            $(this).val('');
                            return;
                        }

                        // Tampilkan info file
                        let fileSize = (file.size / 1024).toFixed(2);
                        $(this).after(`
                <div class="mt-1 text-success small">
                    <i class="bi bi-check-circle"></i> 
                    File: ${file.name} (${fileSize} KB)
                </div>
            `);
                    }
                });

                // ============================================
                // 5. SUBMIT FORM - Loading State
                // ============================================
                $('#formPengajuan').on('submit', function(e) {
                    let btnSubmit = $('#btnSubmit');

                    // Cek apakah ada file yang diupload
                    let hasFile = false;
                    $('input[type="file"]').each(function() {
                        if ($(this).val()) hasFile = true;
                    });

                    // Validasi required fields
                    let isValid = true;
                    $('.dynamic-field[style*="display: none"]').find('input, select, textarea').prop('required',
                        false);

                    // Reset required untuk field yang visible
                    $('.dynamic-field:visible').each(function() {
                        $(this).find('input, select, textarea').each(function() {
                            if ($(this).closest('.dynamic-field').data('field-key')) {
                                let field = $(this).closest('.dynamic-field');
                                let isRequired = field.find('.text-danger').length > 0;
                                if (isRequired) {
                                    $(this).prop('required', true);
                                }
                            }
                        });
                    });

                    // Submit form
                    btnSubmit.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Mengirim...
        `);
                });

                // ============================================
                // 6. TRIGGER CHANGE ON PAGE LOAD (if value exists)
                // ============================================
                if ($('#jenis_surat_id').val()) {
                    $('#jenis_surat_id').trigger('change');
                }

                // ============================================
                // 7. RESET FORM ON MODAL CLOSE
                // ============================================
                // Tidak ada modal, langsung form
            });
        </script>
    @endpush
@endsection

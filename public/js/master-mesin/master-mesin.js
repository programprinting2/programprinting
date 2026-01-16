// MesinHelper - Fungsi-fungsi umum untuk manajemen mesin
if (typeof MesinHelper === 'undefined') {
    window.MesinHelper = {
        /**
         * Format angka menjadi format mata uang Rupiah
         * @param {number} amount - Jumlah uang
         * @returns {string} Format Rupiah
         */
        formatCurrency: function(amount) {
            return amount.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        },

        /**
         * Format angka dengan pemisah ribuan
         * @param {number} number - Angka yang akan diformat
         * @returns {string} Format angka dengan pemisah ribuan
         */
        formatNumber: function(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        /**
         * Mengaplikasikan format mata uang pada input
         * @param {jQuery} $input - Element input yang akan diformat
         */
        applyMoneyFormat: function($input) {
            // Simpan posisi kursor
            const cursorPos = $input[0].selectionStart;
            const cursorEnd = $input[0].selectionEnd;
            
            // Ambil nilai tanpa format
            let value = $input.val().replace(/\./g, '');
            
            if (value !== '') {
                // Hitung perbedaan panjang sebelum dan sesudah format
                const lengthBefore = $input.val().length;
                
                // Format angka
                const formattedValue = this.formatNumber(value);
                $input.val(formattedValue);
                
                // Hitung perbedaan panjang
                const lengthAfter = formattedValue.length;
                const lengthDiff = lengthAfter - lengthBefore;
                
                // Sesuaikan posisi kursor
                if (cursorPos !== undefined) {
                    $input[0].setSelectionRange(cursorPos + lengthDiff, cursorEnd + lengthDiff);
                }
            }
        },

        /**
         * Inisialisasi format mata uang untuk semua input uang
         */
        initMoneyFormat: function() {
            // Format saat focus
            $(document).on('focus', '.money-format', (e) => {
                this.applyMoneyFormat($(e.target));
            });
            
            // Format saat input
            $(document).on('input', '.money-format', (e) => {
                this.applyMoneyFormat($(e.target));
            });
        },

        /**
         * Persiapan data form sebelum submit
         * @param {string} formSelector - Selector form
         */
        prepareFormSubmit: function(formSelector) {
            $(formSelector).on('submit', function() {
                // Hapus format dari semua input uang sebelum submit
                $(this).find('input[type="text"].money-format').each(function() {
                    const rawValue = $(this).val().replace(/\./g, '');
                    $(this).val(rawValue);
                });
            });
        }
    };
}

$(document).ready(function() {
    // Inisialisasi feather icons
    feather.replace();
    
    // Inisialisasi format mata uang
    MesinHelper.initMoneyFormat();
    
    // Persiapan form submit
    MesinHelper.prepareFormSubmit('#formTambahMesin');
    MesinHelper.prepareFormSubmit('#formEditMesin');
    
    // Inisialisasi ikon status khusus dengan ukuran yang lebih kecil
    feather.replace('.icon-status', {
        width: 14,
        height: 14
    });
    
    // Toggle view (Card/Table)
    $('#cardViewBtn').click(function() {
        $(this).addClass('active');
        $('#tableViewBtn').removeClass('active');
        $('#cardView').removeClass('d-none');
        $('#tableView').addClass('d-none');
        
        // Reinisialisasi ikon status
        feather.replace('.icon-status', {
            width: 14,
            height: 14
        });
    });
    
    $('#tableViewBtn').click(function() {
        $(this).addClass('active');
        $('#cardViewBtn').removeClass('active');
        $('#tableView').removeClass('d-none');
        $('#cardView').addClass('d-none');
        
        // Reinisialisasi ikon status
        feather.replace('.icon-status', {
            width: 14,
            height: 14
        });
    });
    
    // Handler untuk tombol hapus
    $('.btn-delete-mesin').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        
        Swal.fire({
            title: 'Hapus Mesin?',
            text: `Apakah Anda yakin ingin menghapus mesin "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formDeleteMesin' + id).submit();
            }
        });
    });
    
    // Handler untuk form tambah mesin
    $('#formTambahMesin').submit(function(e) {
        e.preventDefault();
        
        // Reset error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Validasi biaya produksi 
        const profileData = collectProfileData('biaya_perhitungan_profil_container');
        
        if (profileData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Biaya Produksi Tidak Lengkap',
                text: 'Minimal harus ada satu profil biaya produksi yang lengkap dan valid.',
                confirmButtonText: 'OK'
            });
            return false;
        }
        
        // Nonaktifkan tombol submit dan simpan teks aslinya
        let submitBtn = $('#submitFormMesin');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

        // Kumpulkan data profil biaya dan set ke input hidden SEBELUM FormData diambil
        $('#biaya_perhitungan_profil_json').val(JSON.stringify(profileData));

        let formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Data mesin berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#tambahMesinModal').modal('hide');
                        location.reload();
                    });
                } else {
                    // Aktifkan kembali tombol submit
                    submitBtn.prop('disabled', false).html(originalText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message || 'Terjadi kesalahan saat menyimpan data',
                    });
                }
            },
            error: function(xhr) {
                // Aktifkan kembali tombol submit
                submitBtn.prop('disabled', false).html(originalText);
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });
    
    // Handler untuk form edit mesin
    $('.btn-submit-edit').click(function(e) {
        e.preventDefault(); 

        let id = $(this).data('id');
        let form = $('#formEditMesin' + id);
        let submitBtn = $(this);
        let originalText = submitBtn.html();

        // Reset error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Validasi biaya produksi - pastikan setidaknya ada satu profil yang valid
        const profileData = collectProfileData('edit_biaya_perhitungan_profil_container' + id);
        
        if (profileData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Biaya Produksi Tidak Lengkap',
                text: 'Minimal harus ada satu profil biaya produksi yang lengkap dan valid.',
                confirmButtonText: 'OK'
            });
            return false;
        }

        // Nonaktifkan tombol submit
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');

        // Kumpulkan data profil biaya dan set ke input hidden
        // $('#edit_biaya_perhitungan_profil_json' + id).val(JSON.stringify(profileData));
        if (!$('#edit_biaya_perhitungan_profil_json' + id).data('updated-by-ajax')) {
            $('#edit_biaya_perhitungan_profil_json' + id).val(JSON.stringify(profileData));
        }
        let formData = new FormData(form[0]);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Data mesin berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    // Aktifkan kembali tombol submit
                    submitBtn.prop('disabled', false).html(originalText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message || 'Terjadi kesalahan saat menyimpan data',
                    });
                }
            },
            error: function(xhr) {
                // Aktifkan kembali tombol submit
                submitBtn.prop('disabled', false).html(originalText);
                
                let errorMessage = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });
    
    // Trigger perubahan tipe mesin saat halaman dimuat
    if ($('#tipe_mesin').val()) {
        $('#tipe_mesin').trigger('change');
    }
    
    // Trigger perubahan tipe mesin saat modal edit dibuka
    $('.edit-tipe-mesin').each(function() {
        $(this).trigger('change');
    });

    // Event handler untuk perubahan tipe mesin
    $(document).on('change', '#tipe_mesin, .edit-tipe-mesin', function() {
        const selectedType = $(this).val();
        const container = $(this).closest('.modal-body');
        
        // Reset semua field khusus
        container.find('.printer-specs').hide();
        container.find('.printer-specs input').prop('required', false);
        
        // Tampilkan field khusus berdasarkan tipe mesin
        if (selectedType) {
            // Semua tipe mesin sekarang menggunakan spesifikasi dinamis
            container.find('.printer-specs').show();
            container.find('.printer-specs input').prop('required', true);
        }
    });
}); 

// Fungsi untuk preview gambar
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
} 

// --- Fungsi untuk Mengelola Profil Biaya ---

// Fungsi untuk membuat form profil baru (untuk tambah mesin)
function createProfileForm() {
    const profileCount = $('#biaya_perhitungan_profil_container .card').length;
    const profileHtml = `
        <div class="profile-item accordion mb-3" id="accordionProfile${profileCount}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingProfile${profileCount}">
                    <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapseProfile${profileCount}" aria-expanded="true" 
                        aria-controls="collapseProfile${profileCount}">
                        <span class="nomor-profile fw-bold">Profil Baru</span>
                    </button>
                </h2>
                <div id="collapseProfile${profileCount}" class="accordion-collapse collapse show" 
                    aria-labelledby="headingProfile${profileCount}" data-bs-parent="#accordionProfile${profileCount}">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-10 mb-2">
                                <label class="form-label">Nama Profil <span class="text-danger">*</span></label>
                                <input type="text" class="form-control profile-nama" placeholder="Contoh: Profil Banner" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end mb-2">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile" data-row="profile_row_${profileCount}">
                                    <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                </button>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label class="form-label">Tipe Perhitungan <span class="text-danger">*</span></label>
                                <select class="form-select profile-tipe-perhitungan" required>
                                    <option value="per_satuan_area">Per Satuan Area (m²/cm²)</option>
                                    <option value="per_klik">Per Klik</option>
                                    <option value="per_lembar">Per Lembar</option>
                                    <option value="per_waktu">Per Waktu (Menit)</option>
                                    <option value="per_berat">Per Berat</option>
                                    <option value="per_job">Per Job</option>
                                </select>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_satuan_area">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Harga Tinta per Liter (Rp)</label>
                                    <input type="number" class="form-control profile-harga-tinta" placeholder="0" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Konsumsi Tinta per m² (mL)</label>
                                    <input type="number" class="form-control profile-konsumsi-tinta" placeholder="0" min="0" step="0.01">
                                    <small class="text-muted mt-1 d-block">
                                        Jumlah tinta dalam mililiter (mL) yang digunakan untuk mencetak 1 m²
                                    </small>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        <div class="text-muted mb-2 no-profile-biaya-message">
                                            Belum ada biaya tambahan untuk profil ini.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_klik" style="display: none;">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Ukuran Kertas</label>
                                    <input type="text" class="form-control profile-ukuran-kertas" placeholder="A4+, A3+, Custom, dll">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mode Warna</label>
                                    <select class="form-select profile-mode-warna">
                                        ${(window.MODE_WARNA_OPTIONS || []).map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Lebar Kertas (cm)</label>
                                    <input type="number" class="form-control profile-lebar-kertas" placeholder="0" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tinggi Kertas (cm)</label>
                                    <input type="number" class="form-control profile-tinggi-kertas" placeholder="0" min="0">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Harga per Klik (Rp)</label>
                                    <input type="number" class="form-control profile-harga-per-klik" placeholder="0" min="0">
                                    <small class="text-muted mt-1 d-block">Biaya per klik sesuai mode warna yang dipilih</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah Klik</label>
                                    <input type="number" class="form-control profile-jumlah-klik" placeholder="1" min="1" value="1">
                                    <small class="text-muted mt-1 d-block">Jumlah klik yang dibutuhkan untuk ukuran ini</small>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        <div class="text-muted mb-2 no-profile-biaya-message">
                                            Belum ada biaya tambahan untuk profil ini.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_lembar" style="display: none;">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya per Lembar (Rp)</label>
                                    <input type="number" class="form-control profile-biaya-per-lembar" placeholder="0" min="0">
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        <div class="text-muted mb-2 no-profile-biaya-message">
                                            Belum ada biaya tambahan untuk profil ini.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_waktu" style="display: none;">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya per Menit (Rp)</label>
                                    <input type="number" class="form-control profile-biaya-per-menit" placeholder="0" min="0">
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        <div class="text-muted mb-2 no-profile-biaya-message">
                                            Belum ada biaya tambahan untuk profil ini.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_berat" style="display: none;">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya per Kilogram (Rp)</label>
                                    <input type="number" class="form-control profile-biaya-per-kg" placeholder="0" min="0">
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        <div class="text-muted mb-2 no-profile-biaya-message">
                                            Belum ada biaya tambahan untuk profil ini.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_job" style="display: none;">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya per Job (Rp)</label>
                                    <input type="number" class="form-control profile-biaya-per-job" placeholder="0" min="0">
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        <div class="text-muted mb-2 no-profile-biaya-message">
                                            Belum ada biaya tambahan untuk profil ini.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#biaya_perhitungan_profil_container').append(profileHtml);
    feather.replace(); // Re-initialize feather icons
    $('#no_profil_message').hide();
}

// Fungsi untuk membuat form profil baru (untuk edit mesin)
function createEditProfileForm(mesinId, profileData = null, profileIndex = null) {
    const profileCount = $(`#edit_biaya_perhitungan_profil_container${mesinId} .profile-item`).length;
    if (profileIndex === null) {
        profileIndex = $(`#edit_biaya_perhitungan_profil_container${mesinId} .profile-item`).length;
    }
    const profileHtml = `
        <div class="profile-item accordion mb-3" id="editAccordionProfile${profileIndex}_${mesinId}" data-mesin-id="${mesinId}" data-profile-index="${profileIndex}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="editHeadingProfile${profileCount}_${mesinId}">
                    <button class="accordion-button bg-light collapsed" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#editCollapseProfile${profileCount}_${mesinId}" aria-expanded="false" 
                        aria-controls="editCollapseProfile${profileCount}_${mesinId}">
                        <span class="nomor-profile fw-bold">${profileData ? profileData.nama ?? 'Profil Baru' : 'Profil Baru'}</span>
                    </button>
                </h2>
                <div id="editCollapseProfile${profileCount}_${mesinId}" class="accordion-collapse collapse" 
                    aria-labelledby="editHeadingProfile${profileCount}_${mesinId}" data-bs-parent="#editAccordionProfile${profileCount}_${mesinId}">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-10 mb-2">
                                <label class="form-label">Nama Profil <span class="text-danger">*</span></label>
                                <input type="text" class="form-control profile-nama" placeholder="Contoh: Profil Banner" value="${profileData ? profileData.nama ?? '' : ''}" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end mb-2">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile" data-row="profile_row_edit_${profileCount}_${mesinId}">
                                    <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                </button>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <label class="form-label">Tipe Perhitungan <span class="text-danger">*</span></label>
                                <select class="form-select profile-tipe-perhitungan" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="per_satuan_area" ${profileData && profileData.tipe === 'per_satuan_area' ? 'selected' : ''}>Per Satuan Area (m²/cm²)</option>
                                    <option value="per_klik" ${profileData && profileData.tipe === 'per_klik' ? 'selected' : ''}>Per Klik</option>
                                    <option value="per_lembar" ${profileData && profileData.tipe === 'per_lembar' ? 'selected' : ''}>Per Lembar</option>
                                    <option value="per_waktu" ${profileData && profileData.tipe === 'per_waktu' ? 'selected' : ''}>Per Waktu (Menit)</option>
                                    <option value="per_berat" ${profileData && profileData.tipe === 'per_berat' ? 'selected' : ''}>Per Berat</option>
                                    <option value="per_job" ${profileData && profileData.tipe === 'per_job' ? 'selected' : ''}>Per Job</option>
                                </select>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_satuan_area" style="display: ${profileData && profileData.tipe === 'per_satuan_area' ? 'block' : 'none'};">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Harga Tinta per Liter (Rp)</label>
                                    <input type="number" class="form-control profile-harga-tinta" placeholder="0" min="0" value="${profileData && profileData.settings ? profileData.settings.harga_tinta_per_liter ?? 0 : 0}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Konsumsi Tinta per m² (mL)</label>
                                    <input type="number" class="form-control profile-konsumsi-tinta" placeholder="0" min="0" step="0.01" value="${profileData && profileData.settings ? profileData.settings.konsumsi_tinta_per_m2 ?? 0 : 0}">
                                    <small class="text-muted mt-1 d-block">
                                        Jumlah tinta dalam mililiter (mL) yang digunakan untuk mencetak 1 m²
                                    </small>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        ${profileData && profileData.settings && profileData.settings.biaya_tambahan_profil && profileData.settings.biaya_tambahan_profil.length > 0
                                            ? profileData.settings.biaya_tambahan_profil.map((biaya) => `
                                                <div class="row mb-2 profile-biaya-item">
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nama Biaya</label>
                                                        <input type="text" class="form-control form-control-sm profile-biaya-nama" placeholder="Contoh: Biaya Media" value="${biaya.nama ?? ''}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nilai (Rp/m²)</label>
                                                        <input type="number" class="form-control form-control-sm profile-biaya-nilai" placeholder="0" min="0" step="0.01" value="${biaya.nilai ?? 0}">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile-biaya">
                                                            <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            `).join('')
                                            : `
                                                <div class="text-muted mb-2 no-profile-biaya-message">
                                                    Belum ada biaya tambahan untuk profil ini.
                                                </div>
                                            `}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_klik" style="display: ${profileData && profileData.tipe === 'per_klik' ? 'block' : 'none'};">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Ukuran Kertas</label>
                                    <input type="text" class="form-control profile-ukuran-kertas" placeholder="A4+, A3+, Custom, dll" value="${profileData && profileData.settings ? profileData.settings.ukuran_kertas ?? '' : ''}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mode Warna</label>
                                    <select class="form-select profile-mode-warna">
                                        ${(window.MODE_WARNA_OPTIONS || []).map(opt =>
                                            `<option value="${opt}" ${profileData && profileData.settings && profileData.settings.mode_warna === opt ? 'selected' : ''}>${opt}</option>`
                                        ).join('')}
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Lebar Kertas (cm)</label>
                                    <input type="number" class="form-control profile-lebar-kertas" placeholder="0" min="0" value="${profileData && profileData.settings ? profileData.settings.lebar_kertas ?? 0 : 0}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tinggi Kertas (cm)</label>
                                    <input type="number" class="form-control profile-tinggi-kertas" placeholder="0" min="0" value="${profileData && profileData.settings ? profileData.settings.tinggi_kertas ?? 0 : 0}">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Harga per Klik (Rp)</label>
                                    <input type="number" class="form-control profile-harga-per-klik" placeholder="0" min="0" value="${profileData && profileData.settings ? profileData.settings.harga_per_klik ?? 0 : 0}">
                                    <small class="text-muted mt-1 d-block">Biaya per klik sesuai mode warna yang dipilih</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jumlah Klik</label>
                                    <input type="number" class="form-control profile-jumlah-klik" placeholder="1" min="1" value="${profileData && profileData.settings ? profileData.settings.jumlah_klik ?? 1 : 1}">
                                    <small class="text-muted mt-1 d-block">Jumlah klik yang dibutuhkan untuk ukuran ini</small>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        ${profileData && profileData.settings && profileData.settings.biaya_tambahan_profil && profileData.settings.biaya_tambahan_profil.length > 0
                                            ? profileData.settings.biaya_tambahan_profil.map((biaya) => `
                                                <div class="row mb-2 profile-biaya-item">
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nama Biaya</label>
                                                        <input type="text" class="form-control form-control-sm profile-biaya-nama" placeholder="Contoh: Biaya Media" value="${biaya.nama ?? ''}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nilai (Rp/klik)</label>
                                                        <input type="number" class="form-control form-control-sm profile-biaya-nilai" placeholder="0" min="0" step="0.01" value="${biaya.nilai ?? 0}">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile-biaya">
                                                            <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            `).join('')
                                            : `
                                                <div class="text-muted mb-2 no-profile-biaya-message">
                                                    Belum ada biaya tambahan untuk profil ini.
                                                </div>
                                            `}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_lembar" style="display: ${profileData && profileData.tipe === 'per_lembar' ? 'block' : 'none'};">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya per Lembar (Rp)</label>
                                    <input type="number" class="form-control profile-biaya-per-lembar" placeholder="0" min="0" value="${profileData && profileData.settings ? profileData.settings.biaya_per_lembar ?? 0 : 0}">
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        ${profileData && profileData.settings && profileData.settings.biaya_tambahan_profil && profileData.settings.biaya_tambahan_profil.length > 0
                                            ? profileData.settings.biaya_tambahan_profil.map((biaya) => `
                                                <div class="row mb-2 profile-biaya-item">
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nama Biaya</label>
                                                        <input type="text" class="form-control form-control-sm profile-biaya-nama" placeholder="Contoh: Biaya Media" value="${biaya.nama ?? ''}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nilai (Rp/lembar)</label>
                                                        <input type="number" class="form-control form-control-sm profile-biaya-nilai" placeholder="0" min="0" step="0.01" value="${biaya.nilai ?? 0}">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile-biaya">
                                                            <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            `).join('')
                                            : `
                                                <div class="text-muted mb-2 no-profile-biaya-message">
                                                    Belum ada biaya tambahan untuk profil ini.
                                                </div>
                                            `}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_waktu" style="display: ${profileData && profileData.tipe === 'per_waktu' ? 'block' : 'none'};">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya per Menit (Rp)</label>
                                    <input type="number" class="form-control profile-biaya-per-menit" placeholder="0" min="0" value="${profileData && profileData.settings ? profileData.settings.biaya_per_menit ?? 0 : 0}">
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        ${profileData && profileData.settings && profileData.settings.biaya_tambahan_profil && profileData.settings.biaya_tambahan_profil.length > 0
                                            ? profileData.settings.biaya_tambahan_profil.map((biaya) => `
                                                <div class="row mb-2 profile-biaya-item">
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nama Biaya</label>
                                                        <input type="text" class="form-control form-control-sm profile-biaya-nama" placeholder="Contoh: Biaya Media" value="${biaya.nama ?? ''}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nilai (Rp/menit)</label>
                                                        <input type="number" class="form-control form-control-sm profile-biaya-nilai" placeholder="0" min="0" step="0.01" value="${biaya.nilai ?? 0}">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile-biaya">
                                                            <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            `).join('')
                                            : `
                                                <div class="text-muted mb-2 no-profile-biaya-message">
                                                    Belum ada biaya tambahan untuk profil ini.
                                                </div>
                                            `}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_berat" style="display: ${profileData && profileData.tipe === 'per_berat' ? 'block' : 'none'};">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya per Kilogram (Rp)</label>
                                    <input type="number" class="form-control profile-biaya-per-kg" placeholder="0" min="0" value="${profileData && profileData.settings ? profileData.settings.biaya_per_kg ?? 0 : 0}">
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        ${profileData && profileData.settings && profileData.settings.biaya_tambahan_profil && profileData.settings.biaya_tambahan_profil.length > 0
                                            ? profileData.settings.biaya_tambahan_profil.map((biaya) => `
                                                <div class="row mb-2 profile-biaya-item">
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nama Biaya</label>
                                                        <input type="text" class="form-control form-control-sm profile-biaya-nama" placeholder="Contoh: Biaya Media" value="${biaya.nama ?? ''}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nilai (Rp/kg)</label>
                                                        <input type="number" class="form-control form-control-sm profile-biaya-nilai" placeholder="0" min="0" step="0.01" value="${biaya.nilai ?? 0}">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile-biaya">
                                                            <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            `).join('')
                                            : `
                                                <div class="text-muted mb-2 no-profile-biaya-message">
                                                    Belum ada biaya tambahan untuk profil ini.
                                                </div>
                                            `}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-tipe-settings" data-tipe="per_job" style="display: ${profileData && profileData.tipe === 'per_job' ? 'block' : 'none'};">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label class="form-label">Biaya per Job (Rp)</label>
                                    <input type="number" class="form-control profile-biaya-per-job" placeholder="0" min="0" value="${profileData && profileData.settings ? profileData.settings.biaya_per_job ?? 0 : 0}">
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header bg-light py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">Biaya Tambahan Profil</label>
                                        <button type="button" class="btn btn-outline-primary btn-sm tambah-profile-biaya">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Biaya
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="profile-biaya-tambahan-container">
                                        ${profileData && profileData.settings && profileData.settings.biaya_tambahan_profil && profileData.settings.biaya_tambahan_profil.length > 0
                                            ? profileData.settings.biaya_tambahan_profil.map((biaya) => `
                                                <div class="row mb-2 profile-biaya-item">
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nama Biaya</label>
                                                        <input type="text" class="form-control form-control-sm profile-biaya-nama" placeholder="Contoh: Biaya Media" value="${biaya.nama ?? ''}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <label class="form-label small">Nilai (Rp/job)</label>
                                                        <input type="number" class="form-control form-control-sm profile-biaya-nilai" placeholder="0" min="0" step="0.01" value="${biaya.nilai ?? 0}">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile-biaya">
                                                            <i data-feather="trash-2" class="icon-sm"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            `).join('')
                                            : `
                                                <div class="text-muted mb-2 no-profile-biaya-message">
                                                    Belum ada biaya tambahan untuk profil ini.
                                                </div>
                                            `}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $(`#edit_biaya_perhitungan_profil_container${mesinId}`).append(profileHtml);
    feather.replace(); // Re-initialize feather icons
    $(`#edit_no_profil_message${mesinId}`).hide();

    // Trigger perhitungan awal untuk menampilkan total
    const profileItem = $(`#editAccordionProfile${profileCount}_${mesinId}`);
    const namaProfil = profileItem.find('.profile-nama').val() || 'Profil Baru';
    updateProfileTitle(profileItem, namaProfil);
}

// Fungsi untuk mengumpulkan data profil dari form
function collectProfileData(containerId) {
    const profiles = [];
    $(`#${containerId} .profile-item`).each(function() {
        const nama = $(this).find('.profile-nama').val()?.trim();
        const tipe = $(this).find('.profile-tipe-perhitungan').val();
        let settings = {};

        // Collect settings based on calculation type
        if (tipe === 'per_satuan_area') {
            settings.harga_tinta_per_liter = parseFloat($(this).find('.profile-harga-tinta').val()) || 0;
            settings.konsumsi_tinta_per_m2 = parseFloat($(this).find('.profile-konsumsi-tinta').val()) || 0;
        } else if (tipe === 'per_klik') {
            settings.ukuran_kertas = $(this).find('.profile-ukuran-kertas').val() || '';
            settings.mode_warna = $(this).find('.profile-mode-warna').val() || '';
            settings.lebar_kertas = parseFloat($(this).find('.profile-lebar-kertas').val()) || 0;
            settings.tinggi_kertas = parseFloat($(this).find('.profile-tinggi-kertas').val()) || 0;
            settings.harga_per_klik = parseFloat($(this).find('.profile-harga-per-klik').val()) || 0;
            let jumlahKlik = parseFloat($(this).find('.profile-jumlah-klik').val()) || 1;
            if (jumlahKlik < 1) jumlahKlik = 1;
            settings.jumlah_klik = jumlahKlik;
        } else if (tipe === 'per_lembar') {
            settings.biaya_per_lembar = parseFloat($(this).find('.profile-biaya-per-lembar').val()) || 0;
        } else if (tipe === 'per_waktu') {
            settings.biaya_per_menit = parseFloat($(this).find('.profile-biaya-per-menit').val()) || 0;
        } else if (tipe === 'per_berat') {
            settings.biaya_per_kg = parseFloat($(this).find('.profile-biaya-per-kg').val()) || 0;
        } else if (tipe === 'per_job') {
            settings.biaya_per_job = parseFloat($(this).find('.profile-biaya-per-job').val()) || 0;
        }
        
        // Kumpulkan biaya tambahan profil
        const biayaTambahanProfil = [];
        const biayaNamaSet = new Set(); // Untuk mengecek duplikasi nama biaya
        
        $(this).find('.profile-biaya-tambahan-container .profile-biaya-item').each(function() {
            const biayaNama = $(this).find('.profile-biaya-nama').val()?.trim();
            const biayaNilai = parseFloat($(this).find('.profile-biaya-nilai').val()) || 0;
            
            // Validasi: nama harus ada, tidak duplikasi, dan nilai valid
            if (biayaNama) {
                if (biayaNamaSet.has(biayaNama)) {
                    // Tampilkan error duplikasi
                    $(this).find('.profile-biaya-nama').addClass('is-invalid');
                    $(this).find('.invalid-feedback').remove();
                    $(this).find('.profile-biaya-nama').after('<div class="invalid-feedback">Nama biaya sudah digunakan dalam profil ini.</div>');
                    return; 
                }
                
                if (!isNaN(biayaNilai)) {
                    biayaNamaSet.add(biayaNama);
                    biayaTambahanProfil.push({ 
                        nama: biayaNama, 
                        nilai: biayaNilai 
                    });
                }
            }
        });
        
        // Hanya tambahkan biaya_tambahan_profil jika ada biaya
        if (biayaTambahanProfil.length > 0) {
            settings.biaya_tambahan_profil = biayaTambahanProfil;
        }

        // Validasi field wajib untuk semua tipe perhitungan
        let isValid = true;
        let errorMessage = '';

        if (tipe === 'per_satuan_area') {
            if (settings.harga_tinta_per_liter < 0 || settings.konsumsi_tinta_per_m2 < 0) {
                isValid = false;
                errorMessage = 'Harga tinta per liter dan konsumsi tinta per m² harus diisi dengan nilai > 0';
            }
        } else if (tipe === 'per_klik') {
            if (settings.ukuran_kertas == '' || settings.mode_warna == '' || settings.lebar_kertas <= 0 || settings.tinggi_kertas <= 0 || settings.harga_per_klik < 0 || settings.jumlah_klik < 1) {
                isValid = false;
                errorMessage = 'Ukuran kertas, mode warna, dimensi kertas, harga per klik, dan jumlah klik harus diisi dengan benar';
            }
        } else if (tipe === 'per_lembar') {
            if (settings.biaya_per_lembar <= 0) {
                isValid = false;
                errorMessage = 'Biaya per lembar harus diisi dengan nilai > 0';
            }
        } else if (tipe === 'per_waktu') {
            if (settings.biaya_per_menit <= 0) {
                isValid = false;
                errorMessage = 'Biaya per menit harus diisi dengan nilai > 0';
            }
        } else if (tipe === 'per_berat') {
            if (settings.biaya_per_kg <= 0) {
                isValid = false;
                errorMessage = 'Biaya per kilogram harus diisi dengan nilai > 0';
            }
        } else if (tipe === 'per_job') {
            if (settings.biaya_per_job <= 0) {
                isValid = false;
                errorMessage = 'Biaya per job harus diisi dengan nilai > 0';
            }
        }

        if (!isValid) {
            // Tampilkan error dan skip push
            $(this).find('.profile-error-message').remove();
            $(this).prepend(`<div class="alert alert-danger alert-dismissible fade show profile-error-message" role="alert">
                <strong>Profil "${nama}" tidak valid:</strong> ${errorMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`);
            return; 
        }

        if (nama && tipe) {
            const result = calculateProfileTotal($(this));
            profiles.push({ 
                nama: nama, 
                tipe: tipe, 
                settings: settings,
                total: result.total
            });
        }
    });
    return profiles;
}

// --- Event Listeners untuk Modal Tambah Mesin ---

// Tambah Profil button handler
$(document).on('click', '#tambah_profil', function() {
    createProfileForm();
});

// Hapus Profil button handler
$(document).on('click', '.remove-profile', function() {
    const profileItem = $(this).closest('.profile-item');
    profileItem.remove();
    
    // Show message if no profiles left
    if ($('#biaya_perhitungan_profil_container .profile-item').length === 0) {
        $('#no_profil_message').show();
    }
});

// Tambah Biaya Profil button handler
$(document).on('click', '.tambah-profile-biaya', function() {
    const container = $(this).closest('.profile-tipe-settings').find('.profile-biaya-tambahan-container');
    const tipePerhitungan = $(this).closest('.profile-item').find('.profile-tipe-perhitungan').val();
    const satuan = getSatuanBiaya(tipePerhitungan);
    
    // Cek apakah sudah ada biaya dengan nama yang sama
    const existingBiayaNama = new Set();
    container.find('.profile-biaya-item').each(function() {
        const nama = $(this).find('.profile-biaya-nama').val()?.trim();
        if (nama) {
            existingBiayaNama.add(nama.toLowerCase());
        }
    });
    
    const biayaHtml = `
        <div class="row mb-2 profile-biaya-item">
            <div class="col-md-5">
                <label class="form-label small">Nama Biaya</label>
                <input type="text" class="form-control form-control-sm profile-biaya-nama" 
                    placeholder="Contoh: Biaya Media">
            </div>
            <div class="col-md-5">
                <label class="form-label small">Nilai (Rp/${satuan})</label>
                <input type="number" class="form-control form-control-sm profile-biaya-nilai" 
                    placeholder="0" min="0" step="0.01">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-profile-biaya">
                    <i data-feather="trash-2" class="icon-sm"></i> Hapus
                </button>
            </div>
        </div>
    `;
    container.append(biayaHtml);
    feather.replace(); // Re-initialize feather icons
    container.find('.no-profile-biaya-message').hide();
    
    // Tambahkan validasi untuk mencegah duplikasi nama biaya
    container.find('.profile-biaya-nama').last().on('input', function() {
        const input = $(this);
        const nama = input.val()?.trim().toLowerCase();
        
        // Cek duplikasi dengan nama biaya yang sudah ada
        let isDuplicate = false;
        container.find('.profile-biaya-item').not(input.closest('.profile-biaya-item')).each(function() {
            const existingNama = $(this).find('.profile-biaya-nama').val()?.trim().toLowerCase();
            if (existingNama === nama) {
                isDuplicate = true;
                return false; // break the loop
            }
        });
        
        if (isDuplicate) {
            input.addClass('is-invalid');
            if (!input.next('.invalid-feedback').length) {
                input.after('<div class="invalid-feedback">Nama biaya ini sudah ada</div>');
            }
        } else {
            input.removeClass('is-invalid');
            input.next('.invalid-feedback').remove();
        }
    });
});

// Hapus Biaya Profil button handler
$(document).on('click', '.remove-profile-biaya', function() {
    const container = $(this).closest('.profile-biaya-tambahan-container');
    const profileItem = $(this).closest('.profile-item');
    const mesinId = profileItem.data('mesin-id') || $('input[name="mesin_id"]').val();
    const profileIndex = profileItem.data('profile-index');
    const biayaNama = $(this).closest('.profile-biaya-item').find('.profile-biaya-nama').val()?.trim();
    
    // Jika dalam mode edit dan ada mesin ID, kirim request ke backend
    if (mesinId && profileIndex !== undefined && biayaNama) {
        Swal.fire({
            title: 'Hapus Biaya Tambahan?',
            text: `Apakah Anda yakin ingin menghapus biaya tambahan "${biayaNama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                removeBiayaTambahanFromBackend(mesinId, profileIndex, biayaNama, $(this));
            }
        });
    } else {
        // Mode tambah baru atau tidak ada data, hapus dari UI saja
        $(this).closest('.profile-biaya-item').remove();
        updateProfileTitle(profileItem, profileItem.find('.profile-nama').val() || 'Profil Baru');
        
        if (container.find('.profile-biaya-item').length === 0) {
            container.find('.no-profile-biaya-message').show();
        }
    }
});

// Tipe Perhitungan dropdown change handler
$(document).on('change', '.profile-tipe-perhitungan', function() {
    const selectedType = $(this).val();
    const profileItem = $(this).closest('.profile-item');
    const settingsContainer = profileItem.find('.profile-tipe-settings');
    
    // Hide all settings containers initially
    settingsContainer.hide();
    
    // Show the selected type's settings
    settingsContainer.filter(`[data-tipe="${selectedType}"]`).show();
    
    // Update satuan biaya tambahan berdasarkan tipe
    const biayaContainer = profileItem.find('.profile-biaya-tambahan-container');
    biayaContainer.find('.profile-biaya-item').each(function() {
        const label = $(this).find('label:contains("Nilai")');
        label.text(`Nilai (Rp/${getSatuanBiaya(selectedType)})`);
    });
    
    // Update title dengan total baru
    const namaProfil = profileItem.find('.profile-nama').val() || 'Profil Baru';
    updateProfileTitle(profileItem, namaProfil);
});

// Fungsi helper untuk mendapatkan satuan biaya
function getSatuanBiaya(tipe) {
    switch(tipe) {
        case 'per_satuan_area': return 'm²';
        case 'per_klik': return 'klik';
        case 'per_lembar': return 'lembar';
        case 'per_waktu': return 'menit';
        case 'per_berat': return 'kg';
        case 'per_job': return 'job';
        default: return '';
    }
}

// --- Event Listeners untuk Modal Edit Mesin ---

// Tambah Profil button handler for Edit Modal
// $(document).on('click', '.tambah-profil-edit', function() {
//     const mesinId = $(this).data('id');
//     createEditProfileForm(mesinId);
// });
$(document).on('click', '.tambah-profil-edit', function() {
    const mesinId = $(this).data('id');
    const profileIndex = $(`#edit_biaya_perhitungan_profil_container${mesinId} .profile-item`).length;
    createEditProfileForm(mesinId, null, profileIndex);
    $(`#edit_no_profil_message${mesinId}`).hide();
    // Trigger change event untuk mengatur satuan biaya
    $(`#edit_biaya_perhitungan_profil_container${mesinId} .profile-item:last .profile-tipe-perhitungan`).trigger('change');
});

// Hapus Profil button handler for Edit Modal
$(document).on('click', '.remove-profile', function() {
    const profileItem = $(this).closest('.profile-item');
    const mesinId = profileItem.data('mesin-id');
    profileItem.remove();
    
    // Show message if no profiles left
    if ($(`#edit_biaya_perhitungan_profil_container${mesinId} .profile-item`).length === 0) {
        $(`#edit_no_profil_message${mesinId}`).show();
    }
});

// Tambah Biaya Profil button handler for Edit Modal (reusing .tambah-profile-biaya)

// Hapus Biaya Profil button handler for Edit Modal (reusing .remove-profile-biaya)

// Tipe Perhitungan dropdown change handler for Edit Modal (reusing .profile-tipe-perhitungan)

// Trigger change event on existing profile type dropdowns when Edit modal is opened/loaded
$(document).on('shown.bs.modal', function (e) {
    if ($(e.target).is('[id^="editMesinModal"]')) {
        const mesinId = $(e.target).attr('id').replace('editMesinModal', '');
        $(`#edit_biaya_perhitungan_profil_container${mesinId} .profile-tipe-perhitungan`).trigger('change');
    }
});

// Load existing profile data when Edit modal is opened/loaded
$(document).on('shown.bs.modal', function (e) {
    if ($(e.target).is('[id^="editMesinModal"]')) {
        const mesinId = $(e.target).attr('id').replace('editMesinModal', '');
        const existingProfilesJson = $(`#edit_biaya_perhitungan_profil_json${mesinId}`).val();
        const container = $(`#edit_biaya_perhitungan_profil_container${mesinId}`);
        
        // Clear existing dynamic content but keep the empty message container
        container.find('.profile-item').remove(); 
        
        if (existingProfilesJson) {
            try {
                const existingProfiles = JSON.parse(existingProfilesJson);
                if (existingProfiles && existingProfiles.length > 0) {
                    existingProfiles.forEach((profile, index) => {
                        createEditProfileForm(mesinId, profile, index);
                    });
                    $(`#edit_no_profil_message${mesinId}`).hide();
                } else {
                     $(`#edit_no_profil_message${mesinId}`).show();
                }
            } catch (error) {
                console.error("Error parsing existing profile data:", error);
                 $(`#edit_no_profil_message${mesinId}`).show();
            }
        } else {
             $(`#edit_no_profil_message${mesinId}`).show();
        }
    }
});


// Event handler untuk update title accordion saat nama profil berubah
$(document).on('input', '.profile-nama', function() {
    const profileItem = $(this).closest('.profile-item');
    const newTitle = $(this).val() || 'Profil Baru';
    updateProfileTitle(profileItem, newTitle);
});

// Fungsi untuk update title accordion dengan total biaya
function updateProfileTitle(profileItem, namaProfil) {
    const result = calculateProfileTotal(profileItem);
    const totalFormatted = result.total.toLocaleString('id-ID');
    const titleText = `${namaProfil} - Total: Rp ${totalFormatted} ${result.satuan}`;
    profileItem.find('.nomor-profile').text(titleText);
}

// Event handler untuk update total biaya saat ada perubahan input
$(document).on('input', '.profile-item input, .profile-item select', function() {
    const profileItem = $(this).closest('.profile-item');
    const namaProfil = profileItem.find('.profile-nama').val() || 'Profil Baru';
    updateProfileTitle(profileItem, namaProfil);
});

// Fungsi untuk menghapus biaya tambahan dari backend
function removeBiayaTambahanFromBackend(mesinId, profileIndex, biayaNama, buttonElement) {
    const container = buttonElement.closest('.profile-biaya-tambahan-container');
    const profileItem = buttonElement.closest('.profile-item');
    
    // Disable button selama proses
    buttonElement.prop('disabled', true);
    const originalText = buttonElement.html();
    buttonElement.html('<i class="fas fa-spinner fa-spin"></i>');
    
    $.ajax({
        url: `/backend/master-mesin/${mesinId}/remove-biaya-tambahan`,
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            profile_index: profileIndex,
            biaya_nama: biayaNama
        },
        success: function(response) {
            if (response.success) {
                // Hapus dari UI
                buttonElement.closest('.profile-biaya-item').remove();
                
                // Update total di title accordion
                updateProfileTitle(profileItem, profileItem.find('.profile-nama').val() || 'Profil Baru');
                
                // Show message if no profile costs left
                if (container.find('.profile-biaya-item').length === 0) {
                    container.find('.no-profile-biaya-message').show();
                }
            
                if (response.mesin && response.mesin.biaya_perhitungan_profil) {
                    $(`#edit_biaya_perhitungan_profil_json${mesinId}`).val(JSON.stringify(response.mesin.biaya_perhitungan_profil));
                } else {
                    console.warn('WARNING: biaya_perhitungan_profil tidak ditemukan di response!');
                    // Fallback: Update hidden input dengan data dari UI yang sudah terupdate
                    const updatedProfileData = collectProfileData('edit_biaya_perhitungan_profil_container' + mesinId);
                    $(`#edit_biaya_perhitungan_profil_json${mesinId}`).val(JSON.stringify(updatedProfileData));
                }
                $(`#edit_biaya_perhitungan_profil_json${mesinId}`).data('updated-by-ajax', true);
                
                // Tampilkan pesan sukses
                Swal.fire({
                    icon:'success',
                    title:'Berhasil!',
                    text:'Biaya tambahan berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500
                })
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message || 'Gagal menghapus biaya tambahan'
                });
            }
        },
        error: function(xhr) {
            let message = 'Terjadi kesalahan saat menghapus biaya tambahan';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: message
            });
        },
        complete: function() {
            // Re-enable button
            buttonElement.prop('disabled', false);
            buttonElement.html(originalText);
        }
    });
}

// Fungsi untuk menghitung total biaya per profil
function calculateProfileTotal(profileItem) {
    const tipe = profileItem.find('.profile-tipe-perhitungan').val();
    let totalBiaya = 0;
    let satuan = '';

    // Hitung biaya berdasarkan tipe perhitungan
    if (tipe === 'per_satuan_area') {
        const hargaTinta = parseFloat(profileItem.find('.profile-harga-tinta').val()) || 0;
        const konsumsiTinta = parseFloat(profileItem.find('.profile-konsumsi-tinta').val()) || 0;
        totalBiaya = hargaTinta * (konsumsiTinta / 1000); // Konversi mL ke L
        satuan = 'Rp/m²';
    } else if (tipe === 'per_klik') {
        const hargaPerKlik = parseFloat(profileItem.find('.profile-harga-per-klik').val()) || 0;
        let jumlahKlik = parseFloat(profileItem.find('.profile-jumlah-klik').val()) || 1;
        if (jumlahKlik < 1) jumlahKlik = 1;
        totalBiaya = hargaPerKlik * jumlahKlik;
        satuan = 'Rp/klik';
    } else if (tipe === 'per_lembar') {
        totalBiaya = parseFloat(profileItem.find('.profile-biaya-per-lembar').val()) || 0;
        satuan = 'Rp/lembar';
    } else if (tipe === 'per_waktu') {
        totalBiaya = parseFloat(profileItem.find('.profile-biaya-per-menit').val()) || 0;
        satuan = 'Rp/menit';
    } else if (tipe === 'per_berat') {
        totalBiaya = parseFloat(profileItem.find('.profile-biaya-per-kg').val()) || 0;
        satuan = 'Rp/kg';
    } else if (tipe === 'per_job') {
        totalBiaya = parseFloat(profileItem.find('.profile-biaya-per-job').val()) || 0;
        satuan = 'Rp/job';
    }

    // Tambahkan biaya tambahan
    profileItem.find(`.profile-tipe-settings[data-tipe="${tipe}"] .profile-biaya-item`).each(function() {
        const biayaNilai = parseFloat($(this).find('.profile-biaya-nilai').val()) || 0;
        totalBiaya += biayaNilai;
    });

    return {
        total: totalBiaya,
        satuan: satuan
    };
} 
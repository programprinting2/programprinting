// Fungsi untuk mengisi form dengan data pemasok
window.fillFormData = function(data) {
    // Reset form dan state
    const form = document.getElementById('formEditPemasok');
    if (!form) {
        return;
    }
    form.reset();

    // Reset konten dinamis
    $("#edit-alamat-list").empty();
    $("#edit-rekening-list").empty();
    $("#editTableUtangAwal tbody tr:not(:first)").remove();

    // Isi data umum
    $("#edit_id").val(data.id);
    $("#edit_nama").val(data.nama);
    $("#edit_handphone").val(data.handphone);
    $("#edit_no_telp").val(data.no_telp);
    $("#edit_email").val(data.email);
    $("#edit_website").val(data.website);
    $("#edit_status").val(data.status ? "1" : "0");
    $("#edit_kategori").val(data.kategori);
    $("#edit_syarat_pembayaran").val(data.syarat_pembayaran);
    $("#edit_default_diskon").val(data.default_diskon);
    $("#edit_deskripsi_pembelian").val(data.deskripsi_pembelian);
    $("#edit_akun_utang").val(data.akun_utang);
    $("#edit_akun_uang_muka").val(data.akun_uang_muka);
    $("#edit_npwp").val(data.npwp);
    $("#edit_nik").val(data.nik);
    $("#edit_wajib_pajak").prop("checked", data.wajib_pajak);

    // Isi data alamat
    if (data.alamat && Array.isArray(data.alamat) && data.alamat.length > 0) {
        data.alamat.forEach((alamat, index) => {
            addAlamatItem(alamat, index);
        });
    } else {
        // Jika tidak ada alamat, tambahkan satu alamat kosong
        addAlamatItem(null, 0);
    }

    // Set alamat utama
    if (data.alamat_utama !== undefined) {
        $("#edit_alamat_utama").val(data.alamat_utama);
    }

    // Isi data rekening
    if (data.rekening && Array.isArray(data.rekening) && data.rekening.length > 0) {
        // Hapus semua rekening yang ada
        $("#edit-rekening-list").empty();
        
        // Tambahkan setiap rekening
        data.rekening.forEach((rekening, index) => {
            let rekeningCount = $("#edit-rekening-list .rekening-item").length + 1;
            let html = `
                <div class="rekening-item accordion mb-3" id="accordionRekening${rekeningCount}">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingRekening${rekeningCount}">
                            <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#collapseRekening${rekeningCount}" aria-expanded="true" 
                                aria-controls="collapseRekening${rekeningCount}">
                                <span class="nomor-rekening fw-bold">${rekening.bank || `Rekening ${index + 1}`}</span>
                            </button>
                        </h2>
                        <div id="collapseRekening${rekeningCount}" class="accordion-collapse collapse show" 
                            aria-labelledby="headingRekening${rekeningCount}" data-bs-parent="#accordionRekening${rekeningCount}">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Bank</label>
                                        <input type="text" class="form-control rekening-bank-input" name="rekening_bank[]" 
                                            placeholder="Nama Bank" value="${rekening.bank || ''}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Cabang</label>
                                        <input type="text" class="form-control" name="rekening_cabang[]" 
                                            placeholder="Cabang Bank" value="${rekening.cabang || ''}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nomor Rekening</label>
                                        <input type="text" class="form-control" name="rekening_nomor[]" 
                                            placeholder="Nomor Rekening" value="${rekening.nomor || ''}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Nama Pemilik</label>
                                        <input type="text" class="form-control" name="rekening_nama_pemilik[]" 
                                            placeholder="Nama Pemilik" value="${rekening.nama_pemilik || ''}">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm removeRekening">
                                            <i data-feather="trash-2" class="icon-sm"></i> Hapus Rekening
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            $("#edit-rekening-list").append(html);
        });
    } else {
        // Jika tidak ada rekening, tambahkan satu rekening kosong
        let html = `
            <div class="rekening-item accordion mb-3" id="accordionRekening1">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingRekening1">
                        <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapseRekening1" aria-expanded="true" 
                            aria-controls="collapseRekening1">
                            <span class="nomor-rekening fw-bold">Rekening 1</span>
                        </button>
                    </h2>
                    <div id="collapseRekening1" class="accordion-collapse collapse show" 
                        aria-labelledby="headingRekening1" data-bs-parent="#accordionRekening1">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Bank</label>
                                    <input type="text" class="form-control rekening-bank-input" name="rekening_bank[]" 
                                        placeholder="Nama Bank">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cabang</label>
                                    <input type="text" class="form-control" name="rekening_cabang[]" 
                                        placeholder="Cabang Bank">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" class="form-control" name="rekening_nomor[]" 
                                        placeholder="Nomor Rekening">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nama Pemilik</label>
                                    <input type="text" class="form-control" name="rekening_nama_pemilik[]" 
                                        placeholder="Nama Pemilik">
                                </div>
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm removeRekening">
                                        <i data-feather="trash-2" class="icon-sm"></i> Hapus Rekening
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        $("#edit-rekening-list").append(html);
    }

    // Set rekening utama
    if (data.rekening_utama !== undefined) {
        $("#edit_rekening_utama").val(data.rekening_utama);
    }

    // Isi data utang awal
    if (data.utang_awal && Array.isArray(data.utang_awal)) {
        
        // Hapus semua baris utang yang ada
        $("#editTableUtangAwal tbody").empty();
        
        // Tambahkan baris untuk setiap utang
        data.utang_awal.forEach((utang, index) => {
            const tbody = document.querySelector("#editTableUtangAwal tbody");
            const newRow = document.createElement('tr');
            newRow.className = 'utang-row';
            
            // Buat HTML untuk baris utang
            newRow.innerHTML = `
                <td>
                    <input type="date" class="form-control" name="utang_tanggal[]" value="${utang.tanggal || ''}">
                </td>
                <td>
                    <input type="text" class="form-control money-format" name="utang_jumlah[]" value="${utang.jumlah || '0'}">
                </td>
                <td>
                    <select class="form-select" name="utang_mata_uang[]">
                        <option value="IDR" ${utang.mata_uang === 'IDR' ? 'selected' : ''}>IDR</option>
                        <option value="USD" ${utang.mata_uang === 'USD' ? 'selected' : ''}>USD</option>
                    </select>
                </td>
                <td>
                    <select class="form-select" name="utang_syarat_pembayaran[]">
                        <option value="COD" ${utang.syarat_pembayaran === 'COD' ? 'selected' : ''}>COD</option>
                        <option value="Net 15" ${utang.syarat_pembayaran === 'Net 15' ? 'selected' : ''}>Net 15</option>
                        <option value="Net 30" ${utang.syarat_pembayaran === 'Net 30' ? 'selected' : ''}>Net 30</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" name="utang_nomor[]" value="${utang.nomor || ''}">
                </td>
                <td>
                    <input type="text" class="form-control" name="utang_keterangan[]" value="${utang.keterangan || ''}">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-hapus-utang">
                        <i data-feather="trash-2" class="icon-sm"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
        });
        
        // Jika tidak ada data utang, tambahkan satu baris kosong
        if (data.utang_awal.length === 0) {
            addUtangRow();
        }
        
        // Format ulang nilai mata uang
        PemasokHelper.initMoneyFormat('edit_');
        
        // Refresh Feather Icons
        feather.replace();
    } else {
        // Jika tidak ada data utang, tambahkan satu baris kosong
        addUtangRow();
    }

    // Update formatters
    PemasokHelper.initMoneyFormat('edit_');
    
    // Update select alamat utama dan rekening utama
    renderAlamatSelect();
    renderRekeningSelect();
    
    // Refresh Feather Icons
    feather.replace();
};

// Fungsi untuk menambahkan item alamat
function addAlamatItem(alamat, index) {
    let alamatCount = $("#edit-alamat-list .alamat-item").length + 1;
    let html = `
        <div class="alamat-item accordion mb-3" id="accordionAlamat${alamatCount}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingAlamat${alamatCount}">
                    <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapseAlamat${alamatCount}" aria-expanded="true" 
                        aria-controls="collapseAlamat${alamatCount}">
                        <span class="nomor-alamat fw-bold">${alamat?.label || `Alamat ${index + 1}`}</span>
                    </button>
                </h2>
                <div id="collapseAlamat${alamatCount}" class="accordion-collapse collapse show" 
                    aria-labelledby="headingAlamat${alamatCount}" data-bs-parent="#accordionAlamat${alamatCount}">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Label Alamat</label>
                                <input type="text" class="form-control alamat-label-input" name="alamat_label[]" 
                                    placeholder="Contoh: Kantor Pusat, Gudang, dll" value="${alamat?.label || ''}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" name="alamat_alamat[]" rows="2" 
                                    placeholder="Masukkan alamat lengkap">${alamat?.alamat || ''}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" name="alamat_kode_pos[]" 
                                    placeholder="Kode Pos" value="${alamat?.kode_pos || ''}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kota</label>
                                <input type="text" class="form-control" name="alamat_kota[]" 
                                    placeholder="Nama Kota" value="${alamat?.kota || ''}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Provinsi</label>
                                <input type="text" class="form-control" name="alamat_provinsi[]" 
                                    placeholder="Nama Provinsi" value="${alamat?.provinsi || ''}">
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm removeAlamat">
                                    <i data-feather="trash-2" class="icon-sm"></i> Hapus Alamat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    $("#edit-alamat-list").append(html);
    feather.replace();
}

// Fungsi untuk menambahkan item rekening
function addRekeningItem(rekening, index) {
    let rekeningCount = $("#edit-rekening-list .rekening-item").length + 1;
    let html = `
        <div class="rekening-item accordion mb-3" id="accordionRekening${rekeningCount}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingRekening${rekeningCount}">
                    <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapseRekening${rekeningCount}" aria-expanded="true" 
                        aria-controls="collapseRekening${rekeningCount}">
                        <span class="nomor-rekening fw-bold">${rekening?.bank || `Rekening ${index + 1}`}</span>
                    </button>
                </h2>
                <div id="collapseRekening${rekeningCount}" class="accordion-collapse collapse show" 
                    aria-labelledby="headingRekening${rekeningCount}" data-bs-parent="#accordionRekening${rekeningCount}">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Bank</label>
                                <input type="text" class="form-control rekening-bank-input" name="rekening_bank[]" 
                                    placeholder="Nama Bank" value="${rekening?.bank || ''}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cabang</label>
                                <input type="text" class="form-control" name="rekening_cabang[]" 
                                    placeholder="Cabang Bank" value="${rekening?.cabang || ''}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" class="form-control" name="rekening_nomor[]" 
                                    placeholder="Nomor Rekening" value="${rekening?.nomor || ''}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Nama Pemilik</label>
                                <input type="text" class="form-control" name="rekening_nama_pemilik[]" 
                                    placeholder="Nama Pemilik" value="${rekening?.nama_pemilik || ''}">
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm removeRekening">
                                    <i data-feather="trash-2" class="icon-sm"></i> Hapus Rekening
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    $("#edit-rekening-list").append(html);
    feather.replace();
}

// Fungsi untuk render select alamat utama
function renderAlamatSelect() {
    let options = "";
    let alamatCount = $("#edit-alamat-list .alamat-item").length;

    $("#edit-alamat-list .alamat-item").each(function (i) {
        let label = $(this).find('input[name^="alamat_label"]').val() || "Alamat " + (i + 1);
        options += `<option value="${i}">${label}</option>`;
    });

    $("#edit_alamat_utama").html(options);

    // Jika hanya ada 1 alamat, otomatis pilih sebagai alamat utama
    if (alamatCount === 1) {
        $("#edit_alamat_utama").val("0");
    }
}

// Fungsi untuk render select rekening utama
function renderRekeningSelect() {
    let options = "";
    let rekeningCount = $("#edit-rekening-list .rekening-item").length;

    $("#edit-rekening-list .rekening-item").each(function (i) {
        let bank = $(this).find('input[name^="rekening_bank"]').val();
        let nomor = $(this).find('input[name^="rekening_nomor"]').val();
        let label = bank ? `${bank} - ${nomor || ''}` : `Rekening ${i + 1}`;
        options += `<option value="${i}">${label}</option>`;
    });

    $("#edit_rekening_utama").html(options);

    // Jika hanya ada 1 rekening, otomatis pilih sebagai rekening utama
    if (rekeningCount === 1) {
        $("#edit_rekening_utama").val("0");
    }
}

// Event listener untuk perubahan label alamat
$("#edit-alamat-list").on("input", ".alamat-label-input", function () {
    let label = $(this).val() || "Alamat " + ($(this).closest(".alamat-item").index() + 1);
    $(this).closest(".alamat-item").find(".accordion-button .nomor-alamat").text(label);
    renderAlamatSelect();
});

// Event listener untuk menghapus alamat
$("#edit-alamat-list").on("click", ".removeAlamat", function () {
    $(this).closest(".alamat-item").remove();
    // Update nomor alamat
    $("#edit-alamat-list .alamat-item").each(function (i) {
        let label = $(this).find('input[name^="alamat_label"]').val() || "Alamat " + (i + 1);
        $(this).find(".nomor-alamat").text(label);
    });
    renderAlamatSelect();
});

// Event listener untuk perubahan nama bank
$("#edit-rekening-list").on("input", ".rekening-bank-input", function () {
    let bank = $(this).val() || "Rekening " + ($(this).closest(".rekening-item").index() + 1);
    $(this).closest(".rekening-item").find(".accordion-button .nomor-rekening").text(bank);
    renderRekeningSelect();
});

// Event listener untuk menghapus rekening
$("#edit-rekening-list").on("click", ".removeRekening", function () {
    $(this).closest(".rekening-item").remove();
    renderRekeningSelect();
});

// Event listener untuk perubahan input rekening
$("#edit-rekening-list").on("input", 'input[name^="rekening_bank"], input[name^="rekening_nomor"]', function () {
    renderRekeningSelect();
});

// Fungsi untuk menambahkan baris utang
function addUtangRow(utang = null) {
    const tbody = document.querySelector("#editTableUtangAwal tbody");
    const firstRow = document.querySelector(".utang-row");
    const newRow = firstRow.cloneNode(true);

    // Reset nilai input di baris baru
    newRow.querySelectorAll('input[type="text"], input[type="number"]').forEach((input) => {
        if (input.name.includes("utang_jumlah")) {
            input.value = utang ? utang.jumlah : "0";
        } else if (input.name.includes("utang_tanggal")) {
            input.value = utang ? utang.tanggal : new Date().toISOString().split("T")[0];
        } else if (input.name.includes("utang_nomor")) {
            input.value = utang ? utang.nomor : "";
        } else if (input.name.includes("utang_keterangan")) {
            input.value = utang ? utang.keterangan : "";
        }
    });

    // Set nilai select
    if (utang) {
        newRow.querySelector('select[name^="utang_mata_uang"]').value = utang.mata_uang || 'IDR';
        newRow.querySelector('select[name^="utang_syarat_pembayaran"]').value = utang.syarat_pembayaran || 'COD';
    }

    // Tambahkan event listener untuk tombol hapus
    const deleteButton = newRow.querySelector(".btn-hapus-utang");
    if (deleteButton) {
        deleteButton.addEventListener("click", function () {
            if (tbody.querySelectorAll(".utang-row").length > 1) {
                this.closest("tr").remove();
                feather.replace();
            }
        });
    }

    tbody.appendChild(newRow);
    feather.replace();
    
    // Format nilai mata uang
    PemasokHelper.initMoneyFormat('edit_');
}

// Inisialisasi saat dokumen siap
$(document).ready(function() {
    // Setup form submit
    $("#formEditPemasok").off('submit').on("submit", function(e) {
        e.preventDefault();
        
        // Format ulang nilai mata uang sebelum submit
        $('.money-format').each(function() {
            let value = $(this).val().replace(/[^\d]/g, '');
            $(this).val(value);
        });

        // Siapkan data alamat
        let alamat = [];
        $("#edit-alamat-list .alamat-item").each(function() {
            alamat.push({
                label: $(this).find('input[name^="alamat_label"]').val().trim(),
                alamat: $(this).find('textarea[name^="alamat_alamat"]').val().trim(),
                kota: $(this).find('input[name^="alamat_kota"]').val().trim(),
                provinsi: $(this).find('input[name^="alamat_provinsi"]').val().trim(),
                kode_pos: $(this).find('input[name^="alamat_kode_pos"]').val().trim()
            });
        });

        // Siapkan data rekening
        let rekening = [];
        $("#edit-rekening-list .rekening-item").each(function() {
            rekening.push({
                bank: $(this).find('input[name^="rekening_bank"]').val().trim(),
                cabang: $(this).find('input[name^="rekening_cabang"]').val().trim(),
                nomor: $(this).find('input[name^="rekening_nomor"]').val().trim(),
                nama_pemilik: $(this).find('input[name^="rekening_nama_pemilik"]').val().trim()
            });
        });

        // Siapkan data utang awal
        let utang_awal = [];
        $("#editTableUtangAwal tbody tr").each(function() {
            // Ambil nilai dari setiap field
            let row = $(this);
            let utang = {
                tanggal: row.find('input[name^="utang_tanggal"]').val() || null,
                jumlah: row.find('input[name^="utang_jumlah"]').val().replace(/[^\d]/g, '') || "0",
                mata_uang: row.find('select[name^="utang_mata_uang"]').val() || "IDR",
                syarat_pembayaran: row.find('select[name^="utang_syarat_pembayaran"]').val() || "COD",
                nomor: row.find('input[name^="utang_nomor"]').val().trim() || null,
                keterangan: row.find('input[name^="utang_keterangan"]').val().trim() || null
            };

        
            // Hanya tambahkan jika minimal ada tanggal dan jumlah tidak 0
            if (utang.tanggal && utang.jumlah !== "0") {
                utang_awal.push(utang);
            }
        });

        // Siapkan data form
        let formData = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT',
            nama: $("#edit_nama").val().trim(),
            handphone: $("#edit_handphone").val().trim(),
            no_telp: $("#edit_no_telp").val().trim(),
            email: $("#edit_email").val().trim(),
            website: $("#edit_website").val().trim(),
            status: $("#edit_status").val(),
            kategori: $("#edit_kategori").val(),
            syarat_pembayaran: $("#edit_syarat_pembayaran").val(),
            default_diskon: $("#edit_default_diskon").val().replace(/[^\d]/g, ''),
            deskripsi_pembelian: $("#edit_deskripsi_pembelian").val().trim(),
            akun_utang: $("#edit_akun_utang").val(),
            akun_uang_muka: $("#edit_akun_uang_muka").val(),
            npwp: $("#edit_npwp").val().trim(),
            nik: $("#edit_nik").val().trim(),
            wajib_pajak: $("#edit_wajib_pajak").is(":checked") ? "1" : "0",
            alamat: alamat,
            rekening: rekening,
            alamat_utama: $("#edit_alamat_utama").val(),
            rekening_utama: $("#edit_rekening_utama").val(),
            utang_awal: utang_awal
        };

        // Tampilkan loading state
        Swal.fire({
            title: 'Menyimpan Data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kirim data ke server
        $.ajax({
            url: `/backend/pemasok/${$("#edit_id").val()}`,
            method: "PUT",
            data: formData,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message || "Data pemasok berhasil diperbarui",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: response.message || "Gagal memperbarui data pemasok"
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMessage = "Terjadi kesalahan saat memperbarui data";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: errorMessage
                });
            }
        });
    });

    // Event handler untuk menambah alamat
    $("#editAddAlamat").off('click').on("click", function () {
        let alamatCount = $("#edit-alamat-list .alamat-item").length;
        addAlamatItem(null, alamatCount);
        renderAlamatSelect();
    });

    // Event handler untuk menambah rekening
    $("#editAddRekening").off('click').on("click", function () {
        let rekeningCount = $("#edit-rekening-list .rekening-item").length;
        addRekeningItem(null, rekeningCount);
        renderRekeningSelect();
    });

    // Event handler untuk menambah utang awal
    $("#editBtnTambahUtang").off('click').on("click", function () {
        addUtangRow();
    });

    // Event listener untuk tombol hapus pada baris utang yang sudah ada
    $(document).on("click", ".btn-hapus-utang", function () {
        const tbody = document.querySelector("#editTableUtangAwal tbody");
        if (tbody.querySelectorAll(".utang-row").length > 1) {
            $(this).closest("tr").remove();
            feather.replace();
        }
    });

    // Inisialisasi format mata uang
    PemasokHelper.initMoneyFormat('edit_');
}); 
// Setup AJAX CSRF token
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

// Alamat dinamis
function renderAlamatSelect() {
    let options = "";
    $("#alamat-list .alamat-item").each(function (i) {
        let label = $(this).find('input[name^="alamat_label"]').val() || "Alamat " + (i + 1);
        options += `<option value="${i}">${label}</option>`;
    });
    $("#alamat_utama").html(options);
}

function updateAlamatLabel(input) {
    let label = input.val() || "Alamat " + ($(input).closest(".alamat-item").index() + 1);
    $(input).closest(".alamat-item").find(".accordion-button .nomor-alamat").text(label);
}

$("#addAlamat").click(function () {
    let alamatCount = $("#alamat-list .alamat-item").length + 1;
    let html = `
        <div class="alamat-item accordion mb-3" id="accordionAlamat${alamatCount}">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingAlamat${alamatCount}">
                    <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAlamat${alamatCount}" aria-expanded="true" aria-controls="collapseAlamat${alamatCount}">
                        <span class="nomor-alamat fw-bold">Alamat ${alamatCount}</span>
                    </button>
                </h2>
                <div id="collapseAlamat${alamatCount}" class="accordion-collapse collapse show" aria-labelledby="headingAlamat${alamatCount}" data-bs-parent="#accordionAlamat${alamatCount}">
                    <div class="accordion-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Label Alamat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control alamat-label-input" name="alamat_label[]" placeholder="Contoh: Kantor, Gudang, dll" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="alamat_alamat[]" rows="2" placeholder="Isi alamat lengkap" required></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kota/Kabupaten</label>
                                <input type="text" class="form-control" name="alamat_kota[]" placeholder="Kota/Kabupaten">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Provinsi</label>
                                <input type="text" class="form-control" name="alamat_provinsi[]" placeholder="Provinsi">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" name="alamat_kode_pos[]" placeholder="Kode Pos">
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
    $("#alamat-list").append(html);
    renderAlamatSelect();
    feather.replace();
});

// Event listener untuk perubahan label alamat
$("#alamat-list").on("input", ".alamat-label-input", function () {
    updateAlamatLabel($(this));
    renderAlamatSelect();
});

$("#alamat-list").on("click", ".removeAlamat", function () {
    $(this).closest(".alamat-item").remove();
    $("#alamat-list .alamat-item").each(function (i) {
        let label = $(this).find('input[name^="alamat_label"]').val() || "Alamat " + (i + 1);
        $(this).find(".nomor-alamat").text(label);
    });
    renderAlamatSelect();
});

// Fungsi untuk mengatur enable/disable input fields
function toggleInputFields() {
    // Batas umur faktur
    const batasUmurFakturCheck = $("#batas_umur_faktur_check").is(":checked");
    $("#batas_umur_faktur").prop("disabled", !batasUmurFakturCheck);
    if (!batasUmurFakturCheck) {
        $("#batas_umur_faktur").val("0");
    }

    // Batas total piutang
    const batasTotalPiutangCheck = $("#batas_total_piutang_check").is(":checked");
    $("#batas_total_piutang_nilai").prop("disabled", !batasTotalPiutangCheck);
    if (!batasTotalPiutangCheck) {
        $("#batas_total_piutang_nilai").val("0");
    }
}

// Event listener untuk checkbox
$("#batas_umur_faktur_check, #batas_total_piutang_check").on("change", toggleInputFields);

// Reset form dan state saat modal ditutup
$("#tambahPelanggan").on("hidden.bs.modal", function () {
    $("#formPelanggan")[0].reset();
    $("#alamat-list").empty();
    $("#tableKontakLain tbody").empty();
    $("#addAlamat").click();
    $("#addKontakLain").click();
    
    // Reset state tab
    $("#pelangganTabs button").removeClass("active");
    $("#pelangganTabs button:first").addClass("active");
    $(".tab-pane").removeClass("show active");
    $("#umum").addClass("show active");

    // Reset input fields state
    toggleInputFields();
});

// Inisialisasi saat modal dibuka
$("#tambahPelanggan").on("show.bs.modal", function () {
    $("#alamat-list").empty();
    $("#tableKontakLain tbody").empty();
    $("#addAlamat").click();
    $("#addKontakLain").click();
    
    // Aktifkan tab pertama
    $("#pelangganTabs button").removeClass("active");
    $("#pelangganTabs button:first").addClass("active");
    $(".tab-pane").removeClass("show active");
    $("#umum").addClass("show active");

    // Set initial state input fields
    toggleInputFields();
});

// Kontak lain dinamis
$("#addKontakLain").click(function () {
    let html = `
        <tr>
            <td><input type="text" class="form-control form-control-sm" name="kontak_nama[]" placeholder="Nama Lengkap"></td>
            <td><input type="text" class="form-control form-control-sm" name="kontak_posisi[]" placeholder="Posisi Jabatan"></td>
            <td><input type="email" class="form-control form-control-sm" name="kontak_email[]" placeholder="Email"></td>
            <td><input type="text" class="form-control form-control-sm" name="kontak_handphone[]" placeholder="Handphone"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger removeKontakLain">
                    <i data-feather="x" class="icon-sm"></i>
                </button>
            </td>
        </tr>`;
    $("#tableKontakLain tbody").append(html);
    feather.replace();
});

// Event listener untuk menghapus kontak lain
$("#tableKontakLain").on("click", ".removeKontakLain", function () {
    $(this).closest("tr").remove();
});

// Submit form via AJAX
$("#formPelanggan").submit(function (e) {
    e.preventDefault();

    // Reset error states
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").remove();

    // Tampilkan loading
    Swal.fire({
        title: "Menyimpan...",
        text: "Mohon tunggu sebentar",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Siapkan data
    let data = {
        nama: $("#nama").val().trim(),
        status: $("#status").val(),
        no_telp: $("#no_telp").val().trim(),
        handphone: $("#handphone").val().trim(),
        email: $("#email").val().trim(),
        website: $("#website").val().trim(),
        alamat: [],
        kontak: [],
        alamat_utama: parseInt($("#alamat_utama").val()) || 0,
        kategori_harga: $("#kategori_harga").val(),
        syarat_pembayaran: $("#syarat_pembayaran").val(),
        default_diskon: parseFloat($("#default_diskon").val()) || 0,
        npwp: $("#npwp").val().trim(),
        nik: $("#nik").val().trim(),
        wajib_pajak: $("#wajib_pajak").is(":checked"),
        piutang_awal: [],
        data_lain: {
            batas_umur_faktur_check: $("#batas_umur_faktur_check").is(":checked"),
            batas_umur_faktur: parseInt($("#batas_umur_faktur").val()) || 0,
            batas_total_piutang_check: $("#batas_total_piutang_check").is(":checked"),
            batas_total_piutang_nilai: parseFloat($("#batas_total_piutang_nilai").val()) || 0
        }
    };

    // Kumpulkan data alamat
    $("#alamat-list .alamat-item").each(function () {
        data.alamat.push({
            label: $(this).find('input[name^="alamat_label"]').val().trim(),
            alamat: $(this).find('textarea[name^="alamat_alamat"]').val().trim(),
            kota: $(this).find('input[name^="alamat_kota"]').val().trim(),
            provinsi: $(this).find('input[name^="alamat_provinsi"]').val().trim(),
            kode_pos: $(this).find('input[name^="alamat_kode_pos"]').val().trim(),
        });
    });

    // Kumpulkan data kontak
    $("#tableKontakLain tbody tr").each(function () {
        data.kontak.push({
            nama: $(this).find('input[name^="kontak_nama"]').val().trim(),
            posisi: $(this).find('input[name^="kontak_posisi"]').val().trim(),
            email: $(this).find('input[name^="kontak_email"]').val().trim(),
            handphone: $(this).find('input[name^="kontak_handphone"]').val().trim(),
        });
    });

    // Kumpulkan data piutang awal
    $("#tablePiutangAwal tbody tr").each(function () {
        data.piutang_awal.push({
            tanggal: $(this).find('input[name^="piutang_tanggal"]').val(),
            jumlah: parseFloat($(this).find('input[name^="piutang_jumlah"]').val()) || 0,
            mata_uang: $(this).find('select[name^="piutang_mata_uang"]').val(),
            syarat_pembayaran: $(this).find('select[name^="piutang_syarat_pembayaran"]').val(),
            nomor: $(this).find('input[name^="piutang_nomor"]').val(),
            keterangan: $(this).find('input[name^="piutang_keterangan"]').val(),
        });
    });

    // Kirim data ke server
    $.ajax({
        url: pelangganStoreUrl,
        method: "POST",
        data: data,
        dataType: "json",
        success: function (res) {
            Swal.close();
            if (res.success) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: res.message || "Data pelanggan berhasil ditambahkan",
                    showConfirmButton: false,
                    timer: 1500,
                }).then(() => {
                    $("#tambahPelanggan").modal("hide");
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: res.message || "Terjadi kesalahan saat menyimpan data",
                });
            }
        },
        error: function (xhr) {
            Swal.close();
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '<ul class="text-start">';
                Object.keys(errors).forEach(function(key) {
                    errors[key].forEach(function(error) {
                        errorMessage += `<li>${error}</li>`;
                    });
                });
                errorMessage += '</ul>';

                Swal.fire({
                    icon: "error",
                    title: "Validasi Gagal",
                    html: errorMessage,
                    confirmButtonText: "OK"
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Terjadi kesalahan saat menyimpan data. Silakan coba lagi.",
                });
            }
        },
    });
});

$(function () {
    // Setup AJAX CSRF token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Event handler untuk tombol edit
    $(document).on('click', '.btn-edit-pelanggan', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        
        // Tampilkan loading
        Swal.fire({
            title: 'Memuat Data...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil data pelanggan
        $.ajax({
            url: `/backend/pelanggan/${id}/json`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    fillFormData(response.data);
                    $('#modalEditPelanggan').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Gagal mengambil data pelanggan'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat mengambil data'
                });
            },
            complete: function() {
                Swal.close();
            }
        });
    });

    // Tab navigation logic
    let editTabIds = [
        "edit-umum",
        "edit-kontak",
        "edit-alamat",
        "edit-penjualan",
        "edit-pajak",
        "edit-saldo-piutang",
        "edit-lain-lain",
    ];
    let currentEditTab = 0;

    function showValidationErrors(errors) {
        // Reset semua error state
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#editPelangganTabs button").removeClass("text-danger");

        // Tampilkan error untuk setiap field
        $.each(errors, function (field, messages) {
            const input = $(`[name="${field}"]`);
            input.addClass("is-invalid");
            input.after($("<div>").addClass("invalid-feedback").text(messages[0]));

            // Jika field ada di tab lain, tampilkan indikator error pada tab tersebut
            const tabContent = input.closest(".tab-pane");
            const tabId = tabContent.attr("id");
            const tabIndex = editTabIds.indexOf(tabId);

            if (tabIndex !== -1) {
                $(`#edit-tab-${tabId}`).addClass("text-danger");
            }
        });

        // Pindah ke tab yang memiliki error pertama
        const firstErrorField = Object.keys(errors)[0];
        const firstErrorInput = $(`[name="${firstErrorField}"]`);
        const firstErrorTab = firstErrorInput.closest(".tab-pane").attr("id");
        const firstErrorTabIndex = editTabIds.indexOf(firstErrorTab);

        if (firstErrorTabIndex !== -1) {
            currentEditTab = firstErrorTabIndex;
            showEditTab(currentEditTab);
        }
    }

    function showEditTab(idx) {
        $("#editPelangganTabs button").removeClass("active");
        $("#editPelangganTabs button").eq(idx).addClass("active");
        $(".tab-pane").removeClass("show active");
        $("#" + editTabIds[idx]).addClass("show active");
    }

    $("#editPelangganTabs button").click(function () {
        currentEditTab = $(this).parent().index();
        showEditTab(currentEditTab);
    });

    // Fungsi untuk mengatur enable/disable input fields
    function toggleEditInputFields() {
        // Batas umur faktur
        const batasUmurFakturCheck = $("#edit_batas_umur_faktur_check").is(":checked");
        $("#edit_batas_umur_faktur").prop("disabled", !batasUmurFakturCheck);
        if (!batasUmurFakturCheck) {
            $("#edit_batas_umur_faktur").val("0");
        }

        // Batas total piutang
        const batasTotalPiutangCheck = $("#edit_batas_total_piutang_check").is(":checked");
        $("#edit_batas_total_piutang_nilai").prop("disabled", !batasTotalPiutangCheck);
        if (!batasTotalPiutangCheck) {
            $("#edit_batas_total_piutang_nilai").val("0");
        }
    }

    // Event listener untuk checkbox
    $("#edit_batas_umur_faktur_check, #edit_batas_total_piutang_check").on("change", toggleEditInputFields);

    // Reset form dan state saat modal ditutup
    $("#modalEditPelanggan").on("hidden.bs.modal", function () {
        $("#formEditPelanggan")[0].reset();
        $("#edit-alamat-list").empty();
        $("#editTableKontakLain tbody").empty();
        $("#editTablePiutangAwal tbody").empty();
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#editPelangganTabs button").removeClass("text-danger");
        
        // Reset state tab
        $("#editPelangganTabs button").removeClass("active");
        $("#editPelangganTabs button:first").addClass("active");
        $(".tab-pane").removeClass("show active");
        $("#edit-umum").addClass("show active");

        // Reset input fields state
        toggleEditInputFields();
    });

    // Fungsi untuk mengisi data ke form
    window.fillFormData = function(data) {
        // Reset form dan state
        $("#formEditPelanggan")[0].reset();
        $("#edit-alamat-list").empty();
        $("#editTableKontakLain tbody").empty();
        $("#editTablePiutangAwal tbody").empty();

        // Set ID
        $("#edit_id").val(data.id);

        // Data Utama
        $("#edit_nama").val(data.nama || '');
        $("#edit_status").val(data.status ? "1" : "0");
        $("#edit_no_telp").val(data.no_telp || '');
        $("#edit_handphone").val(data.handphone || '');
        $("#edit_email").val(data.email || '');
        $("#edit_website").val(data.website || '');

        // Alamat
        if (data.alamat?.length > 0) {
            data.alamat.forEach(function (alamat, index) {
                $("#editAddAlamat").click();
                let $lastAlamat = $("#edit-alamat-list .alamat-item:last");
                
                // Set nilai input
                $lastAlamat.find("[name='alamat_label[]']").val(alamat.label || '');
                $lastAlamat.find("[name='alamat_alamat[]']").val(alamat.alamat || '');
                $lastAlamat.find("[name='alamat_kota[]']").val(alamat.kota || '');
                $lastAlamat.find("[name='alamat_provinsi[]']").val(alamat.provinsi || '');
                $lastAlamat.find("[name='alamat_kode_pos[]']").val(alamat.kode_pos || '');
                
                // Update title accordion sesuai label
                let accordionTitle = alamat.label || `Alamat ${index + 1}`;
                $lastAlamat.find(".nomor-alamat").text(accordionTitle);
            });
            
            // Render select alamat utama dan set nilai default
            renderAlamatSelect();
            $("#edit_alamat_utama").val(data.alamat_utama || 0);
        }

        // Kontak
        if (data.kontak?.length > 0) {
            data.kontak.forEach(function (kontak) {
                $("#editAddKontakLain").click();
                let $lastKontak = $("#editTableKontakLain tbody tr:last");
                $lastKontak.find("[name='kontak_nama[]']").val(kontak.nama || '');
                $lastKontak.find("[name='kontak_posisi[]']").val(kontak.posisi || '');
                $lastKontak.find("[name='kontak_email[]']").val(kontak.email || '');
                $lastKontak.find("[name='kontak_handphone[]']").val(kontak.handphone || '');
            });
        }

        // Penjualan
        $("#edit_kategori_harga").val(data.kategori_harga || '');
        $("#edit_syarat_pembayaran").val(data.syarat_pembayaran || '');
        $("#edit_default_diskon").val(data.default_diskon || 0);
        $("#edit_batas_total_piutang").val(data.batas_total_piutang || 0);

        // Pajak
        $("#edit_npwp").val(data.npwp || '');
        $("#edit_nik").val(data.nik || '');
        $("#edit_wajib_pajak").prop("checked", !!data.wajib_pajak);

        // Piutang Awal
        if (data.piutang_awal?.length > 0) {
            data.piutang_awal.forEach(function (piutang) {
                $("#editBtnTambahPiutang").click();
                let $lastPiutang = $("#editTablePiutangAwal tbody tr:last");
                $lastPiutang.find("[name='piutang_tanggal[]']").val(piutang.tanggal || '');
                $lastPiutang.find("[name='piutang_jumlah[]']").val(piutang.jumlah || 0);
                $lastPiutang.find("[name='piutang_mata_uang[]']").val(piutang.mata_uang || 'IDR');
                $lastPiutang.find("[name='piutang_syarat_pembayaran[]']").val(piutang.syarat_pembayaran || '');
                $lastPiutang.find("[name='piutang_nomor[]']").val(piutang.nomor || '');
                $lastPiutang.find("[name='piutang_keterangan[]']").val(piutang.keterangan || '');
            });
        }

        // Lain-lain
        if (data.data_lain) {
            $("#edit_batas_umur_faktur_check").prop("checked", !!data.data_lain.batas_umur_faktur_check);
            $("#edit_batas_umur_faktur").val(data.data_lain.batas_umur_faktur || 0);
            $("#edit_batas_total_piutang_check").prop("checked", !!data.data_lain.batas_total_piutang_check);
            $("#edit_batas_total_piutang_nilai").val(data.data_lain.batas_total_piutang_nilai || 0);
            
            // Update input fields state
            toggleEditInputFields();
        }

        // Reset tab ke awal
        currentEditTab = 0;
        showEditTab(0);

        // Reinisialisasi Feather Icons
        feather.replace();
    };

    // Alamat dinamis
    function renderAlamatSelect() {
        let options = "";
        $("#edit-alamat-list .alamat-item").each(function (i) {
            let label = $(this).find('input[name^="alamat_label"]').val() || `Alamat ${i + 1}`;
            options += `<option value="${i}">${label}</option>`;
        });
        $("#edit_alamat_utama").html(options);
    }

    function updateAlamatLabel(input) {
        let label = input.val() || `Alamat ${$(input).closest(".alamat-item").index() + 1}`;
        $(input).closest(".alamat-item").find(".accordion-button .nomor-alamat").text(label);
    }

    $("#editAddAlamat").off('click').on('click', function () {
        let alamatCount = $("#edit-alamat-list .alamat-item").length + 1;
        let html = `
            <div class="alamat-item accordion mb-3" id="editAccordionAlamat${alamatCount}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="editHeadingAlamat${alamatCount}">
                        <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#editCollapseAlamat${alamatCount}" aria-expanded="false" aria-controls="editCollapseAlamat${alamatCount}">
                            <span class="nomor-alamat fw-bold">Alamat ${alamatCount}</span>
                        </button>
                    </h2>
                    <div id="editCollapseAlamat${alamatCount}" class="accordion-collapse collapse" aria-labelledby="editHeadingAlamat${alamatCount}" data-bs-parent="#editAccordionAlamat${alamatCount}">
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
        $("#edit-alamat-list").append(html);
        renderAlamatSelect();
        feather.replace();
    });

    // Event listener untuk perubahan label alamat
    $("#edit-alamat-list").off('input', '.alamat-label-input').on("input", ".alamat-label-input", function () {
        updateAlamatLabel($(this));
        renderAlamatSelect();
    });

    $("#edit-alamat-list").off('click', '.removeAlamat').on("click", ".removeAlamat", function () {
        $(this).closest(".alamat-item").remove();
        $("#edit-alamat-list .alamat-item").each(function (i) {
            let label = $(this).find('input[name^="alamat_label"]').val() || "Alamat " + (i + 1);
            $(this).find(".nomor-alamat").text(label);
        });
        renderAlamatSelect();
    });

    // Kontak lain dinamis
    $("#editAddKontakLain").off('click').on('click', function () {
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
        $("#editTableKontakLain tbody").append(html);
        feather.replace();
    });

    // Event listener untuk menghapus kontak lain
    $("#editTableKontakLain").off('click', '.removeKontakLain').on("click", ".removeKontakLain", function () {
        $(this).closest("tr").remove();
    });

    // Piutang awal dinamis
    $("#editBtnTambahPiutang").off('click').on('click', function () {
        let html = `
            <tr class="piutang-row">
                <td>
                    <input type="date" class="form-control form-control-sm" name="piutang_tanggal[]" value="${new Date().toISOString().split("T")[0]}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" name="piutang_jumlah[]" value="0">
                </td>
                <td>
                    <select class="form-select form-select-sm" name="piutang_mata_uang[]">
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                    </select>
                </td>
                <td>
                    <select class="form-select form-select-sm" name="piutang_syarat_pembayaran[]">
                        <option value="COD">COD</option>
                        <option value="Net 7">Net 7</option>
                        <option value="Net 14">Net 14</option>
                        <option value="Net 30">Net 30</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="piutang_nomor[]">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="piutang_keterangan[]">
                </td>
                <td>
                    <button type="button" class="btn btn-xs btn-danger btn-hapus-piutang">
                        <i data-feather="x" class="icon-sm"></i>
                    </button>
                </td>
            </tr>`;
        $("#editTablePiutangAwal tbody").append(html);
        feather.replace();
    });

    // Event listener untuk menghapus piutang
    $("#editTablePiutangAwal").off('click', '.btn-hapus-piutang').on("click", ".btn-hapus-piutang", function () {
        $(this).closest("tr").remove();
    });

    // Submit form edit via AJAX
    $("#formEditPelanggan").off('submit').on("submit", function (e) {
        e.preventDefault();

        // Reset error states
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#editPelangganTabs button").removeClass("text-danger");

        // Validasi form sebelum submit
        if (!validateForm()) {
            return false;
        }

        // Tampilkan loading
        Swal.fire({
            title: "Menyimpan...",
            text: "Mohon tunggu sebentar",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Kumpulkan data
        let data = {
            _method: 'PUT',
            nama: $("#edit_nama").val().trim(),
            status: $("#edit_status").val(),
            no_telp: $("#edit_no_telp").val().trim(),
            handphone: $("#edit_handphone").val().trim(),
            email: $("#edit_email").val().trim(),
            website: $("#edit_website").val().trim(),
            alamat: [],
            kontak: [],
            alamat_utama: parseInt($("#edit_alamat_utama").val()) || 0,
            kategori_harga: $("#edit_kategori_harga").val(),
            syarat_pembayaran: $("#edit_syarat_pembayaran").val(),
            default_diskon: parseFloat($("#edit_default_diskon").val()) || 0,
            npwp: $("#edit_npwp").val().trim(),
            nik: $("#edit_nik").val().trim(),
            wajib_pajak: $("#edit_wajib_pajak").is(":checked") ? "1" : "0",
            piutang_awal: [],
            data_lain: {
                batas_umur_faktur_check: $("#edit_batas_umur_faktur_check").is(":checked") ? "1" : "0",
                batas_umur_faktur: parseInt($("#edit_batas_umur_faktur").val()) || 0,
                batas_total_piutang_check: $("#edit_batas_total_piutang_check").is(":checked") ? "1" : "0",
                batas_total_piutang_nilai: parseFloat($("#edit_batas_total_piutang_nilai").val().replace(/\./g, '')) || 0
            }
        };

        // Kumpulkan data alamat
        $("#edit-alamat-list .alamat-item").each(function () {
            data.alamat.push({
                label: $(this).find('input[name^="alamat_label"]').val().trim(),
                alamat: $(this).find('textarea[name^="alamat_alamat"]').val().trim(),
                kota: $(this).find('input[name^="alamat_kota"]').val().trim(),
                provinsi: $(this).find('input[name^="alamat_provinsi"]').val().trim(),
                kode_pos: $(this).find('input[name^="alamat_kode_pos"]').val().trim()
            });
        });

        // Kumpulkan data kontak
        $("#editTableKontakLain tbody tr").each(function () {
            data.kontak.push({
                nama: $(this).find("[name='kontak_nama[]']").val().trim(),
                posisi: $(this).find("[name='kontak_posisi[]']").val().trim(),
                email: $(this).find("[name='kontak_email[]']").val().trim(),
                handphone: $(this).find("[name='kontak_handphone[]']").val().trim()
            });
        });

        // Kumpulkan data piutang awal
        $("#editTablePiutangAwal tbody tr").each(function () {
            data.piutang_awal.push({
                tanggal: $(this).find("[name='piutang_tanggal[]']").val(),
                jumlah: parseFloat($(this).find("[name='piutang_jumlah[]']").val()) || 0,
                mata_uang: $(this).find("[name='piutang_mata_uang[]']").val(),
                syarat_pembayaran: $(this).find("[name='piutang_syarat_pembayaran[]']").val(),
                nomor: $(this).find("[name='piutang_nomor[]']").val(),
                keterangan: $(this).find("[name='piutang_keterangan[]']").val()
            });
        });

        // Dapatkan ID pelanggan
        let pelangganId = $("#edit_id").val();
        if (!pelangganId) {
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "ID Pelanggan tidak ditemukan",
            });
            return;
        }

        // Kirim data ke server
        $.ajax({
            url: `/backend/pelanggan/${pelangganId}`,
            method: "POST",
            data: data,
            dataType: "json",
            success: function (res) {
                Swal.close();
                if (res.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: res.message || "Data pelanggan berhasil diperbarui",
                        showConfirmButton: false,
                        timer: 1500,
                    }).then(() => {
                        $("#modalEditPelanggan").modal("hide");
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

                    showValidationErrors(errors);
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

    // Fungsi untuk validasi form
    function validateForm() {
        let isValid = true;
        
        // Validasi field wajib
        const requiredFields = {
            'edit_nama': 'Nama'
        };
        
        Object.entries(requiredFields).forEach(([id, label]) => {
            const value = $(`#${id}`).val().trim();
            if (!value) {
                $(`#${id}`).addClass('is-invalid');
                $(`#${id}`).after(`<div class="invalid-feedback">${label} harus diisi</div>`);
                isValid = false;
            }
        });
        
        // Validasi alamat
        $("#edit-alamat-list .alamat-item").each(function() {
            const alamat = $(this).find('textarea[name^="alamat_alamat"]').val().trim();
            const label = $(this).find('input[name^="alamat_label"]').val().trim();
            
            if (!alamat || !label) {
                $(this).find('textarea[name^="alamat_alamat"], input[name^="alamat_label"]').addClass('is-invalid');
                $(this).find('textarea[name^="alamat_alamat"]').after('<div class="invalid-feedback">Alamat lengkap harus diisi</div>');
                $(this).find('input[name^="alamat_label"]').after('<div class="invalid-feedback">Label alamat harus diisi</div>');
                isValid = false;
            }
        });
        
        return isValid;
    }

    // Delete confirmation
    $(".btn-delete-pelanggan").click(function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        let nama = $(this).data("nama");

        Swal.fire({
            title: "Hapus Data Pelanggan?",
            text: `Anda akan menghapus data pelanggan "${nama}". Data yang sudah dihapus tidak dapat dikembalikan.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                $(`#formDeletePelanggan${id}`).submit();
            }
        });
    });
});

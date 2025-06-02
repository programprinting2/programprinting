$(function () {
    // Setup AJAX CSRF token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Inisialisasi Feather Icons
    if (typeof feather !== "undefined") {
        feather.replace();
    }

    // Reset form dan state saat modal ditutup
    $("#tambahPemasok").on("hidden.bs.modal", function () {
        // Reset form
        $("#formPemasok")[0].reset();

        // Reset tab ke awal
        currentTab = 0;
        showTab(0);

        // Reset alamat, rekening, dan utang
        $("#alamat-list").empty();
        $("#rekening-list").empty();
        $("#tableUtangAwal tbody tr:not(:first)").remove();

        // Tambahkan alamat dan rekening default
        $("#addAlamat").click();
        $("#addRekening").click();

        // Reset error states
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#pemasokTabs button").removeClass("text-danger");
    });

    // Inisialisasi saat modal dibuka
    $("#tambahPemasok").on("show.bs.modal", function () {
        // Pastikan form bersih
        $("#formPemasok")[0].reset();

        // Reset tab ke awal
        currentTab = 0;
        showTab(0);

        // Reset alamat dan rekening
        $("#alamat-list").empty();
        $("#rekening-list").empty();

        // Tambahkan alamat dan rekening default
        $("#addAlamat").click();
        $("#addRekening").click();

        // Reset error states
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#pemasokTabs button").removeClass("text-danger");
    });

    // Tab navigation logic
    let tabIds = [
        "umum",
        "alamat",
        "pembelian",
        "pajak",
        "rekening",
        "utang-awal",
    ];
    let currentTab = 0;

    function showTab(n) {
        // Tampilkan tab yang aktif
        $(`#${tabIds[n]}`).tab("show");
    }

    // Fungsi untuk render select alamat utama
    function renderAlamatSelect() {
        let options = "";
        let alamatCount = $("#alamat-list .alamat-item").length;

        $("#alamat-list .alamat-item").each(function (i) {
            let label =
                $(this).find('input[name^="alamat_label"]').val() ||
                "Alamat " + (i + 1);
            options += `<option value="${i}">${label}</option>`;
        });

        $("#alamat_utama").html(options);

        // Jika hanya ada 1 alamat, otomatis pilih sebagai alamat utama
        if (alamatCount === 1) {
            $("#alamat_utama").val("0");
        }
    }

    // Event handler untuk menambah alamat
    $("#addAlamat").click(function () {
        let alamatCount = $("#alamat-list .alamat-item").length + 1;
        let html = `
            <div class="alamat-item accordion mb-3" id="accordionAlamat${alamatCount}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingAlamat${alamatCount}">
                        <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapseAlamat${alamatCount}" aria-expanded="true" 
                            aria-controls="collapseAlamat${alamatCount}">
                            <span class="nomor-alamat fw-bold">Alamat ${alamatCount}</span>
                        </button>
                    </h2>
                    <div id="collapseAlamat${alamatCount}" class="accordion-collapse collapse show" 
                        aria-labelledby="headingAlamat${alamatCount}" data-bs-parent="#accordionAlamat${alamatCount}">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Label Alamat</label>
                                    <input type="text" class="form-control alamat-label-input" name="alamat_label[]" 
                                        placeholder="Contoh: Kantor Pusat, Gudang, dll">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea class="form-control" name="alamat_alamat[]" rows="2" 
                                        placeholder="Masukkan alamat lengkap"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control" name="alamat_kode_pos[]" placeholder="Kode Pos">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kota</label>
                                    <input type="text" class="form-control" name="alamat_kota[]" placeholder="Nama Kota">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" class="form-control" name="alamat_provinsi[]" placeholder="Nama Provinsi">
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
        let label =
            $(this).val() ||
            "Alamat " + ($(this).closest(".alamat-item").index() + 1);
        $(this)
            .closest(".alamat-item")
            .find(".accordion-button .nomor-alamat")
            .text(label);
        renderAlamatSelect();
    });

    // Event listener untuk menghapus alamat
    $("#alamat-list").on("click", ".removeAlamat", function () {
        $(this).closest(".alamat-item").remove();
        // Update nomor alamat
        $("#alamat-list .alamat-item").each(function (i) {
            let label =
                $(this).find('input[name^="alamat_label"]').val() ||
                "Alamat " + (i + 1);
            $(this).find(".nomor-alamat").text(label);
        });
        renderAlamatSelect();
    });

    // Fungsi untuk render select rekening utama
    function renderRekeningSelect() {
        let options = "";
        let rekeningCount = $("#rekening-list .rekening-item").length;

        $("#rekening-list .rekening-item").each(function (i) {
            let bank = $(this).find('input[name^="rekening_bank"]').val();
            let nomor = $(this).find('input[name^="rekening_nomor"]').val();

            // Jika bank kosong, gunakan nomor rekening sebagai label
            let label = bank ? `${bank} - ${nomor || ""}` : `Rekening ${i + 1}`;

            options += `<option value="${i}">${label}</option>`;
        });

        $("#rekening_utama").html(options);

        // Jika hanya ada 1 rekening, otomatis pilih sebagai rekening utama
        if (rekeningCount === 1) {
            $("#rekening_utama").val("0");
        }
    }

    // Event handler untuk menambah rekening
    $("#addRekening").click(function () {
        let rekeningCount = $("#rekening-list .rekening-item").length + 1;
        let html = `
            <div class="rekening-item accordion mb-3" id="accordionRekening${rekeningCount}">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingRekening${rekeningCount}">
                        <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapseRekening${rekeningCount}" aria-expanded="true" 
                            aria-controls="collapseRekening${rekeningCount}">
                            <span class="nomor-rekening fw-bold">Rekening ${rekeningCount}</span>
                        </button>
                    </h2>
                    <div id="collapseRekening${rekeningCount}" class="accordion-collapse collapse show" 
                        aria-labelledby="headingRekening${rekeningCount}" data-bs-parent="#accordionRekening${rekeningCount}">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Bank</label>
                                    <input type="text" class="form-control rekening-bank-input" name="rekening_bank[]" placeholder="Nama Bank">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cabang</label>
                                    <input type="text" class="form-control" name="rekening_cabang[]" placeholder="Cabang Bank">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" class="form-control" name="rekening_nomor[]" placeholder="Nomor Rekening">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nama Pemilik</label>
                                    <input type="text" class="form-control" name="rekening_nama_pemilik[]" placeholder="Nama Pemilik">
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
        $("#rekening-list").append(html);
        renderRekeningSelect();
        feather.replace();
    });

    // Event listener untuk perubahan nama bank
    $("#rekening-list").on("input", ".rekening-bank-input", function () {
        let bank =
            $(this).val() ||
            "Rekening " + ($(this).closest(".rekening-item").index() + 1);
        $(this)
            .closest(".rekening-item")
            .find(".accordion-button .nomor-rekening")
            .text(bank);
        renderRekeningSelect();
    });

    // Event listener untuk menghapus rekening
    $("#rekening-list").on("click", ".removeRekening", function () {
        $(this).closest(".rekening-item").remove();
        renderRekeningSelect();
    });

    // Event listener untuk perubahan input rekening
    $("#rekening-list").on(
        "input",
        'input[name^="rekening_bank"], input[name^="rekening_nomor"]',
        function () {
            renderRekeningSelect();
        }
    );

    // Tambah baris utang
    $("#btnTambahUtang").click(function () {
        const tbody = document.querySelector("#tableUtangAwal tbody");
        const firstRow = document.querySelector(".utang-row");
        const newRow = firstRow.cloneNode(true);

        // Reset nilai input di baris baru
        newRow
            .querySelectorAll('input[type="text"], input[type="number"]')
            .forEach((input) => {
                if (input.name.includes("utang_jumlah")) {
                    input.value = "0";
                } else if (!input.name.includes("utang_tanggal")) {
                    input.value = "";
                }
            });

        // Set tanggal hari ini
        const dateInput = newRow.querySelector('input[type="date"]');
        if (dateInput) {
            dateInput.value = new Date().toISOString().split("T")[0];
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
    });

    // Event listener untuk tombol hapus pada baris utang yang sudah ada
    $(document).on("click", ".btn-hapus-utang", function () {
        const tbody = document.querySelector("#tableUtangAwal tbody");
        if (tbody.querySelectorAll(".utang-row").length > 1) {
            $(this).closest("tr").remove();
            feather.replace();
        }
    });

    // Fungsi validasi email
    function isValidEmail(email) {
        if (!email) return true;
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Fungsi validasi nomor telepon
    function isValidPhone(phone) {
        const re = /^[0-9+\-\s()]{8,20}$/;
        return re.test(phone);
    }

    // Fungsi validasi website
    function isValidWebsite(website) {
        if (!website) return true;
        const re = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/;
        return re.test(website);
    }

    // Fungsi validasi NPWP
    function isValidNPWP(npwp) {
        if (!npwp) return true;
        const re = /^\d{2}[.]\d{3}[.]\d{3}[.]\d{1}[.]\d{3}[.]\d{3}$/;
        return re.test(npwp);
    }

    // Fungsi validasi NIK
    function isValidNIK(nik) {
        if (!nik) return true;
        const re = /^\d{16}$/;
        return re.test(nik);
    }

    // Fungsi untuk menampilkan error
    function showError(element, message) {
        element.addClass("is-invalid");
        element.after(`<div class="invalid-feedback">${message}</div>`);

        // Tandai tab yang memiliki error
        const tabId = element.closest(".tab-pane").attr("id");
        $(`#pemasokTabs button[data-bs-target="#${tabId}"]`).addClass(
            "text-danger"
        );
    }

    // Fungsi untuk reset error
    function resetErrors() {
        $(".is-invalid").removeClass("is-invalid");
        $(".invalid-feedback").remove();
        $("#pemasokTabs button").removeClass("text-danger");
    }

    // Submit form via AJAX
    $("#formPemasok").submit(function (e) {
        e.preventDefault();
        resetErrors();

        // Nonaktifkan tombol simpan
        const submitButton = $(this).find('button[type="submit"]');
        submitButton.prop("disabled", true);
        submitButton.html(
            '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...'
        );

        let hasError = false;

        let formData = {
            nama: $("#nama").val().trim(),
            handphone: $("#handphone").val().trim(),
            no_telp: $("#no_telp").val().trim(),
            email: $("#pemasok_email").val().trim(),
            website: $("#website").val().trim(),
            status: $("#status").val(),
            syarat_pembayaran: $("#syarat_pembayaran").val(),
            default_diskon: $("#default_diskon").val(),
            deskripsi_pembelian: $("#deskripsi-pembelian").val().trim(),
            akun_utang: $("#akun_utang").val(),
            akun_uang_muka: $("#akun_uang_muka").val(),
            npwp: $("#npwp").val().trim(),
            nik: $("#nik").val().trim(),
            wajib_pajak: $("#wajib_pajak").is(":checked") ? "1" : "0",
            alamat: [],
            rekening: [],
            alamat_utama: $("#alamat_utama").val(),
            rekening_utama: $("#rekening_utama").val(),
            utang_awal: [],
        };

        // Validasi field wajib
        if (!formData.nama) {
            showError($("#nama"), "Nama pemasok wajib diisi");
            hasError = true;
        }

        if (!formData.akun_utang) {
            showError($("#akun_utang"), "Akun utang wajib diisi");
            hasError = true;
            // Tandai tab pembelian dengan warna merah
            $("#pemasokTabs button[data-bs-target='#pembelian']").addClass("text-danger");
        }

        if (!formData.akun_uang_muka) {
            showError($("#akun_uang_muka"), "Akun uang muka wajib diisi");
            hasError = true;
            // Tandai tab pembelian dengan warna merah
            $("#pemasokTabs button[data-bs-target='#pembelian']").addClass("text-danger");
        }

        // Validasi email hanya jika ada nilainya
        if (formData.email && !isValidEmail(formData.email)) {
            showError($("#pemasok_email"), "Format email tidak valid");
            hasError = true;
        }

        if (formData.handphone && !isValidPhone(formData.handphone)) {
            showError($("#handphone"), "Format nomor handphone tidak valid");
            hasError = true;
        }

        if (formData.no_telp && !isValidPhone(formData.no_telp)) {
            showError($("#no_telp"), "Format nomor telepon tidak valid");
            hasError = true;
        }

        if (formData.website && !isValidWebsite(formData.website)) {
            showError($("#website"), "Format website tidak valid");
            hasError = true;
        }

        if (formData.npwp && !isValidNPWP(formData.npwp)) {
            showError(
                $("#npwp"),
                "Format NPWP tidak valid (contoh: 12.345.678.9.012.345)"
            );
            hasError = true;
        }

        if (formData.nik && !isValidNIK(formData.nik)) {
            showError($("#nik"), "NIK harus 16 digit");
            hasError = true;
        }

        // Validasi alamat
        $("#alamat-list .alamat-item").each(function (index) {
            const alamat = {
                label: $(this).find('input[name^="alamat_label"]').val().trim(),
                alamat: $(this)
                    .find('textarea[name^="alamat_alamat"]')
                    .val()
                    .trim(),
                kota: $(this).find('input[name^="alamat_kota"]').val().trim(),
                provinsi: $(this)
                    .find('input[name^="alamat_provinsi"]')
                    .val()
                    .trim(),
                kode_pos: $(this)
                    .find('input[name^="alamat_kode_pos"]')
                    .val()
                    .trim(),
            };

            if (!alamat.alamat) {
                showError(
                    $(this).find('textarea[name^="alamat_alamat"]'),
                    "Alamat wajib diisi"
                );
                hasError = true;
            }

            formData.alamat.push(alamat);
        });

        if (formData.alamat.length === 0) {
            Swal.fire({
                icon: "error",
                title: "Validasi Gagal",
                text: "Minimal satu alamat harus diisi",
            });
            hasError = true;
        }

        // Validasi rekening
        $("#rekening-list .rekening-item").each(function (index) {
            const rekening = {
                bank: $(this).find('input[name^="rekening_bank"]').val().trim(),
                cabang: $(this)
                    .find('input[name^="rekening_cabang"]')
                    .val()
                    .trim(),
                nomor: $(this)
                    .find('input[name^="rekening_nomor"]')
                    .val()
                    .trim(),
                nama_pemilik: $(this)
                    .find('input[name^="rekening_nama_pemilik"]')
                    .val()
                    .trim(),
            };

            if (rekening.bank || rekening.nomor) {
                if (!rekening.bank) {
                    showError(
                        $(this).find('input[name^="rekening_bank"]'),
                        "Nama bank wajib diisi"
                    );
                    hasError = true;
                }
                if (!rekening.nomor) {
                    showError(
                        $(this).find('input[name^="rekening_nomor"]'),
                        "Nomor rekening wajib diisi"
                    );
                    hasError = true;
                }
            }

            formData.rekening.push(rekening);
        });

        // Validasi utang awal
        $("#tableUtangAwal tbody tr").each(function (index) {
            const utang = {
                tanggal: $(this).find('input[name^="utang_tanggal"]').val(),
                jumlah: $(this).find('input[name^="utang_jumlah"]').val(),
                mata_uang: $(this)
                    .find('select[name^="utang_mata_uang"]')
                    .val(),
                syarat_pembayaran: $(this)
                    .find('select[name^="utang_syarat_pembayaran"]')
                    .val(),
                nomor: $(this).find('input[name^="utang_nomor"]').val().trim(),
                keterangan: $(this)
                    .find('input[name^="utang_keterangan"]')
                    .val()
                    .trim(),
            };

            if (utang.jumlah || utang.tanggal) {
                if (!utang.tanggal) {
                    showError(
                        $(this).find('input[name^="utang_tanggal"]'),
                        "Tanggal wajib diisi"
                    );
                    hasError = true;
                }
                if (!utang.jumlah) {
                    showError(
                        $(this).find('input[name^="utang_jumlah"]'),
                        "Jumlah wajib diisi"
                    );
                    hasError = true;
                }
                if (!utang.mata_uang) {
                    showError(
                        $(this).find('select[name^="utang_mata_uang"]'),
                        "Mata uang wajib diisi"
                    );
                    hasError = true;
                }
            }

            formData.utang_awal.push(utang);
        });

        if (hasError) {
            Swal.fire({
                icon: "error",
                title: "Validasi Gagal",
                text: "Mohon periksa kembali form yang diisi",
            });
            // Aktifkan kembali tombol simpan
            submitButton.prop("disabled", false);
            submitButton.html("Simpan");
            return;
        }

        // Kirim data ke server
        $.ajax({
            url: pemasokStoreUrl,
            type: "POST",
            data: formData,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message || "Data pemasok berhasil ditambahkan",
                        showConfirmButton: false,
                        timer: 1500,
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: response.message,
                    });
                    // Aktifkan kembali tombol simpan
                    submitButton.prop("disabled", false);
                    submitButton.html("Simpan");
                }
            },
            error: function (xhr) {
                let errorMessage = "Terjadi kesalahan saat menyimpan data";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: errorMessage,
                });
                // Aktifkan kembali tombol simpan
                submitButton.prop("disabled", false);
                submitButton.html("Simpan");
            },
        });
    });
});
 
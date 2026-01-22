$(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Variabel media khusus tambah
    let selectedPhotos = [];
    let selectedVideos = [];
    let selectedDocuments = [];
    let parameterMesinList = [];
    let bahanBakuList = [];
    let alurProduksiList = [];
    let produkKomponenList = [];
    let isModalForParameter = false;

    // Pastikan array tidak ter-reset
    if (
        typeof parameterMesinList === "undefined" ||
        !Array.isArray(parameterMesinList)
    ) {
        parameterMesinList = [];
    }

    if (typeof bahanBakuList === "undefined" || !Array.isArray(bahanBakuList)) {
        bahanBakuList = [];
    }

    if (
        typeof alurProduksiList === "undefined" ||
        !Array.isArray(alurProduksiList)
    ) {
        alurProduksiList = [];
    }

    // Reset media saat modal tambah ditutup
    $("#tambahProduk").on("hidden.bs.modal", function () {
        selectedPhotos = [];
        selectedVideos = [];
        selectedDocuments = [];
        parameterMesinList = [];
        bahanBakuList = [];
        alurProduksiList = [];
        isModalForParameter = false;
        // Reset produk komponen list to ensure clean state
        produkKomponenList = [];
        renderPhotosPreview();
        renderVideosPreview();
        renderDocumentsPreview();
        renderAlurProduksi();
        renderTabelBahanBaku();
        renderParameterMesinTable();
        renderTabelProdukKomponen();
        // Cleanup: remove event listener saat modal ditutup
        window.removeEventListener("mesinDipilih", handleMesinDipilihTambah);
        window.removeEventListener("produkKomponenDipilih", handleProdukKomponenDipilih);
    });

    // Reset data saat modal dibuka
    $("#tambahProduk").on("show.bs.modal", function () {
        parameterMesinList = [];
        bahanBakuList = [];
        alurProduksiList = [];
        isModalForParameter = false;
        // Reset produk komponen list to avoid false duplicate alerts
        produkKomponenList = [];
        renderTabelBahanBaku();
        renderParameterMesinTable();
        renderAlurProduksi();
        renderTabelProdukKomponen();
        // Pastikan event listener terdaftar saat modal dibuka
        window.removeEventListener("mesinDipilih", handleMesinDipilihTambah);
        window.addEventListener("mesinDipilih", handleMesinDipilihTambah);
    });

    // Update sub-kategori saat kategori utama berubah
    $(document).on("change", "#kategori_utama", function () {
        updateSubKategoriOptions($(this).val(), "#sub_kategori_id_produk");
    });

    $(document).on("change", "#satuanBarang", function () {
        updateDetailSatuanOptions($(this).val(), "#detail_satuan");
        toggleDimensiFields($(this).val(), "add");
    });

    $("#tambahProduk").on("shown.bs.modal", function () {
        updateSubKategoriOptions(
            $("#kategori_utama").val(),
            "#sub_kategori_id_produk",
        );
        updateDetailSatuanOptions($("#satuanBarang").val(), "#detail_satuan");
        const selectedSatuan = $("#satuanBarang").val();
        if (selectedSatuan) {
            toggleDimensiFields(selectedSatuan, "add");
        }
    });

    // Fungsi untuk toggle field dimensi
    function toggleDimensiFields(selectedSatuanId, mode = "add") {
        const containerId =
            mode === "add" ? "#dimensi_container" : "#edit_dimensi_container";
        const luasId = mode === "add" ? "#dimensi_luas" : "#edit_dimensi_luas";
        const panjangId =
            mode === "add" ? "#dimensi_panjang" : "#edit_dimensi_panjang";

        // Dapatkan nama satuan dari opsi yang dipilih
        const selectedOption = $(
            `#${mode === "add" ? "satuanBarang" : "edit_satuanBarang"} option:selected`,
        );
        const satuanName = selectedOption.text().trim();

        if (satuanName === "SATUAN LUAS") {
            // Tampilkan kedua field
            $(containerId).show();
            $(luasId).show();
            $(panjangId).show();
        } else if (satuanName === "SATUAN LEBAR") {
            // Tampilkan hanya field lebar
            $(containerId).show();
            $(luasId).show();
            $(panjangId).hide();
        } else {
            // Sembunyikan seluruh field dimensi
            $(containerId).hide();
        }
    }

    // Handler untuk penambahan bahan baku
    window.addEventListener("bahanBakuDipilih", function (e) {
        const data = e.detail;

        // Pastikan array terdefinisi
        if (
            typeof bahanBakuList === "undefined" ||
            !Array.isArray(bahanBakuList)
        ) {
            bahanBakuList = [];
        }

        // Cek duplikasi
        if (bahanBakuList.some((item) => item.id == data.id)) {
            Swal.fire("Info", "Bahan baku sudah ditambahkan.", "info");
            return;
        }

        // Tambahkan bahan baku ke list
        bahanBakuList.push({
            id: data.id,
            nama: data.nama,
            satuan: data.sub_satuan,
            harga: data.harga || 0,
            jumlah: 1,
            total: data.harga || 0,
        });

        // Render tabel bahan baku
        renderTabelBahanBaku();

        // Update total
        updateTotalModalKeseluruhan();
        updateTotalItemModal();
    });

    // Fungsi untuk render tabel bahan baku
    function renderTabelBahanBaku() {
        const tbody = $("#tabelBahanBaku tbody");
        tbody.empty();

        if (!bahanBakuList || bahanBakuList.length === 0) {
            tbody.append(
                '<tr class="text-muted"><td colspan="6" class="text-center">Belum ada bahan baku</td></tr>',
            );
            return;
        }

        bahanBakuList.forEach((row, idx) => {
            tbody.append(`
                <tr>
                    <td>${
                        row.nama || ""
                    }<input type="hidden" name="bahan_baku_id[]" value="${
                        row.id || ""
                    }"></td>
                    <td>${row.satuan || ""}</td>
                    <td class="text-end">Rp ${(row.harga || 0).toLocaleString(
                        "id-ID",
                    )}<input type="hidden" name="harga_bahan[]" value="${
                        row.harga || 0
                    }"></td>
                    <td><input type="number" class="form-control form-control-sm jumlah_bahan" name="jumlah_bahan[]" value="${
                        row.jumlah || 0
                    }" min="0" data-idx="${idx}" step="0.01"></td>
                    <td class="text-success fw-semibold text-end">Rp ${(
                        row.total || 0
                    ).toLocaleString("id-ID")}</td>
                    <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-bahan-baku" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
                </tr>
            `);
        });

        feather.replace();
    }

    // Handler untuk input jumlah bahan baku
    $(document).on("input", ".jumlah_bahan", function () {
        const idx = $(this).data("idx");
        const harga =
            parseInt($(`input[name="harga_bahan[]"]`).eq(idx).val()) || 0;
        const jumlah = parseFloat($(this).val()) || 0;

        // Pastikan array terdefinisi
        if (
            typeof bahanBakuList === "undefined" ||
            !Array.isArray(bahanBakuList)
        ) {
            bahanBakuList = [];
        }

        if (bahanBakuList[idx]) {
            bahanBakuList[idx].harga = harga;
            bahanBakuList[idx].jumlah = jumlah;
            bahanBakuList[idx].total = harga * jumlah;
            const total = harga * jumlah;
            $(this)
                .closest("tr")
                .find("td")
                .eq(4)
                .html(
                    `<span class="text-success fw-semibold text-end">Rp ${total.toLocaleString(
                        "id-ID",
                    )}</span>`,
                );
            updateTotalModalKeseluruhan();
        }
    });

    // Handler untuk hapus bahan baku
    $(document).on("click", ".btn-hapus-bahan-baku", function () {
        const idx = $(this).data("idx");

        // Pastikan array terdefinisi
        if (
            typeof bahanBakuList === "undefined" ||
            !Array.isArray(bahanBakuList)
        ) {
            bahanBakuList = [];
        }

        bahanBakuList.splice(idx, 1);
        renderTabelBahanBaku();
        updateTotalModalKeseluruhan();
        updateTotalItemModal();
    });

    // === PARAMETER MESIN (TAMBAH) ===

    // Helper function untuk menutup modal cari mesin
    function closeModalCariMesinTambah() {
        $("#modalCariMesinProdukTambah").modal("hide");
    }

    // Named function untuk handle mesinDipilih - mencegah duplicate listener
    function handleMesinDipilihTambah(e) {
        if (
            !$("#tambahProduk").hasClass("show") ||
            !$("#modalCariMesinProdukTambah").hasClass("show")
        )
            return;

        const data = e.detail;

        if (isModalForParameter) {
            // Handle untuk parameter mesin
            if (
                typeof parameterMesinList === "undefined" ||
                !Array.isArray(parameterMesinList)
            ) {
                parameterMesinList = [];
            }

            if (parameterMesinList.some((pm) => pm.mesin_id == data.id)) {
                Swal.fire(
                    "Info",
                    "Parameter dari mesin ini sudah ditambahkan.",
                    "info",
                );
                return;
            }

            let profil = data.biaya_perhitungan_profil;
            if (!profil) {
                Swal.fire(
                    "Info",
                    "Mesin ini tidak memiliki parameter biaya yang dapat dipilih.",
                    "info",
                );
                return;
            }

            if (typeof profil === "string") {
                try {
                    profil = JSON.parse(profil);
                } catch {
                    profil = [];
                }
            }

            if (!Array.isArray(profil)) profil = [profil];
            profil = profil.filter(
                (p) => p && typeof p === "object" && p.nama && p.total != null,
            );

            if (profil.length === 0) {
                Swal.fire(
                    "Info",
                    "Mesin ini tidak memiliki parameter biaya yang dapat dipilih.",
                    "info",
                );
                return;
            }

            parameterMesinList.push({
                mesin_id: data.id,
                opsi: profil,
                selected: 0,
                jumlah: 1,
            });

            renderParameterMesinTable();
            updateTotalModalKeseluruhan();
            updateTotalItemModal();
            closeModalCariMesinTambah();
        } else {
            // Handle untuk alur produksi
            const index = window.currentMesinIndex;

            if (typeof index === "undefined" || !alurProduksiList[index]) {
                console.warn("Index mesin tidak valid untuk alur produksi");
                return;
            }

            if (alurProduksiList.some((mesin) => mesin.id == data.id)) {
                Swal.fire(
                    "Info",
                    "Mesin ini sudah ditambahkan ke alur produksi.",
                    "info",
                );
                return;
            }

            alurProduksiList[index] = {
                id: data.id,
                nama_mesin: data.nama,
                tipe_mesin: data.tipe || "",
                estimasi_waktu: alurProduksiList[index].estimasi_waktu || "",
                catatan: alurProduksiList[index].catatan || "",
            };

            renderAlurProduksi();
            closeModalCariMesinTambah();
        }
    }

    // Remove listener lama sebelum menambahkan yang baru (mencegah duplicate)
    window.removeEventListener("mesinDipilih", handleMesinDipilihTambah);
    // window.addEventListener("mesinDipilih", handleMesinDipilihTambah);

    // Tombol + Tambah Parameter (buka modal cari MESIN)
    $(document)
        .off("click", "#btnTambahParameter")
        .on("click", "#btnTambahParameter", function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Cek apakah modal sudah terbuka, jika ya jangan buka lagi
            if ($("#modalCariMesinProdukTambah").hasClass("show")) {
                return;
            }

            isModalForParameter = true;
            var modalMesin = new bootstrap.Modal(
                document.getElementById("modalCariMesinProdukTambah"),
                {
                    backdrop: "static",
                    keyboard: false,
                    focus: true,
                },
            );
            modalMesin.show();
            setTimeout(function () {
                if ($("#tambahProduk").hasClass("show")) {
                    $("body").addClass("modal-open");
                }
            }, 200);
        });

    // Render tabel parameter modal
    function renderParameterMesinTable() {
        const tbody = $("#tabelParameterModal tbody");
        tbody.empty();

        if (parameterMesinList.length === 0) {
            tbody.append(
                '<tr><td colspan="6" class="text-center text-muted">Pilih parameter</td></tr>',
            );
            return;
        }

        parameterMesinList.forEach((row, idx) => {
            let options = "";
            row.opsi.forEach((opt, i) => {
                options += `<option value="${i}" ${
                    i === row.selected ? "selected" : ""
                }>${opt.nama}</option>`;
            });

            tbody.append(`
                <tr data-mesin-id="${row.mesin_id}">
                    <td>${
                        window.masterMesinList?.find(
                            (m) => m.id == row.mesin_id,
                        )?.nama_mesin || "-"
                    }</td>
                    <td>
                        <select class="form-select form-select-sm param-dropdown" data-idx="${idx}">${options}</select>
                    </td>
                    <td class="harga-param">Rp. ${row.opsi[
                        row.selected
                    ].total.toLocaleString("id-ID")}</td>
                    <td><input type="number" class="form-control form-control-sm jumlah-param" min="0" step="0.01" value="${
                        row.jumlah
                    }" data-idx="${idx}"></td>
                    <td class="total-param text-success fw-semibold">Rp ${(
                        row.opsi[row.selected].total * row.jumlah
                    ).toLocaleString("id-ID")}</td>
                    <td><button type="button" class="btn btn-danger btn-xs btn-hapus-param"><i data-feather="trash-2" class="icon-sm"></i></button></td>
                </tr>
            `);
        });

        feather.replace();
    }

    // Handler select parameter dan jumlah
    $(document).on("change", ".param-dropdown", function () {
        const idx = $(this).data("idx");

        // Pastikan array terdefinisi
        if (
            typeof parameterMesinList === "undefined" ||
            !Array.isArray(parameterMesinList)
        ) {
            parameterMesinList = [];
        }

        if (parameterMesinList[idx]) {
            parameterMesinList[idx].selected = parseInt($(this).val());
            renderParameterMesinTable();
            updateTotalModalKeseluruhan();
        }
    });

    $(document).on("input", ".jumlah-param", function () {
        const idx = $(this).data("idx");
        let val = parseFloat($(this).val()) || 0;
        if (val < 0) val = 1;

        // Pastikan array terdefinisi
        if (
            typeof parameterMesinList === "undefined" ||
            !Array.isArray(parameterMesinList)
        ) {
            parameterMesinList = [];
        }

        if (parameterMesinList[idx]) {
            parameterMesinList[idx].jumlah = val;
            const harga =
                parameterMesinList[idx].opsi[parameterMesinList[idx].selected]
                    .total;
            const total = harga * val;
            $(this)
                .closest("tr")
                .find(".total-param")
                .html("Rp " + total.toLocaleString("id-ID"));
            updateTotalModalKeseluruhan();
        }
    });

    $(document).on("click", ".btn-hapus-param", function () {
        const mesinId = $(this).closest("tr").data("mesin-id");
        parameterMesinList = parameterMesinList.filter(
            (row) => row.mesin_id != mesinId,
        );
        renderParameterMesinTable();
        updateTotalModalKeseluruhan();
        updateTotalItemModal();
    });

    $(document).on("change", "#jenis_produk", function () {
        const jenisProduk = $(this).val();
        toggleRakitanMode(jenisProduk, "add");
    });

    // Fungsi untuk toggle mode rakitan
    function toggleRakitanMode(jenisProduk, mode = "add") {
        const bahanBakuSection =
            mode === "add" ? "#bahanBakuSection" : "#edit_bahanBakuSection";
        const produkKomponenSection =
            mode === "add"
                ? "#produkKomponenSection"
                : "#edit_produkKomponenSection";
        const parameterModalSection =
            mode === "add"
                ? "#parameterModalSection"
                : "#edit_parameterModalSection";

        if (jenisProduk === "rakitan") {
            $(bahanBakuSection).hide();
            $(parameterModalSection).hide();
            $(produkKomponenSection).show();
            // Reset bahan baku dan load produk komponen
            if (mode === "add") {
                bahanBakuList = [];
                renderTabelBahanBaku();
            }
        } else {
            $(bahanBakuSection).show();
            $(parameterModalSection).show();
            $(produkKomponenSection).hide();
            // Reset produk komponen dan load bahan baku
            if (mode === "add") {
                produkKomponenList = [];
                renderTabelProdukKomponen();
            }
        }
        updateTotalModalKeseluruhan();
    }

    // Fungsi render tabel produk komponen
    function renderTabelProdukKomponen() {
        const tbody = $("#tabelProdukKomponen tbody");
        tbody.empty();

        if (!produkKomponenList || produkKomponenList.length === 0) {
            tbody.append(
                '<tr class="text-muted"><td colspan="6" class="text-center">Belum ada produk komponen</td></tr>',
            );
            return;
        }

        produkKomponenList.forEach((row, idx) => {
            tbody.append(`
                <tr>
                    <td>${row.kode_produk || ""}
                    <input type="hidden" name="produk_komponen[${idx}][id]" value="${row.id}">
                    <input type="hidden" name="produk_komponen[${idx}][harga]" value="${row.total_modal_keseluruhan || 0}">
                    </td>
                    <td>${row.nama_produk || ""}</td>
                    <td>Rp ${(row.total_modal_keseluruhan || 0).toLocaleString("id-ID")}</td>
                    <td><input type="number" class="form-control form-control-sm jumlah-komponen" name="produk_komponen[${idx}][jumlah]" value="${row.jumlah || 1}" min="1" data-idx="${idx}" step="1"></td>
                    <td class="text-success fw-semibold text-end">Rp ${(row.total || 0).toLocaleString("id-ID")}</td>
                    <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-komponen" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
                </tr>
            `);
        });

        feather.replace();
    }
    function handleProdukKomponenDipilih(e) {
        if (!$("#tambahProduk").hasClass("show") || 
            !$("#modalCariProdukRakitanTambah").hasClass("show")) {
            return;
        }

        const data = e.detail;
        if (data.sourceModal !== 'add') {
            return;
        }
    
        if (
            typeof produkKomponenList === "undefined" ||
            !Array.isArray(produkKomponenList)
        ) {
            produkKomponenList = [];
        }
    
        if (produkKomponenList.some((item) => item.id === data.id)) {
            Swal.fire("Info", "Produk komponen sudah ditambahkan.", "info");
            return;
        }
    
        produkKomponenList.push({
            id: parseInt(data.id) || 0,
            kode_produk: data.kode_produk,
            nama_produk: data.nama_produk,
            total_modal_keseluruhan: parseFloat(data.total_modal_keseluruhan) || 0,
            harga: parseFloat(data.total_modal_keseluruhan) || 0,
            jumlah: 1,
            total: parseFloat(data.total_modal_keseluruhan) || 0,
        });
    
        renderTabelProdukKomponen();
        updateTotalModalKeseluruhan();
        updateTotalItemModal();
    }
    window.removeEventListener("produkKomponenDipilih", handleProdukKomponenDipilih);
    window.addEventListener("produkKomponenDipilih", handleProdukKomponenDipilih);

    $(document).on("input", ".jumlah-komponen", function () {
        const idx = $(this).data("idx");
        const harga =
            parseFloat(produkKomponenList[idx].total_modal_keseluruhan) || 0;
        const jumlah = parseInt($(this).val()) || 1;

        produkKomponenList[idx].harga = harga;
        produkKomponenList[idx].jumlah = jumlah;
        produkKomponenList[idx].total = harga * jumlah;

        $(this)
            .closest("tr")
            .find("td")
            .eq(4)
            .html(
                `<span class="text-success fw-semibold text-end">Rp ${produkKomponenList[idx].total.toLocaleString("id-ID")}</span>`,
            );

        updateTotalModalKeseluruhan();
    });

    // Handler hapus komponen
    $(document).on("click", ".btn-hapus-komponen", function () {
        const idx = $(this).data("idx");
        produkKomponenList.splice(idx, 1);
        renderTabelProdukKomponen();
        updateTotalModalKeseluruhan();
    });

    function updateTotalModalKeseluruhan() {
        const jenisProduk = $("#jenis_produk").val();
        let totalKomponen = 0;
        let totalBahan = 0;
        let totalParam = 0;

        if (jenisProduk === "rakitan") {
            // Hitung dari produk komponen
            totalKomponen = produkKomponenList.reduce(
                (sum, row) => sum + (row.harga || 0) * (row.jumlah || 1),
                0,
            );
        } else {
            // Hitung dari bahan baku (existing logic)
            totalBahan = bahanBakuList.reduce(
                (sum, row) => sum + (row.harga || 0) * (row.jumlah || 1),
                0,
            );
            totalParam = parameterMesinList.reduce((sum, row) => {
                const param =
                    row.opsi && row.opsi[row.selected]
                        ? row.opsi[row.selected]
                        : { total: 0 };
                return sum + (param.total || 0) * (row.jumlah || 1);
            }, 0);
        }

        let totalBiayaTambahan = 0;
        $("#tabelBiayaTambahan .biaya-tambahan-item").each(function () {
            const nilai =
                parseFloat($(this).find(".biaya-tambahan-nilai").val()) || 0;
            totalBiayaTambahan += nilai;
        });

        // Update DOM sesuai jenis produk
        if (jenisProduk === "rakitan") {
            $("#totalKomponenText").text(
                "Rp " + totalKomponen.toLocaleString("id-ID"),
            );
            $("#totalBahanBakuText").text("Rp 0");
            $("#totalParameterText").text("Rp 0");
        } else {
            $("#totalBahanBakuText").text(
                "Rp " + totalBahan.toLocaleString("id-ID"),
            );
            $("#totalParameterText").text(
                "Rp " + totalParam.toLocaleString("id-ID"),
            );
            $("#totalKomponenText").text("Rp 0");
        }

        $("#totalBiayaTambahanText").text(
            "Rp " + totalBiayaTambahan.toLocaleString("id-ID"),
        );

        const totalKeseluruhan =
            jenisProduk === "rakitan"
                ? totalKomponen + totalBiayaTambahan
                : totalBahan + totalParam + totalBiayaTambahan;

        $("#totalModalKeseluruhan").text(
            "Rp " + totalKeseluruhan.toLocaleString("id-ID"),
        );
        $("#totalModalBahan").text(
            jenisProduk === "rakitan"
                ? "Rp 0"
                : "Rp " + totalBahan.toLocaleString("id-ID"),
        );

        renderHargaBertingkat();
        renderHargaReseller();
    }

    // Tombol tambah produk komponen
    $(document).on("click", "#btnTambahProdukKomponen", function () {
        if ($("#modalCariProdukRakitanTambah").hasClass("show")) {
            return;
        }

        const modalElement = document.getElementById(
            "modalCariProdukRakitanTambah",
        );
        if (!modalElement) {
            console.error(
                "Modal element modalCariProdukRakitanTambah not found",
            );
            return;
        }

        try {
            var modalProduk = new bootstrap.Modal(modalElement, {
                backdrop: "static",
                keyboard: false,
                focus: true,
            });
            modalProduk.show();
        } catch (error) {
            console.error("Error creating modal:", error);
            Swal.fire("Error", "Gagal membuka modal pencarian produk", "error");
        }
    });

    // Perhitungan total seluruh modal
    // function updateTotalModalKeseluruhan() {
    //     // Pastikan bahanBakuList dan parameterMesinList terdefinisi
    //     if (typeof bahanBakuList === "undefined") bahanBakuList = [];
    //     if (typeof parameterMesinList === "undefined") parameterMesinList = [];

    //     let totalBahan = bahanBakuList.reduce(
    //         (sum, row) => sum + (row.harga || 0) * (row.jumlah || 1),
    //         0
    //     );
    //     let totalParam = parameterMesinList.reduce((sum, row) => {
    //         const param =
    //             row.opsi && row.opsi[row.selected]
    //                 ? row.opsi[row.selected]
    //                 : { total: 0 };
    //         return sum + (param.total || 0) * (row.jumlah || 1);
    //     }, 0);

    //     let totalBiayaTambahan = 0;
    //     $('#tabelBiayaTambahan .biaya-tambahan-item').each(function() {
    //         const nilai = parseFloat($(this).find('.biaya-tambahan-nilai').val()) || 0;
    //         totalBiayaTambahan += nilai;
    //     });

    //     $("#totalBahanBakuText").text(
    //         "Rp " + totalBahan.toLocaleString("id-ID")
    //     );
    //     $("#totalParameterText").text(
    //         "Rp " + totalParam.toLocaleString("id-ID")
    //     );
    //     $("#totalBiayaTambahanText").text(
    //         "Rp " + totalBiayaTambahan.toLocaleString("id-ID")
    //     );
    //     $("#totalModalKeseluruhan").text(
    //         "Rp " + (totalBahan + totalParam + totalBiayaTambahan).toLocaleString("id-ID")
    //     );
    //     $("#totalModalBahan").text("Rp " + totalBahan.toLocaleString("id-ID"));
    //     updateTotalItemModal();

    //     renderHargaBertingkat();
    //     renderHargaReseller();
    // }

    // Jumlah item total
    function updateTotalItemModal() {
        const totalBahanBaku = bahanBakuList.length;
        const totalParameter = parameterMesinList.length;
        const totalBiayaTambahan = $(
            "#tabelBiayaTambahan .biaya-tambahan-item",
        ).length;
        const totalProdukKomponen = produkKomponenList.length;

        $("#totalItemModal").text(
            totalBahanBaku +
                totalParameter +
                totalBiayaTambahan +
                totalProdukKomponen +
                " item",
        );
    }

    // === ALUR PRODUKSI ===
    function mesinTemplate(index = 0, data = {}) {
        return `
        <div class="border rounded mb-3 p-3 position-relative mesin-item" data-index="${index}">
            <button type="button" class="btn btn-link text-danger position-absolute top-0 end-0 mt-2 me-2 btnHapusMesin" title="Hapus Mesin"><i data-feather="trash-2"></i></button>
            <div class="mb-2 fw-semibold">Mesin ${index + 1}</div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Nama Mesin</label>
                    <div class="input-group">
                        <input type="text" class="form-control nama-mesin-input" name="alur_produksi[${index}][nama_mesin]" value="${data.nama_mesin || ""}" placeholder="Pilih mesin..." readonly style="cursor: pointer; background-color: #fff;">
                        <input type="hidden" class="mesin-id-input" name="alur_produksi[${index}][mesin_id]" value="${data.id || ""}">
                        <button type="button" class="btn btn-outline-secondary btn-cari-mesin" title="Cari Mesin"><i class="fa fa-search"></i></button>
                    </div>
                    <small class="text-muted">Tipe: <span class="tipe-mesin-span">${
                        data.tipe_mesin || "Tidak diketahui"
                    }</span></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estimasi Waktu (menit) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="alur_produksi[${index}][estimasi_waktu]" value="${data.estimasi_waktu || ""}" min="0" placeholder="Estimasi waktu" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Catatan</label>
                <textarea class="form-control" name="alur_produksi[${index}][catatan]" rows="2" placeholder="Catatan proses">${data.catatan || ""}</textarea>
            </div>
        </div>
        `;
    }

    function renderAlurProduksi() {
        const container = $("#daftarMesin");
        container.empty();
        if (!alurProduksiList || alurProduksiList.length === 0) {
            container.append(
                '<div class="text-muted text-center">Belum ada mesin ditambahkan</div>',
            );
            return;
        }
        alurProduksiList.forEach((row, idx) => {
            container.append(mesinTemplate(idx, row));
        });
        feather.replace();
    }

    // Handler tombol tambah mesin
    $(document)
        .off("click", "#btnTambahMesin")
        .on("click", "#btnTambahMesin", function () {
            // Cek apakah ada mesin kosong yang belum diisi
            const hasEmptyMesin = alurProduksiList.some(
                (item) => item.id === "" || item.nama_mesin === "",
            );
            if (hasEmptyMesin) {
                Swal.fire(
                    "Info",
                    "Silakan isi mesin yang sudah ditambahkan terlebih dahulu.",
                    "info",
                );
                return;
            }
            alurProduksiList.push({
                id: "",
                nama_mesin: "",
                tipe_mesin: "",
                estimasi_waktu: "",
                catatan: "",
            });
            renderAlurProduksi();
        });

    // Handler klik input nama mesin untuk membuka modal cari mesin
    $(document)
        .off("click", ".nama-mesin-input, .btn-cari-mesin")
        .on("click", ".nama-mesin-input, .btn-cari-mesin", function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Cek apakah modal sudah terbuka, jika ya jangan buka lagi
            if ($("#modalCariMesinProdukTambah").hasClass("show")) {
                return;
            }

            const mesinItem = $(this).closest(".mesin-item");
            const index = mesinItem.data("index");

            // Simpan index mesin yang sedang dipilih
            window.currentMesinIndex = index;
            isModalForParameter = false;

            // Buka modal cari mesin
            var modalMesin = new bootstrap.Modal(
                document.getElementById("modalCariMesinProdukTambah"),
                {
                    backdrop: "static",
                    keyboard: false,
                    focus: true,
                },
            );
            modalMesin.show();

            // Tambahkan class stack untuk modal
            setTimeout(function () {
                if ($("#tambahProduk").hasClass("show")) {
                    $("body").addClass("modal-open");
                }
            }, 200);
        });

    // Fungsi utilitas untuk mengecek duplikasi mesin
    function isMesinDuplicate(mesinId, excludeIndex = null) {
        if (!mesinId) return false;
        return alurProduksiList.some(
            (item, idx) =>
                idx !== excludeIndex && item.id == mesinId && mesinId !== "",
        );
    }

    // Handler hapus mesin
    $(document).on("click", ".btnHapusMesin", function () {
        const idx = $(this).closest(".mesin-item").data("index");
        alurProduksiList.splice(idx, 1);
        renderAlurProduksi();
    });

    // Handler ketika mesin dipilih dari modal
    // window.addEventListener("mesinDipilih", function (e) {
    //     // Pastikan ini dari modal tambah produk
    //     if (!$("#modalCariMesinProdukTambah").hasClass("show")) return;

    //     const data = e.detail;
    //     const index = window.currentMesinIndex;

    //     // Cek duplikasi mesin di alur produksi
    //     if (typeof index !== "undefined" && alurProduksiList[index]) {
    //         // Cek apakah mesin dengan ID ini sudah ada di alur produksi (kecuali index saat ini)
    //         if (isMesinDuplicate(data.id, index)) {
    //             Swal.fire(
    //                 "Info",
    //                 "Mesin ini sudah ditambahkan di alur produksi.",
    //                 "info"
    //             );
    //             return;
    //         }

    //         alurProduksiList[index] = {
    //             id: data.id,
    //             nama_mesin: data.nama,
    //             tipe_mesin: data.tipe || "",
    //             estimasi_waktu: alurProduksiList[index].estimasi_waktu || "",
    //             catatan: alurProduksiList[index].catatan || "",
    //         };
    //         renderAlurProduksi();
    //     }
    // });

    // Sinkronisasi data alur produksi secara real-time
    $(document).on(
        "input change",
        "#daftarMesin .mesin-item input, #daftarMesin .mesin-item textarea",
        function () {
            const mesinDiv = $(this).closest(".mesin-item");
            const idx = mesinDiv.data("index");
            if (typeof idx === "undefined" || !alurProduksiList[idx]) return;

            alurProduksiList[idx].nama_mesin =
                mesinDiv.find(".nama-mesin-input").val() || "";
            alurProduksiList[idx].estimasi_waktu =
                parseInt(
                    mesinDiv.find('input[name*="[estimasi_waktu]"]').val(),
                ) || 0;
            alurProduksiList[idx].catatan =
                mesinDiv.find('textarea[name*="[catatan]"]').val() || "";
            alurProduksiList[idx].id =
                mesinDiv.find(".mesin-id-input").val() || "";
            alurProduksiList[idx].tipe_mesin =
                mesinDiv.find(".tipe-mesin-span").text() || "";
        },
    );

    // === HARGA BERTINGKAT & RESELLER ===
    let hargaBertingkatList = [];
    let hargaResellerList = [];

    function formatRupiahInput(value) {
        value = value.replace(/[^\d]/g, "");
        if (!value) return "";
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    $(document).on("input", ".money-format", function () {
        const input = $(this);
        let value = input.val().replace(/[^\d]/g, "");
        if (!value) {
            input.val("");
            return;
        }
        const formatted = formatRupiahInput(value);
        input.val(formatted);
        this.setSelectionRange(formatted.length, formatted.length);
    });

    $(document).on("blur", ".money-format", function () {
        const input = $(this);
        let value = input.val().replace(/[^\d]/g, "");
        input.val(formatRupiahInput(value));
    });

    function renderHargaBertingkat() {
        const tbody = $("#tabelHargaBertingkat tbody");
        tbody.empty();

        if (hargaBertingkatList.length === 0) {
            tbody.append(
                '<tr class="text-muted"><td colspan="6" class="text-center">Belum ada harga bertingkat</td></tr>',
            );
            return;
        }

        hargaBertingkatList.forEach((row, idx) => {
            const profitRp =
                row.harga -
                (parseInt(
                    $("#totalModalKeseluruhan").text().replace(/[^\d]/g, ""),
                ) || 0);
            const profitPersen =
                row.harga > 0
                    ? (
                          (profitRp /
                              parseInt(
                                  $("#totalModalKeseluruhan")
                                      .text()
                                      .replace(/[^\d]/g, ""),
                              )) *
                          100
                      ).toFixed(1)
                    : 0;

            tbody.append(`
                <tr>
                    <td><input type="number" class="form-control form-control-sm min-qty" value="${
                        row.min_qty
                    }" min="1" step="0.01" data-idx="${idx}"></td>
                    <td><input type="number" class="form-control form-control-sm max-qty" value="${
                        row.max_qty
                    }" min="1" step="0.01" data-idx="${idx}"></td>
                    <td><input type="text" class="form-control form-control-sm harga money-format" value="${
                        row.harga ? formatRupiahInput(row.harga.toString()) : ""
                    }" min="0" data-idx="${idx}"></td>
                    <td class="text-success fw-semibold">Rp ${profitRp.toLocaleString(
                        "id-ID",
                    )}</td>
                    <td class="text-success fw-semibold">${profitPersen}%</td>
                    <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-harga-bertingkat" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
                </tr>
            `);
        });

        feather.replace();
    }

    function renderHargaReseller() {
        const tbody = $("#tabelHargaReseller tbody");
        tbody.empty();

        if (hargaResellerList.length === 0) {
            tbody.append(
                '<tr class="text-muted"><td colspan="6" class="text-center">Belum ada harga reseller</td></tr>',
            );
            return;
        }

        hargaResellerList.forEach((row, idx) => {
            const profitRp =
                row.harga -
                (parseInt(
                    $("#totalModalKeseluruhan").text().replace(/[^\d]/g, ""),
                ) || 0);
            const profitPersen =
                row.harga > 0
                    ? (
                          (profitRp /
                              parseInt(
                                  $("#totalModalKeseluruhan")
                                      .text()
                                      .replace(/[^\d]/g, ""),
                              )) *
                          100
                      ).toFixed(1)
                    : 0;

            tbody.append(`
                <tr>
                    <td><input type="number" class="form-control form-control-sm min-qty" value="${
                        row.min_qty
                    }" min="1" step="0.01" data-idx="${idx}"></td>
                    <td><input type="number" class="form-control form-control-sm max-qty" value="${
                        row.max_qty
                    }" min="1" step="0.01" data-idx="${idx}"></td>
                    <td><input type="text" class="form-control form-control-sm harga money-format" value="${
                        row.harga ? formatRupiahInput(row.harga.toString()) : ""
                    }" min="0" data-idx="${idx}"></td>
                    <td class="text-success fw-semibold">Rp ${profitRp.toLocaleString(
                        "id-ID",
                    )}</td>
                    <td class="text-success fw-semibold">${profitPersen}%</td>
                    <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-harga-reseller" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
                </tr>
            `);
        });

        feather.replace();
    }

    $("#btnTambahHargaBertingkat")
        .off("click")
        .on("click", function () {
            hargaBertingkatList.push({ min_qty: 1, max_qty: 1, harga: 0 });
            renderHargaBertingkat();
        });

    $("#btnTambahHargaReseller")
        .off("click")
        .on("click", function () {
            hargaResellerList.push({ min_qty: 1, max_qty: 1, harga: 0 });
            renderHargaReseller();
        });

    $(document).on("click", ".btn-hapus-harga-bertingkat", function () {
        const idx = $(this).data("idx");
        hargaBertingkatList.splice(idx, 1);
        renderHargaBertingkat();
    });

    $(document).on("click", ".btn-hapus-harga-reseller", function () {
        const idx = $(this).data("idx");
        hargaResellerList.splice(idx, 1);
        renderHargaReseller();
    });

    $(document).on("input", "#tabelHargaBertingkat input", function () {
        const idx = $(this).data("idx");
        const field = $(this).hasClass("min-qty")
            ? "min_qty"
            : $(this).hasClass("max-qty")
              ? "max_qty"
              : "harga";

        if (field === "harga") {
            hargaBertingkatList[idx][field] =
                parseInt($(this).val().replace(/\./g, "")) || 0;
        } else {
            hargaBertingkatList[idx][field] = parseFloat($(this).val()) || 0;
        }

        updateProfitCalculation(
            "#tabelHargaBertingkat",
            idx,
            hargaBertingkatList[idx],
        );
    });

    $(document).on("blur", "#tabelHargaBertingkat input", function () {
        renderHargaBertingkat();
    });

    $(document).on("input", "#tabelHargaReseller input", function () {
        const idx = $(this).data("idx");
        const field = $(this).hasClass("min-qty")
            ? "min_qty"
            : $(this).hasClass("max-qty")
              ? "max_qty"
              : "harga";

        if (field === "harga") {
            hargaResellerList[idx][field] =
                parseInt($(this).val().replace(/\./g, "")) || 0;
        } else {
            hargaResellerList[idx][field] = parseFloat($(this).val()) || 0;
        }

        updateProfitCalculation(
            "#tabelHargaReseller",
            idx,
            hargaResellerList[idx],
        );
    });

    $(document).on("blur", "#tabelHargaReseller input", function () {
        renderHargaReseller();
    });

    function updateProfitCalculation(tableSelector, idx, rowData) {
        const totalModalKeseluruhan =
            parseInt(
                $("#totalModalKeseluruhan").text().replace(/[^\d]/g, ""),
            ) || 0;
        const profitRp = rowData.harga - totalModalKeseluruhan;
        const profitPersen =
            totalModalKeseluruhan > 0
                ? ((profitRp / totalModalKeseluruhan) * 100).toFixed(1)
                : 0;

        const row = $(`${tableSelector} tbody tr`).eq(idx);
        row.find("td")
            .eq(3)
            .html(
                `<span class="text-success fw-semibold">Rp ${profitRp.toLocaleString(
                    "id-ID",
                )}</span>`,
            );
        row.find("td")
            .eq(4)
            .html(
                `<span class="text-success fw-semibold">${profitPersen}%</span>`,
            );
    }

    // === Spesifikasi Teknis Dinamis (Tambah Produk)
    $("#tambah_spesifikasi_produk").on("click", function () {
        tambahSpesifikasiBaris("#spesifikasi_produk_container");
        $("#no_spesifikasi_message").hide();
    });

    // Fungsi untuk menambah baris spesifikasi
    function tambahSpesifikasiBaris(containerId) {
        var newSpesifikasi = `
            <div class="row mb-2 spesifikasi-item align-items-center">
                <div class="col-md-4 mb-1">
                    <input type="text" class="form-control form-control-sm" 
                        name="spesifikasi_nama[]" placeholder="Nama Spesifikasi">
                </div>
                <div class="col-md-4 mb-1">
                    <input type="text" class="form-control form-control-sm" 
                        name="spesifikasi_nilai[]" placeholder="Nilai">
                </div>
                <div class="col-md-3 mb-1">
                    <input type="text" class="form-control form-control-sm" 
                        name="spesifikasi_satuan[]" placeholder="Satuan">
                </div>
                <div class="col-md-1 mb-1 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-spesifikasi">
                        <i data-feather="trash-2" class="icon-sm"></i>
                    </button>
                </div>
            </div>
        `;
        $(containerId).append(newSpesifikasi);
        feather.replace();
    }

    // Event handler untuk tombol hapus spesifikasi
    $(document).on("click", ".remove-spesifikasi", function () {
        $(this).closest(".spesifikasi-item").remove();

        // Cek apakah masih ada spesifikasi yang tersisa
        var container = $("#spesifikasi_produk_container");
        if (container.find(".spesifikasi-item").length === 0) {
            $("#no_spesifikasi_message").show();
        }
    });

    // === BIAYA TAMBAHAN (TAMBAH PRODUK) ===

    // Handler tombol Tambah Biaya
    $(document).on("click", "#btnTambahBiayaTambahan", function () {
        // Cek apakah sudah ada biaya dengan nama yang sama
        const existingBiayaNama = new Set();
        $("#tabelBiayaTambahan .biaya-tambahan-item").each(function () {
            const nama = $(this).find(".biaya-tambahan-nama").val()?.trim();
            if (nama) {
                existingBiayaNama.add(nama.toLowerCase());
            }
        });

        // Hapus row pesan jika ada
        $('#tabelBiayaTambahan tbody tr td[colspan="3"]').parent().remove();

        const biayaHtml = `
            <tr class="biaya-tambahan-item">
                <td>
                    <input type="text" class="form-control form-control-sm biaya-tambahan-nama" 
                        placeholder="Contoh: Biaya Pengiriman, Biaya Admin">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control biaya-tambahan-nilai" 
                            placeholder="0" min="0" step="0.01">
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-biaya-tambahan">
                        <i data-feather="trash-2" class="icon-sm"></i>
                    </button>
                </td>
            </tr>
        `;

        $("#tabelBiayaTambahan tbody").append(biayaHtml);
        feather.replace();

        // Tambahkan validasi untuk mencegah duplikasi nama biaya
        $("#tabelBiayaTambahan .biaya-tambahan-nama")
            .last()
            .on("input", function () {
                const input = $(this);
                const nama = input.val()?.trim().toLowerCase();

                // Cek duplikasi dengan nama biaya yang sudah ada
                let isDuplicate = false;
                $("#tabelBiayaTambahan .biaya-tambahan-item")
                    .not(input.closest(".biaya-tambahan-item"))
                    .each(function () {
                        const existingNama = $(this)
                            .find(".biaya-tambahan-nama")
                            .val()
                            ?.trim()
                            .toLowerCase();
                        if (existingNama === nama && nama !== "") {
                            isDuplicate = true;
                            return false;
                        }
                    });

                if (isDuplicate) {
                    input.addClass("is-invalid");
                    if (!input.next(".invalid-feedback").length) {
                        input.after(
                            '<div class="invalid-feedback">Nama biaya sudah ada.</div>',
                        );
                    }
                } else {
                    input.removeClass("is-invalid");
                    input.next(".invalid-feedback").remove();
                }
            });

        updateTotalItemModal();
        updateTotalModalKeseluruhan();
    });

    // Handler tombol hapus biaya tambahan
    $(document).on("click", ".remove-biaya-tambahan", function () {
        $(this).closest(".biaya-tambahan-item").remove();

        if ($("#tabelBiayaTambahan .biaya-tambahan-item").length === 0) {
            $("#tabelBiayaTambahan tbody").append(
                '<tr><td colspan="3" class="text-center text-muted">Belum ada biaya tambahan ditambahkan</td></tr>',
            );
        }
        updateTotalModalKeseluruhan();
        updateTotalItemModal();
    });

    $(document).on("input", ".biaya-tambahan-nilai", function () {
        updateTotalModalKeseluruhan();
    });

    // Handler submit tambah produk
    $("#formProduk")
        .off("submit")
        .on("submit", function (e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html(
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...',
            );
            submitBtn.prop("disabled", true);

            // const bahanBakuArr = [];
            // $("#tabelBahanBaku tbody tr").each(function () {
            //     if ($(this).find('input[name="bahan_baku_id[]"]').length > 0) {
            //         bahanBakuArr.push({
            //             id: $(this).find('input[name="bahan_baku_id[]"]').val(),
            //             nama: $(this).find("td").eq(0).text().trim(),
            //             satuan: $(this).find("td").eq(1).text().trim(),
            //             harga:
            //                 parseInt(
            //                     $(this)
            //                         .find('input[name="harga_bahan[]"]')
            //                         .val()
            //                 ) || 0,
            //             jumlah:
            //                 parseInt(
            //                     $(this)
            //                         .find('input[name="jumlah_bahan[]"]')
            //                         .val()
            //                 ) || 0,
            //             total:
            //                 parseInt(
            //                     $(this)
            //                         .find('input[name="harga_bahan[]"]')
            //                         .val()
            //                 ) *
            //                     parseInt(
            //                         $(this)
            //                             .find('input[name="jumlah_bahan[]"]')
            //                             .val()
            //                     ) || 0,
            //         });
            //     }
            // });

            // $("#bahan_baku_json").val(JSON.stringify(bahanBakuArr));

            const alurArr = [];
            $("#daftarMesin .mesin-item").each(function () {
                alurArr.push({
                    nama_mesin:
                        $(this).find('input[name*="[nama_mesin]"]').val() || "",
                    tipe_mesin: $(this).find("span").text() || "",
                    estimasi_waktu:
                        parseInt(
                            $(this)
                                .find('input[name*="[estimasi_waktu]"]')
                                .val(),
                        ) || 0,
                    catatan:
                        $(this).find('textarea[name*="[catatan]"]').val() || "",
                });
            });

            $("#alur_produksi_json").val(JSON.stringify(alurArr));
            $("#harga_bertingkat_json").val(
                JSON.stringify(hargaBertingkatList),
            );
            const bahanBakuData = [];
            $("#tabelBahanBaku tbody tr").each(function () {
                const bahanBakuId = $(this)
                    .find('input[name="bahan_baku_id[]"]')
                    .val();
                const jumlah = $(this).find(".jumlah_bahan").val();
                const harga = $(this).find('input[name="harga_bahan[]"]').val();

                if (bahanBakuId && jumlah && harga) {
                    bahanBakuData.push({
                        id: parseInt(bahanBakuId),
                        jumlah: parseFloat(jumlah),
                        harga: parseInt(harga),
                    });
                }
            });

            $("#harga_reseller_json").val(JSON.stringify(hargaResellerList));
            const paramArr = parameterMesinList.map((row) => {
                const param = row.opsi[row.selected];
                return {
                    mesin_id: row.mesin_id,
                    nama_parameter: param.nama,
                    harga: param.total,
                    jumlah: row.jumlah,
                    total: param.total * row.jumlah,
                };
            });
            $("#parameter_modal_json").val(JSON.stringify(paramArr));
            const spesifikasiArr = [];
            $("#spesifikasi_produk_container .spesifikasi-item").each(
                function () {
                    const nama = $(this)
                        .find('input[name="spesifikasi_nama[]"]')
                        .val();
                    const nilai = $(this)
                        .find('input[name="spesifikasi_nilai[]"]')
                        .val();
                    const satuan = $(this)
                        .find('input[name="spesifikasi_satuan[]"]')
                        .val();
                    if (nama && nilai) {
                        spesifikasiArr.push({ nama, nilai, satuan });
                    }
                },
            );
            $("#spesifikasi_teknis_json").val(JSON.stringify(spesifikasiArr));
            const biayaTambahanArr = [];
            $("#tabelBiayaTambahan .biaya-tambahan-item").each(function () {
                const nama = $(this).find(".biaya-tambahan-nama").val()?.trim();
                const nilai =
                    parseFloat($(this).find(".biaya-tambahan-nilai").val()) ||
                    0;
                if (nama && nilai > 0) {
                    biayaTambahanArr.push({ nama, nilai });
                }
            });
            $("#biaya_tambahan_json").val(JSON.stringify(biayaTambahanArr));
            $("#lebar").val(parseFloat($("#lebar").val()) || 0);
            $("#panjang").val(parseFloat($("#panjang").val()) || 0);

            var form = $(this)[0];
            var formData = new FormData(form);
            const jenisProduk = $("#jenis_produk").val();
            if (jenisProduk === "rakitan") {
                $("#tabelProdukKomponen tbody tr").each(function (index) {
                    const komponenId = $(this)
                        .find(
                            'input[name="produk_komponen[' + index + '][id]"]',
                        )
                        .val();
                    const jumlah = $(this)
                        .find(
                            'input[name="produk_komponen[' +
                                index +
                                '][jumlah]"]',
                        )
                        .val();
                    const harga = $(this)
                        .find(
                            'input[name="produk_komponen[' +
                                index +
                                '][harga]"]',
                        )
                        .val();

                    if (komponenId && jumlah && harga) {
                        formData.append(
                            `produk_komponen[${index}][id]`,
                            parseInt(komponenId),
                        );
                        formData.append(
                            `produk_komponen[${index}][jumlah]`,
                            parseInt(jumlah),
                        );
                        formData.append(
                            `produk_komponen[${index}][harga]`,
                            parseFloat(harga),
                        );
                    }
                });

                // formData.append('produk_komponen', JSON.stringify(produkKomponenData));
            } else {
                bahanBakuData.forEach((item, index) => {
                    formData.append(`bahan_baku[${index}][id]`, item.id);
                    formData.append(
                        `bahan_baku[${index}][jumlah]`,
                        item.jumlah,
                    );
                    formData.append(`bahan_baku[${index}][harga]`, item.harga);
                });
            }

            selectedPhotos.forEach((file) => {
                formData.append("foto_pendukung_new[]", file);
            });

            selectedVideos.forEach((file) => {
                formData.append("video_pendukung_new[]", file);
            });

            selectedDocuments.forEach((file) => {
                formData.append("dokumen_pendukung_new[]", file);
            });

            $.ajax({
                url: "/backend/master-produk",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            title: "Sukses",
                            text: res.message,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        submitBtn.html(originalText);
                        submitBtn.prop("disabled", false);
                        Swal.fire({
                            title: "Gagal",
                            text: res.message,
                            icon: "error",
                            timer: 3000,
                            showConfirmButton: false,
                        });
                    }
                },
                error: function (xhr) {
                    submitBtn.html(originalText);
                    submitBtn.prop("disabled", false);
                    let msg = "Terjadi kesalahan.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: "Gagal",
                        text: msg,
                        icon: "error",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                },
            });
        });

    // Fungsi render preview media/dokumen (khusus tambah)
    function renderPhotosPreview() {
        const previewContainer = $("#fotoPendukungPreview");
        previewContainer.empty();

        if (selectedPhotos.length === 0) {
            previewContainer.append(`
                <div class="col-12 text-center text-muted" id="noFotoMessage">
                    <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang ditambahkan.
                </div>
            `);
            feather.replace();
            return;
        }

        selectedPhotos.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewItem = `
                    <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
                        <div class="card border-0 preview-card">
                            <img src="${e.target.result}" class="card-img-top img-fluid rounded" style="height: 100px; object-fit: cover;" alt="${file.name}">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-photo" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                                <i data-feather="x" class="icon-sm"></i>
                            </button>
                            <div class="card-body p-1 text-truncate" style="font-size: 0.75rem;">
                                ${file.name}
                            </div>
                        </div>
                    </div>
                `;
                previewContainer.append(previewItem);
                feather.replace();
            };
            reader.readAsDataURL(file);
        });
    }

    function renderVideosPreview() {
        const previewContainer = $("#videoPendukungPreview");
        previewContainer.empty();

        if (selectedVideos.length === 0) {
            previewContainer.append(`
                <div class="col-12 text-center text-muted" id="noVideoMessage">
                    <i data-feather="video" class="icon-lg mb-2"></i><br>Belum ada video yang ditambahkan.
                </div>
            `);
            feather.replace();
            return;
        }

        selectedVideos.forEach((file, index) => {
            const previewItem = `
                <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
                    <div class="card border-0 preview-card d-flex flex-column align-items-center justify-content-center p-2" style="height: 100px; overflow: hidden;">
                        <i data-feather="video" class="icon-lg text-primary mb-1"></i>
                        <div class="text-truncate w-100 text-center" style="font-size: 0.75rem;">${file.name}</div>
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-video" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                            <i data-feather="x" class="icon-sm"></i>
                        </button>
                    </div>
                </div>
            `;
            previewContainer.append(previewItem);
            feather.replace();
        });
    }

    function renderDocumentsPreview() {
        const previewContainer = $("#dokumenPendukungBody");
        previewContainer.empty();

        if (selectedDocuments.length === 0) {
            previewContainer.append(`
                <tr id="noDokumenMessage">
                    <td colspan="4" class="text-center text-muted py-4">
                        <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
                    </td>
                </tr>
            `);
            feather.replace();
            return;
        }

        selectedDocuments.forEach((file, index) => {
            const row = `
                <tr>
                    <td>${file.name}</td>
                    <td>${file.type}</td>
                    <td>${(file.size / 1024).toFixed(1)} KB</td>
                    <td><button type="button" class="btn btn-danger btn-sm delete-document" data-index="${index}"><i data-feather="trash" class="icon-sm"></i></button></td>
                </tr>
            `;
            previewContainer.append(row);
        });

        feather.replace();
    }

    const mediaDropzone = document.getElementById("mediaDropzoneArea");
    const mediaInput = document.getElementById("mediaPendukungInput");

    if (mediaDropzone && mediaInput) {
        mediaDropzone.addEventListener("click", function (e) {
            if (
                e.target === mediaDropzone ||
                e.target.classList.contains("dz-message") ||
                e.target.closest(".dz-message")
            ) {
                mediaInput.click();
            }
        });

        mediaDropzone.addEventListener("dragover", function (e) {
            e.preventDefault();
            mediaDropzone.classList.add("dragover");
        });

        mediaDropzone.addEventListener("dragleave", function (e) {
            e.preventDefault();
            mediaDropzone.classList.remove("dragover");
        });

        mediaDropzone.addEventListener("drop", function (e) {
            e.preventDefault();
            mediaDropzone.classList.remove("dragover");
            Array.from(e.dataTransfer.files).forEach((file) => {
                if (file.type.startsWith("image/")) selectedPhotos.push(file);
                else if (file.type.startsWith("video/"))
                    selectedVideos.push(file);
            });
            renderPhotosPreview();
            renderVideosPreview();
        });

        mediaInput.addEventListener("change", function (e) {
            Array.from(e.target.files).forEach((file) => {
                if (file.type.startsWith("image/")) selectedPhotos.push(file);
                else if (file.type.startsWith("video/"))
                    selectedVideos.push(file);
            });
            renderPhotosPreview();
            renderVideosPreview();
            mediaInput.value = "";
        });
    }

    $(document).on("click", "#tambahDokumen", function () {
        $("#dokumenPendukungInput").trigger("click");
    });

    $("#dokumenPendukungInput").on("change", function (e) {
        Array.from(e.target.files).forEach((file) => {
            selectedDocuments.push(file);
        });
        renderDocumentsPreview();
        $(this).val("");
    });

    $(document).on("click", ".delete-photo", function () {
        const indexToDelete = $(this).data("index");
        selectedPhotos.splice(indexToDelete, 1);
        renderPhotosPreview();
    });

    $(document).on("click", ".delete-video", function () {
        const indexToDelete = $(this).data("index");
        selectedVideos.splice(indexToDelete, 1);
        renderVideosPreview();
    });

    $(document).on("click", ".delete-document", function () {
        const indexToDelete = $(this).data("index");
        selectedDocuments.splice(indexToDelete, 1);
        renderDocumentsPreview();
    });
});

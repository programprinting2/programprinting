$(function () {
    // Variabel media khusus edit
    let existingPhotos = [],
        existingVideos = [],
        existingDocuments = [];
    let selectedPhotos = [],
        selectedVideos = [],
        selectedDocuments = [];
    let deletedPhotoIndexes = [],
        deletedVideoIndexes = [],
        deletedDocumentIndexes = [];
    let editAlurProduksiList = [];
    let editFinishingList = [];

    // === BAHAN BAKU (EDIT) ===
    let editBahanBakuList = [];

    function renderEditTabelBahanBaku() {
        const tbody = $("#editTabelBahanBaku tbody");
        tbody.empty();
        if (!editBahanBakuList || editBahanBakuList.length === 0) {
            tbody.append(
                '<tr class="text-muted"><td colspan="6" class="text-center">Belum ada bahan baku</td></tr>',
            );
            hitungTotalModalBahanEdit();
            return;
        }
        editBahanBakuList.forEach((row, idx) => {
            const isMetric = row.is_metric || false;
            const panjangDisabled = isMetric ? '' : 'disabled';
            const lebarDisabled = isMetric ? '' : 'disabled';
            const panjangValue = isMetric ? (row.panjang || '') : '';
            const lebarValue = isMetric ? (row.lebar || '') : '';
            const panjangPlaceholder = isMetric ? 'Panjang' : 'Tidak berlaku';
            const lebarPlaceholder = isMetric ? 'Lebar' : 'Tidak berlaku';
            
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
                    <td><input type="number" class="form-control form-control-sm jumlah_bahan_edit" name="jumlah_bahan[]" value="${
                        row.jumlah || 1
                    }" min="0" step="0.01" data-idx="${idx}"></td>
                    <td>
                        <input type="number" class="form-control form-control-sm panjang_bahan_edit" name="panjang_bahan[]" 
                               value="${panjangValue}" ${panjangDisabled} placeholder="${panjangPlaceholder}" 
                               data-idx="${idx}" step="0.01" min="0">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm lebar_bahan_edit" name="lebar_bahan[]" 
                               value="${lebarValue}" ${lebarDisabled} placeholder="${lebarPlaceholder}" 
                               data-idx="${idx}" step="0.01" min="0">
                    </td>
                    <td class="text-success fw-semibold text-end">Rp ${(
                        row.total || 0
                    ).toLocaleString("id-ID")}</td>
                    <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-edit-bahan-baku" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
                </tr>
            `);
        });
        feather.replace();
        hitungTotalModalBahanEdit();
    }

    // === PRODUK KOMPONEN (EDIT) ===
    let editProdukKomponenList = [];

    function renderEditTabelProdukKomponen() {
        const tbody = $("#editTabelProdukKomponen tbody");
        tbody.empty();

        if (!editProdukKomponenList || editProdukKomponenList.length === 0) {
            tbody.append(
                '<tr class="text-muted"><td colspan="6" class="text-center">Belum ada produk komponen</td></tr>',
            );
            hitungTotalModalKomponenEdit();
            return;
        }

        editProdukKomponenList.forEach((row, idx) => {
            tbody.append(`
                <tr>
                    <td>${row.kode_produk || ""}
                    <input type="hidden" name="produk_komponen[${idx}][id]" value="${row.id}">
                    <input type="hidden" name="produk_komponen[${idx}][harga]" value="${row.total_modal_keseluruhan || 0}">
                    </td>
                    <td>${row.nama_produk || ""}</td>
                    <td>Rp ${(parseFloat(row.total_modal_keseluruhan) || 0).toLocaleString("id-ID")}</td>
                    <td><input type="number" class="form-control form-control-sm jumlah-komponen-edit" name="produk_komponen[${idx}][jumlah]" value="${row.jumlah || 1}" data-idx="${idx}" step="0.01"></td>
                    <td class="text-success fw-semibold text-end">Rp ${(row.total || 0).toLocaleString("id-ID")}</td>
                    <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-edit-komponen" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
                </tr>
            `);
        });

        feather.replace();
        hitungTotalModalKomponenEdit();
    }

    function hitungTotalModalKomponenEdit() {
        let total = 0;
        if (editProdukKomponenList && editProdukKomponenList.length > 0) {
            total = editProdukKomponenList.reduce((sum, item) => {
                return sum + (item.total || 0);
            }, 0);
        }
        $("#editTotalModalKomponen").text(
            `Rp ${total.toLocaleString("id-ID")}`,
        );
        $("#editTotalKomponenText").text(`Rp ${total.toLocaleString("id-ID")}`);
        updateTotalModalKeseluruhanEdit();
    }

    function toggleEditJenisProduk() {
        const jenisProduk = $("#edit_jenis_produk").val();
        const isRakitan = jenisProduk === "rakitan";

        // Toggle visibility sections berdasarkan jenis produk
        if (isRakitan) {
            $("#editProdukKomponenSection").show();
            $("#editBahanBakuSection").show();
            $("#editParameterModalSection").show();

            // Reset bahan baku list
            // editBahanBakuList = [];
            // renderEditTabelBahanBaku();
        } else {
            $("#editProdukKomponenSection").hide();
            $("#editBahanBakuSection").show();
            $("#editParameterModalSection").show();

            // Reset komponen list
            // editProdukKomponenList.length = 0;
            // renderEditTabelProdukKomponen();
        }
        let description = "";
        switch (jenisProduk) {
            case "jasa":
                description = "Non Stok";
                break;
            case "produk":
                description = "Kumpulan dari beberapa bahan baku";
                break;
            case "rakitan":
                description =
                    "Gabungan dari beberapa produk yang bisa dijual terpisah";
                break;
            default:
                description = "Pilih jenis produk terlebih dahulu";
        }

        $("#edit-jenis-produk-description").text(description);
        renderEditTabelBahanBaku();
        renderEditTabelProdukKomponen();
        updateTotalModalKeseluruhanEdit();
    }

    $(document).on("change", "#edit_jenis_produk", function () {
        toggleEditJenisProduk();
    });

    // Event handler untuk tombol tambah produk
    $(document).on("click", "#editBtnTambahProdukKomponen", function () {
        if ($("#modalCariProdukRakitanEdit").hasClass("show")) {
            return;
        }

        const modalElement = document.getElementById(
            "modalCariProdukRakitanEdit",
        );
        if (!modalElement) {
            console.error("Modal element modalCariProdukRakitanEdit not found");
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

    $(document).on("input", ".jumlah-komponen-edit", function () {
        const idx = $(this).data("idx");
        const jumlah = parseFloat($(this).val()) || 1;

        if (editProdukKomponenList[idx]) {
            editProdukKomponenList[idx].jumlah = jumlah;
            editProdukKomponenList[idx].total =
                jumlah *
                (editProdukKomponenList[idx].total_modal_keseluruhan || 0);

            $(this)
                .closest("tr")
                .find("td")
                .eq(4)
                .html(
                    `<span class="text-success fw-semibold text-end">Rp ${editProdukKomponenList[idx].total.toLocaleString("id-ID")}</span>`,
                );

            updateTotalModalKeseluruhanEdit();
        }
    });

    $(document).on("click", ".btn-hapus-edit-komponen", function () {
        const idx = $(this).data("idx");
        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Apakah Anda yakin ingin menghapus komponen ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                editProdukKomponenList.splice(idx, 1);
                renderEditTabelProdukKomponen();
            }
        });
    });

    // Handler tombol edit produk
    $(document).on("click", ".btn-edit-produk", function () {
        const id = $(this).data("id");
        editProdukKomponenList = [];
        $.get("/backend/master-produk/" + id + "/edit", function (res) {
            if (res.success) {
                const p = res.produk;
                $("#edit_produk_id").val(p.id);
                $("#edit_nama_produk").val(p.nama_produk);
                $("#edit_kode_produk").val(p.kode_produk);
                $("#edit_kategori_utama")
                    .val(p.kategori_utama_id)
                    .trigger("change");
                updateEditSubKategoriOptions(
                    p.kategori_utama_id,
                    p.sub_kategori_id,
                );
                toggleFinishingTab(p.kategori_utama_id, true);
                $("#edit_satuanBarang").val(p.satuan_id);
                updateEditDetailSatuanOptions(p.satuan_id, p.sub_satuan_id);
                $("#edit_lebar").val(p.lebar);
                $("#edit_panjang").val(p.panjang);
                $("#edit_status_aktif").prop("checked", !!p.status_aktif);
                $("#edit_jenis_produk").val(p.jenis_produk);
                $("#edit_keterangan").val(p.keterangan || "");
                $("#edit_warna_id").val(p.warna_id || "");
                $('#edit_lebar_locked').prop('checked', !!p.lebar_locked);
                $('#edit_panjang_locked').prop('checked', !!p.panjang_locked);
                var $editTags = $('#edit_tags');
                $editTags.empty();
                if (Array.isArray(p.tags) && p.tags.length > 0) {
                    p.tags.forEach(function(tag) {
                        $editTags.append(new Option(tag, tag, true, true));
                    });
                    $editTags.val(p.tags).trigger('change');
                } else {
                    $editTags.val(null).trigger('change');
                }
                $('#edit_gunakan_dimensi').prop('checked', !!p.is_metric);
                $('#edit_metric_unit').val(p.metric_unit);
                updateEditMetricLabels();
                setTimeout(() => {
                    toggleDimensiByCheckbox("edit");
                    $('#edit_lebar_locked, #edit_panjang_locked').each(function() {
                        const checkboxId = $(this).attr('id');
                        updateLockIcon(checkboxId);
                    });
                }, 100);

                editBahanBakuList = Array.isArray(p.bahan_bakus)
                    ? p.bahan_bakus.map((bahanBaku) => {
                        const harga = bahanBaku.pivot.harga_snapshot;
                        const jumlah = bahanBaku.pivot.jumlah;
                        const panjang = bahanBaku.pivot.panjang;
                        const lebar = bahanBaku.pivot.lebar;
                          return {
                              id: bahanBaku.id,
                              nama: bahanBaku.nama_bahan,
                              satuan:
                                  p.sub_satuan?.nama_sub_detail_parameter ||
                                  p.satuan?.nama_detail_parameter ||
                                  "",
                              harga: bahanBaku.pivot.harga_snapshot,
                              jumlah: bahanBaku.pivot.jumlah,
                              is_metric: bahanBaku.is_metric || false,
                              panjang: panjang,
                              lebar: lebar,
                              total: harga * jumlah * panjang * lebar,
                          };
                      })
                    : [];
                if (!editProdukKomponenList || editProdukKomponenList.length === 0) {
                editProdukKomponenList = Array.isArray(p.produk_komponen)
                    ? p.produk_komponen.map((komponen) => {
                          return {
                              id: komponen.id,
                              kode_produk: komponen.kode_produk,
                              nama_produk: komponen.nama_produk,
                              total_modal_keseluruhan:
                                  komponen.total_modal_keseluruhan,
                              harga: komponen.total_modal_keseluruhan,
                              jumlah: komponen.pivot
                                  ? komponen.pivot.jumlah
                                  : 1,
                              total:
                                  (komponen.pivot ? komponen.pivot.jumlah : 1) *
                                  komponen.total_modal_keseluruhan,
                          };
                      })
                    : [];
                }
                renderEditTabelProdukKomponen();
                editFinishingList = Array.isArray(res.finishing_data) 
                    ? res.finishing_data.map(item => ({
                        id: parseInt(item.id),
                        nama: item.nama || '',
                        keterangan: item.keterangan || '',
                    }))
                    : [];
                $("#edit_finishing_json").val(JSON.stringify(editFinishingList));
                renderEditTabelFinishing();
                $("#edit_harga_bertingkat_json").val(
                    JSON.stringify(p.harga_bertingkat_json || []),
                );
                $("#edit_harga_reseller_json").val(
                    JSON.stringify(p.harga_reseller_json || []),
                );
                $("#edit_foto_pendukung_json").val(
                    JSON.stringify(p.foto_pendukung_json || []),
                );
                $("#edit_video_pendukung_json").val(
                    JSON.stringify(p.video_pendukung_json || []),
                );
                $("#edit_dokumen_pendukung_json").val(
                    JSON.stringify(p.dokumen_pendukung_json || []),
                );
                $("#edit_alur_produksi_json").val(
                    JSON.stringify(p.alur_produksi_json || []),
                );
                window.parameterMesinListFromBackend =
                    p.parameter_modal_json || [];
                existingPhotos = p.foto_pendukung_json
                    ? [...p.foto_pendukung_json]
                    : [];
                existingVideos = p.video_pendukung_json
                    ? [...p.video_pendukung_json]
                    : [];
                existingDocuments = p.dokumen_pendukung_json
                    ? [...p.dokumen_pendukung_json]
                    : [];
                selectedPhotos = [];
                selectedVideos = [];
                selectedDocuments = [];
                deletedPhotoIndexes = [];
                deletedVideoIndexes = [];
                deletedDocumentIndexes = [];
                renderPhotosPreview();
                renderVideosPreview();
                renderDocumentsPreview();
                renderEditTabelBahanBaku();
                toggleEditJenisProduk();
                // hitungTotalModalBahanEdit();
                editTotalModalKeseluruhanNumeric = 0;
                updateTotalModalKeseluruhanEdit();
                editHargaBertingkatList = Array.isArray(p.harga_bertingkat_json)
                    ? p.harga_bertingkat_json
                    : [];
                editHargaResellerList = Array.isArray(p.harga_reseller_json)
                    ? p.harga_reseller_json
                    : [];
                renderEditHargaBertingkat();
                renderEditHargaReseller();
                editAlurProduksiList = Array.isArray(p.alur_produksi_json)
                    ? p.alur_produksi_json.map((row) => ({
                        divisi_mesin: row.divisi_mesin || row.nama_mesin || "", 
                        divisi_mesin_id: parseInt(row.divisi_mesin_id || ""),
                        keterangan_divisi: row.keterangan_divisi || row.tipe_mesin || "",
                        estimasi_waktu: row.estimasi_waktu,
                        catatan: row.catatan,
                      }))
                    : [];
                renderEditAlurProduksi();
                editBiayaTambahanList = Array.isArray(p.biaya_tambahan_json)
                    ? p.biaya_tambahan_json
                    : [];
                renderEditBiayaTambahan();
                loadEditSpesifikasiTeknis(p.spesifikasi_teknis_json);
                const selectedWarnaId = p.warna_id;
                if (selectedWarnaId) {
                    const selectedOption = $(
                        '#edit_warna_id option[value="' +
                            selectedWarnaId +
                            '"]',
                    );
                    const hexCode = selectedOption.data("hex");
                    if (hexCode) {
                        updateEditWarnaPreviewModal(
                            hexCode,
                            "editWarnaPreviewModal",
                        );
                    }
                }
                $("#editProdukModalLabel").text(
                    `Edit Produk: ${p.nama_produk}`,
                );
                $("#editProdukModal").modal("show");
            } else {
                Swal.fire("Gagal", "Data produk tidak ditemukan", "error");
            }
        });
    });

    $(document).on("change", "#edit_kategori_utama", function () {
        const selectedKategoriId = $(this).val();
        updateEditSubKategoriOptions(selectedKategoriId);
        toggleFinishingTab(selectedKategoriId, true);
    });

    $(document).on("change", "#edit_satuanBarang", function () {
        updateEditDetailSatuanOptions($(this).val());
    });

    function calculateLuas(mode = "add") {
        const lebarInput = mode === "add" ? "#lebar" : "#edit_lebar";
        const panjangInput = mode === "add" ? "#panjang" : "#edit_panjang";
        const luasInput = mode === "add" ? "#luas" : "#edit_luas";
        
        const lebar = parseFloat($(lebarInput).val()) || 0;
        const panjang = parseFloat($(panjangInput).val()) || 0;
        const luas = lebar * panjang;
        
        $(luasInput).val(luas.toFixed(2));
    }
    
    function toggleDimensiByCheckbox(mode = "edit") {
        const checkboxId = "#edit_gunakan_dimensi";
        const containerId ="#edit_metric_unit_container";
        const metricUnitId = "#edit_metric_unit";
        const lebarInput = "#edit_lebar";
        const panjangInput = "#edit_panjang";
        const luasInput = "#edit_luas";
        const lebarLockedId = "#edit_lebar_locked";
        const panjangLockedId = "#edit_panjang_locked";
        
        const isChecked = $(checkboxId).is(':checked');
        
        if (isChecked) {
            $(containerId).show();
            $(metricUnitId).prop('disabled', false);
            $(lebarInput).prop('disabled', false);
            $(panjangInput).prop('disabled', false);
            $(luasInput).prop('disabled', false);
            $(lebarLockedId).prop('disabled', false);
            $(panjangLockedId).prop('disabled', false);
            
            calculateLuas("edit");
        } else {
            $(containerId).hide();
            $(metricUnitId).prop('disabled', true);
            $(lebarInput).prop('disabled', true);
            $(panjangInput).prop('disabled', true);
            $(luasInput).prop('disabled', true);
            $(lebarLockedId).prop('disabled', true);
            $(panjangLockedId).prop('disabled', true);
            
            $(lebarInput).val('0');
            $(panjangInput).val('0');
            $(luasInput).val('0');
        }
    }
    
    $(document).on('change', '#edit_gunakan_dimensi', function() {
        toggleDimensiByCheckbox("edit");
    });

    $(document).on("input", "#edit_lebar, #edit_panjang", function() {
        const isEnabled = $('#edit_gunakan_dimensi').is(':checked');
        if (isEnabled) {
            calculateLuas("edit");
        }
    });

    function updateEditMetricLabels() {
        const unit = $('#edit_metric_unit').val() || 'cm';
    
        $('#edit_label_metric_lebar').text(unit);
        $('#edit_label_metric_panjang').text(unit);
    
        if (unit === 'm') {
            $('#edit_label_metric_luas').text('m²');
        } else if (unit === 'mm') {
            $('#edit_label_metric_luas').text('mm²');
        } else {
            $('#edit_label_metric_luas').text('cm²');
        }
    }

    $(document).on('change', '#edit_metric_unit', function () {
        updateEditMetricLabels();
    });
    

    $("#editProdukModal").on("shown.bs.modal", function () {
        const selectedKategoriOnShow = $("#edit_kategori_utama").val();
        const currentSubKategoriOnShow = $(
            "#edit_sub_kategori_id_produk",
        ).val();
        updateEditSubKategoriOptions(
            selectedKategoriOnShow,
            currentSubKategoriOnShow,
        );
        const selectedSatuanOnShow = $("#edit_satuanBarang").val();
        const currentSubSatuanOnShow = $("#edit_detail_satuan").val();
        updateEditDetailSatuanOptions(
            selectedSatuanOnShow,
            currentSubSatuanOnShow,
        );

        if (selectedKategoriOnShow) {
            toggleFinishingTab(selectedKategoriOnShow, true);
        }

        $('#edit_lebar_locked, #edit_panjang_locked').each(function() {
            const checkboxId = $(this).attr('id');
            updateLockIcon(checkboxId);
        });
    });

    // === MEDIA & DOKUMEN (EDIT) ===
    // Fungsi render preview media/dokumen (khusus edit)
    function renderPhotosPreview() {
        const previewContainer = $("#editFotoPendukungPreview");
        previewContainer.empty();
        // Foto lama
        existingPhotos.forEach((url, idx) => {
            if (!deletedPhotoIndexes.includes(idx)) {
                previewContainer.append(` <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
                    <div class="card border-0 preview-card">
                        <img src="${url}" class="card-img-top img-fluid rounded" style="height: 100px; object-fit: cover;" alt="Foto lama">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-photo-existing" data-index="${idx}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                            <i data-feather="x" class="icon-sm"></i>
                        </button>
                    </div>
                </div> `);
            }
        });
        // Foto baru
        selectedPhotos.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewItem = ` <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
                    <div class="card border-0 preview-card">
                        <img src="${e.target.result}" class="card-img-top img-fluid rounded" style="height: 100px; object-fit: cover;" alt="${file.name}">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-photo" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                            <i data-feather="x" class="icon-sm"></i>
                        </button>
                        <div class="card-body p-1 text-truncate" style="font-size: 0.75rem;">
                            <span class="badge bg-primary">Baru</span> ${file.name}
                        </div>
                    </div>
                </div> `;
                previewContainer.append(previewItem);
                feather.replace();
            };
            reader.readAsDataURL(file);
        });
        if (
            existingPhotos.filter(
                (_, idx) => !deletedPhotoIndexes.includes(idx),
            ).length === 0 &&
            selectedPhotos.length === 0
        ) {
            previewContainer.append(` <div class="col-12 text-center text-muted" id="noEditFotoMessage">
                <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang ditambahkan.
            </div> `);
            feather.replace();
        }
    }

    function renderVideosPreview() {
        const previewContainer = $("#editVideoPendukungPreview");
        previewContainer.empty();
        // Video lama
        existingVideos.forEach((url, idx) => {
            if (!deletedVideoIndexes.includes(idx)) {
                previewContainer.append(` <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
                    <div class="card border-0 preview-card d-flex flex-column align-items-center justify-content-center p-2" style="height: 100px; overflow: hidden;">
                        <i data-feather="video" class="icon-lg text-primary mb-1"></i>
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-video-existing" data-index="${idx}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                            <i data-feather="x" class="icon-sm"></i>
                        </button>
                    </div>
                </div> `);
            }
        });
        // Video baru
        selectedVideos.forEach((file, index) => {
            const previewItem = ` <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
                <div class="card border-0 preview-card d-flex flex-column align-items-center justify-content-center p-2" style="height: 100px; overflow: hidden;">
                    <i data-feather="video" class="icon-lg text-primary mb-1"></i>
                    <div class="text-truncate w-100 text-center" style="font-size: 0.75rem;"><span class="badge bg-primary">Baru</span> ${file.name}</div>
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-video" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                        <i data-feather="x" class="icon-sm"></i>
                    </button>
                </div>
            </div> `;
            previewContainer.append(previewItem);
            feather.replace();
        });
        if (
            existingVideos.filter(
                (_, idx) => !deletedVideoIndexes.includes(idx),
            ).length === 0 &&
            selectedVideos.length === 0
        ) {
            previewContainer.append(` <div class="col-12 text-center text-muted" id="noEditVideoMessage">
                <i data-feather="video" class="icon-lg mb-2"></i><br>Belum ada video yang ditambahkan.
            </div> `);
            feather.replace();
        }
    }

    function renderDocumentsPreview() {
        const previewContainer = $("#editDokumenPendukungBody");
        previewContainer.empty();
        // Dokumen lama
        existingDocuments.forEach((doc, idx) => {
            if (!deletedDocumentIndexes.includes(idx)) {
                previewContainer.append(` <tr>
                    <td>${doc.nama || "-"}</td>
                    <td>${doc.jenis || "-"}</td>
                    <td>${
                        doc.ukuran
                            ? (doc.ukuran / 1024).toFixed(1) + " KB"
                            : "-"
                    }</td>
                    <td><button type="button" class="btn btn-danger btn-sm delete-document-existing" data-index="${idx}"><i data-feather="trash-2" class="icon-sm"></i></button></td>
                </tr> `);
            }
        });
        // Dokumen baru
        selectedDocuments.forEach((file, index) => {
            const row = ` <tr>
                <td>${file.name}</td>
                <td>${file.type}</td>
                <td>${(file.size / 1024).toFixed(1)} KB</td>
                <td><button type="button" class="btn btn-danger btn-sm delete-document" data-index="${index}"><i data-feather="trash-2" class="icon-sm"></i></button></td>
            </tr> `;
            previewContainer.append(row);
        });
        if (
            existingDocuments.filter(
                (_, idx) => !deletedDocumentIndexes.includes(idx),
            ).length === 0 &&
            selectedDocuments.length === 0
        ) {
            previewContainer.append(` <tr id="noEditDokumenMessage">
                <td colspan="4" class="text-center text-muted py-4">
                    <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
                </td>
            </tr> `);
        }
        feather.replace();
    }

    // === PARAMETER MESIN (EDIT) ===
    let editParameterMesinList = [];
    let isEditModalForParameter = false;

    // Helper function untuk menutup modal cari mesin
    function closeEditModalCariMesin() {
        const modal = bootstrap.Modal.getInstance(
            document.getElementById("modalCariMesinProdukEdit"),
        );
        if (modal) {
            modal.hide();
        }
    }

    function handleEditDivisiMesinDipilih(e) {
        const data = e.detail;
        const index = window.currentMesinIndex;
        
        if (typeof index !== "undefined") {
            $(`.mesin-item[data-index="${index}"] .divisi-mesin-input`).val(data.nama_detail_parameter || data.nama);
            $(`.mesin-item[data-index="${index}"] .divisi-mesin-id-input`).val(data.id);
            $(`.mesin-item[data-index="${index}"] .keterangan-divisi-span`).text(data.keterangan || 'Tidak ada keterangan');
            
            // Update array data
            if (editAlurProduksiList[index]) {
                editAlurProduksiList[index].divisi_mesin = data.nama_detail_parameter || data.nama;
                editAlurProduksiList[index].divisi_mesin_id = parseInt(data.id);
                editAlurProduksiList[index].keterangan_divisi = data.keterangan || '';
                editAlurProduksiList[index].estimasi_waktu = editAlurProduksiList[index].estimasi_waktu || 0;
                editAlurProduksiList[index].catatan = editAlurProduksiList[index].catatan || '';
            }
        }
    }

    // Named function untuk handle mesinDipilih
    function handleEditMesinDipilih(e) {
        if (!$("#editProdukModal").hasClass("show")) return;
        const data = e.detail;
        if (isEditModalForParameter) {
            // Handle untuk parameter mesin
            if (editParameterMesinList.some((pm) => pm.mesin_id == data.id)) {
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
            editParameterMesinList.push({
                mesin_id: data.id,
                opsi: profil,
                selected: 0,
                jumlah: 1,
            });
            renderEditParameterMesinTable();
            updateTotalModalKeseluruhanEdit();
            updateTotalItemModalEdit();
            closeEditModalCariMesin();
        } else {
            // Handle untuk alur produksi
            const index = window.currentMesinIndex;
            // Validasi: pastikan index valid dan ada di array
            if (typeof index === "undefined" || !editAlurProduksiList[index]) {
                console.warn("Index mesin tidak valid untuk alur produksi", {
                    index: index,
                    arrayLength: editAlurProduksiList
                        ? editAlurProduksiList.length
                        : 0,
                    array: editAlurProduksiList,
                });
                return;
            }
            if (isEditMesinDuplicate(data.id, index)) {
                Swal.fire(
                    "Info",
                    "Mesin ini sudah ditambahkan di alur produksi.",
                    "info",
                );
                return;
            }

            // Update array data
            editAlurProduksiList[index] = {
                id: data.id,
                nama_mesin: data.nama,
                tipe_mesin: data.tipe || "",
                estimasi_waktu:
                    editAlurProduksiList[index].estimasi_waktu || "",
                catatan: editAlurProduksiList[index].catatan || "",
            };

            renderEditAlurProduksi();
            closeEditModalCariMesin();
        }
    }

    function convertSavedParameterToInternal(savedData) {
        if (!Array.isArray(savedData) || !savedData.length) return [];
        return savedData
            .map((savedItem) => {
                // Cari mesin berdasarkan mesin_id
                const mesin = window.masterMesinList?.find(
                    (m) => m.id == savedItem.mesin_id,
                );
                if (!mesin) {
                    console.warn(
                        `Mesin dengan ID ${savedItem.mesin_id} tidak ditemukan`,
                    );
                    return null;
                }
                // Ambil dan parse biaya_perhitungan_profil
                let profil = mesin.biaya_perhitungan_profil;
                if (!profil) {
                    console.warn(
                        `Mesin ${mesin.nama_mesin} tidak memiliki parameter biaya`,
                    );
                    return null;
                }
                if (typeof profil === "string") {
                    try {
                        profil = JSON.parse(profil);
                    } catch {
                        profil = [];
                    }
                }
                if (!Array.isArray(profil)) profil = [profil];
                // Filter parameter yang valid
                profil = profil.filter(
                    (p) =>
                        p && typeof p === "object" && p.nama && p.total != null,
                );
                // Cari index parameter yang sesuai dengan nama_parameter yang disimpan
                const selectedIndex = profil.findIndex(
                    (p) => p.nama === savedItem.nama_parameter,
                );
                if (selectedIndex === -1) {
                    console.warn(
                        `Parameter "${savedItem.nama_parameter}" tidak ditemukan di mesin ${mesin.nama_mesin}`,
                    );
                    return null;
                }
                // Return struktur internal
                return {
                    mesin_id: savedItem.mesin_id,
                    opsi: profil,
                    selected: selectedIndex,
                    jumlah: savedItem.jumlah || 1,
                };
            })
            .filter((item) => item !== null);
    }

    // LOAD DATA DARI BACKEND SAAT MODAL EDIT DIBUKA
    $("#editProdukModal").on("show.bs.modal", function () {
        // if (!editProdukKomponenList || editProdukKomponenList.length === 0) {
        //     editProdukKomponenList = [];
        // }
        // Berasal dari backend: window.parameterMesinListFromBackend
        const produkData = window.parameterMesinListFromBackend;
        if (Array.isArray(produkData) && produkData.length > 0) {
            editParameterMesinList =
                convertSavedParameterToInternal(produkData);
        } else {
            editParameterMesinList = [];
        }
        renderEditParameterMesinTable();
        updateTotalModalKeseluruhanEdit();
        // Pastikan event listener terdaftar saat modal dibuka
        window.removeEventListener("mesinDipilih", handleEditMesinDipilih);
        window.addEventListener("mesinDipilih", handleEditMesinDipilih);

        window.removeEventListener(
            "produkKomponenDipilih",
            handleEditProdukKomponenDipilih,
        );
        window.addEventListener(
            "produkKomponenDipilih",
            handleEditProdukKomponenDipilih,
        );
        window.removeEventListener("divisiMesinDipilih", handleEditDivisiMesinDipilih);
        window.addEventListener("divisiMesinDipilih", handleEditDivisiMesinDipilih);
        window.removeEventListener("finishingDipilih", handleEditFinishingDipilih);
        window.addEventListener("finishingDipilih", handleEditFinishingDipilih);
    });

    function handleEditFinishingDipilih(e) {
        const data = e.detail;
        
        if (editFinishingList.some(item => item.id === data.id)) {
            // Swal.fire('Info', 'Finishing sudah ditambahkan.', 'info');
            return;
        }
        
        editFinishingList.push({
            id: data.id,
            nama: data.nama,
            keterangan: data.keterangan,
        });
        
        $('#edit_finishing_json').val(JSON.stringify(editFinishingList));
        renderEditTabelFinishing();
        Swal.fire('Berhasil', 'Finishing berhasil ditambahkan.', 'success');
    }

    // Cleanup saat modal edit ditutup
    $("#editProdukModal").on("hidden.bs.modal", function () {
        // Cleanup: remove event listener saat modal ditutup
        window.removeEventListener("mesinDipilih", handleEditMesinDipilih);
        window.removeEventListener(
            "produkKomponenDipilih",
            handleEditProdukKomponenDipilih,
        );
        window.removeEventListener("divisiMesinDipilih", handleEditDivisiMesinDipilih);
        $("#editProdukModalLabel").text("Edit Produk");
        $("#editProdukForm")[0].reset();
        editProdukKomponenList = [];
        renderEditTabelProdukKomponen();
        editFinishingList = [];
        renderEditTabelFinishing();
    });

    // Tombol + Tambah Parameter (edit)
    $(document)
        .off("click", "#editBtnTambahParameter")
        .on("click", "#editBtnTambahParameter", function (e) {
            e.preventDefault();
            e.stopPropagation();
            // Cek apakah modal sudah terbuka, jika ya jangan buka lagi
            if ($("#modalCariMesinProdukEdit").hasClass("show")) {
                return;
            }
            isEditModalForParameter = true;
            var modalMesin = new bootstrap.Modal(
                document.getElementById("modalCariMesinProdukEdit"),
                {
                    backdrop: "static",
                    keyboard: false,
                    focus: true,
                },
            );
            modalMesin.show();
            setTimeout(function () {
                if ($("#editProdukModal").hasClass("show")) {
                    $("body").addClass("modal-open");
                }
            }, 200);
        });

    // RENDER tabel parameter di modal edit
    function renderEditParameterMesinTable() {
        const tbody = $("#editTabelParameterModal tbody");
        tbody.empty();
        if (editParameterMesinList.length === 0) {
            tbody.append(
                '<tr><td colspan="6" class="text-center text-muted">Pilih parameter</td></tr>',
            );
            return;
        }
        editParameterMesinList.forEach((row, idx) => {
            let options = "";
            row.opsi.forEach((opt, i) => {
                options += `<option value="${i}" ${
                    i === row.selected ? "selected" : ""
                }>${opt.nama}</option>`;
            });
            tbody.append(` <tr data-mesin-id="${row.mesin_id}">
                <td>${
                    window.masterMesinList?.find((m) => m.id == row.mesin_id)
                        ?.nama_mesin || "-"
                }</td>
                <td>
                    <select class="form-select form-select-sm edit-param-dropdown" data-idx="${idx}">${options}</select>
                </td>
                <td class="harga-param">Rp. ${row.opsi[
                    row.selected
                ].total.toLocaleString("id-ID")}</td>
                <td><input type="number" class="form-control form-control-sm edit-jumlah-param" min="0" step="0.01" value="${
                    row.jumlah
                }" data-idx="${idx}"></td>
                <td class="total-param text-success fw-semibold">Rp ${(
                    row.opsi[row.selected].total * row.jumlah
                ).toLocaleString("id-ID")}</td>
                <td><button type="button" class="btn btn-danger btn-xs btn-hapus-edit-param"><i data-feather="trash-2" class="icon-sm"></i></button></td>
            </tr> `);
        });
        feather.replace();
    }

    // Event Handler (edit)
    $(document).on("change", ".edit-param-dropdown", function () {
        const idx = $(this).data("idx");
        editParameterMesinList[idx].selected = parseInt($(this).val());
        renderEditParameterMesinTable();
        updateTotalModalKeseluruhanEdit();
    });

    $(document).on("input", ".edit-jumlah-param", function () {
        const idx = $(this).data("idx");
        let val = parseFloat($(this).val()) || 0;
        if (val < 0) val = 0;
        editParameterMesinList[idx].jumlah = val;
        const harga =
            editParameterMesinList[idx].opsi[
                editParameterMesinList[idx].selected
            ].total;
        const total = harga * val;
        $(this)
            .closest("tr")
            .find(".total-param")
            .html("Rp " + total.toLocaleString("id-ID"));
        updateTotalModalKeseluruhanEdit();
    });

    $(document).on("click", ".btn-hapus-edit-param", function () {
        const mesinId = $(this).closest("tr").data("mesin-id");
        editParameterMesinList = editParameterMesinList.filter(
            (row) => row.mesin_id != mesinId,
        );
        renderEditParameterMesinTable();
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    let editTotalModalKeseluruhanNumeric = 0;
    // Fungsi update total modal keseluruhan (EDIT)
    function updateTotalModalKeseluruhanEdit() {
        const jenisProduk = $("#edit_jenis_produk").val();

        let totalBahan = 0;
        let totalKomponen = 0;
        let totalParam = 0;
        let totalBiayaTambahan = 0;

        // Hitung total komponen untuk produk rakitan
        totalKomponen = editProdukKomponenList.reduce((sum, item) => {
            return (
                sum + (item.total_modal_keseluruhan || 0) * (item.jumlah || 1)
            );
        }, 0);

        // Hitung total parameter mesin
        if (typeof editParameterMesinList !== "undefined") {
            editParameterMesinList.forEach((row) => {
                const param =
                    row.opsi && row.opsi[row.selected]
                        ? row.opsi[row.selected]
                        : { total: 0 };
                totalParam += (param.total || 0) * (row.jumlah || 1);
            });
        }
        // Hitung total bahan baku untuk produk biasa
        totalBahan = editBahanBakuList.reduce((sum, row) => {
            return sum + (row.total || 0);
        }, 0);

        // Hitung total biaya tambahan
        $("#editTabelBiayaTambahan .edit-biaya-tambahan-item").each(
            function () {
                const nilai =
                    parseFloat(
                        $(this).find(".edit-biaya-tambahan-nilai").val(),
                    ) || 0;
                totalBiayaTambahan += nilai;
            },
        );

        // Hitung total keseluruhan
        const totalKeseluruhan =
            totalBahan + totalKomponen + totalParam + totalBiayaTambahan;

        editTotalModalKeseluruhanNumeric = totalKeseluruhan;
        $("#editTotalBahanBakuText").text(
            `Rp ${totalBahan.toLocaleString("id-ID")}`,
        );
        $("#editTotalKomponenText").text(
            `Rp ${totalKomponen.toLocaleString("id-ID")}`,
        );
        $("#editTotalParameterText").text(
            `Rp ${totalParam.toLocaleString("id-ID")}`,
        );
        $("#editTotalBiayaTambahanText").text(
            `Rp ${totalBiayaTambahan.toLocaleString("id-ID")}`,
        );
        $("#editTotalModalKeseluruhan").text(
            `Rp ${totalKeseluruhan.toLocaleString("id-ID")}`,
        );

        // Update total modal bahan (untuk backward compatibility)
        $("#editTotalModalBahan").text(
            `Rp ${totalBahan.toLocaleString("id-ID")}`,
        );

        // Update jumlah item
        updateTotalItemModalEdit();

        // Update profit di tabel harga bertingkat & reseller secara realtime
        renderEditHargaBertingkat();
        renderEditHargaReseller();
    }

    // Fungsi untuk update jumlah item (bahan baku + parameter) - EDIT
    function updateTotalItemModalEdit() {
        const totalBahanBaku = editBahanBakuList.length;
        const totalParameter =
            typeof editParameterMesinList !== "undefined"
                ? editParameterMesinList.length
                : 0;
        const totalBiayaTambahan = $(
            "#editTabelBiayaTambahan .edit-biaya-tambahan-item",
        ).length;
        const totalItem = totalBahanBaku + totalParameter + totalBiayaTambahan;
        $("#editTotalItemModal").text(totalItem + " item");
    }

    // Panggil updateTotalModalKeseluruhanEdit di semua event relevan (EDIT)
    // Parameter mesin
    $(document).on("input", ".edit-jumlah-param", function () {
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    $(document).on("change", ".edit-param-dropdown", function () {
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    $(document).on("click", ".btn-hapus-edit-param", function () {
        setTimeout(() => {
            updateTotalModalKeseluruhanEdit();
            updateTotalItemModalEdit();
        }, 10);
    });

    // Fungsi untuk re-index mesin-item setelah ada yang dihapus (edit)
    function reindexEditMesinItems() {
        $(".mesin-item").each(function (i) {
            $(this).attr("data-index", i);
            $(this)
                .find(".fw-semibold")
                .text("Mesin " + (i + 1));
            $(this).find(".divisi-mesin-input").attr("name", `edit_alur_produksi[${i}][divisi_mesin]`);
            $(this).find(".divisi-mesin-id-input").attr("name", `edit_alur_produksi[${i}][divisi_mesin_id]`);
            $(this).find('input[name*="[estimasi_waktu]"]').attr("name", `edit_alur_produksi[${i}][estimasi_waktu]`);
            $(this).find('textarea[name*="[catatan]"]').attr("name", `edit_alur_produksi[${i}][catatan]`);
        });
    }

    // Handler hapus mesin-item (edit)
    $(document).on("click", ".btnHapusEditMesin", function () {
        const idx = $(this).closest(".mesin-item").data("index");
        $(this).closest(".mesin-item").remove();
        editAlurProduksiList.splice(idx, 1);
        reindexEditMesinItems();
        feather.replace();
    });

    // === HARGA BERTINGKAT & RESELLER (EDIT) ===
    let editHargaBertingkatList = [];
    let editHargaResellerList = [];

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

    function renderEditHargaBertingkat() {
        const tbody = $("#editTabelHargaBertingkat tbody");
        tbody.empty();
        if (editHargaBertingkatList.length === 0) {
            tbody.append(
                '<tr class="text-muted"><td colspan="6" class="text-center">Belum ada harga bertingkat</td></tr>',
            );
            return;
        }
        editHargaBertingkatList.forEach((row, idx) => {
            const profitRp = row.harga - editTotalModalKeseluruhanNumeric;
            const profitPersen =
            editTotalModalKeseluruhanNumeric > 0
                ? ((profitRp / editTotalModalKeseluruhanNumeric) * 100).toFixed(1)
                : 0;
            tbody.append(` <tr>
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
                <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-edit-harga-bertingkat" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
            </tr> `);
        });
        feather.replace();
    }

    function renderEditHargaReseller() {
        const tbody = $("#editTabelHargaReseller tbody");
        tbody.empty();
        if (editHargaResellerList.length === 0) {
            tbody.append(
                '<tr class="text-muted"><td colspan="6" class="text-center">Belum ada harga reseller</td></tr>',
            );
            return;
        }
        editHargaResellerList.forEach((row, idx) => {
            const profitRp = row.harga - editTotalModalKeseluruhanNumeric;
            const profitPersen =
                editTotalModalKeseluruhanNumeric > 0
                    ? ((profitRp / editTotalModalKeseluruhanNumeric) * 100).toFixed(1)
                    : 0;
            tbody.append(` <tr>
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
                <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-edit-harga-reseller" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
            </tr> `);
        });
        feather.replace();
    }

    $("#editBtnTambahHargaBertingkat")
        .off("click")
        .on("click", function () {
            editHargaBertingkatList.push({
                min_qty: 1,
                max_qty: 1,
                harga: 0,
            });
            renderEditHargaBertingkat();
        });

    $("#editBtnTambahHargaReseller")
        .off("click")
        .on("click", function () {
            editHargaResellerList.push({
                min_qty: 1,
                max_qty: 1,
                harga: 0,
            });
            renderEditHargaReseller();
        });

    $(document).on("click", ".btn-hapus-edit-harga-bertingkat", function () {
        const idx = $(this).data("idx");
        editHargaBertingkatList.splice(idx, 1);
        renderEditHargaBertingkat();
    });

    $(document).on("click", ".btn-hapus-edit-harga-reseller", function () {
        const idx = $(this).data("idx");
        editHargaResellerList.splice(idx, 1);
        renderEditHargaReseller();
    });

    $(document).on("input", "#editTabelHargaBertingkat input", function () {
        const idx = $(this).data("idx");
        const field = $(this).hasClass("min-qty")
            ? "min_qty"
            : $(this).hasClass("max-qty")
              ? "max_qty"
              : "harga";
        if (field === "harga") {
            editHargaBertingkatList[idx][field] =
                parseInt($(this).val().replace(/\./g, "")) || 0;
        } else {
            editHargaBertingkatList[idx][field] =
                parseFloat($(this).val()) || 0;
        }
        updateEditProfitCalculation(
            "#editTabelHargaBertingkat",
            idx,
            editHargaBertingkatList[idx],
        );
    });

    $(document).on("blur", "#editTabelHargaBertingkat input", function () {
        renderEditHargaBertingkat();
    });

    $(document).on("input", "#editTabelHargaReseller input", function () {
        const idx = $(this).data("idx");
        const field = $(this).hasClass("min-qty")
            ? "min_qty"
            : $(this).hasClass("max-qty")
              ? "max_qty"
              : "harga";
        if (field === "harga") {
            editHargaResellerList[idx][field] =
                parseInt($(this).val().replace(/\./g, "")) || 0;
        } else {
            editHargaResellerList[idx][field] = parseFloat($(this).val()) || 0;
        }
        updateEditProfitCalculation(
            "#editTabelHargaReseller",
            idx,
            editHargaResellerList[idx],
        );
    });

    $(document).on("blur", "#editTabelHargaReseller input", function () {
        renderEditHargaReseller();
    });

    function updateEditProfitCalculation(tableSelector, idx, rowData) {
        const profitRp = rowData.harga - editTotalModalKeseluruhanNumeric;
        const profitPersen =
            editTotalModalKeseluruhanNumeric > 0
                ? ((profitRp / editTotalModalKeseluruhanNumeric) * 100).toFixed(1)
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

    // === ALUR PRODUKSI (EDIT) ===
    // Fungsi utilitas untuk mengecek duplikasi mesin di edit modal
    function isEditMesinDuplicate(mesinId, excludeIndex = null) {
        if (!mesinId) return false;
        return editAlurProduksiList.some(
            (item, idx) =>
                idx !== excludeIndex && item.id == mesinId && mesinId !== "",
        );
    }

    function mesinTemplateEdit(index = 0, data = {}) {
        return ` <div class="border rounded mb-3 p-3 position-relative mesin-item" data-index="${index}">
            <button type="button" class="btn btn-link text-danger position-absolute top-0 end-0 mt-2 me-2 btnHapusEditMesin" title="Hapus Mesin"><i data-feather="trash-2"></i></button>
            <div class="mb-2 fw-semibold">Mesin ${index + 1}</div>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Divisi Mesin</label>
                    <div class="input-group">
                        <input type="text" class="form-control divisi-mesin-input" name="edit_alur_produksi[${index}][divisi_mesin]" value="${data.divisi_mesin || ""}" placeholder="Pilih divisi mesin..." style="cursor: pointer;">
                        <input type="hidden" class="divisi-mesin-id-input" name="edit_alur_produksi[${index}][divisi_mesin_id]" value="${data.divisi_mesin_id || ""}">
                        <button type="button" class="btn btn-outline-secondary btn-cari-divisi-mesin" title="Cari Divisi Mesin"><i class="fa fa-search"></i></button>
                    </div>
                    <small class="text-muted">Keterangan: <span class="keterangan-divisi-span">${
                        data.keterangan_divisi || "Tidak ada keterangan"
                    }</span></small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Estimasi Waktu (menit) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="edit_alur_produksi[${index}][estimasi_waktu]" value="${data.estimasi_waktu || ""}" min="0" placeholder="Estimasi waktu" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Catatan</label>
                <textarea class="form-control" name="edit_alur_produksi[${index}][catatan]" rows="2" placeholder="Catatan proses">${data.catatan || ""}</textarea>
            </div>
        </div> `;
    }

    function renderEditAlurProduksi() {
        const container = $("#editDaftarMesin");
        container.empty();
        if (!editAlurProduksiList || editAlurProduksiList.length === 0) {
            container.append(
                '<div class="text-muted text-center">Belum ada mesin ditambahkan</div>',
            );
            return;
        }
        editAlurProduksiList.forEach((row, idx) => {
            const mesinHtml = mesinTemplateEdit(idx, {
                divisi_mesin: row.divisi_mesin || row.nama_mesin || "",
                divisi_mesin_id: row.divisi_mesin_id || "",
                keterangan_divisi: row.keterangan_divisi || row.tipe_mesin || "",
                estimasi_waktu: row.estimasi_waktu || 0,
                catatan: row.catatan || "",
            });
            container.append(mesinHtml);
        });
        feather.replace();
    }

    // Handler klik input nama mesin untuk membuka modal cari mesin (EDIT)
    $(document)
        .off("click", ".nama-mesin-input, .btn-cari-mesin")
        .on("click", ".nama-mesin-input, .btn-cari-mesin", function (e) {
            e.preventDefault();
            e.stopPropagation();
            // Cek apakah modal sudah terbuka
            if ($("#modalCariMesinProdukEdit").hasClass("show")) {
                return;
            }
            const mesinItem = $(this).closest(".mesin-item");
            const index = mesinItem.data("index");
            // Simpan index mesin yang sedang dipilih
            window.currentMesinIndex = index;
            isEditModalForParameter = false;
            // Buka modal cari mesin
            var modalMesin = new bootstrap.Modal(
                document.getElementById("modalCariMesinProdukEdit"),
                {
                    backdrop: "static",
                    keyboard: false,
                    focus: true,
                },
            );
            modalMesin.show();
            // Tambahkan class stack untuk modal
            setTimeout(function () {
                if ($("#editProdukModal").hasClass("show")) {
                    $("body").addClass("modal-open");
                }
            }, 200);
        });

        $(document).off("click", ".divisi-mesin-input, .btn-cari-divisi-mesin")
        .on("click", ".divisi-mesin-input, .btn-cari-divisi-mesin", function (e) {
        e.preventDefault();
        e.stopPropagation();
        // Cek apakah modal sudah terbuka
        if ($("#modalCariDivisiMesinProdukEdit").hasClass("show")) {
            return;
        }
        const mesinItem = $(this).closest(".mesin-item");
        const index = mesinItem.data("index");
        window.currentMesinIndex = index;
        
        // Buka modal cari divisi mesin
        var modalDivisi = new bootstrap.Modal(
            document.getElementById("modalCariDivisiMesinProdukEdit"),
            {
                backdrop: "static",
                keyboard: false,
                focus: true,
            },
        );
        modalDivisi.show();
        // Tambahkan class stack untuk modal
        setTimeout(function () {
            if ($("#editProdukModal").hasClass("show")) {
                $("body").addClass("modal-open");
            }
        }, 200);
    });

    $('#modalCariDivisiMesinProdukEdit').on('hidden.bs.modal', function() {
        $('.btn-cari-divisi-mesin').removeClass('active');
    });

    $(document)
        .off("click", "#editBtnTambahMesin")
        .on("click", "#editBtnTambahMesin", function () {
            editAlurProduksiList.push({
                divisi_mesin: "",
                divisi_mesin_id: 0,
                keterangan_divisi: "",
                estimasi_waktu: 0,
                catatan: "",
            });
            renderEditAlurProduksi();
        });

    // Sinkronisasi data alur produksi (edit) secara real-time
    $(document).on(
        "input change",
        "#editDaftarMesin .mesin-item input, #editDaftarMesin .mesin-item textarea",
        function () {
            const mesinDiv = $(this).closest(".mesin-item");
            const idx = mesinDiv.data("index");
            if (typeof idx === "undefined" || !editAlurProduksiList[idx])
                return;
            editAlurProduksiList[idx].divisi_mesin = mesinDiv.find('input[name*="[divisi_mesin]"]').val() || "";
            editAlurProduksiList[idx].divisi_mesin_id = parseInt(mesinDiv.find('.divisi-mesin-id-input').val() || "");
            editAlurProduksiList[idx].keterangan_divisi = mesinDiv.find('.keterangan-divisi-span').text() || "";
            editAlurProduksiList[idx].estimasi_waktu = parseInt(mesinDiv.find('input[name*="[estimasi_waktu]"]').val()) || 0;
            editAlurProduksiList[idx].catatan = mesinDiv.find('textarea[name*="[catatan]"]').val() || "";
        },
    );

    // === MODAL CARI BAHAN BAKU (EDIT) ===
    $("#editBtnTambahBahan")
        .off("click")
        .on("click", function () {
            var modalBahan = new bootstrap.Modal(
                document.getElementById("modalCariBahanBakuProdukEdit"),
                {
                    backdrop: "static",
                    keyboard: false,
                    focus: true,
                },
            );
            modalBahan.show();
            setTimeout(function () {
                if ($("#editProdukModal").hasClass("show")) {
                    $("body").addClass("modal-open");
                }
            }, 200);
        });

    window.addEventListener("bahanBakuDipilih", function (e) {
        if (!$("#modalCariBahanBakuProdukEdit").hasClass("show")) return;
        const data = e.detail;
        if (editBahanBakuList.some((item) => item.id == data.id)) {
            Swal.fire("Info", "Bahan baku sudah ditambahkan.", "info");
            return;
        }
        editBahanBakuList.push({
            id: data.id,
            nama: data.nama,
            satuan: data.satuan,
            harga: data.harga || 0,
            jumlah: 1,
            is_metric: !!data.is_metric,
            panjang: data.is_metric ? (data.panjang || null) : null,
            lebar: data.is_metric ? (data.lebar || null) : null,
            total: parseFloat(data.harga || 0),
        });
        renderEditTabelBahanBaku();
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    $(document).on("click", ".btn-hapus-edit-bahan-baku", function () {
        const idx = $(this).data("idx");
        editBahanBakuList.splice(idx, 1);
        renderEditTabelBahanBaku();
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    $(document).on("input", ".jumlah_bahan_edit", function () {
        const idx = $(this).data("idx");
        const harga =
            parseInt(
                $(this).closest("tr").find('input[name="harga_bahan[]"]').val(),
            ) || 0;
        const jumlah = parseFloat($(this).val()) || 0;
        const panjang = parseFloat($(`.panjang_bahan_edit[data-idx="${idx}"]`).val()) || 1;
        const lebar = parseFloat($(`.lebar_bahan_edit[data-idx="${idx}"]`).val()) || 1;
        editBahanBakuList[idx].harga = harga;
        editBahanBakuList[idx].jumlah = jumlah;
        editBahanBakuList[idx].total = harga * jumlah * panjang * lebar;
        const total = harga * jumlah;
        $(this)
            .closest("tr")
            .find("td")
            .eq(6)
            .html(
                `<span class="text-success fw-semibold text-end">Rp ${total.toLocaleString(
                    "id-ID",
                )}</span>`,
            );
        // hitungTotalModalBahanEdit();
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    $(document).on("input", ".panjang_bahan_edit", function () {
        const idx = $(this).data("idx");
        const panjang = parseFloat($(this).val()) || 1;
        const lebar = parseFloat($(`.lebar_bahan_edit[data-idx="${idx}"]`).val()) || 1;
        const jumlah = parseFloat($(`.jumlah_bahan_edit[data-idx="${idx}"]`).val()) || 0;
        const harga = editBahanBakuList[idx].harga || 0;
        
        editBahanBakuList[idx].panjang = panjang;
        editBahanBakuList[idx].total = harga * jumlah * panjang * lebar;
        
        $(this).closest("tr").find("td").eq(6).html(
            `<span class="text-success fw-semibold text-end">Rp ${editBahanBakuList[idx].total.toLocaleString("id-ID")}</span>`
        );
        
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    $(document).on("input", ".lebar_bahan_edit", function () {
        const idx = $(this).data("idx");
        const lebar = parseFloat($(this).val()) || 1;
        const panjang = parseFloat($(`.panjang_bahan_edit[data-idx="${idx}"]`).val()) || 1;
        const jumlah = parseFloat($(`.jumlah_bahan_edit[data-idx="${idx}"]`).val()) || 0;
        const harga = editBahanBakuList[idx].harga || 0;
        
        editBahanBakuList[idx].lebar = lebar;
        editBahanBakuList[idx].total = harga * jumlah * panjang * lebar;
        
        $(this).closest("tr").find("td").eq(6).html(
            `<span class="text-success fw-semibold text-end">Rp ${editBahanBakuList[idx].total.toLocaleString("id-ID")}</span>`
        );
        
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    $(document).on("blur", ".jumlah_bahan_edit", function () {
        renderEditTabelBahanBaku();
    });

    function hitungTotalModalBahanEdit() {
        const jenisProduk = $("#edit_jenis_produk").val();

        if (jenisProduk === "rakitan") {
            $("#editTotalModalBahan").text("Rp 0");
            return;
        }

        const total = editBahanBakuList.reduce((sum, item) => {
            return sum + item.harga * item.jumlah;
        }, 0);

        $("#editTotalModalBahan").text("Rp " + total.toLocaleString("id-ID"));
        $("#editTotalModalKeseluruhan").text(
            "Rp " + total.toLocaleString("id-ID"),
        );
        updateTotalItemModalEdit();

        // Re-render profit calculations
        // renderEditHargaBertingkat();
        // renderEditHargaReseller();
    }

    function hitungTotalModalKomponenEdit() {
        let total = 0;
        if (editProdukKomponenList && editProdukKomponenList.length > 0) {
            total = editProdukKomponenList.reduce((sum, item) => {
                return sum + (item.total || 0);
            }, 0);
        }
        $("#editTotalModalKomponen").text(
            `Rp ${total.toLocaleString("id-ID")}`,
        );
        $("#editTotalKomponenText").text(`Rp ${total.toLocaleString("id-ID")}`);
        updateTotalModalKeseluruhanEdit();
    }

    // Sinkronisasi data alur produksi (edit) secara real-time
    $(document).on(
        "input change",
        "#editDaftarMesin .mesin-item input, #editDaftarMesin .mesin-item textarea",
        function () {
            const mesinDiv = $(this).closest(".mesin-item");
            const idx = mesinDiv.data("index");
            if (typeof idx === "undefined" || !editAlurProduksiList[idx])
                return;
            editAlurProduksiList[idx].divisi_mesin = mesinDiv.find('input[name*="[divisi_mesin]"]').val() || "";
            editAlurProduksiList[idx].divisi_mesin_id = parseInt(mesinDiv.find('.divisi-mesin-id-input').val() || "");
            editAlurProduksiList[idx].keterangan_divisi = mesinDiv.find('.keterangan-divisi-span').text() || "";
            editAlurProduksiList[idx].estimasi_waktu = parseInt(mesinDiv.find('input[name*="[estimasi_waktu]"]').val()) || 0;
            editAlurProduksiList[idx].catatan = mesinDiv.find('textarea[name*="[catatan]"]').val() || "";
        },
    );

    // === Spesifikasi Teknis Dinamis (Edit Produk) ===
    $("#edit_tambah_spesifikasi_produk").on("click", function () {
        tambahEditSpesifikasiBaris("#edit_spesifikasi_produk_container");
        $("#edit_no_spesifikasi_message").hide();
    });

    // Fungsi untuk menambah baris spesifikasi edit
    function tambahEditSpesifikasiBaris(containerId) {
        var newSpesifikasi = `
            <div class="row mb-2 edit-spesifikasi-item align-items-center">
                <div class="col-md-4 mb-1">
                    <input type="text" class="form-control form-control-sm" 
                        name="edit_spesifikasi_nama[]" placeholder="Nama Spesifikasi">
                </div>
                <div class="col-md-4 mb-1">
                    <input type="text" class="form-control form-control-sm" 
                        name="edit_spesifikasi_nilai[]" placeholder="Nilai">
                </div>
                <div class="col-md-3 mb-1">
                    <input type="text" class="form-control form-control-sm" 
                        name="edit_spesifikasi_satuan[]" placeholder="Satuan">
                </div>
                <div class="col-md-1 mb-1 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm edit-remove-spesifikasi">
                        <i data-feather="trash-2" class="icon-sm"></i>
                    </button>
                </div>
            </div>
        `;
        $(containerId).append(newSpesifikasi);
        feather.replace();
    }

    // Event handler untuk tombol hapus spesifikasi edit
    $(document).on("click", ".edit-remove-spesifikasi", function () {
        $(this).closest(".edit-spesifikasi-item").remove();

        // Cek apakah masih ada spesifikasi yang tersisa
        var container = $("#edit_spesifikasi_produk_container");
        if (container.find(".edit-spesifikasi-item").length === 0) {
            $("#edit_no_spesifikasi_message").show();
        }
    });

    // Fungsi untuk memuat spesifikasi teknis dari data produk yang akan diedit
    function loadEditSpesifikasiTeknis(spesifikasiJson = []) {
        const spesifikasiData = Array.isArray(spesifikasiJson)
            ? spesifikasiJson
            : [];

        // Render existing spesifikasi dari data yang dimuat
        const container = $("#edit_spesifikasi_produk_container");
        container.empty();

        if (spesifikasiData.length === 0) {
            container.append(
                '<div class="text-muted text-center py-3" id="edit_no_spesifikasi_message">Belum ada spesifikasi teknis. Klik tombol "Tambah Spesifikasi" untuk menambahkan.</div>',
            );
            return;
        }

        spesifikasiData.forEach((item) => {
            var existingSpesifikasi = `
                <div class="row mb-2 edit-spesifikasi-item align-items-center">
                    <div class="col-md-4 mb-1">
                        <input type="text" class="form-control form-control-sm" 
                            name="edit_spesifikasi_nama[]" placeholder="Nama Spesifikasi" 
                            value="${item.nama || ""}">
                    </div>
                    <div class="col-md-4 mb-1">
                        <input type="text" class="form-control form-control-sm" 
                            name="edit_spesifikasi_nilai[]" placeholder="Nilai" 
                            value="${item.nilai || ""}">
                    </div>
                    <div class="col-md-3 mb-1">
                        <input type="text" class="form-control form-control-sm" 
                            name="edit_spesifikasi_satuan[]" placeholder="Satuan" 
                            value="${item.satuan || ""}">
                    </div>
                    <div class="col-md-1 mb-1 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm edit-remove-spesifikasi">
                            <i data-feather="trash-2" class="icon-sm"></i>
                        </button>
                    </div>
                </div>
            `;
            container.append(existingSpesifikasi);
        });

        feather.replace();
    }

    // === BIAYA TAMBAHAN (EDIT PRODUK) ===
    let editBiayaTambahanList = [];

    // Handler tombol Tambah Biaya (Edit)
    $(document).on("click", "#editBtnTambahBiayaTambahan", function () {
        // Cek apakah sudah ada biaya dengan nama yang sama
        const existingBiayaNama = new Set();
        $("#editTabelBiayaTambahan .edit-biaya-tambahan-item").each(
            function () {
                const nama = $(this)
                    .find(".edit-biaya-tambahan-nama")
                    .val()
                    ?.trim();
                if (nama) {
                    existingBiayaNama.add(nama.toLowerCase());
                }
            },
        );

        // Hapus row pesan jika ada
        $('#editTabelBiayaTambahan tbody tr td[colspan="3"]').parent().remove();

        const biayaHtml = `
            <tr class="edit-biaya-tambahan-item">
                <td>
                    <input type="text" class="form-control form-control-sm edit-biaya-tambahan-nama" 
                        placeholder="Contoh: Biaya Pengiriman, Biaya Admin">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control edit-biaya-tambahan-nilai" 
                            placeholder="0" min="0" step="0.01">
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm edit-remove-biaya-tambahan">
                        <i data-feather="trash-2" class="icon-sm"></i>
                    </button>
                </td>
            </tr>
        `;

        $("#editTabelBiayaTambahan tbody").append(biayaHtml);
        feather.replace();

        // Tambahkan validasi untuk mencegah duplikasi nama biaya
        $("#editTabelBiayaTambahan .edit-biaya-tambahan-nama")
            .last()
            .on("input", function () {
                const input = $(this);
                const nama = input.val()?.trim().toLowerCase();

                // Cek duplikasi dengan nama biaya yang sudah ada
                let isDuplicate = false;
                $("#editTabelBiayaTambahan .edit-biaya-tambahan-item")
                    .not(input.closest(".edit-biaya-tambahan-item"))
                    .each(function () {
                        const existingNama = $(this)
                            .find(".edit-biaya-tambahan-nama")
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

        updateTotalItemModalEdit();
        updateTotalModalKeseluruhanEdit();
    });

    // Handler tombol hapus biaya tambahan (Edit)
    $(document).on("click", ".edit-remove-biaya-tambahan", function () {
        $(this).closest(".edit-biaya-tambahan-item").remove();

        if (
            $("#editTabelBiayaTambahan .edit-biaya-tambahan-item").length === 0
        ) {
            $("#editTabelBiayaTambahan tbody").append(
                '<tr><td colspan="3" class="text-center text-muted">Belum ada biaya tambahan ditambahkan</td></tr>',
            );
        }
        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    });

    $(document).on("input", ".edit-biaya-tambahan-nilai", function () {
        updateTotalModalKeseluruhanEdit();
    });

    $("#editProdukForm")
        .off("submit")
        .on("submit", function (e) {
            e.preventDefault();
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html(
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...',
            );
            submitBtn.prop("disabled", true);
            // $("#edit_bahan_baku_json").val(JSON.stringify(editBahanBakuList));
            $("#edit_alur_produksi_json").val(
                JSON.stringify(editAlurProduksiList),
            );
            $("#edit_harga_bertingkat_json").val(
                JSON.stringify(editHargaBertingkatList),
            );
            $("#edit_harga_reseller_json").val(
                JSON.stringify(editHargaResellerList),
            );
            const bahanBakuData = [];
            $("#editTabelBahanBaku tbody tr").each(function () {
                const bahanBakuId = $(this)
                    .find('input[name="bahan_baku_id[]"]')
                    .val();
                const jumlah = $(this)
                    .find('input[name="jumlah_bahan[]"]')
                    .val();
                const harga = $(this).find('input[name="harga_bahan[]"]').val();
                const panjang = $(this).find('input[name="panjang_bahan[]"]').val();  
                const lebar = $(this).find('input[name="lebar_bahan[]"]').val();      

                if (bahanBakuId && jumlah && harga) {
                    bahanBakuData.push({
                        id: parseInt(bahanBakuId),
                        jumlah: parseFloat(jumlah),
                        harga: parseInt(harga),
                        panjang: panjang !== '' ? parseFloat(panjang) : null,
                        lebar: lebar !== '' ? parseFloat(lebar) : null,
                    });
                }
            });

            const paramArr = editParameterMesinList.map((row) => {
                const param = row.opsi[row.selected];
                return {
                    mesin_id: row.mesin_id,
                    nama_parameter: param.nama,
                    harga: param.total,
                    jumlah: row.jumlah,
                    total: param.total * row.jumlah,
                };
            });
            $("#edit_parameter_modal_json").val(JSON.stringify(paramArr));
            const spesifikasiArr = [];
            $("#edit_spesifikasi_produk_container .edit-spesifikasi-item").each(
                function () {
                    const nama = $(this)
                        .find('input[name="edit_spesifikasi_nama[]"]')
                        .val();
                    const nilai = $(this)
                        .find('input[name="edit_spesifikasi_nilai[]"]')
                        .val();
                    const satuan = $(this)
                        .find('input[name="edit_spesifikasi_satuan[]"]')
                        .val();
                    if (nama && nilai) {
                        spesifikasiArr.push({ nama, nilai, satuan });
                    }
                },
            );
            $("#edit_spesifikasi_teknis_json").val(
                JSON.stringify(spesifikasiArr),
            );

            const editBiayaTambahanArr = [];
            $("#editTabelBiayaTambahan .edit-biaya-tambahan-item").each(
                function () {
                    const nama = $(this)
                        .find(".edit-biaya-tambahan-nama")
                        .val()
                        ?.trim();
                    const nilai =
                        parseFloat(
                            $(this).find(".edit-biaya-tambahan-nilai").val(),
                        ) || 0;
                    if (nama && nilai > 0) {
                        editBiayaTambahanArr.push({ nama, nilai });
                    }
                },
            );
            $("#edit_biaya_tambahan_json").val(
                JSON.stringify(editBiayaTambahanArr),
            );
            // Filter dokumen lama yang tidak dihapus
            const dokumenDipertahankan = existingDocuments.filter(
                (_, idx) => !deletedDocumentIndexes.includes(idx),
            );
            $("#edit_dokumen_pendukung_json").val(
                JSON.stringify(dokumenDipertahankan),
            );

            var form = $(this)[0];
            var formData = new FormData(form);
            formData.append("_method", "PUT");
            formData.append('lebar_locked', $('#edit_lebar_locked').is(':checked') ? 1 : 0);
            formData.append('panjang_locked', $('#edit_panjang_locked').is(':checked') ? 1 : 0);
            const tagsArray = $('#edit_tags').val() || [];
            formData.append('tags', JSON.stringify(tagsArray));
            // bahanBakuData.forEach((item, index) => {
            //     formData.append(`bahan_baku[${index}][id]`, item.id);
            //     formData.append(`bahan_baku[${index}][jumlah]`, item.jumlah);
            //     formData.append(`bahan_baku[${index}][harga]`, item.harga);
            // });

            const jenisProduk = $("#edit_jenis_produk").val();
            if (jenisProduk === "rakitan") {
                $("#editTabelProdukKomponen tbody tr").each(function (index) {
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
                            parseFloat(jumlah),
                        );
                        formData.append(
                            `produk_komponen[${index}][harga]`,
                            parseFloat(harga),
                        );
                    }
                });
            }
            // else {
            //     bahanBakuData.forEach((item, index) => {
            //         formData.append(`bahan_baku[${index}][id]`, item.id);
            //         formData.append(
            //             `bahan_baku[${index}][jumlah]`,
            //             item.jumlah,
            //         );
            //         formData.append(`bahan_baku[${index}][harga]`, item.harga);
            //     });
            // }
            if (bahanBakuData.length > 0) {
                bahanBakuData.forEach((item, index) => {
                    formData.append(`bahan_baku[${index}][id]`, item.id);
                    formData.append(
                        `bahan_baku[${index}][jumlah]`,
                        item.jumlah,
                    );
                    formData.append(`bahan_baku[${index}][harga]`, item.harga);
                    if (item.panjang !== null) {
                        formData.append(`bahan_baku[${index}][panjang]`, item.panjang);
                    }
                    if (item.lebar !== null) {
                        formData.append(`bahan_baku[${index}][lebar]`, item.lebar);
                    }
                });
            } else {
                formData.append('bahan_baku', []);
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

            const id = $("#edit_produk_id").val();
            $.ajax({
                url: "/backend/master-produk/" + id,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    if (res.success) {
                        if (res.produk && res.produk.total_modal_keseluruhan) {
                            const totalModal =
                                res.produk.total_modal_keseluruhan;
                            $("#editTotalModalKeseluruhan").text(
                                "Rp " + totalModal.toLocaleString("id-ID"),
                            );
                            $("#editTotalModalBahan").text(
                                "Rp " + totalModal.toLocaleString("id-ID"),
                            );
                            updateTotalItemModalEdit();

                            // Re-render profit calculations
                            renderEditHargaBertingkat();
                            renderEditHargaReseller();
                        }
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
    // Handler file input media (edit)
    const mediaDropzone = document.getElementById("editMediaDropzoneArea");
    const mediaInput = document.getElementById("editMediaPendukungInput");
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
    $(document).on("click", "#editTambahDokumen", function () {
        $("#editDokumenPendukungInput").trigger("click");
    });
    $("#editDokumenPendukungInput").on("change", function (e) {
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

    // Handler hapus file lama (foto, video, dokumen) di edit modal
    $(document).on("click", ".delete-photo-existing", function () {
        const idx = $(this).data("index");
        if (!deletedPhotoIndexes.includes(idx)) {
            deletedPhotoIndexes.push(idx);
            renderPhotosPreview();
        }
    });
    $(document).on("click", ".delete-video-existing", function () {
        const idx = $(this).data("index");
        if (!deletedVideoIndexes.includes(idx)) {
            deletedVideoIndexes.push(idx);
            renderVideosPreview();
        }
    });
    $(document).on("click", ".delete-document-existing", function () {
        const idx = $(this).data("index");
        if (!deletedDocumentIndexes.includes(idx)) {
            deletedDocumentIndexes.push(idx);
            renderDocumentsPreview();
        }
    });

    // Fungsi untuk render biaya tambahan di edit modal
    function renderEditBiayaTambahan() {
        const tbody = $("#editTabelBiayaTambahan tbody");
        tbody.empty();

        if (!editBiayaTambahanList || editBiayaTambahanList.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted">Belum ada biaya tambahan ditambahkan</td>
                </tr>
            `);
            return;
        }

        editBiayaTambahanList.forEach((biaya, idx) => {
            const biayaHtml = `
                <tr class="edit-biaya-tambahan-item">
                    <td>
                        <input type="text" class="form-control form-control-sm edit-biaya-tambahan-nama" 
                            placeholder="Contoh: Biaya Pengiriman, Biaya Admin" value="${biaya.nama || ""}">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control edit-biaya-tambahan-nilai" 
                                placeholder="0" min="0" step="0.01" value="${biaya.nilai || 0}">
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm edit-remove-biaya-tambahan">
                            <i data-feather="trash-2" class="icon-sm"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(biayaHtml);
        });

        feather.replace();

        updateTotalModalKeseluruhanEdit();
        updateTotalItemModalEdit();
    }

    function handleEditProdukKomponenDipilih(e) {
        if (!$("#editProdukModal").hasClass("show")) {
            return;
        }

        const data = e.detail;
        if (data.sourceModal !== "edit") {
            return;
        }

        if (!editProdukKomponenList) {
            editProdukKomponenList = [];
        }

        if (!Array.isArray(editProdukKomponenList)) {
            editProdukKomponenList = [];
        }

        if (editProdukKomponenList.some((item) => item.id === data.id)) {
            // Swal.fire("Info", "Produk komponen sudah ditambahkan.", "info");
            return;
        }

        editProdukKomponenList.push({
            id: parseInt(data.id) || 0,
            kode_produk: data.kode_produk,
            nama_produk: data.nama_produk,
            total_modal_keseluruhan:
                parseFloat(data.total_modal_keseluruhan) || 0,
            harga: parseFloat(data.total_modal_keseluruhan) || 0,
            jumlah: 1,
            total: parseFloat(data.total_modal_keseluruhan) || 0,
        });

        renderEditTabelProdukKomponen();
        updateTotalModalKeseluruhanEdit();
    }

    // Fungsi untuk update preview warna
    function updateEditWarnaPreviewModal(hexCode, previewId) {
        const preview = document.getElementById(previewId);
        if (hexCode && /^#[0-9A-F]{6}$/i.test(hexCode)) {
            preview.style.backgroundColor = hexCode;
            preview.style.display = "block";
            preview.title = `Warna: ${hexCode}`;
        } else {
            preview.style.display = "none";
            preview.style.backgroundColor = "";
            preview.title = "Tidak ada preview warna";
        }
    }

    $("#edit_warna_id").on("change", function () {
        const selectedOption = $(this).find("option:selected");
        const hexCode = selectedOption.data("hex");
        updateEditWarnaPreviewModal(hexCode, "editWarnaPreviewModal");
    });

    if (!$('#edit_tags').hasClass('select2-hidden-accessible')) {
        $('#edit_tags').select2({
            width: '100%',
            dropdownParent: $('#editProdukModal'),
            dropdownAutoWidth: true,
            placeholder: 'Ketik tags...',
            tags: true,
            tokenSeparators: [','],
            ajax: {
                url: '/api/tags/search',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.value,
                                text: item.label
                            };
                        })
                    };
                }
            },
        });
    }

    function renderEditTabelFinishing() {
        const tbody = $("#editTabelFinishing tbody");
        tbody.empty();
        
        if (!editFinishingList || editFinishingList.length === 0) {
            tbody.append(
                '<tr><td colspan="4" class="text-center text-muted">Belum ada finishing ditambahkan</td></tr>',
            );
            return;
        }
        
        editFinishingList.forEach(function(item, index) {
            tbody.append(`
                <tr data-id="${item.id}" data-index="${index}">
                    <td>${item.nama || '-'}</td>
                    <td>${item.keterangan || '-'}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-xs editBtnHapusFinishing" 
                                data-id="${item.id}" title="Hapus Finishing">
                            <i data-feather="trash-2" class="icon-sm"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
        
        if (typeof feather !== 'undefined') feather.replace();
    }
    
    $(document).on('click', '.editBtnHapusFinishing', function () {
        const finishingId = $(this).data('id');
        editFinishingList = editFinishingList.filter(item => item.id !== finishingId);
        
        $('#edit_finishing_json').val(JSON.stringify(editFinishingList));
        renderEditTabelFinishing();
    });

    function updateLockIcon(checkboxId) {
        const checkbox = $(`#${checkboxId}`);
        const icon = $(`.lock-icon[data-target="${checkboxId}"]`);
        
        if (checkbox.is(':checked')) {
            icon.removeClass('fa-unlock').addClass('fa-lock');
            icon.css('color', '#dc3545'); 
        } else {
            icon.removeClass('fa-lock').addClass('fa-unlock');
            icon.css('color', '#28a745'); 
        }
    }
    
    $(document).on('change', '#edit_lebar_locked, #edit_panjang_locked', function() {
        const checkboxId = $(this).attr('id');
        updateLockIcon(checkboxId);
    });
});

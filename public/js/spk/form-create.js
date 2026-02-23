/**
 * SPK Form Create - Script modular untuk form create SPK
 */
(function () {
    "use strict";

    // Guard optional helper
    const SafeHelper = {
        notify(type, title, message) {
            if (
                window.SPKHelper &&
                typeof SPKHelper.showNotification === "function"
            ) {
                SPKHelper.showNotification(title, message, type);
            }
        },
        confirm(title, message, okText = "Ya", cancelText = "Batal") {
            if (
                window.SPKHelper &&
                typeof SPKHelper.confirmDialog === "function"
            ) {
                return SPKHelper.confirmDialog(
                    title,
                    message,
                    okText,
                    cancelText,
                );
            }
            return Promise.resolve(window.confirm(message));
        },
    };

    // --- State Global ---
    let itemsData = [];
    // let tugasProduksiTabData = [];
    let filePendukungData = [];

    let editItemIndex = null;
    let itemTugasEditIndex = null;
    let itemTugasEditTaskIndex = null;
    let editTugasTabIndex = null;

    // Preview item state

    let modalFinishingData = [];
    let orderanPreviewObjectUrl = null;
    let currentFileImageInfo = null;
    let currentFilePdfInfo = null;
    let imageRotationDegrees = 0;
    let imageToolsModalInstance = null;
    // let previewFinishing = [];

    let currentExpandedFinishingIndex = 0;
    let currentSelectedProduk = null;

    // Explorer State
    let explorerContext = "spk"; // 'spk' or 'item'
    let currentExplorerPath = "";
    let selectedExplorerFiles = [];

    // --- Element refs (lazy via getter) ---
    const el = {
        form: () => document.getElementById("formTambahSPK"),
        // Item UI
        itemCardsContainer: () => document.getElementById("itemCardsContainer"),
        btnTambahItem: () => document.getElementById("btnTambahItem"),
        // Tugas Produksi
        tabTugasPane: () => document.getElementById("tugasProduksi"),
        modalTugas: () => document.getElementById("modalTugasProduksi"),
        modalTugasLabel: () =>
            document.getElementById("modalTugasProduksiLabel"),
        formTugas: () => document.getElementById("formTugasProduksi"),
        inputNamaTugas: () => document.getElementById("inputNamaTugas"),
        inputDitugaskan: () => document.getElementById("inputDitugaskan"),
        inputDitugaskanId: () => document.getElementById("inputDitugaskanId"),
        inputMesin: () => document.getElementById("inputMesin"),
        inputWaktu: () => document.getElementById("inputWaktu"),
        inputHarga: () => document.getElementById("inputHarga"),
        inputDeskripsi: () => document.getElementById("inputDeskripsi"),
        // Tugas table
        emptyTugasState: () => document.getElementById("emptyTugasState"),
        // File pendukung
        dropZone: () => document.getElementById("dropZone"),
        inputFilePendukung: () => document.getElementById("inputFilePendukung"),
        filePendukungBody: () => document.getElementById("filePendukungBody"),
        filePendukungHidden: () =>
            document.getElementById("filePendukungInput"),
        // Submit
        itemsHidden: () => document.getElementById("itemsInput"),
        tugasHidden: () => document.getElementById("tugasProduksiInput"),
        // Customer
        namaCustomerInput: () => document.getElementById("namaCustomerInput"),
        customerIdInput: () => document.getElementById("customerIdInput"),
        // Cari Bahan
        btnCariBahan: () => document.getElementById("btnCariBahan"),
    };

    // --- Utils ---
    function formatBytes(bytes) {
        if (!bytes) return "0 B";
        const k = 1024;
        const sizes = ["B", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    }

    function deepClone(obj) {
        return JSON.parse(JSON.stringify(obj));
    }

    async function loadAndRenderRelasiProduk(produkId) {
        const sectionRelasi = document.getElementById("sectionRelasiProduk");
        const bahanBody = document.getElementById("relasiBahanBakuBody");
        const kompBody = document.getElementById("relasiKomponenBody");

        if (!sectionRelasi || !bahanBody || !kompBody || !produkId) {
            return;
        }

        sectionRelasi.style.display = "block";
        bahanBody.innerHTML =
            '<p class="text-info small mb-0"><i class="fa fa-spinner fa-spin"></i> Memuat bahan baku...</p>';
        kompBody.innerHTML =
            '<p class="text-info small mb-0"><i class="fa fa-spinner fa-spin"></i> Memuat produk komponen...</p>';

        const bahanItem = document
            .getElementById("headingBahanBaku")
            ?.closest(".accordion-item");
        const kompItem = document
            .getElementById("headingKomponen")
            ?.closest(".accordion-item");

        try {
            const response = await fetch(
                `/backend/cari-relasi-produk/${produkId}`,
            );
            if (!response.ok) throw new Error("Gagal memuat relasi produk");

            const data = await response.json();

            const hasBahanBaku =
                data.bahan_baku &&
                Array.isArray(data.bahan_baku) &&
                data.bahan_baku.length > 0;
            const hasKomponen =
                data.komponen &&
                Array.isArray(data.komponen) &&
                data.komponen.length > 0;
            const hasAnyData = hasBahanBaku || hasKomponen;

            if (!hasAnyData) {
                sectionRelasi.style.display = "none";
                return;
            }

            if (hasBahanBaku) {
                bahanBody.innerHTML = `
                    <ul class="mb-0 small">
                        ${data.bahan_baku
                            .map(
                                (b) => `
                            <li>${b.nama}${b.kode ? ' <span class="text-muted">[' + b.kode + "]</span>" : ""}</li>
                        `,
                            )
                            .join("")}
                    </ul>
                `;
                if (bahanItem) bahanItem.style.display = "block";
            } else {
                bahanBody.innerHTML = "";
                if (bahanItem) bahanItem.style.display = "none";
            }

            if (hasKomponen) {
                kompBody.innerHTML = `
                    <ul class="mb-0 small">
                        ${data.komponen
                            .map(
                                (k) => `
                            <li>${k.nama}${k.kode ? ' <span class="text-muted">[' + k.kode + "]</span>" : ""}</li>
                        `,
                            )
                            .join("")}
                    </ul>
                `;
                if (kompItem) kompItem.style.display = "block";
            } else {
                kompBody.innerHTML = "";
                if (kompItem) kompItem.style.display = "none";
            }
        } catch (err) {
            console.error(err);
            bahanBody.innerHTML =
                '<p class="text-danger small mb-0">Gagal memuat data bahan baku.</p>';
            kompBody.innerHTML =
                '<p class="text-danger small mb-0">Gagal memuat data komponen.</p>';
            sectionRelasi.style.display = "none";
        }
    }

    let modalUploadedFiles = [];
    let currentMetricUnit = "cm";

    window.addEventListener("produkDipilih", (e) => {
        const data = e.detail;
        currentSelectedProduk = data;

        const produkSelect = document.getElementById("modalProdukSelect");
        const produkId = document.getElementById("modalProdukId");
        if (produkSelect) {
            produkSelect.value =
                data.nama_produk +
                (data.kode_produk ? " [" + data.kode_produk + "]" : "");
        }
        if (produkId) {
            produkId.value = data.id;
        }

        if (produkId && produkId.value) {
            loadAndRenderRelasiProduk(produkId.value);
        }

        const sectionUkuran = document.getElementById("sectionUkuran");
        const summaryUkuranContainer = document.getElementById(
            "summaryUkuranContainer",
        );
        if (sectionUkuran) {
            const shouldShow =
                data.is_metric === true || data.is_metric === "true";
            sectionUkuran.style.display = shouldShow ? "block" : "none";

            if (summaryUkuranContainer) {
                summaryUkuranContainer.style.display = shouldShow
                    ? "block"
                    : "none";
            }

            if (!shouldShow) {
                const panjangInput =
                    document.getElementById("modalPanjangInput");
                const lebarInput = document.getElementById("modalLebarInput");
                if (panjangInput) panjangInput.value = "";
                if (lebarInput) lebarInput.value = "";
            }
        }
        const satuanDisplay = document.getElementById("modalSatuanDisplay");
        if (satuanDisplay) {
            satuanDisplay.textContent = data.satuan_nama || "pcs";
        }

        const satuanPanjang = document.getElementById("modalSatuanPanjang");
        const satuanLebar = document.getElementById("modalSatuanLebar");
        const metricUnit = data.metric_unit || "-";
        currentMetricUnit = data.metric_unit || "cm";

        if (satuanPanjang) {
            satuanPanjang.textContent = metricUnit;
        }
        if (satuanLebar) {
            satuanLebar.textContent = metricUnit;
        }

        const satuanLuas = document.getElementById("modalSatuanLuas");
        if (satuanLuas) {
            satuanLuas.textContent = metricUnit + "²";
        }

        const panjangInput = document.getElementById("modalPanjangInput");
        const lebarInput = document.getElementById("modalLebarInput");

        const panjangStatus = document.getElementById("panjangStatus");
        const lebarStatus = document.getElementById("lebarStatus");
        if (panjangStatus) {
            panjangStatus.style.display = data.panjang_locked
                ? "block"
                : "none";
        }
        if (lebarStatus) {
            lebarStatus.style.display = data.lebar_locked ? "block" : "none";
        }

        if (panjangInput) {
            panjangInput.value = data.panjang || "";
            panjangInput.disabled = Boolean(data.panjang_locked);
            panjangInput.style.backgroundColor = data.panjang_locked
                ? "#f8f9fa"
                : "";
            panjangInput.style.cursor = data.panjang_locked
                ? "not-allowed"
                : "";
        }

        if (lebarInput) {
            lebarInput.value = data.lebar || "";
            lebarInput.disabled = Boolean(data.lebar_locked);
            lebarInput.style.backgroundColor = data.lebar_locked
                ? "#f8f9fa"
                : "";
            lebarInput.style.cursor = data.lebar_locked ? "not-allowed" : "";
        }

        const luasColumn = document.getElementById("luasColumn");
        if (luasColumn) {
            const isAnyLocked = Boolean(
                data.panjang_locked || data.lebar_locked,
            );
            luasColumn.style.display = isAnyLocked ? "none" : "";
        }

        if (typeof updateLuas === "function") {
            updateLuas();
        }

        if (typeof updateModalSummary === "function") {
            updateModalSummary();
        }

        if (typeof showImageControls === "function") {
            const controls = document.getElementById("imageFileControls");
            if (controls && controls.style.display !== "none") {
                showImageControls();
            }
        }
        updateFileImageControlUnits();
        if (currentFileImageInfo) {
            updateImageControls(currentFileImageInfo);
        }
        updateSyncButtonState();
    });

    function updateLuas() {
        const panjangInput = document.getElementById("modalPanjangInput");
        const lebarInput = document.getElementById("modalLebarInput");
        const luasInput = document.getElementById("modalLuasInput");

        if (panjangInput && lebarInput && luasInput) {
            const panjang = parseFloat(panjangInput.value) || 0;
            const lebar = parseFloat(lebarInput.value) || 0;
            const luas = panjang * lebar;

            luasInput.value = luas.toFixed(2);
        }
    }

    // --- Init ---
    document.addEventListener("DOMContentLoaded", () => {
        if (window.SPK_EDIT_INITIAL) {
            const initial = window.SPK_EDIT_INITIAL;

            if (Array.isArray(initial.items) && initial.items.length) {
                itemsData = initial.items.map((item) => {
                    return {
                        ...item,
                        tugasProduksi:
                            item.tugasProduksi || item.tugas_produksi || [],
                        filePendukung:
                            item.filePendukung ||
                            item.file_pendukung ||
                            item.files ||
                            [],
                        files:
                            item.files ||
                            item.file_pendukung ||
                            item.filePendukung ||
                            [],
                        tipe_finishing: item.tipe_finishing || [],
                    };
                });
            }

            if (initial.customer) {
                const c = initial.customer;
                if (el.namaCustomerInput()) {
                    el.namaCustomerInput().value =
                        (c.nama || "") + (c.kode ? ` [${c.kode}]` : "");
                }
                if (el.customerIdInput()) {
                    el.customerIdInput().value = c.id || "";
                }
                const kategoriEl = document.getElementById(
                    "customerKategoriHarga",
                );
                if (kategoriEl) {
                    kategoriEl.value = c.kategori_harga || "Umum";
                }
            }
        }

        wireGlobalDelegates();
        initFileUpload();
        initExternalModals();
        renderItemCards();
        // updateTugasTabTable();
        renderFilePendukungTable();
        initModalTambahItem();
    });

    // --- Event Delegation ---
    function wireGlobalDelegates() {
        document.addEventListener("click", handleGlobalClick);
        document.addEventListener("submit", handleGlobalSubmit);
    }

    function ensureJenisPlongPerJarak() {
        const jenisEl = document.getElementById("imageToolJenisPlong");
        if (jenisEl) {
            jenisEl.value = "plong_per_jarak";
            if (typeof togglePlongCountByJenis === "function") {
                togglePlongCountByJenis();
            }
        }
    }

    function handleGlobalClick(e) {
        // Tambah item
        if (e.target.closest("#btnTambahItem")) {
            e.preventDefault();
            // tambahItemPekerjaan();
            openModalTambahItem();
            return;
        }

        // Simpan item dari modal
        if (e.target.closest("#modalBtnSimpanItem")) {
            e.preventDefault();

            const item = getItemFormData();
            if (!validateItem(item)) return;

            tambahItemPekerjaan();

            // Tutup modal jika masih terbuka
            const modalElement = document.getElementById(
                "modalTambahItemPesanan",
            );
            if (modalElement) {
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                modalInstance?.hide();
            }

            SafeHelper.notify(
                "success",
                "Berhasil",
                "Item berhasil ditambahkan",
            );
            return;
        }

        // Tugas Produksi - Tab
        // if (
        //     e.target.closest("#btnTambahTugasTab") ||
        //     e.target.closest("#btnTambahTugasPertama")
        // ) {
        //     editTugasTabIndex = null;
        //     if (el.formTugas()) el.formTugas().reset();
        //     if (el.modalTugasLabel())
        //         el.modalTugasLabel().textContent = "Tambah Tugas Produksi";
        //     const btnSimpan = document.getElementById("btnSimpanTugas");
        //     if (btnSimpan) btnSimpan.textContent = "Tambah Tugas";
        //     new bootstrap.Modal(el.modalTugas()).show();
        //     return;
        // }

        // // Edit tugas di tab
        // if (e.target.closest(".btn-edit-tugas-tab")) {
        //     const idx = parseInt(
        //         e.target
        //             .closest(".btn-edit-tugas-tab")
        //             .getAttribute("data-idx"),
        //     );
        //     editTugasTabIndex = idx;
        //     const tugas = tugasProduksiTabData[idx];
        //     if (!tugas) return;
        //     el.inputNamaTugas().value = tugas.nama;
        //     el.inputDitugaskan().value = tugas.ditugaskan;
        //     el.inputMesin().value = tugas.mesin || "";
        //     el.inputWaktu().value = tugas.waktu;
        //     el.inputHarga().value = tugas.harga;
        //     el.inputDeskripsi().value = tugas.deskripsi || "";
        //     el.modalTugasLabel().textContent = "Edit Tugas Produksi";
        //     const btnSimpan = document.getElementById("btnSimpanTugas");
        //     if (btnSimpan) btnSimpan.textContent = "Simpan Perubahan";
        //     new bootstrap.Modal(el.modalTugas()).show();
        //     return;
        // }

        // // Hapus tugas di tab
        // if (e.target.closest(".btn-delete-tugas-tab")) {
        //     const idx = parseInt(
        //         e.target
        //             .closest(".btn-delete-tugas-tab")
        //             .getAttribute("data-idx"),
        //     );
        //     tugasProduksiTabData.splice(idx, 1);
        //     updateTugasTabTable();
        //     return;
        // }

        // File pendukung - browse
        if (
            e.target.closest("#btnBrowseFile") ||
            e.target.closest("#dropZone")
        ) {
            if (
                !e.target.closest(".btn-hapus-file") &&
                el.inputFilePendukung()
            ) {
                el.inputFilePendukung().click();
            }
            return;
        }

        if (e.target.closest("#btnSaveImageToolsTemplate")) {
            e.preventDefault();
        
            const nameInput = document.getElementById("imageToolTemplateName");
            const templateName = (nameInput?.value || "").trim();
        
            if (!templateName) {
                SafeHelper.notify("warning", "Template", "Nama template wajib diisi.");
                return;
            }
        
            let payload;
            try {
                payload = buildImageToolsPayload();
                delete payload.AlamatFile;
            } catch (err) {
                console.error(err);
                SafeHelper.notify("error", "Template", err.message || "Gagal membangun payload template.");
                return;
            }
        
            (async () => {
                try {
                    const token =
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "";
        
                    const response = await fetch("/backend/finishing-templates", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": token,
                        },
                        body: JSON.stringify({
                            nama: templateName,
                            payload: payload,
                        }),
                    });
        
                    if (!response.ok) {
                        throw new Error("HTTP status " + response.status);
                    }
        
                    const data = await response.json();
                    if (data.success) {
                        SafeHelper.notify(
                            "success",
                            "Template",
                            "Template finishing berhasil disimpan.",
                        );
                    } else {
                        SafeHelper.notify(
                            "warning",
                            "Template",
                            data.message || "Gagal menyimpan template.",
                        );
                    }
                } catch (error) {
                    console.error(error);
                    SafeHelper.notify(
                        "error",
                        "Template",
                        "Terjadi kesalahan saat menyimpan template.",
                    );
                }
            })();
        
            return;
        }

        if (e.target.closest("#imageToolPlongLipat4")) {
            const cb = document.getElementById("imageToolPlongLipat4");
            if (!cb) return;
        
            ensureJenisPlongPerJarak();
        
            if (cb.checked) {
                const val = 2;
                const ids = [
                    "imageToolPlongAtas",
                    "imageToolPlongBawah",
                    "imageToolPlongKiri",
                    "imageToolPlongKanan",
                ];
                ids.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.value = val;
                });
            }
            return;
        }
        
        if (e.target.closest("#imageToolPlongAtasPerM")) {
            const cb = document.getElementById("imageToolPlongAtasPerM");
            if (!cb) return;
        
            ensureJenisPlongPerJarak();
        
            if (cb.checked) {
                if (!currentFileImageInfo || !currentFileImageInfo.width_cm) {
                    SafeHelper.notify(
                        "warning",
                        "Plong",
                        "Info lebar file belum tersedia. Pilih file gambar terlebih dahulu.",
                    );
                    cb.checked = false;
                    return;
                }
                const widthCm = parseFloat(currentFileImageInfo.width_cm) || 0;
                const widthM = widthCm / 100; // cm -> meter
                const count = Math.ceil(widthM) + 1; // dibulatkan ke atas + 1
                const elAtas = document.getElementById("imageToolPlongAtas");
                if (elAtas) elAtas.value = count;
            }
            return;
        }
        
        if (e.target.closest("#imageToolPlongBawahPojok")) {
            const cb = document.getElementById("imageToolPlongBawahPojok");
            if (!cb) return;
        
            ensureJenisPlongPerJarak();
        
            if (cb.checked) {
                const elBawah = document.getElementById("imageToolPlongBawah");
                if (elBawah) elBawah.value = 2;
            }
            return;
        }

        if (e.target.closest("#btnLoadImageToolsTemplate")) {
            e.preventDefault();
            const select = document.getElementById("imageToolTemplateSelect");
            const id = select?.value;
            if (!id) {
                SafeHelper.notify("warning", "Load Template", "Pilih template terlebih dahulu.");
                return;
            }
            (async () => {
                try {
                    const res = await fetch(`/backend/finishing-templates/${id}`);
                    const data = await res.json();
                    if (!data.success || !data.payload) {
                        SafeHelper.notify("warning", "Load Template", data.message || "Template tidak ditemukan.");
                        return;
                    }
                    applyImageToolsPayload(data.payload);
                    SafeHelper.notify("success", "Load Template", `Template "${data.nama || ""}" berhasil dimuat.`);
                } catch (err) {
                    console.error(err);
                    SafeHelper.notify("error", "Load Template", "Gagal memuat template.");
                }
            })();
            return;
        }

        // Item actions di daftar
        if (e.target.closest("#itemCardsContainer")) {
            // Edit item
            if (e.target.closest(".btn-edit-item")) {
                const idx = parseInt(
                    e.target.closest(".btn-edit-item").getAttribute("data-idx"),
                );
                prefillItemForm(idx);
                masukModeEdit(idx);
                return;
            }
            // Delete item
            if (e.target.closest(".btn-delete-item")) {
                const idx = parseInt(
                    e.target
                        .closest(".btn-delete-item")
                        .getAttribute("data-idx"),
                );
                SafeHelper.confirm(
                    "Konfirmasi",
                    "Apakah Anda yakin ingin menghapus item ini?",
                    "Ya, Hapus",
                    "Batal",
                ).then((confirmed) => {
                    if (!confirmed) return;
                    itemsData.splice(idx, 1);
                    renderItemCards();
                });
                return;
            }
            // Tambah tugas per item
            // if (e.target.closest(".btn-tambah-tugas-item")) {
            //     const idx = parseInt(
            //         e.target
            //             .closest(".btn-tambah-tugas-item")
            //             .getAttribute("data-idx"),
            //     );
            //     itemTugasEditIndex = idx;
            //     itemTugasEditTaskIndex = null;
            //     if (el.formTugas()) el.formTugas().reset();
            //     const item = itemsData[idx];
            //     if (el.modalTugasLabel())
            //         el.modalTugasLabel().textContent =
            //             "Tambah Tugas Produksi untuk: " +
            //             (item?.nama_produk || "Item");
            //     const btnSimpan = document.getElementById("btnSimpanTugas");
            //     if (btnSimpan) btnSimpan.textContent = "Tambah Tugas";
            //     new bootstrap.Modal(el.modalTugas()).show();
            //     return;
            // }
            // // Edit tugas per item
            // if (
            //     e.target.closest(".btn-edit-tugas-item") &&
            //     e.target
            //         .closest(".btn-edit-tugas-item")
            //         .hasAttribute("data-tugas")
            // ) {
            //     const idx = parseInt(
            //         e.target
            //             .closest(".btn-edit-tugas-item")
            //             .getAttribute("data-idx"),
            //     );
            //     const tIdx = parseInt(
            //         e.target
            //             .closest(".btn-edit-tugas-item")
            //             .getAttribute("data-tugas"),
            //     );
            //     itemTugasEditIndex = idx;
            //     itemTugasEditTaskIndex = tIdx;
            //     const tugas = itemsData[idx]?.tugasProduksi?.[tIdx];
            //     if (!tugas) return;
            //     el.inputNamaTugas().value = tugas.nama;
            //     el.inputDitugaskan().value = tugas.ditugaskan;
            //     el.inputMesin().value = tugas.mesin || "";
            //     el.inputWaktu().value = tugas.waktu;
            //     el.inputHarga().value = tugas.harga;
            //     el.inputDeskripsi().value = tugas.deskripsi || "";
            //     el.modalTugasLabel().textContent =
            //         "Edit Tugas Produksi untuk: " +
            //         (itemsData[idx]?.nama_produk || "Item");
            //     const btnSimpan = document.getElementById("btnSimpanTugas");
            //     if (btnSimpan) btnSimpan.textContent = "Simpan Perubahan";
            //     new bootstrap.Modal(el.modalTugas()).show();
            //     return;
            // }
            // // Hapus tugas per item
            // if (e.target.closest(".btn-delete-tugas-item")) {
            //     const idx = parseInt(
            //         e.target
            //             .closest(".btn-delete-tugas-item")
            //             .getAttribute("data-idx"),
            //     );
            //     const tIdx = parseInt(
            //         e.target
            //             .closest(".btn-delete-tugas-item")
            //             .getAttribute("data-tugas"),
            //     );
            //     SafeHelper.confirm(
            //         "Konfirmasi",
            //         "Apakah Anda yakin ingin menghapus tugas ini?",
            //         "Ya, Hapus",
            //         "Batal",
            //     ).then((confirmed) => {
            //         if (!confirmed) return;
            //         itemsData[idx]?.tugasProduksi?.splice(tIdx, 1);
            //         renderItemCards();
            //     });
            //     return;
            // }
        }

        // Hapus file pendukung
        if (e.target.closest(".btn-hapus-file")) {
            const idx = parseInt(
                e.target.closest(".btn-hapus-file").getAttribute("data-idx"),
            );
            SafeHelper.confirm(
                "Konfirmasi",
                "Apakah Anda yakin ingin menghapus file ini?",
                "Ya, Hapus",
                "Batal",
            ).then((confirmed) => {
                if (!confirmed) return;
                filePendukungData.splice(idx, 1);
                renderFilePendukungTable();
            });
            return;
        }
    }

    function handleGlobalSubmit(e) {
        // Form modal tugas produksi (selalu prevent default)
        // if (e.target.id === "formTugasProduksi") {
        //     e.preventDefault();
        //     const tugas = {
        //         nama: el.inputNamaTugas().value,
        //         ditugaskan: el.inputDitugaskan().value,
        //         ditugaskan_id:
        //             document.getElementById("inputDitugaskanId")?.value || "",
        //         mesin: el.inputMesin().value,
        //         waktu: el.inputWaktu().value,
        //         harga: el.inputHarga().value,
        //         deskripsi: el.inputDeskripsi().value,
        //     };
        //     // Validasi minimal
        //     if (!tugas.nama || !tugas.ditugaskan || !tugas.waktu) {
        //         SafeHelper.notify(
        //             "error",
        //             "Error",
        //             "Nama/ditugaskan/waktu wajib diisi",
        //         );
        //         return;
        //     }

        //     // Jika form item aktif, simpan ke item tertentu
        //     const itemTabActive = document
        //         .getElementById("itemPekerjaan")
        //         ?.classList.contains("active");
        //     if (itemTabActive && itemTugasEditIndex !== null) {
        //         if (!Array.isArray(itemsData[itemTugasEditIndex].tugasProduksi))
        //             itemsData[itemTugasEditIndex].tugasProduksi = [];
        //         if (itemTugasEditTaskIndex !== null) {
        //             itemsData[itemTugasEditIndex].tugasProduksi[
        //                 itemTugasEditTaskIndex
        //             ] = tugas;
        //         } else {
        //             itemsData[itemTugasEditIndex].tugasProduksi.push(tugas);
        //         }
        //         renderItemCards();
        //         bootstrap.Modal.getOrCreateInstance(el.modalTugas()).hide();
        //         itemTugasEditIndex = null;
        //         itemTugasEditTaskIndex = null;
        //         return;
        //     }
        //     // Jika di tab tugas global
        //     if (el.tabTugasPane()?.classList.contains("active")) {
        //         if (editTugasTabIndex !== null) {
        //             tugasProduksiTabData[editTugasTabIndex] = tugas;
        //         } else {
        //             tugasProduksiTabData.push(tugas);
        //         }
        //         // updateTugasTabTable();
        //         bootstrap.Modal.getOrCreateInstance(el.modalTugas()).hide();
        //         editTugasTabIndex = null;
        //     }
        //     return;
        // }

        // Form utama SPK: siapkan hidden inputs lalu biarkan submit normal
        if (e.target.id === "formTambahSPK") {
            // Validasi minimal: harus ada 1 item
            if (itemsData.length === 0) {
                e.preventDefault();
                SafeHelper.notify(
                    "error",
                    "Error",
                    "Minimal harus ada 1 item pekerjaan",
                );
                return;
            }
            // Pastikan pelanggan dipilih
            if (!el.customerIdInput()?.value) {
                e.preventDefault();
                SafeHelper.notify("error", "Error", "Pelanggan belum dipilih");
                return;
            }

            // if (el.itemsHidden())
            //     el.itemsHidden().value = JSON.stringify(itemsData);
            // if (el.tugasHidden())
            //     el.tugasHidden().value = JSON.stringify(tugasProduksiTabData);
            if (el.filePendukungHidden()) {
                el.filePendukungHidden().value = JSON.stringify(
                    filePendukungData.map((f) => ({
                        name: f.name,
                        type: f.type,
                        size: f.size,
                    })),
                );
            }

            const btnSimpan = document.getElementById("btnSimpanSPK");
            const spinner = document.getElementById("spinnerSimpanSPK");
            const labelSimpan = document.querySelector(
                "#btnSimpanSPK .label-simpan",
            );
            if (btnSimpan) btnSimpan.disabled = true;
            if (spinner) spinner.classList.remove("d-none");
            if (labelSimpan) labelSimpan.textContent = "Menyimpan...";
        }
    }

    // --- Item logic ---
    function tambahItemPekerjaan() {
        const item = getItemFormData();
        if (!validateItem(item)) return;
        if (editItemIndex !== null) {
            itemsData[editItemIndex] = item;
            keluarModeEdit();
            editItemIndex = null;
        } else {
            itemsData.push(item);
        }
        // Reset form item + tab item terkait
        resetItemFormUI();
        tugasProduksiTabData = [];
        filePendukungData = [];
        renderItemCards();
        renderFilePendukungTable();
        // updateTugasTabTable();
    }

    function isModalItemVisible() {
        const modal = document.getElementById("modalTambahItemPesanan");
        return modal && modal.classList.contains("show");
    }

    function getItemFormData() {
        const modalProdukSelect = document.getElementById("modalProdukSelect");
        const modalProdukId = document.getElementById("modalProdukId");
        const modalJumlah = document.getElementById("modalJumlahInput");
        const modalDeadline = document.getElementById("modalDeadlineInput");
        const modalUrgentValue = document.getElementById("modalUrgentValue");
        const modalSatuanDisplay =
            document.getElementById("modalSatuanDisplay");
        const satuanText = (modalSatuanDisplay?.textContent || "pcs")
            .trim()
            .toLowerCase();
        const jumlah = parseFloat(modalJumlah?.value) || 0;
        const panjang =
            parseFloat(document.getElementById("modalPanjangInput")?.value) ||
            0;
        const lebar =
            parseFloat(document.getElementById("modalLebarInput")?.value) || 0;

        let biayaProduk = 0;
        if (currentSelectedProduk && jumlah > 0) {
            const hargaJual = getHargaJualFinishing(
                {
                    harga_bertingkat_json:
                        currentSelectedProduk.harga_bertingkat_json || [],
                    harga_reseller_json:
                        currentSelectedProduk.harga_reseller_json || [],
                },
                jumlah,
            );
            if (hargaJual > 0) {
                if (currentSelectedProduk.is_metric) {
                    const panjangLocked =
                        document.getElementById("panjangStatus")?.style
                            .display === "block";
                    const lebarLocked =
                        document.getElementById("lebarStatus")?.style
                            .display === "block";
                    let factorDimensi = 1;
                    if (!panjangLocked && !lebarLocked) {
                        factorDimensi = panjang * lebar;
                    } else if (panjangLocked && !lebarLocked) {
                        factorDimensi = lebar;
                    } else if (!panjangLocked && lebarLocked) {
                        factorDimensi = panjang;
                    }
                    biayaProduk = jumlah * factorDimensi * hargaJual;
                } else {
                    biayaProduk = jumlah * hargaJual;
                }
            }
        }

        return {
            produk_id: modalProdukId?.value || "",
            nama_produk: modalProdukSelect?.value || "",
            jumlah: modalJumlah?.value || 0,
            satuan: satuanText || "pcs",
            deadline: modalDeadline?.value || "",
            is_urgent: modalUrgentValue?.value === "true",
            keterangan: (
                document.getElementById("modalKeteranganInput")?.value || ""
            ).trim(),
            lebar: document.getElementById("modalLebarInput")?.value || 0,
            panjang: document.getElementById("modalPanjangInput")?.value || 0,
            biaya_produk: biayaProduk,
            files: [...modalUploadedFiles], // Simpan daftar path file
            // Simpan data produk mentah untuk edit modal agar kalkulasi harga tetap jalan
            raw_produk: currentSelectedProduk
                ? deepClone(currentSelectedProduk)
                : null,
            // tugasProduksi: deepClone(tugasProduksiTabData),
            filePendukung: deepClone(modalUploadedFiles),
            tipe_finishing: deepClone(modalFinishingData),
        };
    }

    function validateItem(item) {
        if (!item.nama_produk) {
            SafeHelper.notify("error", "Error", "Nama produk harus diisi");
            return false;
        }
        if (!item.jumlah || Number(item.jumlah) <= 0) {
            SafeHelper.notify("error", "Error", "Jumlah harus lebih dari 0");
            return false;
        }
        if (!item.satuan) {
            SafeHelper.notify("error", "Error", "Satuan harus diisi");
            return false;
        }
        return true;
    }

    function prefillItemForm(idx) {
        const item = itemsData[idx];
        if (!item) return;

        openModalTambahItem();
        editItemIndex = idx;

        // Reset and load files
        modalUploadedFiles = item.files ? [...item.files] : [];

        // Restore currentSelectedProduk
        if (item.raw_produk) {
            currentSelectedProduk = deepClone(item.raw_produk);
            currentMetricUnit = item.raw_produk.metric_unit || "cm";
        }

        // Populate modal fields
        const modalProduk = document.getElementById("modalProdukSelect");
        const modalProdukId = document.getElementById("modalProdukId");
        const modalJumlah = document.getElementById("modalJumlahInput");
        const modalDeadline = document.getElementById("modalDeadlineInput");
        const modalUrgentToggle = document.getElementById("modalUrgentToggle");
        const modalUrgentValue = document.getElementById("modalUrgentValue");
        const modalUrgentStatus = document.querySelector(".urgent-status");
        const modalSatuanDisplay =
            document.getElementById("modalSatuanDisplay");
        const modalKeterangan = document.getElementById("modalKeteranganInput");
        const modalPanjang = document.getElementById("modalPanjangInput");
        const modalLebar = document.getElementById("modalLebarInput");

        if (modalProduk) modalProduk.value = item.nama_produk;
        if (modalProdukId) modalProdukId.value = item.produk_id || "";
        if (modalJumlah) modalJumlah.value = item.jumlah;
        if (modalDeadline) modalDeadline.value = item.deadline || "";

        if (modalUrgentToggle) modalUrgentToggle.checked = item.is_urgent;
        if (modalUrgentValue)
            modalUrgentValue.value = item.is_urgent ? "true" : "false";
        if (modalUrgentStatus)
            modalUrgentStatus.textContent = item.is_urgent ? "Ya" : "Tidak";

        if (modalSatuanDisplay) modalSatuanDisplay.textContent = item.satuan;
        if (modalKeterangan) modalKeterangan.value = item.keterangan || "";
        if (modalPanjang) modalPanjang.value = item.panjang || 0;
        if (modalLebar) modalLebar.value = item.lebar || 0;

        // Metric visibility & status
        const sectionUkuran = document.getElementById("sectionUkuran");
        const summaryUkuranContainer = document.getElementById(
            "summaryUkuranContainer",
        );
        if (sectionUkuran && item.raw_produk) {
            const shouldShow =
                item.raw_produk.is_metric === true ||
                item.raw_produk.is_metric === "true";
            sectionUkuran.style.display = shouldShow ? "block" : "none";
            if (summaryUkuranContainer)
                summaryUkuranContainer.style.display = shouldShow
                    ? "block"
                    : "none";

            // Set units
            const mu = item.raw_produk.metric_unit || "cm";
            const sPanjang = document.getElementById("modalSatuanPanjang");
            const sLebar = document.getElementById("modalSatuanLebar");
            const sLuas = document.getElementById("modalSatuanLuas");
            if (sPanjang) sPanjang.textContent = mu;
            if (sLebar) sLebar.textContent = mu;
            if (sLuas) sLuas.textContent = mu + "²";

            // Locked status
            const pStatus = document.getElementById("panjangStatus");
            const lStatus = document.getElementById("lebarStatus");
            if (pStatus)
                pStatus.style.display = item.raw_produk.panjang_locked
                    ? "block"
                    : "none";
            if (lStatus)
                lStatus.style.display = item.raw_produk.lebar_locked
                    ? "block"
                    : "none";

            if (modalPanjang) {
                modalPanjang.disabled = Boolean(item.raw_produk.panjang_locked);
                modalPanjang.style.backgroundColor = item.raw_produk
                    .panjang_locked
                    ? "#f8f9fa"
                    : "";
                modalPanjang.style.cursor = item.raw_produk.panjang_locked
                    ? "not-allowed"
                    : "";
            }
            if (modalLebar) {
                modalLebar.disabled = Boolean(item.raw_produk.lebar_locked);
                modalLebar.style.backgroundColor = item.raw_produk.lebar_locked
                    ? "#f8f9fa"
                    : "";
                modalLebar.style.cursor = item.raw_produk.lebar_locked
                    ? "not-allowed"
                    : "";
            }
        }

        // Tab data
        tugasProduksiTabData = Array.isArray(item.tugasProduksi)
            ? deepClone(item.tugasProduksi)
            : [];
        modalUploadedFiles = Array.isArray(item.filePendukung)
            ? deepClone(item.filePendukung)
            : [];
        modalFinishingData = Array.isArray(item.tipe_finishing)
            ? deepClone(item.tipe_finishing)
            : [];

        renderModalFinishingAccordion();
        renderModalUploadedFiles();
        // updateTugasTabTable();

        // Relasi Produk (Bahan Baku & Komponen)
        if (item.produk_id) {
            loadAndRenderRelasiProduk(item.produk_id);
        }

        updateLuas();
        updateModalSummary();
        renderOrderanPreviewTab();

        editItemIndex = idx;
    }

    function masukModeEdit(idx) {
        highlightItemRow(idx);
    }

    function keluarModeEdit() {
        removeHighlightItemRow();
    }

    function highlightItemRow(idx) {
        removeHighlightItemRow();
        const container = el.itemCardsContainer();
        if (!container) return;
        const rows = container.querySelectorAll(".item-card");
        if (rows[idx]) rows[idx].classList.add("border-primary", "bg-light");
    }

    function removeHighlightItemRow() {
        const container = el.itemCardsContainer();
        if (!container) return;
        container
            .querySelectorAll(".item-card")
            .forEach((row) =>
                row.classList.remove("border-primary", "bg-light"),
            );
    }

    function resetItemFormUI() {
        // Form utama tetap ada untuk hidden inputs, tapi item entry sekarang di modal
        // resetModalTambahItem() sudah menangani reset modal
    }

    function renderItemCards() {
        const container = el.itemCardsContainer();
        if (!container) return;
        container.innerHTML = "";
        if (itemsData.length === 0) {
            container.innerHTML = `<div class="text-center text-muted py-4" id="noItemsMessage">
                <i class="fa fa-list-alt fa-2x mb-2"></i>
                <p class="mb-0">Belum ada item yang ditambahkan</p>
            </div>`;
            return;
        }
        itemsData.forEach((item, idx) => {
            const collapseId = `itemTugasCollapse${idx}`;
            const tugasCount =
                item.tugasProduksi && item.tugasProduksi.length
                    ? item.tugasProduksi.length
                    : 0;
            container.innerHTML += `
              <div class="row g-0 border-bottom align-items-center item-card">
                <div class="col-3 p-3 fw-semibold">
                  ${item.nama_produk}
                </div>
                <div class="col-2 p-3">${item.jumlah}</div>
                <div class="col-2 p-3">${item.satuan}</div>
                <div class="col-2 p-3">${item.nama_bahan || "-"}</div>
                <div class="col-2 p-3">
                  ${item.keterangan || "-"}
                  <div class="small text-muted mt-1">
                    ${item.previewFileName ? `<i class='fa fa-file-image-o me-1'></i>${item.previewFileName}` : ""}
                    <-- ${item.previewFinishing && item.previewFinishing.length > 0 ? `<span class='ms-2'><i class='fa fa-cogs me-1'></i>${item.previewFinishing.join(", ")}</span>` : ""} -->
                  </div>
                </div>
                <div class="col-1 p-3 text-center">
                  <div class="d-flex gap-1 justify-content-center">
                    <button type="button" class="btn btn-sm btn-light btn-edit-item" data-idx="${idx}"><i class="fa fa-edit"></i></button>
                    <button type="button" class="btn btn-sm btn-light text-danger btn-delete-item" data-idx="${idx}"><i class="fa fa-trash"></i></button>
                  </div>
                </div>
              </div>
            `;
        });
    }

    // --- Tugas Tab ---
    // function updateTugasTabTable() {
    //     let emptyState = el.emptyTugasState();
    //     let tableState = document.getElementById("tugasTabTableState");
    //     if (!tableState) {
    //         tableState = document.createElement("div");
    //         tableState.id = "tugasTabTableState";
    //         tableState.innerHTML = `
    //             <div class="d-flex justify-content-between align-items-center mb-3">
    //               <div class="fw-semibold">Daftar Tugas Produksi</div>
    //               <button type="button" class="btn btn-primary btn-sm" id="btnTambahTugasTab"><i class="fa fa-plus me-1"></i>Tambah Tugas</button>
    //             </div>
    //             <div class="table-responsive mb-3">
    //               <table class="table table-bordered align-middle mb-0">
    //                 <thead class="table-light">
    //                   <tr>
    //                     <th>Nama Tugas</th>
    //                     <th>Ditugaskan Ke</th>
    //                     <th>Mesin</th>
    //                     <th>Est. Jam</th>
    //                     <th>Harga</th>
    //                     <th>Aksi</th>
    //                   </tr>
    //                 </thead>
    //                 <tbody id="tugasTabBody"></tbody>
    //               </table>
    //             </div>
    //         `;
    //         if (emptyState) emptyState.parentNode.appendChild(tableState);
    //     }
    //     if (tugasProduksiTabData.length === 0) {
    //         if (!el.emptyTugasState()) {
    //             const tabPane = el.tabTugasPane();
    //             const emptyDiv = document.createElement("div");
    //             emptyDiv.id = "emptyTugasState";
    //             emptyDiv.className =
    //                 "d-flex flex-column align-items-center justify-content-center py-5";
    //             emptyDiv.style =
    //                 "min-height:320px; border:1.5px dashed #e3e6ea; border-radius:12px; background:#f8fafc;";
    //             emptyDiv.innerHTML = `
    //               <div class="mb-3">
    //                 <svg width="64" height="64" fill="none" xmlns="http://www.w3.org/2000/svg">
    //                   <circle cx="32" cy="32" r="30" stroke="#cfd8dc" stroke-width="4" fill="#f4f6f8"/>
    //                   <path d="M32 18v14l8 4" stroke="#b0bec5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
    //                 </svg>
    //               </div>
    //               <h5 class="fw-semibold text-secondary mb-1">Belum ada tugas</h5>
    //               <div class="text-muted mb-3">Tambahkan tugas produksi untuk item ini</div>
    //               <button type="button" class="btn btn-outline-primary btn-lg px-4 py-2" id="btnTambahTugasPertama"><i class="fa fa-plus me-2"></i>Tambah Tugas Pertama</button>
    //             `;
    //             tabPane.appendChild(emptyDiv);
    //         }
    //         if (tableState) tableState.style.display = "none";
    //     } else {
    //         if (emptyState) emptyState.remove();
    //         if (tableState) tableState.style.display = "";
    //         const tbody = document.getElementById("tugasTabBody");
    //         if (tbody) {
    //             tbody.innerHTML = "";
    //             tugasProduksiTabData.forEach((tugas, idx) => {
    //                 tbody.innerHTML += `
    //                   <tr>
    //                     <td>${tugas.nama}</td>
    //                     <td>${tugas.ditugaskan}</td>
    //                     <td>${tugas.mesin || "-"}</td>
    //                     <td>${tugas.waktu} jam</td>
    //                     <td>Rp ${parseInt(tugas.harga || 0).toLocaleString()}</td>
    //                     <td>
    //                       <button type="button" class="btn btn-sm btn-light btn-edit-tugas-tab" data-idx="${idx}" title="Edit"><i class="fa fa-edit"></i></button>
    //                       <button type="button" class="btn btn-sm btn-light text-danger btn-delete-tugas-tab" data-idx="${idx}" title="Hapus"><i class="fa fa-trash"></i></button>
    //                     </td>
    //                   </tr>
    //                 `;
    //             });
    //         }
    //     }
    // }

    // --- File Pendukung ---
    function initFileUpload() {
        const dz = el.dropZone();
        const input = el.inputFilePendukung();
        if (dz) {
            dz.addEventListener("dragover", (e) => {
                e.preventDefault();
                dz.classList.add("border-primary");
            });
            dz.addEventListener("dragleave", (e) => {
                e.preventDefault();
                dz.classList.remove("border-primary");
            });
            dz.addEventListener("drop", (e) => {
                e.preventDefault();
                dz.classList.remove("border-primary");
                handleFiles(e.dataTransfer.files);
            });
        }
        if (input) {
            input.addEventListener("change", (e) => {
                if (e.target.files && e.target.files.length > 0) {
                    handleFiles(e.target.files);
                    setTimeout(() => {
                        input.value = "";
                    }, 100);
                }
            });
        }
    }

    function handleFiles(fileList) {
        for (let i = 0; i < fileList.length; i++) {
            const file = fileList[i];
            const duplicate = filePendukungData.some(
                (f) => f.name === file.name && f.size === file.size,
            );
            if (!duplicate) {
                const sourcePath = file.webkitRelativePath || file.name; // Untuk file lokal

                filePendukungData.push({
                    name: file.name,
                    type: file.type,
                    size: file.size,
                    sourcePath: sourcePath,
                    source: "local",
                });
            }
        }
        renderFilePendukungTable();
    }

    function renderFilePendukungTable() {
        const body = el.filePendukungBody();
        if (!body) return;
        body.innerHTML = "";
        if (filePendukungData.length === 0) {
            body.innerHTML =
                '<tr><td colspan="4" class="text-center text-muted">Belum ada file pendukung</td></tr>';
        } else {
            filePendukungData.forEach((file, idx) => {
                const pathInfo = file.path
                    ? `<br><small class="text-muted text-break">Path: ${file.path}</small>`
                    : "";

                body.innerHTML += `
                    <tr>
                        <td>
                            <div class="fw-bold text-truncate" style="max-width:300px;">${file.name}</div>
                            ${pathInfo}
                        </td>
                        <td>${file.type || "-"}</td>
                        <td>${formatBytes(file.size)}</td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-spk-file" data-idx="${idx}"><i class="fa fa-trash"></i></button></td>
                    </tr>
                `;
            });

            // Re-attach delete listeners
            body.querySelectorAll(".remove-spk-file").forEach((btn) => {
                btn.onclick = (e) => {
                    const idx = parseInt(
                        e.currentTarget.getAttribute("data-idx"),
                    );
                    filePendukungData.splice(idx, 1);
                    renderFilePendukungTable();
                };
            });
        }
        if (el.filePendukungHidden()) {
            el.filePendukungHidden().value = JSON.stringify(
                filePendukungData.map((f) => ({
                    name: f.name,
                    type: f.type,
                    size: f.size,
                    path: f.path,
                    source: f.source,
                })),
            );
        }
    }

    // --- External modals (pelanggan & bahan baku) ---
    function initExternalModals() {
        // Open modal: pelanggan
        const btnCariCustomer = document.getElementById("btnCariCustomer");
        const namaCustomerInput = document.getElementById("namaCustomerInput");
        if (btnCariCustomer) {
            btnCariCustomer.addEventListener("click", function () {
                const modal = new bootstrap.Modal(
                    document.getElementById("modalCariPelangganSPK"),
                );
                modal.show();
            });
        }
        if (namaCustomerInput) {
            namaCustomerInput.addEventListener("click", function () {
                const modal = new bootstrap.Modal(
                    document.getElementById("modalCariPelangganSPK"),
                );
                modal.show();
            });
        }
        // Open modal: bahan baku
        const btnCariBahan = document.getElementById("btnCariBahan");
        const namaBahanInput = document.getElementById("namaBahanInput");
        if (btnCariBahan) {
            btnCariBahan.addEventListener("click", function () {
                const modal = new bootstrap.Modal(
                    document.getElementById("modalCariBahanBakuSPK"),
                );
                modal.show();
            });
        }
        if (namaBahanInput) {
            namaBahanInput.addEventListener("click", function () {
                const modal = new bootstrap.Modal(
                    document.getElementById("modalCariBahanBakuSPK"),
                );
                modal.show();
            });
        }
        // Open modal: karyawan untuk inputDitugaskan
        const btnCariKaryawan = document.getElementById("btnCariKaryawan");
        const inputDitugaskan = document.getElementById("inputDitugaskan");
        if (btnCariKaryawan) {
            btnCariKaryawan.addEventListener("click", function () {
                const modal = new bootstrap.Modal(
                    document.getElementById("modalCariKaryawanSPK"),
                );
                modal.show();
            });
        }
        if (inputDitugaskan) {
            inputDitugaskan.addEventListener("click", function () {
                const modal = new bootstrap.Modal(
                    document.getElementById("modalCariKaryawanSPK"),
                );
                modal.show();
            });
        }
        // Customer picked from modal
        window.addEventListener("pelangganDipilih", function (e) {
            const data = e.detail;
            if (el.namaCustomerInput())
                el.namaCustomerInput().value =
                    data.nama + (data.kode ? " [" + data.kode + "]" : "");
            if (el.customerIdInput()) el.customerIdInput().value = data.id;
            if (document.getElementById("customerKategoriHarga")) {
                document.getElementById("customerKategoriHarga").value =
                    data.kategori_harga || "Umum";
            }
        });
        // Bahan baku picked from modal
        window.addEventListener("bahanBakuDipilih", function (e) {
            const data = e.detail;
            if (el.namaBahan())
                el.namaBahan().value =
                    data.nama + (data.kode ? " [" + data.kode + "]" : "");
            if (el.bahanId()) el.bahanId().value = data.id;
        });
        // Karyawan picked from modal untuk diisikan ke inputDitugaskan
        window.addEventListener("karyawanDipilih", function (e) {
            const data = e.detail;
            const display =
                data.nama + (data.kode ? " [" + data.kode + "]" : "");
            const inputNama = document.getElementById("inputDitugaskan");
            const inputId = document.getElementById("inputDitugaskanId");
            if (inputNama) inputNama.value = display;
            if (inputId) inputId.value = data.id;
        });
        window.addEventListener("produkFinishingDipilih", (e) => {
            const data = e.detail;
            tambahModalFinishing(
                data.nama_produk,
                1,
                data.id,
                "",
                data.is_metric,
                data.panjang_locked,
                data.lebar_locked,
                data.metric_unit,
                data.panjang,
                data.lebar,
                data.satuan_nama,
                data.harga_bertingkat_json || [],
                data.harga_reseller_json || [],
            );
        });
        const bentukPlongSelect = document.getElementById("imageToolBentukPlong");
        if (bentukPlongSelect) {
            bentukPlongSelect.addEventListener("change", togglePlongDiameterInputs);
        }
    }

    function openModalTambahItem() {
        currentExpandedFinishingIndex = 0;
        currentSelectedProduk = null;
        // Reset form modal
        resetModalTambahItem();
        // Buka modal
        const modal = new bootstrap.Modal(
            document.getElementById("modalTambahItemPesanan"),
        );
        modal.show();
    }

    function resetModalTambahItem() {
        currentExpandedFinishingIndex = 0;
        currentSelectedProduk = null;
        // Reset semua input
        const form = document.getElementById("modalTambahItemPesanan");
        if (!form) return;

        const urgentToggle = document.getElementById("modalUrgentToggle");
        const urgentValue = document.getElementById("modalUrgentValue");
        const urgentStatus = document.querySelector(".urgent-status");

        if (urgentToggle) {
            urgentToggle.checked = false;
        }
        if (urgentValue) {
            urgentValue.value = "false";
        }
        if (urgentStatus) {
            urgentStatus.textContent = "Tidak";
        }
        const sectionUkuran = document.getElementById("sectionUkuran");
        if (sectionUkuran) {
            sectionUkuran.style.display = "none";
        }

        const luasInput = document.getElementById("modalLuasInput");
        if (luasInput) {
            luasInput.value = "0.00";
        }
        currentMetricUnit = "cm";
        form.querySelector("#modalProdukSelect").value = "";
        form.querySelector("#modalKeteranganInput").value = "";
        form.querySelector("#modalJumlahInput").value = "1";
        form.querySelector("#modalDeadlineInput").value = "";
        form.querySelector("#modalPanjangInput").value = "0";
        form.querySelector("#modalLebarInput").value = "0";
        document.getElementById("modalProdukSelect").value = "";
        document.getElementById("modalProdukId").value = "";

        const satuanDisplay = document.getElementById("modalSatuanDisplay");
        const satuanPanjang = document.getElementById("modalSatuanPanjang");
        const satuanLebar = document.getElementById("modalSatuanLebar");

        if (satuanDisplay) {
            satuanDisplay.textContent = "";
        }
        if (satuanPanjang) {
            satuanPanjang.textContent = "";
        }
        if (satuanLebar) {
            satuanLebar.textContent = "";
        }

        if (orderanPreviewObjectUrl) {
            URL.revokeObjectURL(orderanPreviewObjectUrl);
            orderanPreviewObjectUrl = null;
        }

        // Reset panjang dan lebar, enable kembali
        const panjangInput = document.getElementById("modalPanjangInput");
        const lebarInput = document.getElementById("modalLebarInput");
        document.getElementById("panjangStatus").style.display = "none";
        document.getElementById("lebarStatus").style.display = "none";
        if (panjangInput) {
            panjangInput.value = "0";
            panjangInput.disabled = false;
            panjangInput.style.backgroundColor = "";
            panjangInput.style.cursor = "";
        }

        if (lebarInput) {
            lebarInput.value = "0";
            lebarInput.disabled = false;
            lebarInput.style.backgroundColor = "";
            lebarInput.style.cursor = "";
        }

        // Reset finishing checkboxes
        // form.querySelectorAll('.finishing-option input[type="checkbox"]').forEach(cb => {
        //     cb.checked = false;
        // });

        // Reset files
        modalUploadedFiles = [];
        // modalSelectedFinishing = [];
        renderModalUploadedFiles();
        modalFinishingData = [];
        renderModalFinishingAccordion();
        updateModalSummary();
        renderOrderanPreviewTab();
        hideImageControls();
        hidePdfControls();
        imageRotationDegrees = 0;
        currentFileImageInfo = null;
        currentFilePdfInfo = null;
    }

    function renderModalUploadedFiles() {
        const container = document.getElementById("modalUploadedFilesList");
        if (!container) return;

        if (modalUploadedFiles.length === 0) {
            container.innerHTML =
                '<p class="text-muted small mb-0">Belum ada file yang diupload</p>';
            return;
        }

        container.innerHTML = modalUploadedFiles
            .map((file, idx) => {
                const filePath = file.path || file.sourcePath || "";
                const isDefault = idx === 0;
                return `
                <div class="file-item" data-idx="${idx}">
                    <div class="file-icon">
                        <i class="fa ${getFileIcon(file.type)} text-primary"></i>
                    </div>
                    <div class="file-info flex-grow-1 overflow-hidden">
                        <div class="file-name text-truncate"  title="${file.name}">${file.name}</div>
                        ${filePath ? `<div class="file-path text-truncate text-muted small" title="${filePath}">${filePath}</div>` : ""}
                    </div>
                    <button type="button"
                            class="btn btn-sm ${isDefault ? "btn-outline-secondary" : "btn-outline-primary"} me-1 btn-set-default-file"
                            data-idx="${idx}"
                            ${isDefault ? "disabled" : ""}>
                            ${isDefault ? "Default" : "Set default"}
                    </button>
                    <button type="button" class="btn btn-sm btn-link text-danger btn-remove-file" data-idx="${idx}">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            `;
            })
            .join("");
    }

    function getImageToolsStringValue(selector) {
        const el = document.querySelector(selector);
        if (!el) return undefined;
        const value = (el.value ?? "").trim();
        return value === "" ? undefined : value;
    }

    function wireImageToolsColorTouchedFlag() {
        const selectors = [
            "#imageToolWarnaPesan",
            "#imageToolWarnaLatar",
            "#imageToolWarnaGaris",
            "#imageToolWarnaPlong",
        ];
    
        selectors.forEach((sel) => {
            const el = document.querySelector(sel);
            if (!el) return;
    
            if (el.dataset.touchedWired === "1") return;
            el.dataset.touchedWired = "1";
    
            el.addEventListener("input", () => {
                el.dataset.touched = "1";
            });
            el.addEventListener("change", () => {
                el.dataset.touched = "1";
            });
        });
    }
    function getImageToolsColorValue(selector) {
        const el = document.querySelector(selector);
        if (!el) return undefined;
        const value = (el.value ?? "").trim();
        if (value === "") return undefined;
        
        return el.dataset.touched === "1" ? value : undefined;
    }

    function storeImageToolsInitialColorValues() {
        const colorSelectors = [
            "#imageToolWarnaPesan",
            "#imageToolWarnaLatar",
            "#imageToolWarnaGaris",
            "#imageToolWarnaPlong",
        ];
        colorSelectors.forEach((sel) => {
            const el = document.querySelector(sel);
            if (el) el.dataset.initialColor = (el.value ?? "").trim();
        });
    }

    function getImageToolsNumberValue(selector) {
        const el = document.querySelector(selector);
        if (!el) return undefined;
        const raw = (el.value ?? "").trim();
        if (raw === "") return undefined;
        const num = Number(raw);
        return Number.isNaN(num) ? undefined : num;
    }

    function getImageToolsModalInstance() {
        if (!imageToolsModalInstance) {
            const el = document.getElementById("modalImageTools");
            if (el && window.bootstrap && window.bootstrap.Modal) {
                imageToolsModalInstance = window.bootstrap.Modal.getOrCreateInstance(el);
            }
        }
        return imageToolsModalInstance;
    }

    const IMAGE_TOOLS_DEFAULTS = {
        "#imageToolPesan": "POLOS",
        "#imageToolJenisPlong": "plong_per_jarak",
        "#imageToolBentukPlong": "circle",
        "#imageToolImageScale": "100",
        "#imageToolUkuranPesan": "0.8",
        "#imageToolPosX": "3",
        "#imageToolPosY": "1",
        "#imageToolRotasiPesan": "0",
        "#imageToolCopyX": "2",
        "#imageToolCopyY": "2",
        "#imageToolJarakX": "0",
        "#imageToolJarakY": "0",
        "#imageToolRotasiCopy": "0",
        "#imageToolLebihanKiri": "2.5",
        "#imageToolLebihanKanan": "2.5",
        "#imageToolLebihanAtas": "2.5",
        "#imageToolLebihanBawah": "2.5",
        "#imageToolLebihanKeliling": "2.5",
        "#imageToolUkuranGaris": "1",
        "#imageToolJarakPlong": "2",
        "#imageToolDiameterLebar": "1",
        "#imageToolDiameterPanjang": "0",
        "#imageToolPlongAtas": "0",
        "#imageToolPlongBawah": "0",
        "#imageToolPlongKiri": "0",
        "#imageToolPlongKanan": "0",
        "#imageToolWarnaPesan": "#000000",
        "#imageToolWarnaLatar": "#ffffff",
        "#imageToolWarnaGaris": "#000000",
        "#imageToolWarnaPlong": "#ffffff",
    };

    function resetImageToolsForm() {
        // Reset checkbox tab
        ["imageToolAktifTeksPesan", "imageToolAktifDuplikasiLayout", "imageToolAktifKanvasLatar", "imageToolAktifPlong"].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.checked = false;
        });

        // Reset checkbox plong
        ["imageToolPlongLipat4", "imageToolPlongAtasPerM", "imageToolPlongBawahPojok"].forEach((id) => {
            const cb = document.getElementById(id);
            if (cb) cb.checked = false;
        });

        Object.entries(IMAGE_TOOLS_DEFAULTS).forEach(([selector, defaultVal]) => {
            const el = document.querySelector(selector);
            if (!el) return;
            if (el.type === "color") {
                el.value = defaultVal;
            } else if (el.tagName === "SELECT") {
                const opt = el.querySelector(`option[value="${defaultVal}"]`);
                if (opt) {
                    opt.selected = true;
                } else {
                    el.selectedIndex = 0;
                }
            } else {
                el.value = defaultVal;
            }
        });

        // Reset template name & select
        const templateNameInput = document.getElementById("imageToolTemplateName");
        if (templateNameInput) templateNameInput.value = "";
        const templateSelect = document.getElementById("imageToolTemplateSelect");
        if (templateSelect) templateSelect.value = "";

        const firstTab = document.getElementById("tab-teks");
        if (firstTab) {
            const tabInstance = window.bootstrap?.Tab?.getInstance(firstTab);
            if (tabInstance) {
                tabInstance.show();
            } else {
                firstTab.click();
            }
        }

        togglePlongDiameterInputs();
    }

    function updateImageToolsPreviews() {
        const defaultFile = modalUploadedFiles[0];
        if (!defaultFile) {
            // Tidak ada file default - tampilkan placeholder
            const previewLebihan = document.getElementById("imageToolsPreviewLebihan");
            const previewPlong = document.getElementById("imageToolsPreviewPlong");
            const previewContainer = document.getElementById("imageToolsPreviewContainer");
            
            if (previewLebihan) {
                previewLebihan.innerHTML = '<p class="text-muted small text-center mb-0" style="font-size: 0.7rem;">Preview</p>';
            }
            if (previewPlong) {
                previewPlong.innerHTML = '<p class="text-muted small text-center mb-0" style="font-size: 0.7rem;">Preview</p>';
            }
            if (previewContainer) {
                previewContainer.innerHTML = '<p class="text-muted small text-center">Preview akan ditampilkan di sini</p>';
            }
            return;
        }

        // Dapatkan URL preview
        let previewUrl = null;
        const path = defaultFile.path || defaultFile.sourcePath || "";
        
        if (defaultFile.source === "local" && defaultFile.file instanceof File) {
            // File lokal (dari upload) - gunakan object URL
            if (orderanPreviewObjectUrl) {
                previewUrl = orderanPreviewObjectUrl;
            } else {
                previewUrl = URL.createObjectURL(defaultFile.file);
            }
        } else if (path) {
            if (path.startsWith("http://") || path.startsWith("https://")) {
                previewUrl = path;
            } else {
                // Gunakan endpoint preview dari controller
                previewUrl = `/backend/preview-file?path=${encodeURIComponent(path)}`;
            }
        }

        if (!previewUrl) {
            const previewLebihan = document.getElementById("imageToolsPreviewLebihan");
            const previewPlong = document.getElementById("imageToolsPreviewPlong");
            const previewContainer = document.getElementById("imageToolsPreviewContainer");
            
            if (previewLebihan) {
                previewLebihan.innerHTML = '<p class="text-muted small text-center mb-0" style="font-size: 0.7rem;">Tidak ada file</p>';
            }
            if (previewPlong) {
                previewPlong.innerHTML = '<p class="text-muted small text-center mb-0" style="font-size: 0.7rem;">Tidak ada file</p>';
            }
            if (previewContainer) {
                previewContainer.innerHTML = '<p class="text-muted small text-center">File tidak bisa dipreview</p>';
            }
            return;
        }

        const previewLebihan = document.getElementById("imageToolsPreviewLebihan");
        if (previewLebihan) {
            previewLebihan.innerHTML = `<img src="${previewUrl}" alt="Preview" style="max-width:100%; max-height:100%; object-fit: contain;">`;
        }

        const previewPlong = document.getElementById("imageToolsPreviewPlong");
        if (previewPlong) {
            previewPlong.innerHTML = `<img src="${previewUrl}" alt="Preview" style="max-width:100%; max-height:100%; object-fit: contain;">`;
        }

        const previewContainer = document.getElementById("imageToolsPreviewContainer");
        if (previewContainer) {
            previewContainer.innerHTML = `<img src="${previewUrl}" alt="Preview" style="max-width:100%; max-height:100%; object-fit: contain;">`;
        }
    }

    function togglePlongDiameterInputs() {
        const bentukSelect = document.getElementById("imageToolBentukPlong");
        const diameterPanjangContainer = document.getElementById("imageToolDiameterPanjangContainer");
        
        if (!bentukSelect || !diameterPanjangContainer) return;
        
        const bentuk = bentukSelect.value;
        
        if (bentuk === "circle") {
            diameterPanjangContainer.style.display = "none";
        } else if (bentuk === "square") {
            diameterPanjangContainer.style.display = "";
        } else {
            diameterPanjangContainer.style.display = "none";
        }
    }

    const PAYLOAD_KEYS_BY_TAB = {
        teks: ["pesan", "warna_pesan", "ukuran_pesan", "posX_pesan", "posY_pesan", "rotasi_pesan"],
        layout: ["CopyX", "CopyY", "jarak_gambarX", "jarak_gambarY", "rotasi_copy"],
        kanvas: ["image_scale", "WarnaBackground", "WarnaGaris", "Lebihan_kiri", "Lebihan_kanan", "Lebihan_atas", "Lebihan_bawah", "UkuranGaris"],
        plong: ["jenis_plong", "bentuk_plong", "warna_plong", "jarak_plong_atas", "jarak_plong_bawah", "jarak_plong_kiri", "jarak_plong_kanan", "diameter_lebar", "diameter_panjang", "Plong_atas", "Plong_bawah", "Plong_kiri", "Plong_kanan"],
    };

    const TAB_CHECKBOX_IDS = {
        teks: "imageToolAktifTeksPesan",
        layout: "imageToolAktifDuplikasiLayout",
        kanvas: "imageToolAktifKanvasLatar",
        plong: "imageToolAktifPlong",
    };

    function isTabPayloadEnabled(tabKey) {
        const cb = document.getElementById(TAB_CHECKBOX_IDS[tabKey]);
        return cb ? cb.checked : false;
    }

    function buildImageToolsPayload() {
        const defaultFile = modalUploadedFiles[0];
        if (!defaultFile) {
            throw new Error("Tidak ada file default yang dipreview.");
        }

        const filepath = defaultFile.path || defaultFile.sourcePath || "";
        if (!filepath) {
            throw new Error("File default tidak memiliki path.");
        }

        const payload = { AlamatFile: filepath };

        const stringFields = [
            { key: "pesan", tab: "teks", selector: "#imageToolPesan" },
            { key: "jenis_plong", tab: "plong", selector: "#imageToolJenisPlong" },
            { key: "bentuk_plong", tab: "plong", selector: "#imageToolBentukPlong" },
        ];

        stringFields.forEach(({ key, tab, selector }) => {
            if (!isTabPayloadEnabled(tab)) return;
            const v = getImageToolsStringValue(selector);
            if (v !== undefined) payload[key] = v;
        });

        const colorFields = [
            { key: "warna_pesan", tab: "teks", selector: "#imageToolWarnaPesan" },
            { key: "WarnaBackground", tab: "kanvas", selector: "#imageToolWarnaLatar" },
            { key: "WarnaGaris", tab: "kanvas", selector: "#imageToolWarnaGaris" },
            { key: "warna_plong", tab: "plong", selector: "#imageToolWarnaPlong" },
        ];

        colorFields.forEach(({ key, tab, selector }) => {
            if (!isTabPayloadEnabled(tab)) return;
            const v = getImageToolsColorValue(selector);
            if (v !== undefined) payload[key] = v;
        });

        const numberFields = [
            { key: "image_scale", tab: "kanvas", selector: "#imageToolImageScale" },
            { key: "ukuran_pesan", tab: "teks", selector: "#imageToolUkuranPesan" },
            { key: "posX_pesan", tab: "teks", selector: "#imageToolPosX" },
            { key: "posY_pesan", tab: "teks", selector: "#imageToolPosY" },
            { key: "rotasi_pesan", tab: "teks", selector: "#imageToolRotasiPesan" },
            { key: "CopyX", tab: "layout", selector: "#imageToolCopyX" },
            { key: "CopyY", tab: "layout", selector: "#imageToolCopyY" },
            { key: "jarak_gambarX", tab: "layout", selector: "#imageToolJarakX" },
            { key: "jarak_gambarY", tab: "layout", selector: "#imageToolJarakY" },
            { key: "rotasi_copy", tab: "layout", selector: "#imageToolRotasiCopy" },
            { key: "Lebihan_kiri", tab: "kanvas", selector: "#imageToolLebihanKiri" },
            { key: "Lebihan_kanan", tab: "kanvas", selector: "#imageToolLebihanKanan" },
            { key: "Lebihan_atas", tab: "kanvas", selector: "#imageToolLebihanAtas" },
            { key: "Lebihan_bawah", tab: "kanvas", selector: "#imageToolLebihanBawah" },
            { key: "UkuranGaris", tab: "kanvas", selector: "#imageToolUkuranGaris" },
            { key: "jarak_plong_atas", tab: "plong", selector: "#imageToolJarakPlong" },
            { key: "jarak_plong_bawah", tab: "plong", selector: "#imageToolJarakPlong" },
            { key: "jarak_plong_kiri", tab: "plong", selector: "#imageToolJarakPlong" },
            { key: "jarak_plong_kanan", tab: "plong", selector: "#imageToolJarakPlong" },
            { key: "diameter_lebar", tab: "plong", selector: "#imageToolDiameterLebar" },
            { key: "diameter_panjang", tab: "plong", selector: "#imageToolDiameterPanjang" },
            { key: "Plong_atas", tab: "plong", selector: "#imageToolPlongAtas" },
            { key: "Plong_bawah", tab: "plong", selector: "#imageToolPlongBawah" },
            { key: "Plong_kiri", tab: "plong", selector: "#imageToolPlongKiri" },
            { key: "Plong_kanan", tab: "plong", selector: "#imageToolPlongKanan" },
        ];

        numberFields.forEach(({ key, tab, selector }) => {
            if (!isTabPayloadEnabled(tab)) return;
            const v = getImageToolsNumberValue(selector);
            if (v !== undefined) payload[key] = v;
        });

        if (payload.image_scale === undefined && isTabPayloadEnabled("kanvas")) {
            payload.image_scale = 100;
        }

        return payload;
    }

    const PAYLOAD_TO_SELECTOR = {
        pesan: "#imageToolPesan",
        jenis_plong: "#imageToolJenisPlong",
        bentuk_plong: "#imageToolBentukPlong",
        warna_pesan: "#imageToolWarnaPesan",
        WarnaBackground: "#imageToolWarnaLatar",
        WarnaGaris: "#imageToolWarnaGaris",
        warna_plong: "#imageToolWarnaPlong",
        image_scale: "#imageToolImageScale",
        ukuran_pesan: "#imageToolUkuranPesan",
        posX_pesan: "#imageToolPosX",
        posY_pesan: "#imageToolPosY",
        rotasi_pesan: "#imageToolRotasiPesan",
        CopyX: "#imageToolCopyX",
        CopyY: "#imageToolCopyY",
        jarak_gambarX: "#imageToolJarakX",
        jarak_gambarY: "#imageToolJarakY",
        rotasi_copy: "#imageToolRotasiCopy",
        Lebihan_kiri: "#imageToolLebihanKiri",
        Lebihan_kanan: "#imageToolLebihanKanan",
        Lebihan_atas: "#imageToolLebihanAtas",
        Lebihan_bawah: "#imageToolLebihanBawah",
        UkuranGaris: "#imageToolUkuranGaris",
        jarak_plong_atas: "#imageToolJarakPlong",
        jarak_plong_bawah: "#imageToolJarakPlong",
        jarak_plong_kiri: "#imageToolJarakPlong",
        jarak_plong_kanan: "#imageToolJarakPlong",
        diameter_lebar: "#imageToolDiameterLebar",
        diameter_panjang: "#imageToolDiameterPanjang",
        Plong_atas: "#imageToolPlongAtas",
        Plong_bawah: "#imageToolPlongBawah",
        Plong_kiri: "#imageToolPlongKiri",
        Plong_kanan: "#imageToolPlongKanan",
    };

    function applyImageToolsPayload(payload) {
        if (!payload || typeof payload !== "object") return;

        const processedSelectors = new Set();

        for (const [key, value] of Object.entries(payload)) {
            if (value === undefined || value === null || key === "AlamatFile") continue;

            let selector = PAYLOAD_TO_SELECTOR[key];
            if (!selector) continue;

            const el = document.querySelector(selector);
            if (!el) continue;

            const isColor = el.type === "color";
            const isSelect = el.tagName === "SELECT";
            const isNumber = el.type === "number";

            if (el.type === "color") {
                el.dataset.touched = "1";
            }

            if (isColor || isSelect) {
                el.value = String(value);
            } else if (isNumber) {
                el.value = Number(value);
            } else {
                el.value = String(value);
            }

            processedSelectors.add(selector);
        }

        togglePlongDiameterInputs();
    }

    async function loadQuickTemplatesIntoSelect() {
        const select = document.getElementById("quickTemplateSelect");
        if (!select) return;

        const firstOption = select.querySelector('option[value=""]');
        select.innerHTML = "";
        if (firstOption) select.appendChild(firstOption);

        try {
            const res = await fetch("/backend/finishing-templates");
            const data = await res.json();
            if (!data.success || !Array.isArray(data.templates)) return;

            data.templates.forEach((t) => {
                const opt = document.createElement("option");
                opt.value = t.id;
                opt.textContent = t.nama;
                select.appendChild(opt);
            });
        } catch (e) {
            console.error("Gagal memuat daftar template:", e);
        }
    }

    async function loadFinishingTemplatesIntoSelect() {
        const select = document.getElementById("imageToolTemplateSelect");
        if (!select) return;

        const firstOption = select.querySelector('option[value=""]');
        select.innerHTML = "";
        if (firstOption) select.appendChild(firstOption);

        try {
            const res = await fetch("/backend/finishing-templates");
            const data = await res.json();
            if (!data.success || !Array.isArray(data.templates)) return;

            data.templates.forEach((t) => {
                const opt = document.createElement("option");
                opt.value = t.id;
                opt.textContent = t.nama;
                select.appendChild(opt);
            });
        } catch (e) {
            console.error("Gagal memuat daftar template:", e);
        }
    }

    function getFileIcon(type) {
        if (!type) return "fa-file";
        if (type.includes("pdf")) return "fa-file-pdf";
        if (type.includes("image")) return "fa-file-image";
        if (type.includes("ai") || type.includes("illustrator"))
            return "fa-file-image";
        return "fa-file";
    }

    function updateModalSummary() {
        // Produk
        const produkInput = document.getElementById("modalProdukSelect");
        const produkId = document.getElementById("modalProdukId");
        const produkText = produkInput?.value || "-";
        document.getElementById("summaryProduk").textContent = produkId?.value
            ? produkText
            : "-";

        // Jumlah
        const jumlah =
            document.getElementById("modalJumlahInput")?.value || "0";
        const satuanDisplay = document.getElementById("modalSatuanDisplay");
        const satuan = satuanDisplay?.textContent || "pcs";
        document.getElementById("summaryJumlah").textContent =
            jumlah + " " + satuan;

        // Harga Base
        const qtyForHargaBase = parseFloat(jumlah) || 0;
        const hargaBaseEl = document.getElementById("summaryHargaBase");
        let hargaBaseText = "Rp 0";

        if (currentSelectedProduk && qtyForHargaBase > 0) {
            const hargaJual = getHargaJualFinishing(
                {
                    harga_bertingkat_json:
                        currentSelectedProduk.harga_bertingkat_json || [],
                    harga_reseller_json:
                        currentSelectedProduk.harga_reseller_json || [],
                },
                qtyForHargaBase,
            );

            if (hargaJual > 0) {
                if (currentSelectedProduk.is_metric) {
                    const unit =
                        currentMetricUnit ||
                        currentSelectedProduk.metric_unit ||
                        "cm";
                    const panjangLocked =
                        document.getElementById("panjangStatus")?.style
                            .display === "block";
                    const lebarLocked =
                        document.getElementById("lebarStatus")?.style
                            .display === "block";
                    const useSquareUnit = panjangLocked === lebarLocked;

                    hargaBaseText = `Rp ${hargaJual.toLocaleString("id-ID")} / ${unit}${useSquareUnit ? "²" : ""}`;
                } else {
                    hargaBaseText = `Rp ${hargaJual.toLocaleString("id-ID")} / ${satuan}`;
                }
            }
        }

        if (hargaBaseEl) {
            hargaBaseEl.textContent = hargaBaseText;
        }

        //  Harga per (satuan) untuk produk metric
        const hargaPerSatuanContainer = document.getElementById(
            "summaryHargaPerSatuanContainer",
        );
        const hargaPerSatuanEl = document.getElementById(
            "summaryHargaPerSatuan",
        );
        const hargaPerSatuanLabelEl = document.getElementById(
            "summaryHargaPerSatuanLabel",
        );

        if (
            hargaPerSatuanContainer &&
            hargaPerSatuanEl &&
            hargaPerSatuanLabelEl
        ) {
            hargaPerSatuanContainer.style.display = "none";
            hargaPerSatuanEl.textContent = "Rp 0";
            hargaPerSatuanLabelEl.textContent = satuan || "satuan";

            if (
                currentSelectedProduk &&
                currentSelectedProduk.is_metric &&
                qtyForHargaBase > 0
            ) {
                const hargaJual = getHargaJualFinishing(
                    {
                        harga_bertingkat_json:
                            currentSelectedProduk.harga_bertingkat_json || [],
                        harga_reseller_json:
                            currentSelectedProduk.harga_reseller_json || [],
                    },
                    qtyForHargaBase,
                );

                if (hargaJual > 0) {
                    const panjangVal =
                        parseFloat(
                            document.getElementById("modalPanjangInput")?.value,
                        ) || 0;
                    const lebarVal =
                        parseFloat(
                            document.getElementById("modalLebarInput")?.value,
                        ) || 0;

                    const panjangLocked =
                        document.getElementById("panjangStatus")?.style
                            .display === "block";
                    const lebarLocked =
                        document.getElementById("lebarStatus")?.style
                            .display === "block";

                    let factor = 0;

                    if (panjangLocked === lebarLocked) {
                        factor = panjangVal * lebarVal;
                    } else {
                        if (panjangLocked && !lebarLocked) {
                            factor = lebarVal;
                        } else if (!panjangLocked && lebarLocked) {
                            factor = panjangVal;
                        }
                    }

                    if (factor >= 0) {
                        const hargaPerSatuan = hargaJual * factor;
                        hargaPerSatuanEl.textContent = `Rp ${hargaPerSatuan.toLocaleString("id-ID")} / ${satuan}`;
                        hargaPerSatuanContainer.style.display = "";
                    }
                }
            }
        }

        // Ukuran
        const panjang =
            document.getElementById("modalPanjangInput")?.value || "0";
        const lebar = document.getElementById("modalLebarInput")?.value || "0";

        const panjangLocked =
            document.getElementById("panjangStatus")?.style.display === "block";
        const lebarLocked =
            document.getElementById("lebarStatus")?.style.display === "block";
        const qtyUkuran = parseFloat(jumlah);
        const summaryUkuranEl = document.getElementById("summaryUkuran");
        if (summaryUkuranEl) {
            const bothLocked = panjangLocked && lebarLocked;
            const noneLocked = !panjangLocked && !lebarLocked;

            let displayText,
                sigma,
                sigmaUnit = currentMetricUnit;

            if (bothLocked || noneLocked) {
                const luas = panjang * lebar;
                displayText = `${lebar} x ${panjang} ${currentMetricUnit} = ${luas.toFixed(2)} ${currentMetricUnit}²`;
                sigma = qtyUkuran * luas;
                sigmaUnit += "²";
            } else {
                const unlockedValue = !panjangLocked ? panjang : lebar;
                displayText = `${unlockedValue} ${currentMetricUnit}`;
                sigma = qtyUkuran * unlockedValue;
            }

            summaryUkuranEl.innerHTML = `
                ${displayText}
                <br>
                <small class="text-muted">Σ ${sigma.toFixed(2)} ${sigmaUnit}</small>
            `;
        }

        // Deadline
        const deadlineInput = document.getElementById("modalDeadlineInput");
        if (deadlineInput && deadlineInput.value) {
            const d = new Date(deadlineInput.value);
            const options = {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            };

            const now = new Date();
            const timeDiff = d.getTime() - now.getTime();

            let estimasiText = "";
            if (timeDiff > 0) {
                const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                const hours = Math.floor(
                    (timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60),
                );
                estimasiText = `Estimasi: ${days} hari, ${hours} jam`;
            } else {
                estimasiText = "Sudah lewat deadline";
            }

            document.getElementById("summaryDeadline").innerHTML =
                d.toLocaleDateString("id-ID", options) +
                '<br><small class="text-muted">' +
                estimasiText +
                "</small>";
            // document.getElementById('summaryDeadline').textContent = d.toLocaleDateString('id-ID', options);
        } else {
            document.getElementById("summaryDeadline").textContent = "-";
        }

        // Files
        document.getElementById("summaryFiles").textContent =
            modalUploadedFiles.length + " file";

        // Finishing List
        const finishingListEl = document.getElementById("summaryFinishingList");
        if (modalFinishingData.length === 0) {
            finishingListEl.innerHTML = '<span class="text-muted">-</span>';
        } else {
            finishingListEl.innerHTML = modalFinishingData
                .map((f) => {
                    const qty = parseFloat(f.jumlah) || 0;
                    const total = parseFloat(f.total) || 0;
                    const panjang = parseFloat(f.panjang) || 0;
                    const lebar = parseFloat(f.lebar) || 0;

                    const panjangLocked =
                        f.panjang_locked === true ||
                        f.panjang_locked === "true";
                    const lebarLocked =
                        f.lebar_locked === true || f.lebar_locked === "true";

                    let dimensiValid = false;
                    let faktorDimensi = 1;

                    if (f.is_metric) {
                        if (!panjangLocked && !lebarLocked) {
                            dimensiValid = panjang > 0 && lebar > 0;
                            faktorDimensi = dimensiValid ? panjang * lebar : 0;
                        } else if (panjangLocked && !lebarLocked) {
                            dimensiValid = lebar > 0;
                            faktorDimensi = dimensiValid ? lebar : 0;
                        } else if (!panjangLocked && lebarLocked) {
                            dimensiValid = panjang > 0;
                            faktorDimensi = dimensiValid ? panjang : 0;
                        } else {
                            dimensiValid = true;
                            faktorDimensi = 1;
                        }
                    }

                    let hargaPerUnit = 0;
                    let qtyDisplay = qty;

                    if (f.is_metric && dimensiValid && faktorDimensi > 0) {
                        hargaPerUnit =
                            total > 0 ? total / (qty * faktorDimensi) : 0;
                        qtyDisplay = qty * faktorDimensi;
                    } else if (!f.is_metric) {
                        hargaPerUnit = qty > 0 ? total / qty : 0;
                        qtyDisplay = qty;
                    }

                    return `<div class="d-flex justify-content-between small">
                    <div>
                        <div>${f.nama || f.name || "Finishing"}</div>
                        ${hargaPerUnit > 0 ? `<small class="text-muted">Rp ${hargaPerUnit.toLocaleString("id-ID")} × ${qtyDisplay} = Rp ${total.toLocaleString("id-ID")}</small>` : ""}
                    </div>
                    <!-- <span class="fw-semibold">Rp ${total.toLocaleString("id-ID")}</span> -->
                </div>`;
                })
                .join("");
        }

        // Pricing
        const qty = parseFloat(jumlah) || 0;
        let hargaJual = 0;
        let subtotalCetak = 0;
        let factorDimensi = 1;
        let satuanProduk = satuan || "pcs";

        if (currentSelectedProduk && qty > 0) {
            hargaJual = getHargaJualFinishing(
                {
                    harga_bertingkat_json:
                        currentSelectedProduk.harga_bertingkat_json || [],
                    harga_reseller_json:
                        currentSelectedProduk.harga_reseller_json || [],
                },
                qty,
            );

            if (hargaJual > 0) {
                if (currentSelectedProduk.is_metric) {
                    const panjangVal =
                        parseFloat(
                            document.getElementById("modalPanjangInput")?.value,
                        ) || 0;
                    const lebarVal =
                        parseFloat(
                            document.getElementById("modalLebarInput")?.value,
                        ) || 0;

                    const panjangLocked =
                        document.getElementById("panjangStatus")?.style
                            .display === "block";
                    const lebarLocked =
                        document.getElementById("lebarStatus")?.style
                            .display === "block";

                    if (panjangLocked === lebarLocked) {
                        factorDimensi = panjangVal * lebarVal;
                        satuanProduk =
                            (currentMetricUnit ||
                                currentSelectedProduk.metric_unit ||
                                "cm") + "²";
                    } else {
                        if (panjangLocked && !lebarLocked) {
                            factorDimensi = lebarVal;
                        } else if (!panjangLocked && lebarLocked) {
                            factorDimensi = panjangVal;
                        }
                        satuanProduk =
                            currentMetricUnit ||
                            currentSelectedProduk.metric_unit ||
                            "cm";
                    }

                    subtotalCetak = qty * factorDimensi * hargaJual;
                } else {
                    factorDimensi = 1;
                    satuanProduk = satuan;
                    subtotalCetak = qty * hargaJual;
                }
            }
        }

        const biayaFinishing = modalFinishingData.reduce(
            (sum, item) => sum + parseFloat(item.total || 0),
            0,
        );
        const totalAkhir = subtotalCetak + biayaFinishing;

        let detailText = "";
        if (qty > 0 && hargaJual > 0) {
            if (currentSelectedProduk && currentSelectedProduk.is_metric) {
                detailText = `(${(qty * factorDimensi).toFixed(2)} ${satuanProduk} × Rp ${hargaJual.toLocaleString("id-ID")}) Rp ${subtotalCetak.toLocaleString("id-ID")}`;
            } else {
                detailText = `(${qty} ${satuanProduk} × Rp ${hargaJual.toLocaleString("id-ID")}) Rp ${subtotalCetak.toLocaleString("id-ID")}`;
            }
        } else {
            detailText = "Rp " + subtotalCetak.toLocaleString("id-ID");
        }

        document.getElementById("summarySubtotalCetak").textContent =
            detailText;
        document.getElementById("summaryBiayaFinishing").textContent =
            "Rp " + biayaFinishing.toLocaleString("id-ID");
        document.getElementById("summaryTotalAkhir").textContent =
            "Rp " + totalAkhir.toLocaleString("id-ID");
    }

    // Event listeners untuk modal
    function initModalTambahItem() {
        const modal = document.getElementById("modalTambahItemPesanan");
        if (!modal) return;

        // Dropzone click
        const dropzone = modal.querySelector("#modalDropZone");
        const fileInput = modal.querySelector("#modalInputFiles");
        const btnBrowse = modal.querySelector("#modalBtnBrowseFile");

        if (dropzone && fileInput) {
            dropzone.addEventListener("click", () => fileInput.click());
            btnBrowse?.addEventListener("click", (e) => {
                e.stopPropagation();
                fileInput.click();
            });

            dropzone.addEventListener("dragover", (e) => {
                e.preventDefault();
                dropzone.classList.add("dragover");
            });
            dropzone.addEventListener("dragleave", (e) => {
                e.preventDefault();
                dropzone.classList.remove("dragover");
            });
            dropzone.addEventListener("drop", (e) => {
                e.preventDefault();
                dropzone.classList.remove("dragover");
                handleModalFiles(e.dataTransfer.files);
            });

            fileInput.addEventListener("change", (e) => {
                if (e.target.files.length > 0) {
                    handleModalFiles(e.target.files);
                    e.target.value = "";
                }
            });
        }

        const btnCariProduk = modal.querySelector("#modalBtnCariProduk");
        const inputProduk = modal.querySelector("#modalProdukSelect");
        if (btnCariProduk && inputProduk) {
            btnCariProduk.addEventListener("click", () => {
                const modalProduk = new bootstrap.Modal(
                    document.getElementById("modalCariProdukSPK"),
                );
                modalProduk.show();
            });

            inputProduk.addEventListener("click", () => {
                const modalProduk = new bootstrap.Modal(
                    document.getElementById("modalCariProdukSPK"),
                );
                modalProduk.show();
            });
        }

        // Remove file
        modal.addEventListener("click", (e) => {
            const target = e.target.closest(".btn-set-default-file");
            if (target) {
                const idx = parseInt(target.getAttribute("data-idx"), 10);
                if (
                    !isNaN(idx) &&
                    idx > 0 &&
                    Array.isArray(modalUploadedFiles)
                ) {
                    const [selected] = modalUploadedFiles.splice(idx, 1);
                    modalUploadedFiles.unshift(selected);
                    imageRotationDegrees = 0;
                    renderModalUploadedFiles();
                    renderOrderanPreviewTab();
                }
            }

            if (e.target.closest(".btn-remove-file")) {
                const idx = parseInt(
                    e.target.closest(".btn-remove-file").dataset.idx,
                );
                modalUploadedFiles.splice(idx, 1);
                renderModalUploadedFiles();
                updateModalSummary();
                renderOrderanPreviewTab();
            }
        });

        document.addEventListener("click", (e) => {
            if (e.target.closest("#btnRotateImage")) {
                e.preventDefault();
                rotateImage();
            } else if (e.target.closest("#btnSyncImageDimensions")) {
                e.preventDefault();
                syncImageDimensions();
            }

            if (e.target.closest("#btnLebihanKelilingKunci")) {
                e.preventDefault();
                const inputKeliling = document.getElementById("imageToolLebihanKeliling");
                const value = parseFloat(inputKeliling?.value);
                if (!Number.isFinite(value)) return;
                const ids = ["imageToolLebihanAtas", "imageToolLebihanKiri", "imageToolLebihanBawah", "imageToolLebihanKanan"];
                ids.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.value = value.toFixed(1);
                });
                return;
            }

            if (e.target.closest("#btnImageTools")) {
                e.preventDefault();
                const modalInstance = getImageToolsModalInstance();
                if (!modalInstance) {
                    console.error("Modal Image Tools tidak ditemukan.");
                    SafeHelper.notify(
                        "error",
                        "Image Tools",
                        "Modal Image Tools tidak ditemukan di halaman.",
                    );
                    return;
                }

                resetImageToolsForm();
                wireImageToolsColorTouchedFlag();
                modalInstance.show();
                loadFinishingTemplatesIntoSelect();
                setTimeout(() => storeImageToolsInitialColorValues(), 0);
                togglePlongDiameterInputs();
                updateImageToolsPreviews();
                return;
            }
        
            // Terapkan Image Tools
            if (e.target.closest("#btnApplyImageTools")) {
                e.preventDefault();
                (async function () {
                    try {
                        const payload = buildImageToolsPayload();

                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
                        const response = await fetch(
                            "/backend/image-processing",
                            {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": token,
                                },
                                body: JSON.stringify(payload),
                            },
                        );

                        if (!response.ok) {
                            throw new Error("HTTP status " + response.status);
                        }

                        const data = await response.json();

                        if (data && data.status === "success" && data.output_path) {
                            renderModalUploadedFiles();
                            updateModalSummary();
                            renderOrderanPreviewTab();

                            const modalInstance = getImageToolsModalInstance();
                            if (modalInstance) {
                                modalInstance.hide();
                            }

                            SafeHelper.notify(
                                "success",
                                "Image Tools",
                                "Gambar berhasil diproses.",
                            );
                        } else {
                            SafeHelper.notify(
                                "warning",
                                "Image Tools",
                                "Proses gambar gagal. Silakan coba lagi.",
                            );
                        }
                    } catch (err) {
                        console.error(err);
                        SafeHelper.notify(
                            "error",
                            "Image Tools",
                            "Terjadi kesalahan saat memproses gambar.",
                        );
                    }
                })();
            }

            if (e.target.closest("#btnResetImageTools")) {
                e.preventDefault();
                resetImageToolsForm();
                return;
            }

            if (e.target.closest("#btnQuickApplyTemplate")) {
                e.preventDefault();
                const select = document.getElementById("quickTemplateSelect");
                const templateId = select?.value;
                const btn = document.getElementById("btnQuickApplyTemplate");
                
                if (!templateId) {
                    SafeHelper.notify("warning", "Apply Template", "Pilih template terlebih dahulu.");
                    return;
                }

                const defaultFile = modalUploadedFiles[0];
                if (!defaultFile) {
                    SafeHelper.notify("warning", "Apply Template", "Tidak ada file default yang dipilih.");
                    return;
                }

                const filepath = defaultFile.path || defaultFile.sourcePath || "";
                if (!filepath) {
                    SafeHelper.notify("warning", "Apply Template", "File default tidak memiliki path.");
                    return;
                }

                if (btn) {
                    btn.disabled = true;
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Memproses...';
                    

                    const restoreButton = () => {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = originalHTML;
                        }
                    };

                    (async function () {
                        try {
                            const templateRes = await fetch(`/backend/finishing-templates/${templateId}`);
                            const templateData = await templateRes.json();

                            if (!templateData.success || !templateData.payload) {
                                throw new Error(templateData.message || "Template tidak ditemukan.");
                            }

                            const payload = {
                                ...templateData.payload,
                                AlamatFile: filepath,
                            };

                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
                            const response = await fetch("/backend/image-processing", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": token,
                                },
                                body: JSON.stringify(payload),
                            });

                            if (!response.ok) {
                                throw new Error("HTTP status " + response.status);
                            }

                            const data = await response.json();

                            if (data && data.status === "success" && data.output_path) {
                                renderModalUploadedFiles();
                                updateModalSummary();
                                renderOrderanPreviewTab();

                                SafeHelper.notify(
                                    "success",
                                    "Apply Template",
                                    `Template "${templateData.nama || ""}" berhasil diterapkan.`,
                                );
                            } else {
                                SafeHelper.notify(
                                    "warning",
                                    "Apply Template",
                                    "Proses gambar gagal. Silakan coba lagi.",
                                );
                            }
                        } catch (error) {
                            console.error(error);
                            SafeHelper.notify(
                                "error",
                                "Apply Template",
                                error.message || "Terjadi kesalahan saat menerapkan template.",
                            );
                        } finally {
                            restoreButton();
                        }
                    })();
                }

                return;
            }
        });

        document.addEventListener("input", (e) => {
            if (e.target.id === "filePdfQty") {
                calculatePdfSummary();
                if (typeof updateModalSummary === "function")
                    updateModalSummary();
            }
        });

        document.addEventListener("change", (e) => {
            if (e.target.matches("input[data-finishing-index]")) {
                const input = e.target;
                const index = parseInt(
                    input.getAttribute("data-finishing-index"),
                );
                const newJumlah = input.value;

                updateFinishingJumlah(index, newJumlah);
            }
        });

        document.addEventListener("change", (e) => {
            if (e.target.matches("input[data-finishing-field]")) {
                const input = e.target;
                const index = parseInt(
                    input.getAttribute("data-finishing-index"),
                );
                const field = input.getAttribute("data-finishing-field");
                const value = input.value;

                updateFinishingField(index, field, value);
            }
        });

        const urgentToggle = modal.querySelector("#modalUrgentToggle");
        const urgentValue = modal.querySelector("#modalUrgentValue");
        const urgentStatus = modal.querySelector(".urgent-status");

        if (urgentToggle && urgentValue && urgentStatus) {
            urgentToggle.addEventListener("change", () => {
                const isUrgent = urgentToggle.checked;
                urgentValue.value = isUrgent ? "true" : "false";
                urgentStatus.textContent = isUrgent ? "Ya" : "Tidak";

                if (typeof updateModalSummary === "function") {
                    updateModalSummary();
                }
            });
        }

        const tabPreviewBtn = document.getElementById("tab-preview-orderan");
        if (tabPreviewBtn) {
            tabPreviewBtn.addEventListener("shown.bs.tab", () => {
                renderOrderanPreviewTab();
            });
        }

        // Finishing checkboxes
        // modal.querySelectorAll('.finishing-option input[type="checkbox"]').forEach(cb => {
        //     cb.addEventListener('change', updateModalFinishingCalc);
        // });

        // Form inputs untuk update summary
        const inputsToWatch = [
            "#modalProdukSelect",
            "#modalJumlahInput",
            "#modalPanjangInput",
            "#modalLebarInput",
            "#modalDeadlineInput",
        ];
        inputsToWatch.forEach((sel) => {
            const el = modal.querySelector(sel);
            if (el) {
                el.addEventListener("change", updateModalSummary);
                el.addEventListener("input", updateModalSummary);

                if (
                    sel === "#modalPanjangInput" ||
                    sel === "#modalLebarInput"
                ) {
                    el.addEventListener("change", updateLuas);
                    el.addEventListener("input", updateLuas);
                }
            }
        });
    }

    function handleModalFiles(fileList) {
        for (let i = 0; i < fileList.length; i++) {
            const file = fileList[i];
            const duplicate = modalUploadedFiles.some(
                (f) => f.name === file.name && f.size === file.size,
            );
            if (!duplicate) {
                modalUploadedFiles.push({
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    source: "local",
                    file: file,
                    path: file.path || file.webkitRelativePath || "",
                });
            }
        }
        renderModalUploadedFiles();
        updateModalSummary();
        renderOrderanPreviewTab();
    }

    // function updateFinishingJumlah(index, newJumlah) {
    //     if (index < 0 || index >= modalFinishingData.length) {
    //         return;
    //     }

    //     const qty = parseFloat(newJumlah) || 1;
    //     modalFinishingData[index].jumlah = qty;
    //     modalFinishingData[index].total = qty * parseFloat(modalFinishingData[index].harga_satuan || 0);

    //     const row = document.querySelector(`input[data-finishing-index="${index}"]`).closest('tr');
    //     if (row) {
    //         const totalCell = row.querySelector('td:nth-child(4)'); // Kolom Total
    //         if (totalCell) {
    //             totalCell.innerHTML = `Rp ${modalFinishingData[index].total.toLocaleString('id-ID')}`;
    //         }
    //     }

    //     updateModalFinishingTotal();
    // }

    // function renderModalFinishingTable() {
    //     const tbody = document.getElementById('modalFinishingBody');
    //     if (!tbody) return;

    //     if (modalFinishingData.length === 0) {
    //         tbody.innerHTML = `
    //             <tr>
    //                 <td colspan="5" class="text-center text-muted py-3">
    //                     <i class="fa fa-cogs me-2"></i>Belum ada finishing yang ditambahkan
    //                 </td>
    //             </tr>
    //         `;
    //         updateModalFinishingTotal();
    //         return;
    //     }

    //     tbody.innerHTML = modalFinishingData.map((finishing, index) => `
    //         <tr>
    //             <td>${finishing.nama || '-'}</td>
    //             <td>
    //                 <input type="number" class="form-control form-control-sm"
    //                     value="${finishing.jumlah || 1}"
    //                     min="1"
    //                     step="1"
    //                     data-finishing-index="${index}"
    //                     style="width: 80px;">
    //             </td>
    //             <td>Rp ${parseFloat(finishing.harga_satuan || 0).toLocaleString('id-ID')}</td>
    //             <td>Rp ${parseFloat(finishing.total || 0).toLocaleString('id-ID')}</td>
    //             <td>
    //                 <button type="button" class="btn btn-sm btn-outline-danger"
    //                         data-action="hapus-finishing"
    //                         data-index="${index}"
    //                         title="Hapus ${finishing.nama}">
    //                     <i class="fa fa-trash"></i>
    //                 </button>
    //             </td>
    //         </tr>
    //     `).join('');

    //     updateModalFinishingTotal();
    // }

    function updateFinishingTotalBadge(index) {
        const finishing = modalFinishingData[index];
        if (!finishing) return;

        const qty = finishing.jumlah || 1;
        const total = parseFloat(finishing.total || 0);
        const panjang = parseFloat(finishing.panjang) || 0;
        const lebar = parseFloat(finishing.lebar) || 0;
        const dimensiValid = panjang > 0 && lebar > 0;
        const dataLengkap = finishing.is_metric
            ? qty > 0 && dimensiValid
            : qty > 0;

        const badgeEl = document.querySelector(
            `#headingFinishing${index} .badge.bg-primary`,
        );
        if (badgeEl) {
            if (dataLengkap && total > 0) {
                const hargaPerUnit = qty > 0 ? total / qty : 0;
                badgeEl.innerHTML = `${qty} × Rp ${hargaPerUnit.toLocaleString("id-ID")} = Rp ${total.toLocaleString("id-ID")}`;
                badgeEl.className = "badge bg-primary ms-2"; // Warna normal
            } else {
                badgeEl.innerHTML = "Data belum lengkap";
                badgeEl.className = "badge bg-warning ms-2"; // Warna warning
            }
        }
    }

    function updateFinishingField(index, field, value) {
        if (index < 0 || index >= modalFinishingData.length) {
            console.error(
                "Index tidak valid untuk update field finishing:",
                index,
            );
            return;
        }

        if (field === "jumlah") {
            modalFinishingData[index].jumlah = parseFloat(value) || 1;
        } else if (field === "panjang") {
            modalFinishingData[index].panjang = parseFloat(value) || 0;
        } else if (field === "lebar") {
            modalFinishingData[index].lebar = parseFloat(value) || 0;
        }

        const item = modalFinishingData[index];
        const panjang = parseFloat(item.panjang) || 0;
        const lebar = parseFloat(item.lebar) || 0;
        const jumlah = parseFloat(item.jumlah) || 1;

        const panjangLocked =
            item.panjang_locked === true || item.panjang_locked === "true";
        const lebarLocked =
            item.lebar_locked === true || item.lebar_locked === "true";

        let dimensiValid = false;
        let faktorDimensi = 1;

        if (item.is_metric) {
            if (!panjangLocked && !lebarLocked) {
                dimensiValid = panjang > 0 && lebar > 0;
                faktorDimensi = dimensiValid ? panjang * lebar : 0;
            } else if (panjangLocked && !lebarLocked) {
                dimensiValid = lebar > 0;
                faktorDimensi = dimensiValid ? lebar : 0;
            } else if (!panjangLocked && lebarLocked) {
                dimensiValid = panjang > 0;
                faktorDimensi = dimensiValid ? panjang : 0;
            } else {
                dimensiValid = true;
                faktorDimensi = 1;
            }
        }

        const hargaJual = getHargaJualFinishing(item, item.jumlah || 0);

        if (item.is_metric) {
            if (dimensiValid && faktorDimensi > 0) {
                item.total = jumlah * hargaJual * faktorDimensi;
            } else {
                item.total = 0;
            }
        } else {
            item.total = jumlah * hargaJual;
        }

        updateFinishingLuasField(index);
        updateFinishingTotalBadge(index);
        updateModalFinishingTotal();
        renderModalFinishingAccordion();
    }

    function updateFinishingLuasField(index) {
        const finishing = modalFinishingData[index];
        if (!finishing) return;

        const panjang = parseFloat(finishing.panjang) || 0;
        const lebar = parseFloat(finishing.lebar) || 0;
        const luas = panjang * lebar;
        const metricUnit = finishing.metric_unit || "-";

        const luasInput = document.querySelector(
            `input[data-finishing-field="luas"][data-finishing-index="${index}"]`,
        );
        if (luasInput) {
            luasInput.value = luas.toFixed(2);
        }

        const unitSpan =
            luasInput?.parentElement?.querySelector(".input-group-text");
        if (unitSpan) {
            unitSpan.textContent = metricUnit + "²";
        }
    }

    function renderModalFinishingAccordion() {
        const accordion = document.getElementById("accordionFinishingItems");
        const noMessage = document.getElementById("noFinishingMessage");

        if (!accordion) return;

        if (modalFinishingData.length === 0) {
            if (noMessage) noMessage.style.display = "block";
            accordion.innerHTML =
                '<div class="text-center text-muted py-3" id="noFinishingMessage"><i class="fa fa-cogs me-2"></i>Belum ada finishing yang ditambahkan</div>';
            updateModalFinishingTotal();
            return;
        }

        if (noMessage) noMessage.style.display = "none";

        accordion.innerHTML = modalFinishingData
            .map((finishing, index) => {
                const showDimensionFields =
                    finishing.is_metric === true ||
                    finishing.is_metric === "true";
                const panjangLocked =
                    finishing.panjang_locked === true ||
                    finishing.panjang_locked === "true";
                const lebarLocked =
                    finishing.lebar_locked === true ||
                    finishing.lebar_locked === "true";
                const metricUnit = finishing.metric_unit || "cm";

                return `
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFinishing${index}">
                    <button class="accordion-button ${index === currentExpandedFinishingIndex ? "" : "collapsed"}" type="button" 
                            data-bs-toggle="collapse" data-bs-target="#collapseFinishing${index}" 
                            aria-expanded="${index === currentExpandedFinishingIndex ? "true" : "false"}" 
                            aria-controls="collapseFinishing${index}">
                        <strong>${finishing.nama || "-"}</strong>
                        ${(() => {
                            const qty = parseFloat(finishing.jumlah) || 0;
                            const total = parseFloat(finishing.total) || 0;
                            const panjang = parseFloat(finishing.panjang) || 0;
                            const lebar = parseFloat(finishing.lebar) || 0;

                            const panjangLocked =
                                finishing.panjang_locked === true ||
                                finishing.panjang_locked === "true";
                            const lebarLocked =
                                finishing.lebar_locked === true ||
                                finishing.lebar_locked === "true";

                            let dimensiValid = false;
                            let faktorDimensi = 1;

                            if (finishing.is_metric) {
                                if (!panjangLocked && !lebarLocked) {
                                    dimensiValid = panjang > 0 && lebar > 0;
                                    faktorDimensi = dimensiValid
                                        ? panjang * lebar
                                        : 0;
                                } else if (panjangLocked && !lebarLocked) {
                                    dimensiValid = lebar > 0;
                                    faktorDimensi = dimensiValid ? lebar : 0;
                                } else if (!panjangLocked && lebarLocked) {
                                    dimensiValid = panjang > 0;
                                    faktorDimensi = dimensiValid ? panjang : 0;
                                } else {
                                    dimensiValid = true;
                                    faktorDimensi = 1;
                                }
                            }

                            const dataLengkap = finishing.is_metric
                                ? qty > 0 && dimensiValid
                                : qty > 0;

                            if (dataLengkap && total > 0) {
                                let hargaPerUnit = 0;
                                let qtyDisplay = qty;

                                if (finishing.is_metric && faktorDimensi > 0) {
                                    hargaPerUnit =
                                        total / (qty * faktorDimensi);
                                    qtyDisplay = qty * faktorDimensi;
                                } else {
                                    hargaPerUnit = qty > 0 ? total / qty : 0;
                                    qtyDisplay = qty;
                                }

                                return `<span class="badge bg-primary ms-2">${qtyDisplay} × Rp ${hargaPerUnit.toLocaleString("id-ID")} = Rp ${total.toLocaleString("id-ID")}</span>`;
                            } else {
                                return `<span class="badge bg-warning ms-2">Data belum lengkap</span>`;
                            }
                        })()}
                    </button>
                </h2>
                <div id="collapseFinishing${index}" class="accordion-collapse collapse ${index === currentExpandedFinishingIndex ? "show" : ""}"
                     aria-labelledby="headingFinishing${index}" 
                     data-bs-parent="#accordionFinishingItems">
                    <div class="accordion-body">
                        <div class="row g-3">
                            ${
                                showDimensionFields
                                    ? `
                            <!-- Lebar -->
                            <div class="col-md-3">
                                <label class="form-label">Lebar</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" 
                                           value="${finishing.lebar || ""}" 
                                           min="0" step="0.1" 
                                           placeholder="0"
                                           ${lebarLocked ? "disabled" : ""}
                                           data-finishing-index="${index}"
                                           data-finishing-field="lebar">
                                    <span class="input-group-text">${metricUnit || "cm"}</span>
                                </div>
                            </div>

                            <!-- Panjang -->
                            <div class="col-md-3">
                                <label class="form-label">Panjang</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" 
                                           value="${finishing.panjang || ""}" 
                                           min="0" step="0.1" 
                                           placeholder="0"
                                           ${panjangLocked ? "disabled" : ""}
                                           data-finishing-index="${index}"
                                           data-finishing-field="panjang">
                                    <span class="input-group-text">${metricUnit || "cm"}</span>
                                </div>
                            </div>
                            `
                                    : ""
                            }
                            ${
                                showDimensionFields &&
                                !lebarLocked &&
                                !panjangLocked
                                    ? `
                            <!-- Luas  -->
                            <div class="col-md-3">
                                <label class="form-label">Luas</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                        data-finishing-index="${index}"
                                        data-finishing-field="luas"
                                        value="${showDimensionFields && finishing.panjang && finishing.lebar ? (finishing.panjang * finishing.lebar).toFixed(2) : "0.00"}" 
                                        readonly>
                                    <span class="input-group-text">${metricUnit || "cm"}²</span>
                                </div>
                            </div>
                            `
                                    : ""
                            }
                            <!-- Jumlah -->
                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" 
                                        value="${finishing.jumlah || 1}" 
                                        min="1" 
                                        data-finishing-index="${index}"
                                        data-finishing-field="jumlah">
                                    <span class="input-group-text">${finishing.satuan || "-"}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Aksi -->
                        <div class="mt-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    data-action="hapus-finishing" 
                                    data-index="${index}"
                                    title="Hapus ${finishing.nama}">
                                <i class="fa fa-trash me-1"></i>Hapus Finishing
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            `;
            })
            .join("");

        updateModalFinishingTotal();
    }

    document.addEventListener("click", (e) => {
        if (e.target.closest("#modalBtnTambahFinishing")) {
            e.preventDefault();

            const produkId = document.getElementById("modalProdukId")?.value;
            if (!produkId) {
                if (
                    window.SPKHelper &&
                    typeof SPKHelper.showNotification === "function"
                ) {
                    SPKHelper.showNotification(
                        "Produk belum dipilih",
                        "Silakan pilih produk terlebih dahulu sebelum menambah finishing.",
                        "error",
                    );
                } else {
                    Swal.fire({
                        icon: "warning",
                        text: "Produk belum dipilih. Silakan pilih produk terlebih dahulu sebelum menambah finishing.",
                        confirmButtonText: "OK",
                    });
                }
                return;
            }

            window.SPKCurrentProdukIdForFinishing = produkId;

            const modalElement = document.getElementById(
                "modalCariProdukFinishingSPK",
            );
            if (modalElement) {
                const modalFin = new bootstrap.Modal(modalElement);
                modalFin.show();
            }
        }
    });

    document.addEventListener("click", (e) => {
        if (e.target.closest('[data-action="hapus-finishing"]')) {
            e.preventDefault();
            const button = e.target.closest('[data-action="hapus-finishing"]');
            const index = parseInt(button.getAttribute("data-index"));

            if (typeof hapusModalFinishing === "function") {
                hapusModalFinishing(index);
            } else {
                console.error("Function hapusModalFinishing tidak ditemukan");
            }
        }
    });

    // Function untuk update total finishing
    function updateModalFinishingTotal() {
        const total = modalFinishingData.reduce(
            (sum, item) => sum + parseFloat(item.total || 0),
            0,
        );
        const totalEl = document.getElementById("modalTotalFinishing");
        if (totalEl) {
            totalEl.textContent = "Rp " + total.toLocaleString("id-ID");
        }
        updateModalSummary();
    }

    function getHargaJualFinishing(finishing, qty) {
        const customerKategori = getCustomerKategoriHarga();

        const hargaData =
            customerKategori === "reseller"
                ? finishing.harga_reseller_json || []
                : finishing.harga_bertingkat_json || [];

        if (!hargaData.length) return 0;

        const sorted = [...hargaData].sort((a, b) => a.min_qty - b.min_qty);

        const matched = sorted.find(
            (h) => qty >= h.min_qty && qty <= h.max_qty,
        );
        if (matched) return matched.harga;

        const lastTier = sorted[sorted.length - 1];
        if (qty > lastTier.max_qty) {
            return lastTier.harga;
        }
        return sorted[0].harga;
    }

    // Fungsi untuk mendapatkan kategori harga customer
    function getCustomerKategoriHarga() {
        const customerKategoriInput = document.getElementById(
            "customerKategoriHarga",
        );
        return customerKategoriInput
            ? customerKategoriInput.value
            : "bertingkat";
    }

    function tambahModalFinishing(
        nama,
        jumlah,
        finishingId = null,
        keterangan = "",
        isMetric = false,
        panjangLocked = false,
        lebarLocked = false,
        metricUnit = "-",
        panjangFinishing = 0,
        lebarFinishing = 0,
        satuan = "-",
        hargaBertingkatJson = [],
        hargaResellerJson = [],
    ) {
        // const isDuplicate = modalFinishingData.some(item =>
        //     (finishingId && item.finishing_id === finishingId) ||
        //     (!finishingId && item.nama === nama)
        // );

        // if (isDuplicate) {
        //     if (window.SPKHelper && typeof SPKHelper.showNotification === 'function') {
        //         SPKHelper.showNotification('Item sudah ada', 'Finishing ini sudah ditambahkan ke daftar.', 'warning');
        //     } else {
        //         alert('Finishing ini sudah ditambahkan ke daftar.');
        //     }
        //     return;
        // }

        const dimensiValid = panjangFinishing > 0 && lebarFinishing > 0;
        const luas = dimensiValid ? panjangFinishing * lebarFinishing : 0;

        const qty = parseFloat(jumlah) || 0;
        const hargaJual = getHargaJualFinishing(
            {
                harga_bertingkat_json: hargaBertingkatJson,
                harga_reseller_json: hargaResellerJson,
            },
            qty,
        );

        let total;
        if (isMetric && dimensiValid) {
            total = qty * hargaJual * luas;
        } else {
            total = qty * hargaJual;
        }

        modalFinishingData.push({
            finishing_id: finishingId,
            nama: nama,
            jumlah: parseFloat(jumlah) || 1,
            panjang: panjangFinishing || 0,
            lebar: lebarFinishing || 0,
            total: total,
            keterangan: keterangan,
            is_metric: isMetric,
            panjang_locked: panjangLocked,
            lebar_locked: lebarLocked,
            panjang: panjangFinishing || 0,
            lebar: lebarFinishing || 0,
            metric_unit: metricUnit,
            satuan: satuan,
            harga_bertingkat_json: hargaBertingkatJson,
            harga_reseller_json: hargaResellerJson,
        });

        const newItemIndex = modalFinishingData.length - 1;
        currentExpandedFinishingIndex = newItemIndex;
        renderModalFinishingAccordion();
        updateModalFinishingTotal();

        if (
            window.SPKHelper &&
            typeof SPKHelper.showNotification === "function"
        ) {
            SPKHelper.showNotification(
                "Berhasil",
                "Finishing berhasil ditambahkan.",
                "success",
            );
        }
    }

    function hapusModalFinishing(index) {
        if (index < 0 || index >= modalFinishingData.length) {
            return;
        }

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus finishing ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                const itemName = modalFinishingData[index].nama;
                modalFinishingData.splice(index, 1);
                renderModalFinishingAccordion();

                if (
                    window.SPKHelper &&
                    typeof SPKHelper.showNotification === "function"
                ) {
                    SPKHelper.showNotification(
                        "Berhasil",
                        `Finishing "${itemName}" berhasil dihapus.`,
                        "success",
                    );
                }
            }
        });
    }
    // --- File Explorer Logic ---
    function openExplorer(context) {
        explorerContext = context;
        selectedExplorerFiles = [];
        updateExplorerSelectionUI();
        const modalEl = document.getElementById("modalFileExplorer");
        if (!modalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        loadExplorerPath("");
    }

    async function loadExplorerPath(path) {
        const contentArea = document.getElementById("explorerContent");
        const pathInput = document.getElementById("inputExplorerPath");

        contentArea.innerHTML = `
            <div class="p-4 text-center text-muted">
                <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                <p>Memuat direktori...</p>
            </div>
        `;

        try {
            const response = await fetch(
                `/backend/file-explorer?path=${encodeURIComponent(path)}`,
            );
            const data = await response.json();

            if (data.success) {
                currentExplorerPath = data.current_path;
                pathInput.value = data.current_path;
                renderExplorer(data);
            } else {
                contentArea.innerHTML = `<div class="p-4 text-center text-danger">Error: ${data.message}</div>`;
            }
        } catch (error) {
            contentArea.innerHTML = `<div class="p-4 text-center text-danger">Gagal menghubungi server</div>`;
        }
    }

    function renderExplorer(data) {
        const contentArea = document.getElementById("explorerContent");
        contentArea.innerHTML = "";

        // Add directories
        data.directories.forEach((dir) => {
            const item = document.createElement("div");
            item.className = "explorer-item";
            item.innerHTML = `
                <div class="icon"><i class="fa fa-folder folder-icon"></i></div>
                <div class="name">${dir.name}</div>
                <div class="meta">Folder</div>
            `;
            item.onclick = () => loadExplorerPath(dir.path);
            contentArea.appendChild(item);
        });

        // Add files
        data.files.forEach((file) => {
            const item = document.createElement("div");
            const isSelected = selectedExplorerFiles.some(
                (f) => f.path === file.path,
            );
            item.className = `explorer-item ${isSelected ? "bg-primary bg-opacity-10" : ""}`;
            item.innerHTML = `
                <div class="form-check me-2 pointer-events-none">
                    <input class="form-check-input" type="checkbox" ${isSelected ? "checked" : ""}>
                </div>
                <div class="icon"><i class="fa fa-file file-icon"></i></div>
                <div class="name">${file.name}</div>
                <div class="meta">${formatBytes(file.size)}</div>
            `;
            // Toggle selection on click
            item.onclick = (e) => {
                // Prevent double toggling if clicking directly on checkbox (default behavior)
                if (e.target.type !== "checkbox") {
                    toggleFileSelection(file, item);
                }
            };
            // Handle checkbox click specifically
            const checkbox = item.querySelector('input[type="checkbox"]');
            checkbox.onclick = (e) => {
                e.stopPropagation();
                toggleFileSelection(file, item);
            };

            contentArea.appendChild(item);
        });

        if (data.directories.length === 0 && data.files.length === 0) {
            contentArea.innerHTML =
                '<div class="p-4 text-center text-muted">Folder kosong</div>';
        }
    }

    function toggleFileSelection(file, itemElement) {
        const index = selectedExplorerFiles.findIndex(
            (f) => f.path === file.path,
        );
        if (index === -1) {
            selectedExplorerFiles.push(file);
            if (itemElement) {
                itemElement.classList.add("bg-primary", "bg-opacity-10");
                itemElement.querySelector('input[type="checkbox"]').checked =
                    true;
            }
        } else {
            selectedExplorerFiles.splice(index, 1);
            if (itemElement) {
                itemElement.classList.remove("bg-primary", "bg-opacity-10");
                itemElement.querySelector('input[type="checkbox"]').checked =
                    false;
            }
        }
        updateExplorerSelectionUI();
    }

    function updateExplorerSelectionUI() {
        const count = selectedExplorerFiles.length;
        const countEl = document.getElementById("selectedFileCount");
        const btn = document.getElementById("btnPilihFileExplorer");

        if (countEl) countEl.textContent = count;
        if (btn) btn.disabled = count === 0;
    }

    // Initialize "Pilih File" button listener
    const btnPilihFile = document.getElementById("btnPilihFileExplorer");
    if (btnPilihFile) {
        btnPilihFile.onclick = () => {
            selectedExplorerFiles.forEach((file) => {
                if (explorerContext === "spk") {
                    addFileToSPK(file);
                } else {
                    addFileToItem(file);
                }
            });
            // Close modal
            const explorerModal = document.getElementById("modalFileExplorer");
            const instance = explorerModal
                ? bootstrap.Modal.getInstance(explorerModal)
                : null;
            if (instance) instance.hide();
            // Reset selection
            selectedExplorerFiles = [];
            updateExplorerSelectionUI();
        };
    }

    // Keep selectFile for single click if needed? No, we are moving to multi-select.
    // function selectFile(file) { ... } // Removed or deprecated

    function addFileToSPK(file) {
        const exists = filePendukungData.some((f) => f.path === file.path);
        if (exists) {
            SafeHelper.notify(
                "warning",
                "Peringatan",
                "File sudah ada di daftar",
            );
            return;
        }

        filePendukungData.push({
            name: file.name,
            path: file.path,
            size: file.size,
            type: file.extension || "file",
            source: "local",
        });
        renderFilePendukungTable();
    }

    function addFileToItem(file) {
        const exists = modalUploadedFiles.some((f) => f.path === file.path);
        if (exists) {
            SafeHelper.notify(
                "warning",
                "Peringatan",
                "File sudah ada di daftar",
            );
            return;
        }

        modalUploadedFiles.push({
            name: file.name,
            path: file.path,
            size: file.size,
            type: file.extension || "file",
            source: "local",
        });
        renderModalUploadedFiles();
        updateModalSummary();
        renderOrderanPreviewTab();
        updateImageToolsPreviews();
    }

    // Attach Explorer Events
    document.addEventListener("click", (e) => {
        if (e.target.closest("#btnOpenExplorerModalItem")) {
            e.preventDefault();
            openExplorer("item");
            return;
        }

        if (e.target.closest("#btnOpenExplorerSPK")) {
            e.preventDefault();
            openExplorer("spk");
        } else if (e.target.closest('[data-action="open-explorer-item"]')) {
            e.preventDefault();
            openExplorer("item");
            return;
        } else if (e.target.closest("#btnExplorerBack")) {
            e.preventDefault();
            const parent = currentExplorerPath.substring(
                0,
                currentExplorerPath.lastIndexOf("/"),
            );
            loadExplorerPath(parent || "");
        }
    });

    function formatPdfDate(pdfDate) {
        if (!pdfDate || typeof pdfDate !== "string") return "-";
        const m = pdfDate.match(
            /^D:(\d{4})(\d{2})(\d{2})(\d{2})?(\d{2})?(\d{2})?/,
        );
        if (!m) return pdfDate;
        const [, y, mo, d, h, min, s] = m;
        const date = new Date(
            Date.UTC(
                parseInt(y, 10),
                parseInt(mo, 10) - 1,
                parseInt(d, 10),
                parseInt(h || "0", 10),
                parseInt(min || "0", 10),
                parseInt(s || "0", 10),
            ),
        );
        if (isNaN(date.getTime())) return pdfDate;
        return date.toLocaleString("id-ID", {
            dateStyle: "medium",
            timeStyle: "short",
        });
    }

    function resetOrderanPreviewState() {
        if (orderanPreviewObjectUrl) {
            URL.revokeObjectURL(orderanPreviewObjectUrl);
            orderanPreviewObjectUrl = null;
        }
        const infoEl = document.getElementById("orderanPreviewFileInfo");
        if (infoEl) infoEl.innerHTML = "";
    }

    function resetModalJumlahInput() {
        const modalJumlahInput = document.getElementById("modalJumlahInput");
        if (modalJumlahInput) {
            modalJumlahInput.readOnly = false;
            modalJumlahInput.style.backgroundColor = "";
        }
    }

    function getOutputPathForPreview(currentPath) {
        if (!currentPath || typeof currentPath !== "string") return "";
        console.log(currentPath);
        return currentPath.replace(/(\.[^/.]+)$/, "_output$1");
    }

    function checkOutputFileExists(outputPath) {
        if (!outputPath) return Promise.resolve(false);
        console.log(outputPath);
        return fetch(`/backend/file-exists?path=${encodeURIComponent(outputPath)}`)
            .then((r) => r.json())
            .then((data) => data.success && data.exists === true)
            .catch(() => false);
    }

    function renderOrderanPreviewTab() {
        updateImageToolsPreviews();
        loadQuickTemplatesIntoSelect();
        const container = document.getElementById("orderanPreviewContainer");
        if (!container) return;

        const defaultFile = modalUploadedFiles[0];

        if (!defaultFile) {
            imageRotationDegrees = 0;
            resetOrderanPreviewState();
            hideImageControls();
            hidePdfControls();
            resetModalJumlahInput();
            if (orderanPreviewObjectUrl) {
                URL.revokeObjectURL(orderanPreviewObjectUrl);
                orderanPreviewObjectUrl = null;
            }
            const infoEl = document.getElementById("orderanPreviewFileInfo");
            if (infoEl) infoEl.innerHTML = "";
            container.innerHTML =
                '<p class="text-muted small mb-0 text-center">Belum ada file default. Upload file dan set sebagai default untuk melihat preview.</p>';
            return;
        }

        const type = (defaultFile.type || "").toLowerCase();
        const ext = (defaultFile.name || "").split(".").pop().toLowerCase();
        const imageExts = ["jpg", "jpeg", "png", "gif", "webp", "bmp"];
        const isImage = type.includes("image") || imageExts.includes(ext);
        const isPdf = type.includes("pdf") || ext === "pdf";

        if (!isImage && !isPdf) {
            resetOrderanPreviewState();
            hideImageControls();
            hidePdfControls();
            resetModalJumlahInput();
            container.innerHTML =
                '<p class="text-muted small mb-0 text-center">Preview hanya tersedia untuk file gambar dan PDF.</p>';
            return;
        }

        if (orderanPreviewObjectUrl) {
            URL.revokeObjectURL(orderanPreviewObjectUrl);
            orderanPreviewObjectUrl = null;
        }

        let url = null;

        if (
            defaultFile.source === "local" &&
            defaultFile.file instanceof File
        ) {
            url = URL.createObjectURL(defaultFile.file);
            orderanPreviewObjectUrl = url;
        } else {
            const path = defaultFile.path || defaultFile.sourcePath || "";
            if (
                path &&
                (path.startsWith("http://") || path.startsWith("https://"))
            ) {
                url = path;
            } else if (path) {
                url = `/backend/preview-file?path=${encodeURIComponent(path)}`;
            }
        }

        if (!url) {
            resetOrderanPreviewState();
            container.innerHTML =
                '<p class="text-muted small mb-0 text-center">File tidak bisa dipreview langsung dari browser.</p>';
            return;
        }

        if (isImage) {
            const path = defaultFile.path || defaultFile.sourcePath || "";
            const hasServerPath =
                path &&
                !path.startsWith("http") &&
                !(defaultFile.file instanceof File);
            container.innerHTML = `
                <div class="w-100 text-center">
                    <img id="previewImage" src="${url}" style="max-height: 300px; max-width: 80%; width: auto; transform: rotate(${imageRotationDegrees}deg); transform-origin: center; transition: transform .15s ease;" class="img-fluid rounded" alt="${defaultFile.name || ""}  ">
                </div>
            `;
            const infoEl = document.getElementById("orderanPreviewFileInfo");
            if (infoEl) {
                infoEl.innerHTML = "";
                if (hasServerPath) {
                    infoEl.innerHTML =
                        '<span class="text-muted">Memuat info file...</span>';
                    fetch(
                        `/backend/file-image-info?path=${encodeURIComponent(path)}`,
                    )
                        .then((r) => r.json())
                        .then((json) => {
                            if (!json.success || !json.data) {
                                infoEl.innerHTML =
                                    '<span class="text-muted">Info file tidak tersedia</span>';
                                return;
                            }
                            const d = json.data;
                            currentFileImageInfo = d;
                            updateImageControls(d);
                            showImageControls();
                            resetModalJumlahInput();

                            const outputPath = getOutputPathForPreview(path);
                            checkOutputFileExists(outputPath).then((hasOutput) => {
                                const outputFileLabel = hasOutput
                                    ? `Yes <button type="button" class="btn btn-link btn-sm p-0 ms-1 align-baseline" id="btnPreviewOutputFile" data-output-path="${outputPath.replace(/"/g, '&quot;')}" title="Preview file _output"><i class="fas fa-external-link-alt"></i></button>`
                                    : "No";

                                infoEl.innerHTML = `
                                    <div class="card border-light shadow-sm p-3 mb-3">
                                        <div class="row small text-dark">
                                            <div class="col-6 mb-2"><strong>DPI:</strong> ${d.dpi ? d.dpi.split("x")[0] : "-"}</div>
                                            <div class="col-6 mb-2"><strong>Ukuran file:</strong> ${
                                                d.size_mb != null
                                                    ? d.size_mb + " MB"
                                                    : d.size_bytes != null
                                                        ? (d.size_bytes / 1024).toFixed(1) + " KB"
                                                        : "-"
                                            }</div>
                                            <div class="col-6 mb-2"><strong>Color mode:</strong> ${d.color_mode || "-"}</div>
                                            <div class="col-6 mb-2"><strong>Output File:</strong> ${outputFileLabel}</div>
                                        </div>
                                    </div>
                                `;

                                const btnOutput = document.getElementById("btnPreviewOutputFile");
                                if (btnOutput) {
                                    btnOutput.addEventListener("click", function (ev) {
                                        ev.preventDefault();
                                        const outPath = btnOutput.getAttribute("data-output-path");
                                        if (!outPath) return;
                                        const previewUrl = `/backend/preview-file?path=${encodeURIComponent(outPath)}`;
                                        window.open(previewUrl, "_blank", "noopener,noreferrer");
                                    });
                                }
                            });
                        })
                        .catch(() => {
                            infoEl.innerHTML =
                                '<span class="text-muted">Gagal memuat info file</span>';
                            hideImageControls();
                        });
                } else {
                    hideImageControls();
                    resetModalJumlahInput();
                }
            }
        } else if (isPdf) {
            const path = defaultFile.path || defaultFile.sourcePath || "";
            const hasServerPath =
                path &&
                !path.startsWith("http") &&
                !(defaultFile.file instanceof File);

            container.innerHTML = `
                <iframe src="${url}#toolbar=0" class="w-100 rounded" style="height: 360px;" frameborder="0"></iframe>
            `;

            const infoEl = document.getElementById("orderanPreviewFileInfo");
            if (infoEl) {
                infoEl.innerHTML = "";
                if (hasServerPath) {
                    infoEl.innerHTML =
                        '<span class="text-muted">Memuat info PDF...</span>';
                    fetch(
                        `/backend/file-pdf-info?path=${encodeURIComponent(path)}`,
                    )
                        .then((r) => r.json())
                        .then((json) => {
                            if (!json.success || !json.data) {
                                infoEl.innerHTML =
                                    '<span class="text-muted">Info PDF tidak tersedia</span>';
                                hidePdfControls();
                                return;
                            }
                            const d = json.data;
                            currentFilePdfInfo = d;
                            updatePdfControls(d);
                            showPdfControls();

                            const modalJumlahInput =
                                document.getElementById("modalJumlahInput");
                            if (modalJumlahInput) {
                                modalJumlahInput.readOnly = true;
                                modalJumlahInput.style.backgroundColor =
                                    "#e9ecef";
                            }

                            infoEl.innerHTML = `
                                <div class="card border-light shadow-sm p-3 mb-3 mt-2">
                                    <div class="row small text-dark">
                                        <div class="col-6 mb-2"><strong>Halaman:</strong> ${d.num_pages != null ? d.num_pages : "-"}</div>
                                        <div class="col-6 mb-2"><strong>Ukuran halaman:</strong> ${d.size_summary || "-"}</div>
                                        <div class="col-6 mb-2"><strong>Ukuran file:</strong> ${d.file_size_mb != null ? d.file_size_mb + " MB" : d.file_size_bytes != null ? (d.file_size_bytes / 1024).toFixed(1) + " KB" : "-"}</div>
                                        <div class="col-6 mb-2"><strong>Color mode:</strong> ${d.color_mode || "-"}</div>
                                        <div class="col-6 mb-2"><strong>Judul:</strong> ${d.title || "-"}</div>
                                        <div class="col-6 mb-2"><strong>Size Check:</strong><span class="${d.size_check === true ? "text-success" : d.size_check === false ? "text-danger" : "text-muted"}"> ${d.size_check != null ? d.size_check : "-"}</span></div>
                                        <div class="col-6 mb-2"><strong>Tanggal buat:</strong> ${formatPdfDate(d.creation_date)}</div>
                                    </div>
                                </div>
                            `;
                        })
                        .catch(() => {
                            infoEl.innerHTML =
                                '<span class="text-muted">Gagal memuat info PDF</span>';
                            hidePdfControls();
                        });
                } else {
                    hidePdfControls();
                }
            }
        }
    }

    function showImageControls() {
        const controls = document.getElementById("imageFileControls");
        if (controls) controls.style.display = "block";
        const pdfControls = document.getElementById("pdfFileControls");
        if (pdfControls) pdfControls.style.display = "none";
        const standalone = document.getElementById("explorerOpenStandalone");
        if (standalone) standalone.style.display = "none";

        const shouldShowMetricButtons = isProdukMetricAktif();

        const btnRotate = document.getElementById("btnRotateImage");
        const btnSync = document.getElementById("btnSyncImageDimensions");

        if (btnRotate)
            btnRotate.style.display = shouldShowMetricButtons ? "" : "none";
        if (btnSync)
            btnSync.style.display = shouldShowMetricButtons ? "" : "none";
        updateFileImageControlUnits();
    }

    function hideImageControls() {
        const controls = document.getElementById("imageFileControls");
        if (controls) controls.style.display = "none";
        currentFileImageInfo = null;
        const standalone = document.getElementById("explorerOpenStandalone");
        if (standalone) standalone.style.display = "";
        resetModalJumlahInput();
        updateSyncButtonState();
    }

    function updateImageControls(info) {
        const widthCm = parseFloat(info.width_cm) || 0;
        const heightCm = parseFloat(info.height_cm) || 0;
        const targetUnit = (currentMetricUnit || "cm").toLowerCase();
        const factor = getConversionFactor("cm", targetUnit);
        const width = widthCm * factor;
        const height = heightCm * factor;
        const area = width * height;

        document.getElementById("fileImageWidth").value = width.toFixed(2);
        document.getElementById("fileImageHeight").value = height.toFixed(2);
        document.getElementById("fileImageArea").value = area.toFixed(2);

        updateSyncButtonState();
        updateFileImageControlUnits();
    }

    function rotateImage() {
        if (!currentFileImageInfo) return;

        imageRotationDegrees = imageRotationDegrees === 90 ? 0 : 90;
        const img = document.getElementById("previewImage");
        if (img) {
            img.style.transform = `rotate(${imageRotationDegrees}deg)`;
        }

        // Tukar panjang dan lebar
        const temp = currentFileImageInfo.width_cm;
        currentFileImageInfo.width_cm = currentFileImageInfo.height_cm;
        currentFileImageInfo.height_cm = temp;

        updateImageControls(currentFileImageInfo);
        updateSyncButtonState();
    }

    function syncImageDimensions() {
        const widthEl = document.getElementById("fileImageWidth");
        const heightEl = document.getElementById("fileImageHeight");
        const panjangInput = document.getElementById("modalPanjangInput");
        const lebarInput = document.getElementById("modalLebarInput");
        const panjangStatus = document.getElementById("panjangStatus");
        const lebarStatus = document.getElementById("lebarStatus");

        if (!widthEl || !heightEl || !panjangInput || !lebarInput) return;

        const widthInUnit = parseFloat(widthEl.value) || 0;
        const heightInUnit = parseFloat(heightEl.value) || 0;

        const panjangLocked = panjangStatus?.style.display === "block";
        const lebarLocked = lebarStatus?.style.display === "block";

        if (!panjangLocked && !lebarLocked) {
            panjangInput.value = heightInUnit.toFixed(2);
            lebarInput.value = widthInUnit.toFixed(2);
        } else if (panjangLocked && !lebarLocked) {
            lebarInput.value = widthInUnit.toFixed(2);
        } else if (!panjangLocked && lebarLocked) {
            panjangInput.value = heightInUnit.toFixed(2);
        }

        if (typeof updateLuas === "function") updateLuas();
        if (typeof updateModalSummary === "function") updateModalSummary();
    }

    function getConversionFactor(fromUnit, toUnit) {
        const conversions = {
            cm: { m: 0.01, mm: 10, inch: 0.393701, cm: 1 },
            m: { cm: 100, mm: 1000, inch: 39.3701, m: 1 },
            mm: { cm: 0.1, m: 0.001, inch: 0.0393701, mm: 1 },
            inch: { cm: 2.54, m: 0.0254, mm: 25.4, inch: 1 },
        };
        return conversions[fromUnit]?.[toUnit] || 1;
    }

    function updateSyncButtonState() {
        const btnSync = document.getElementById("btnSyncImageDimensions");
        if (!btnSync) return;

        btnSync.style.cursor = "pointer";

        // harus ada info file gambar
        if (!currentFileImageInfo) {
            btnSync.disabled = true;
            btnSync.style.cursor = "not-allowed";
            return;
        }

        const panjangLocked =
            document.getElementById("panjangStatus")?.style.display === "block";
        const lebarLocked =
            document.getElementById("lebarStatus")?.style.display === "block";

        // dua-duanya locked => disable
        if (panjangLocked && lebarLocked) {
            btnSync.disabled = true;
            btnSync.style.cursor = "not-allowed";
            return;
        }

        const widthEl = document.getElementById("fileImageWidth");
        const heightEl = document.getElementById("fileImageHeight");
        const fileLebar = parseFloat(widthEl?.value) || 0;
        const filePanjang = parseFloat(heightEl?.value) || 0;

        const inputLebarEl = document.getElementById("modalLebarInput");
        const inputPanjangEl = document.getElementById("modalPanjangInput");

        const inputLebar = parseFloat(inputLebarEl?.value);
        const inputPanjang = parseFloat(inputPanjangEl?.value);

        if (lebarLocked) {
            if (
                !Number.isFinite(inputLebar) ||
                inputLebar <= fileLebar
            ) {
                btnSync.disabled = true;
                btnSync.style.cursor = "not-allowed";
                return;
            }
        }

        if (panjangLocked) {
            if (
                !Number.isFinite(inputPanjang) ||
                inputPanjang <= filePanjang
            ) {
                btnSync.disabled = true;
                btnSync.style.cursor = "not-allowed";
                return;
            }
        }

        btnSync.disabled = false;
        btnSync.style.cursor = "pointer";
    }

    // Helper functions untuk PDF Controls
    function showPdfControls() {
        const controls = document.getElementById("pdfFileControls");
        if (controls) controls.style.display = "block";
        const imageControls = document.getElementById("imageFileControls");
        if (imageControls) imageControls.style.display = "none";
        const standalone = document.getElementById("explorerOpenStandalone");
        if (standalone) standalone.style.display = "";
    }

    function hidePdfControls() {
        const controls = document.getElementById("pdfFileControls");
        if (controls) controls.style.display = "none";
        currentFilePdfInfo = null;

        // Reset modalJumlahInput menjadi editable kembali
        const modalJumlahInput = document.getElementById("modalJumlahInput");
        if (modalJumlahInput) {
            modalJumlahInput.readOnly = false;
            modalJumlahInput.style.backgroundColor = "";
        }

        const standalone = document.getElementById("explorerOpenStandalone");
        if (standalone) standalone.style.display = "";
    }

    function updatePdfControls(info) {
        const pages = parseInt(info.num_pages) || 0;
        document.getElementById("filePdfPages").value = pages;

        // Set default qty = 1
        const qtyInput = document.getElementById("filePdfQty");
        if (qtyInput && !qtyInput.value) qtyInput.value = 1;

        calculatePdfSummary();
    }

    function calculatePdfSummary() {
        if (!currentFilePdfInfo) return;

        const pages = parseInt(currentFilePdfInfo.num_pages) || 0;
        const qtyInput = document.getElementById("filePdfQty");
        const qty = parseInt(qtyInput?.value) || 1;
        const summary = pages * qty;

        // Ambil satuan dari produk yang dipilih
        const satuanDisplay = document.getElementById("modalSatuanDisplay");
        const satuan = satuanDisplay?.textContent || "-";

        document.getElementById("filePdfSummary").value = summary;
        document.getElementById("filePdfSummaryUnit").textContent = satuan;

        // Update modalJumlahInput (readonly untuk PDF)
        const modalJumlahInput = document.getElementById("modalJumlahInput");
        if (modalJumlahInput) {
            modalJumlahInput.value = summary;
        }
    }

    function isProdukMetricAktif() {
        return (
            currentSelectedProduk &&
            (currentSelectedProduk.is_metric === true ||
                currentSelectedProduk.is_metric === "true")
        );
    }

    function updateFileImageControlUnits() {
        const unit = (currentMetricUnit || "cm").toLowerCase();
        const widthUnitEl = document.getElementById("fileImageWidthUnit");
        const heightUnitEl = document.getElementById("fileImageHeightUnit");
        const areaUnitEl = document.getElementById("fileImageAreaUnit");
        if (widthUnitEl) widthUnitEl.textContent = unit;
        if (heightUnitEl) heightUnitEl.textContent = unit;
        if (areaUnitEl) areaUnitEl.textContent = unit;
    }

    document.getElementById('btnOpenFolderLocation')?.addEventListener('click', async function() {
        const defaultFile = modalUploadedFiles[0];
        if (!defaultFile) {
            alert('Tidak ada file default yang dipilih.');
            return;
        }

        const filePath = defaultFile.path || defaultFile.sourcePath || "";
        if (!filePath) {
            alert('File tidak memiliki path yang valid.');
            return;
        }

        try {
            const response = await fetch(`/backend/open-folder-location?path=${encodeURIComponent(filePath)}`);
            const data = await response.json();
            
            if (data.success) {
                console.log('Folder dibuka di Windows Explorer');
            } else {
                alert('Gagal membuka folder: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membuka folder.');
        }
    });
})();

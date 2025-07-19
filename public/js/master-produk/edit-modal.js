$(function() {
  // Variabel media khusus edit
  let existingPhotos = [], existingVideos = [], existingDocuments = [];
  let selectedPhotos = [], selectedVideos = [], selectedDocuments = [];
  let deletedPhotoIndexes = [], deletedVideoIndexes = [], deletedDocumentIndexes = [];

  // === BAHAN BAKU (EDIT) ===
  let editBahanBakuList = [];

  function renderEditTabelBahanBaku() {
    const tbody = $('#editTabelBahanBaku tbody');
    tbody.empty();
    if (!editBahanBakuList || editBahanBakuList.length === 0) {
      tbody.append('<tr class="text-muted"><td colspan="6" class="text-center">Belum ada bahan baku</td></tr>');
      hitungTotalModalBahanEdit();
      return;
    }
    editBahanBakuList.forEach((row, idx) => {
      tbody.append(`
        <tr>
          <td>${row.nama || ''}<input type="hidden" name="bahan_baku_id[]" value="${row.id || ''}"></td>
          <td>${row.satuan || ''}</td>
          <td class="text-end">Rp ${(row.harga || 0).toLocaleString('id-ID')}<input type="hidden" name="harga_bahan[]" value="${row.harga || 0}"></td>
          <td><input type="number" class="form-control form-control-sm jumlah_bahan_edit" name="jumlah_bahan[]" value="${row.jumlah || 0}" min="0" data-idx="${idx}"></td>
          <td class="text-success fw-semibold text-end">Rp ${(row.total || 0).toLocaleString('id-ID')}</td>
          <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-edit-bahan-baku" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
        </tr>
      `);
    });
    feather.replace();
    hitungTotalModalBahanEdit();
  }

  // Handler tombol edit produk
  $(document).on('click', '.btn-edit-produk', function() {
    const id = $(this).data('id');
    $.get('/backend/master-produk/' + id + '/edit', function(res) {
      if(res.success) {
        const p = res.produk;
        $('#edit_produk_id').val(p.id);
        $('#edit_nama_produk').val(p.nama_produk);
        $('#edit_kode_produk').val(p.kode_produk);
        $('#edit_kategori_utama').val(p.kategori_utama_id).trigger('change');
        updateEditSubKategoriOptions(p.kategori_utama_id, p.sub_kategori_id);
        $('#edit_satuanBarang').val(p.satuan_id);
        $("input[name='metode_penjualan'][value='"+p.metode_penjualan+"']").prop('checked', true);
        $('#edit_lebar').val(p.lebar);
        $('#edit_panjang').val(p.panjang);
        $('#edit_status_aktif').prop('checked', !!p.status_aktif);
        $('#edit_bahan_baku_json').val(JSON.stringify(p.bahan_baku_json || []));
        $('#edit_harga_bertingkat_json').val(JSON.stringify(p.harga_bertingkat_json || []));
        $('#edit_harga_reseller_json').val(JSON.stringify(p.harga_reseller_json || []));
        $('#edit_foto_pendukung_json').val(JSON.stringify(p.foto_pendukung_json || []));
        $('#edit_video_pendukung_json').val(JSON.stringify(p.video_pendukung_json || []));
        $('#edit_dokumen_pendukung_json').val(JSON.stringify(p.dokumen_pendukung_json || []));
        $('#edit_alur_produksi_json').val(JSON.stringify(p.alur_produksi_json || []));
        existingPhotos = p.foto_pendukung_json ? [...p.foto_pendukung_json] : [];
        existingVideos = p.video_pendukung_json ? [...p.video_pendukung_json] : [];
        existingDocuments = p.dokumen_pendukung_json ? [...p.dokumen_pendukung_json] : [];
        selectedPhotos = [];
        selectedVideos = [];
        selectedDocuments = [];
        deletedPhotoIndexes = [];
        deletedVideoIndexes = [];
        deletedDocumentIndexes = [];
        renderPhotosPreview();
        renderVideosPreview();
        renderDocumentsPreview();
        editBahanBakuList = Array.isArray(p.bahan_baku_json) ? p.bahan_baku_json.map(row => ({
          id: row.id,
          nama: row.nama,
          satuan: row.satuan,
          harga: row.harga,
          jumlah: row.jumlah,
          total: row.total
        })) : [];
        renderEditTabelBahanBaku();
        hitungTotalModalBahanEdit();
        editHargaBertingkatList = Array.isArray(p.harga_bertingkat_json) ? p.harga_bertingkat_json : [];
        editHargaResellerList = Array.isArray(p.harga_reseller_json) ? p.harga_reseller_json : [];
        renderEditHargaBertingkat();
        renderEditHargaReseller();
        editAlurProduksiList = Array.isArray(p.alur_produksi_json) ? p.alur_produksi_json.map(row => ({
          nama_mesin: row.nama_mesin,
          tipe_mesin: row.tipe_mesin,
          estimasi_waktu: row.estimasi_waktu,
          catatan: row.catatan
        })) : [];
        renderEditAlurProduksi();
        $('#editProdukModal').modal('show');
      } else {
        Swal.fire('Gagal', 'Data produk tidak ditemukan', 'error');
      }
    });
  });

  $(document).on('change', '#edit_kategori_utama', function() {
    const selectedKategoriId = $(this).val();
    updateEditSubKategoriOptions(selectedKategoriId);
  });
  $('#editProdukModal').on('shown.bs.modal', function () {
    const selectedKategoriOnShow = $('#edit_kategori_utama').val();
    const currentSubKategoriOnShow = $('#edit_sub_kategori_id_produk').val();
    updateEditSubKategoriOptions(selectedKategoriOnShow, currentSubKategoriOnShow);
  });

  // === MEDIA & DOKUMEN (EDIT) ===
  // (Sudah clean, tidak ada kode tidak terpakai)

  // Fungsi render preview media/dokumen (khusus edit)
  function renderPhotosPreview() {
    const previewContainer = $('#editFotoPendukungPreview');
    previewContainer.empty();
    // Foto lama
    existingPhotos.forEach((url, idx) => {
      if (!deletedPhotoIndexes.includes(idx)) {
        previewContainer.append(`
          <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
            <div class="card border-0 preview-card">
              <img src="${url}" class="card-img-top img-fluid rounded" style="height: 100px; object-fit: cover;" alt="Foto lama">
              <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-photo-existing" data-index="${idx}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                <i data-feather="x" class="icon-sm"></i>
              </button>
            </div>
          </div>
        `);
      }
    });
    // Foto baru
    selectedPhotos.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function(e) {
        const previewItem = `
          <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
            <div class="card border-0 preview-card">
              <img src="${e.target.result}" class="card-img-top img-fluid rounded" style="height: 100px; object-fit: cover;" alt="${file.name}">
              <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-photo" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                <i data-feather="x" class="icon-sm"></i>
              </button>
              <div class="card-body p-1 text-truncate" style="font-size: 0.75rem;">
                <span class="badge bg-primary">Baru</span> ${file.name}
              </div>
            </div>
          </div>
        `;
        previewContainer.append(previewItem);
        feather.replace();
      };
      reader.readAsDataURL(file);
    });
    if (existingPhotos.filter((_, idx) => !deletedPhotoIndexes.includes(idx)).length === 0 && selectedPhotos.length === 0) {
      previewContainer.append(`
        <div class="col-12 text-center text-muted" id="noEditFotoMessage">
            <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang ditambahkan.
        </div>
      `);
      feather.replace();
    }
  }
  function renderVideosPreview() {
    const previewContainer = $('#editVideoPendukungPreview');
    previewContainer.empty();
    // Video lama
    existingVideos.forEach((url, idx) => {
      if (!deletedVideoIndexes.includes(idx)) {
        previewContainer.append(`
          <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
            <div class="card border-0 preview-card d-flex flex-column align-items-center justify-content-center p-2" style="height: 100px; overflow: hidden;">
              <i data-feather="video" class="icon-lg text-primary mb-1"></i>
              <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-video-existing" data-index="${idx}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
                <i data-feather="x" class="icon-sm"></i>
              </button>
            </div>
          </div>
        `);
      }
    });
    // Video baru
    selectedVideos.forEach((file, index) => {
      const previewItem = `
        <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
          <div class="card border-0 preview-card d-flex flex-column align-items-center justify-content-center p-2" style="height: 100px; overflow: hidden;">
            <i data-feather="video" class="icon-lg text-primary mb-1"></i>
            <div class="text-truncate w-100 text-center" style="font-size: 0.75rem;"><span class="badge bg-primary">Baru</span> ${file.name}</div>
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-video" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
              <i data-feather="x" class="icon-sm"></i>
            </button>
          </div>
        </div>
      `;
      previewContainer.append(previewItem);
      feather.replace();
    });
    if (existingVideos.filter((_, idx) => !deletedVideoIndexes.includes(idx)).length === 0 && selectedVideos.length === 0) {
      previewContainer.append(`
        <div class="col-12 text-center text-muted" id="noEditVideoMessage">
            <i data-feather="video" class="icon-lg mb-2"></i><br>Belum ada video yang ditambahkan.
        </div>
      `);
      feather.replace();
    }
  }
  function renderDocumentsPreview() {
    const previewContainer = $('#editDokumenPendukungBody');
    previewContainer.empty();
    // Dokumen lama
    existingDocuments.forEach((doc, idx) => {
      if (!deletedDocumentIndexes.includes(idx)) {
        previewContainer.append(`
          <tr>
            <td>${doc.nama || '-'}</td>
            <td>${doc.jenis || '-'}</td>
            <td>${doc.ukuran ? (doc.ukuran/1024).toFixed(1) + ' KB' : '-'}</td>
            <td><button type="button" class="btn btn-danger btn-sm delete-document-existing" data-index="${idx}"><i data-feather="trash-2" class="icon-sm"></i></button></td>
          </tr>
        `);
      }
    });
    // Dokumen baru
    selectedDocuments.forEach((file, index) => {
      const row = `
        <tr>
          <td>${file.name}</td>
          <td>${file.type}</td>
          <td>${(file.size/1024).toFixed(1)} KB</td>
          <td><button type="button" class="btn btn-danger btn-sm delete-document" data-index="${index}"><i data-feather="trash-2" class="icon-sm"></i></button></td>
        </tr>
      `;
      previewContainer.append(row);
    });
    if (existingDocuments.filter((_, idx) => !deletedDocumentIndexes.includes(idx)).length === 0 && selectedDocuments.length === 0) {
      previewContainer.append(`
        <tr id="noEditDokumenMessage">
            <td colspan="4" class="text-center text-muted py-4">
                <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
            </td>
        </tr>
      `);
    }
    feather.replace();
  }

  // === HARGA BERTINGKAT & RESELLER (EDIT) ===
  let editHargaBertingkatList = [];
  let editHargaResellerList = [];

  function formatRupiahInput(value) {
    value = value.replace(/[^\d]/g, '');
    if (!value) return '';
    return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
  $(document).on('input', '.money-format', function() {
    const input = $(this);
    let value = input.val().replace(/[^\d]/g, '');
    if (!value) {
      input.val('');
      return;
    }
    const formatted = formatRupiahInput(value);
    input.val(formatted);
    this.setSelectionRange(formatted.length, formatted.length);
  });
  $(document).on('blur', '.money-format', function() {
    const input = $(this);
    let value = input.val().replace(/[^\d]/g, '');
    input.val(formatRupiahInput(value));
  });
  function renderEditHargaBertingkat() {
    const tbody = $('#editTabelHargaBertingkat tbody');
    tbody.empty();
    if (editHargaBertingkatList.length === 0) {
      tbody.append('<tr class="text-muted"><td colspan="6" class="text-center">Belum ada harga bertingkat</td></tr>');
      return;
    }
    editHargaBertingkatList.forEach((row, idx) => {
      const profitRp = row.harga - (parseInt($('#editTotalModalBahan').text().replace(/[^\d]/g, '')) || 0);
      const profitPersen = row.harga > 0 ? ((profitRp / row.harga) * 100).toFixed(1) : 0;
      tbody.append(`
        <tr>
          <td><input type="number" class="form-control form-control-sm min-qty" value="${row.min_qty}" min="1" data-idx="${idx}"></td>
          <td><input type="number" class="form-control form-control-sm max-qty" value="${row.max_qty}" min="1" data-idx="${idx}"></td>
          <td><input type="text" class="form-control form-control-sm harga money-format" value="${row.harga ? formatRupiahInput(row.harga.toString()) : ''}" min="0" data-idx="${idx}"></td>
          <td class="text-success fw-semibold">Rp ${profitRp.toLocaleString('id-ID')}</td>
          <td class="text-success fw-semibold">${profitPersen}%</td>
          <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-edit-harga-bertingkat" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
        </tr>
      `);
    });
    feather.replace();
  }
  function renderEditHargaReseller() {
    const tbody = $('#editTabelHargaReseller tbody');
    tbody.empty();
    if (editHargaResellerList.length === 0) {
      tbody.append('<tr class="text-muted"><td colspan="6" class="text-center">Belum ada harga reseller</td></tr>');
      return;
    }
    editHargaResellerList.forEach((row, idx) => {
      const profitRp = row.harga - (parseInt($('#editTotalModalBahan').text().replace(/[^\d]/g, '')) || 0);
      const profitPersen = row.harga > 0 ? ((profitRp / row.harga) * 100).toFixed(1) : 0;
      tbody.append(`
        <tr>
          <td><input type="number" class="form-control form-control-sm min-qty" value="${row.min_qty}" min="1" data-idx="${idx}"></td>
          <td><input type="number" class="form-control form-control-sm max-qty" value="${row.max_qty}" min="1" data-idx="${idx}"></td>
          <td><input type="text" class="form-control form-control-sm harga money-format" value="${row.harga ? formatRupiahInput(row.harga.toString()) : ''}" min="0" data-idx="${idx}"></td>
          <td class="text-success fw-semibold">Rp ${profitRp.toLocaleString('id-ID')}</td>
          <td class="text-success fw-semibold">${profitPersen}%</td>
          <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-edit-harga-reseller" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
        </tr>
      `);
    });
    feather.replace();
  }
  $('#editBtnTambahHargaBertingkat').off('click').on('click', function() {
    editHargaBertingkatList.push({ min_qty: 1, max_qty: 1, harga: 0 });
    renderEditHargaBertingkat();
  });
  $('#editBtnTambahHargaReseller').off('click').on('click', function() {
    editHargaResellerList.push({ min_qty: 1, max_qty: 1, harga: 0 });
    renderEditHargaReseller();
  });
  $(document).on('click', '.btn-hapus-edit-harga-bertingkat', function() {
    const idx = $(this).data('idx');
    editHargaBertingkatList.splice(idx, 1);
    renderEditHargaBertingkat();
  });
  $(document).on('click', '.btn-hapus-edit-harga-reseller', function() {
    const idx = $(this).data('idx');
    editHargaResellerList.splice(idx, 1);
    renderEditHargaReseller();
  });
  $(document).on('input', '#editTabelHargaBertingkat input', function() {
    const idx = $(this).data('idx');
    const field = $(this).hasClass('min-qty') ? 'min_qty' : $(this).hasClass('max-qty') ? 'max_qty' : 'harga';
    if (field === 'harga') {
      editHargaBertingkatList[idx][field] = parseInt($(this).val().replace(/\./g, '')) || 0;
    } else {
      editHargaBertingkatList[idx][field] = parseInt($(this).val()) || 0;
    }
    updateEditProfitCalculation('#editTabelHargaBertingkat', idx, editHargaBertingkatList[idx]);
  });
  $(document).on('blur', '#editTabelHargaBertingkat input', function() {
    renderEditHargaBertingkat();
  });
  $(document).on('input', '#editTabelHargaReseller input', function() {
    const idx = $(this).data('idx');
    const field = $(this).hasClass('min-qty') ? 'min_qty' : $(this).hasClass('max-qty') ? 'max_qty' : 'harga';
    if (field === 'harga') {
      editHargaResellerList[idx][field] = parseInt($(this).val().replace(/\./g, '')) || 0;
    } else {
      editHargaResellerList[idx][field] = parseInt($(this).val()) || 0;
    }
    updateEditProfitCalculation('#editTabelHargaReseller', idx, editHargaResellerList[idx]);
  });
  $(document).on('blur', '#editTabelHargaReseller input', function() {
    renderEditHargaReseller();
  });
  function updateEditProfitCalculation(tableSelector, idx, rowData) {
    const totalModalBahan = parseInt($('#editTotalModalBahan').text().replace(/[^\d]/g, '')) || 0;
    const profitRp = rowData.harga - totalModalBahan;
    const profitPersen = rowData.harga > 0 ? ((profitRp / rowData.harga) * 100).toFixed(1) : 0;
    const row = $(`${tableSelector} tbody tr`).eq(idx);
    row.find('td').eq(3).html(`<span class="text-success fw-semibold">Rp ${profitRp.toLocaleString('id-ID')}</span>`);
    row.find('td').eq(4).html(`<span class="text-success fw-semibold">${profitPersen}%</span>`);
  }

  // === ALUR PRODUKSI (EDIT) ===
  let editAlurProduksiList = [];
  function mesinTemplateEdit(index = 0, data = {}) {
    return `
      <div class="border rounded mb-3 p-3 position-relative mesin-item" data-index="${index}">
        <button type="button" class="btn btn-link text-danger position-absolute top-0 end-0 mt-2 me-2 btnHapusEditMesin" title="Hapus Mesin"><i data-feather="trash-2"></i></button>
        <div class="mb-2 fw-semibold">Mesin ${index + 1}</div>
        <div class="row mb-2">
          <div class="col-md-6">
            <label class="form-label">Nama Mesin</label>
            <input type="text" class="form-control" name="edit_alur_produksi[${index}][nama_mesin]" value="${data.nama_mesin || ''}" placeholder="Nama mesin" required>
            <small class="text-muted">Tipe: <span>${data.tipe_mesin || 'Tidak diketahui'}</span></small>
          </div>
          <div class="col-md-6">
            <label class="form-label">Estimasi Waktu (menit)</label>
            <input type="number" class="form-control" name="edit_alur_produksi[${index}][estimasi_waktu]" value="${data.estimasi_waktu || ''}" min="0" placeholder="Estimasi waktu" required>
          </div>
        </div>
        <div class="mb-2">
          <label class="form-label">Catatan</label>
          <textarea class="form-control" name="edit_alur_produksi[${index}][catatan]" rows="2" placeholder="Catatan proses">${data.catatan || ''}</textarea>
        </div>
      </div>
    `;
  }
  function renderEditAlurProduksi() {
    const container = $('#editDaftarMesin');
    container.empty();
    if (!editAlurProduksiList || editAlurProduksiList.length === 0) {
      container.append('<div class="text-muted text-center">Belum ada mesin ditambahkan</div>');
      return;
    }
    editAlurProduksiList.forEach((row, idx) => {
      container.append(mesinTemplateEdit(idx, row));
    });
    feather.replace();
  }
  $('#editBtnTambahMesin').off('click').on('click', function() {
    editAlurProduksiList.push({ nama_mesin: '', tipe_mesin: '', estimasi_waktu: '', catatan: '' });
    renderEditAlurProduksi();
  });
  $(document).on('click', '.btnHapusEditMesin', function() {
    const idx = $(this).closest('.mesin-item').data('index');
    editAlurProduksiList.splice(idx, 1);
    renderEditAlurProduksi();
  });

  // Sinkronisasi data alur produksi (edit) secara real-time
  $(document).on('input change', '#editDaftarMesin .mesin-item input, #editDaftarMesin .mesin-item textarea', function() {
    const mesinDiv = $(this).closest('.mesin-item');
    const idx = mesinDiv.data('index');
    if (typeof idx === 'undefined' || !editAlurProduksiList[idx]) return;
    editAlurProduksiList[idx].nama_mesin = mesinDiv.find('input[name*="[nama_mesin]"]').val() || '';
    editAlurProduksiList[idx].estimasi_waktu = parseInt(mesinDiv.find('input[name*="[estimasi_waktu]"]').val()) || 0;
    editAlurProduksiList[idx].catatan = mesinDiv.find('textarea[name*="[catatan]"]').val() || '';
    // Tipe mesin (jika ada span)
    editAlurProduksiList[idx].tipe_mesin = mesinDiv.find('span').text() || '';
  });

  // === MODAL CARI BAHAN BAKU (EDIT) ===
  $('#editBtnTambahBahan').off('click').on('click', function() {
    var modalBahan = new bootstrap.Modal(document.getElementById('modalCariBahanBakuProdukEdit'), {
      backdrop: 'static',
      keyboard: false,
      focus: true
    });
    modalBahan.show();
    setTimeout(function() {
      if ($('#editProdukModal').hasClass('show')) {
        $('body').addClass('modal-open');
      }
    }, 200);
  });
  window.addEventListener('bahanBakuDipilih', function(e) {
    if (!$('#modalCariBahanBakuProdukEdit').hasClass('show')) return;
    const data = e.detail;
    if (editBahanBakuList.some(item => item.id == data.id)) {
      Swal.fire('Info', 'Bahan baku sudah ditambahkan.', 'info');
      return;
    }
    editBahanBakuList.push({
      id: data.id,
      nama: data.nama,
      satuan: data.satuan,
      harga: data.harga || 0,
      jumlah: 1,
      total: data.harga || 0
    });
    renderEditTabelBahanBaku();
    hitungTotalModalBahanEdit();
  });
  $(document).on('click', '.btn-hapus-edit-bahan-baku', function() {
    const idx = $(this).data('idx');
    editBahanBakuList.splice(idx, 1);
    renderEditTabelBahanBaku();
    hitungTotalModalBahanEdit();
  });
  $(document).on('input', '.jumlah_bahan_edit', function() {
    const idx = $(this).data('idx');
    const harga = parseInt($(`input[name="harga_bahan[]"]`).eq(idx).val()) || 0;
    const jumlah = parseInt($(this).val()) || 0;
    editBahanBakuList[idx].harga = harga;
    editBahanBakuList[idx].jumlah = jumlah;
    editBahanBakuList[idx].total = harga * jumlah;
    const total = harga * jumlah;
    $(this).closest('tr').find('td').eq(4).html(`<span class="text-success fw-semibold text-end">Rp ${total.toLocaleString('id-ID')}</span>`);
    hitungTotalModalBahanEdit();
  });
  $(document).on('blur', '.jumlah_bahan_edit', function() {
    renderEditTabelBahanBaku();
  });
  function hitungTotalModalBahanEdit() {
    let total = 0;
    editBahanBakuList.forEach(row => {
      total += (row.harga || 0) * (row.jumlah || 0);
    });
    $('#editTotalModalBahan').text('Rp ' + total.toLocaleString('id-ID'));
    editHargaBertingkatList.forEach((row, idx) => {
      updateEditProfitCalculation('#editTabelHargaBertingkat', idx, row);
    });
    editHargaResellerList.forEach((row, idx) => {
      updateEditProfitCalculation('#editTabelHargaReseller', idx, row);
    });
  }
  $('#editProdukForm').off('submit').on('submit', function(e) {
    e.preventDefault();
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...');
    submitBtn.prop('disabled', true);
    $('#edit_bahan_baku_json').val(JSON.stringify(editBahanBakuList));
    $('#edit_alur_produksi_json').val(JSON.stringify(editAlurProduksiList));
    $('#edit_harga_bertingkat_json').val(JSON.stringify(editHargaBertingkatList));
    $('#edit_harga_reseller_json').val(JSON.stringify(editHargaResellerList));

    // Filter dokumen lama yang tidak dihapus
    const dokumenDipertahankan = existingDocuments.filter((_, idx) => !deletedDocumentIndexes.includes(idx));
    $('#edit_dokumen_pendukung_json').val(JSON.stringify(dokumenDipertahankan));

    var form = $(this)[0];
    var formData = new FormData(form);
    selectedPhotos.forEach(file => {
      formData.append('foto_pendukung_new[]', file);
    });
    selectedVideos.forEach(file => {
      formData.append('video_pendukung_new[]', file);
    });
    selectedDocuments.forEach(file => {
      formData.append('dokumen_pendukung_new[]', file);
    });
    formData.append('deleted_photo_indexes', JSON.stringify(deletedPhotoIndexes));
    formData.append('deleted_video_indexes', JSON.stringify(deletedVideoIndexes));
    formData.append('deleted_document_indexes', JSON.stringify(deletedDocumentIndexes));
    const id = $('#edit_produk_id').val();
    $.ajax({
      url: '/backend/master-produk/' + id,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res) {
        if(res.success) {
          Swal.fire({
            title: 'Sukses',
            text: res.message,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            location.reload();
          });
        } else {
          submitBtn.html(originalText);
          submitBtn.prop('disabled', false);
          Swal.fire({
            title: 'Gagal',
            text: res.message,
            icon: 'error',
            timer: 3000,
            showConfirmButton: false
          });
        }
      },
      error: function(xhr) {
        submitBtn.html(originalText);
        submitBtn.prop('disabled', false);
        let msg = 'Terjadi kesalahan.';
        if(xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        Swal.fire({
          title: 'Gagal',
          text: msg,
          icon: 'error',
          timer: 3000,
          showConfirmButton: false
        });
      }
    });
  });

  // Handler file input media (edit)
  const mediaDropzone = document.getElementById('editMediaDropzoneArea');
  const mediaInput = document.getElementById('editMediaPendukungInput');
  if (mediaDropzone && mediaInput) {
    mediaDropzone.addEventListener('click', function(e) {
      if (e.target === mediaDropzone || e.target.classList.contains('dz-message') || e.target.closest('.dz-message')) {
        mediaInput.click();
      }
    });
    mediaDropzone.addEventListener('dragover', function(e) {
      e.preventDefault();
      mediaDropzone.classList.add('dragover');
    });
    mediaDropzone.addEventListener('dragleave', function(e) {
      e.preventDefault();
      mediaDropzone.classList.remove('dragover');
    });
    mediaDropzone.addEventListener('drop', function(e) {
      e.preventDefault();
      mediaDropzone.classList.remove('dragover');
      Array.from(e.dataTransfer.files).forEach(file => {
        if (file.type.startsWith('image/')) selectedPhotos.push(file);
        else if (file.type.startsWith('video/')) selectedVideos.push(file);
      });
      renderPhotosPreview();
      renderVideosPreview();
    });
    mediaInput.addEventListener('change', function(e) {
      Array.from(e.target.files).forEach(file => {
        if (file.type.startsWith('image/')) selectedPhotos.push(file);
        else if (file.type.startsWith('video/')) selectedVideos.push(file);
      });
      renderPhotosPreview();
      renderVideosPreview();
      mediaInput.value = '';
    });
  }
  $(document).on('click', '#editTambahDokumen', function() {
    $('#editDokumenPendukungInput').trigger('click');
  });
  $('#editDokumenPendukungInput').on('change', function(e) {
    Array.from(e.target.files).forEach(file => {
      selectedDocuments.push(file);
    });
    renderDocumentsPreview();
    $(this).val('');
  });
  $(document).on('click', '.delete-photo', function() {
    const indexToDelete = $(this).data('index');
    selectedPhotos.splice(indexToDelete, 1);
    renderPhotosPreview();
  });
  $(document).on('click', '.delete-video', function() {
    const indexToDelete = $(this).data('index');
    selectedVideos.splice(indexToDelete, 1);
    renderVideosPreview();
  });
  $(document).on('click', '.delete-document', function() {
    const indexToDelete = $(this).data('index');
    selectedDocuments.splice(indexToDelete, 1);
    renderDocumentsPreview();
  });

  // Handler hapus file lama (foto, video, dokumen) di edit modal
  $(document).on('click', '.delete-photo-existing', function() {
    const idx = $(this).data('index');
    if (!deletedPhotoIndexes.includes(idx)) {
      deletedPhotoIndexes.push(idx);
      renderPhotosPreview();
    }
  });
  $(document).on('click', '.delete-video-existing', function() {
    const idx = $(this).data('index');
    if (!deletedVideoIndexes.includes(idx)) {
      deletedVideoIndexes.push(idx);
      renderVideosPreview();
    }
  });
  $(document).on('click', '.delete-document-existing', function() {
    const idx = $(this).data('index');
    if (!deletedDocumentIndexes.includes(idx)) {
      deletedDocumentIndexes.push(idx);
      renderDocumentsPreview();
    }
  });
});


$(function() {
  // Variabel media khusus tambah
  let selectedPhotos = [];
  let selectedVideos = [];
  let selectedDocuments = [];

  // Reset media saat modal tambah ditutup
  $('#tambahProduk').on('hidden.bs.modal', function () {
    selectedPhotos = [];
    selectedVideos = [];
    selectedDocuments = [];
    renderPhotosPreview();
    renderVideosPreview();
    renderDocumentsPreview();
  });

  // Update sub-kategori saat kategori utama berubah
  $(document).on('change', '#kategori_utama', function() {
    updateSubKategoriOptions($(this).val(), '#sub_kategori_id_produk');
  });
  $('#tambahProduk').on('shown.bs.modal', function() {
    updateSubKategoriOptions($('#kategori_utama').val(), '#sub_kategori_id_produk');
  });

  // === HARGA BERTINGKAT & RESELLER ===
  let hargaBertingkatList = [];
  let hargaResellerList = [];

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

  function renderHargaBertingkat() {
    const tbody = $('#tabelHargaBertingkat tbody');
    tbody.empty();
    if (hargaBertingkatList.length === 0) {
      tbody.append('<tr class="text-muted"><td colspan="6" class="text-center">Belum ada harga bertingkat</td></tr>');
      return;
    }
    hargaBertingkatList.forEach((row, idx) => {
      const profitRp = row.harga - (parseInt($('#totalModalBahan').text().replace(/[^\d]/g, '')) || 0);
      const profitPersen = row.harga > 0 ? ((profitRp / row.harga) * 100).toFixed(1) : 0;
      tbody.append(`
        <tr>
          <td><input type="number" class="form-control form-control-sm min-qty" value="${row.min_qty}" min="1" data-idx="${idx}"></td>
          <td><input type="number" class="form-control form-control-sm max-qty" value="${row.max_qty}" min="1" data-idx="${idx}"></td>
          <td><input type="text" class="form-control form-control-sm harga money-format" value="${row.harga ? formatRupiahInput(row.harga.toString()) : ''}" min="0" data-idx="${idx}"></td>
          <td class="text-success fw-semibold">Rp ${profitRp.toLocaleString('id-ID')}</td>
          <td class="text-success fw-semibold">${profitPersen}%</td>
          <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-harga-bertingkat" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
        </tr>
      `);
    });
    feather.replace();
  }
  function renderHargaReseller() {
    const tbody = $('#tabelHargaReseller tbody');
    tbody.empty();
    if (hargaResellerList.length === 0) {
      tbody.append('<tr class="text-muted"><td colspan="6" class="text-center">Belum ada harga reseller</td></tr>');
      return;
    }
    hargaResellerList.forEach((row, idx) => {
      const profitRp = row.harga - (parseInt($('#totalModalBahan').text().replace(/[^\d]/g, '')) || 0);
      const profitPersen = row.harga > 0 ? ((profitRp / row.harga) * 100).toFixed(1) : 0;
      tbody.append(`
        <tr>
          <td><input type="number" class="form-control form-control-sm min-qty" value="${row.min_qty}" min="1" data-idx="${idx}"></td>
          <td><input type="number" class="form-control form-control-sm max-qty" value="${row.max_qty}" min="1" data-idx="${idx}"></td>
          <td><input type="text" class="form-control form-control-sm harga money-format" value="${row.harga ? formatRupiahInput(row.harga.toString()) : ''}" min="0" data-idx="${idx}"></td>
          <td class="text-success fw-semibold">Rp ${profitRp.toLocaleString('id-ID')}</td>
          <td class="text-success fw-semibold">${profitPersen}%</td>
          <td><button type="button" class="btn btn-link text-danger p-0 btn-hapus-harga-reseller" data-idx="${idx}"><i data-feather="trash-2"></i></button></td>
        </tr>
      `);
    });
  feather.replace();
  }
  $('#btnTambahHargaBertingkat').off('click').on('click', function() {
    hargaBertingkatList.push({ min_qty: 1, max_qty: 1, harga: 0 });
    renderHargaBertingkat();
  });
  $('#btnTambahHargaReseller').off('click').on('click', function() {
    hargaResellerList.push({ min_qty: 1, max_qty: 1, harga: 0 });
    renderHargaReseller();
  });
  $(document).on('click', '.btn-hapus-harga-bertingkat', function() {
    const idx = $(this).data('idx');
    hargaBertingkatList.splice(idx, 1);
    renderHargaBertingkat();
  });
  $(document).on('click', '.btn-hapus-harga-reseller', function() {
    const idx = $(this).data('idx');
    hargaResellerList.splice(idx, 1);
    renderHargaReseller();
  });
  $(document).on('input', '#tabelHargaBertingkat input', function() {
    const idx = $(this).data('idx');
    const field = $(this).hasClass('min-qty') ? 'min_qty' : $(this).hasClass('max-qty') ? 'max_qty' : 'harga';
    if (field === 'harga') {
      hargaBertingkatList[idx][field] = parseInt($(this).val().replace(/\./g, '')) || 0;
    } else {
      hargaBertingkatList[idx][field] = parseInt($(this).val()) || 0;
    }
    updateProfitCalculation('#tabelHargaBertingkat', idx, hargaBertingkatList[idx]);
  });
  $(document).on('blur', '#tabelHargaBertingkat input', function() {
    renderHargaBertingkat();
  });
  $(document).on('input', '#tabelHargaReseller input', function() {
    const idx = $(this).data('idx');
    const field = $(this).hasClass('min-qty') ? 'min_qty' : $(this).hasClass('max-qty') ? 'max_qty' : 'harga';
    if (field === 'harga') {
      hargaResellerList[idx][field] = parseInt($(this).val().replace(/\./g, '')) || 0;
    } else {
      hargaResellerList[idx][field] = parseInt($(this).val()) || 0;
    }
    updateProfitCalculation('#tabelHargaReseller', idx, hargaResellerList[idx]);
  });
  $(document).on('blur', '#tabelHargaReseller input', function() {
    renderHargaReseller();
  });
  function updateProfitCalculation(tableSelector, idx, rowData) {
    const totalModalBahan = parseInt($('#totalModalBahan').text().replace(/[^\d]/g, '')) || 0;
    const profitRp = rowData.harga - totalModalBahan;
    const profitPersen = rowData.harga > 0 ? ((profitRp / rowData.harga) * 100).toFixed(1) : 0;
    const row = $(`${tableSelector} tbody tr`).eq(idx);
    row.find('td').eq(3).html(`<span class="text-success fw-semibold">Rp ${profitRp.toLocaleString('id-ID')}</span>`);
    row.find('td').eq(4).html(`<span class="text-success fw-semibold">${profitPersen}%</span>`);
  }

  // Handler submit tambah produk
  $('#formProduk').off('submit').on('submit', function(e) {
    e.preventDefault();
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...');
    submitBtn.prop('disabled', true);
    const bahanBakuArr = [];
    $('#tabelBahanBaku tbody tr').each(function() {
      if ($(this).find('input[name="bahan_baku_id[]"]').length > 0) {
        bahanBakuArr.push({
          id: $(this).find('input[name="bahan_baku_id[]"]').val(),
          nama: $(this).find('td').eq(0).text().trim(),
          satuan: $(this).find('td').eq(1).text().trim(),
          harga: parseInt($(this).find('input[name="harga_bahan[]"]').val()) || 0,
          jumlah: parseInt($(this).find('input[name="jumlah_bahan[]"]').val()) || 0,
          total: parseInt($(this).find('input[name="harga_bahan[]"]').val()) * parseInt($(this).find('input[name="jumlah_bahan[]"]').val()) || 0
        });
      }
    });
    $('#bahan_baku_json').val(JSON.stringify(bahanBakuArr));
    const alurArr = [];
    $('#daftarMesin .mesin-item').each(function() {
      alurArr.push({
        nama_mesin: $(this).find('input[name*="[nama_mesin]"]').val() || '',
        tipe_mesin: $(this).find('span').text() || '',
        estimasi_waktu: parseInt($(this).find('input[name*="[estimasi_waktu]"]').val()) || 0,
        catatan: $(this).find('textarea[name*="[catatan]"]').val() || ''
      });
    });
    $('#alur_produksi_json').val(JSON.stringify(alurArr));
    $('#harga_bertingkat_json').val(JSON.stringify(hargaBertingkatList));
    $('#harga_reseller_json').val(JSON.stringify(hargaResellerList));
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
    $.ajax({
      url: '/backend/master-produk',
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

  // Fungsi render preview media/dokumen (khusus tambah)
  function renderPhotosPreview() {
    const previewContainer = $('#fotoPendukungPreview');
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
      reader.onload = function(e) {
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
    const previewContainer = $('#videoPendukungPreview');
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
    const previewContainer = $('#dokumenPendukungBody');
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
          <td>${(file.size/1024).toFixed(1)} KB</td>
          <td><button type="button" class="btn btn-danger btn-sm delete-document" data-index="${index}"><i data-feather="trash" class="icon-sm"></i></button></td>
        </tr>
      `;
      previewContainer.append(row);
    });
    feather.replace();
  }
  const mediaDropzone = document.getElementById('mediaDropzoneArea');
  const mediaInput = document.getElementById('mediaPendukungInput');
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
  $(document).on('click', '#tambahDokumen', function() {
    $('#dokumenPendukungInput').trigger('click');
  });
  $('#dokumenPendukungInput').on('change', function(e) {
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
});
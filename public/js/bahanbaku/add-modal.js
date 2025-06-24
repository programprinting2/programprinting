$(document).ready(function() {
  feather.replace();

  // Fungsi untuk mengupdate label unit pada input stok
  function updateStockUnitLabels() {
    const satuanUtama = $('#satuanUtama').val();
    const unitText = satuanUtama ? satuanUtama.charAt(0).toUpperCase() + satuanUtama.slice(1) : 'Unit';
    $('#stokSaatIniUnit').text(unitText);
    $('#stokMinimumUnit').text(unitText);
    $('#stokMaksimumUnit').text(unitText);
  }

  // Fungsi untuk mengupdate informasi stok secara dinamis
  function updateStokInfo() {
    const stokSaatIni = parseInt($('#stokSaatIni').val()) || 0;
    const stokMinimum = parseInt($('#stokMinimum').val()) || 0;
    const stokMaksimum = parseInt($('#stokMaksimum').val()) || 0;
    const hargaTerakhir = parseFloat($('#hargaTerakhir').val().replace(/\./g, '')) || 0;
    const satuanUtama = $('#satuanUtama').val();

    // Update Status Stok
    let statusText = '';
    let progressBarWidth = 0;
    let progressBarClass = 'bg-primary';
    let stokAlertClass = 'd-none';

    if (stokMaksimum > 0) {
      progressBarWidth = (stokSaatIni / stokMaksimum) * 100;
    }

    if (stokSaatIni < stokMinimum) {
      statusText = 'Stok Minimum';
      progressBarClass = 'bg-danger';
      stokAlertClass = ''; // show alert
    } else if (stokSaatIni >= stokMaksimum) {
      statusText = 'Stok Maksimum';
      progressBarClass = 'bg-success';
    } else if (stokSaatIni >= stokMinimum) {
      statusText = 'Stok Normal';
      progressBarClass = 'bg-primary';
    }

    $('#statusStokText').text(statusText);
    $('#stokProgressBar').css('width', `${progressBarWidth}%`).removeClass('bg-primary bg-danger bg-success').addClass(progressBarClass);
    $('#stokSummary').text(`${stokSaatIni} / ${stokMaksimum} ${satuanUtama ? satuanUtama.charAt(0).toUpperCase() + satuanUtama.slice(1) : 'Unit'}`);
    $('#stokAlert').removeClass('d-none').addClass(stokAlertClass);

    // Update Estimasi Nilai Stok
    const totalNilaiStok = stokSaatIni * hargaTerakhir;
    $('#totalNilaiStok').text(`Rp ${totalNilaiStok.toLocaleString('id-ID')}`);
  }

  // Event listener untuk perubahan input stok dan harga
  $('#stokSaatIni, #stokMinimum, #stokMaksimum, #hargaTerakhir').on('input', updateStokInfo);
  $('#satuanUtama').on('change', function(){
    updateStockUnitLabels();
    updateStokInfo();
  });

  // Panggil saat modal ditampilkan (setelah data bahan baku dimuat jika edit)
  $('#addMaterialModal').on('shown.bs.modal', function () {
    updateStockUnitLabels(); // Update unit labels based on initial satuanUtama
    updateStokInfo(); // Update stock info based on initial values
  });

  // Inisialisasi tampilan stok saat dokumen siap
  updateStockUnitLabels();
  updateStokInfo();

  // Fungsi untuk update visibilitas pesan konversi satuan kosong
  function updateNoConversionMessage() {
    if ($('.conversion-row').length === 0) {
      if ($('#noConversionMessage').length === 0) {
        $('#conversionUnitsContainer').append(`
          <div class="col-12 text-center text-muted py-4" id="noConversionMessage">
            <i data-feather="refresh-cw" class="icon-lg mb-2"></i><br>Belum ada konversi satuan yang ditambahkan.
          </div>
        `);
        feather.replace();
      } else {
        $('#noConversionMessage').show();
      }
    } else {
      $('#noConversionMessage').hide();
    }
  }

  // Panggil saat halaman siap
  updateNoConversionMessage();

  // Fungsi untuk generate <option> satuan dari window.satuanList
  function getSatuanOptionsFromList() {
    if (!window.satuanList) return '<option value="" selected disabled>Pilih satuan</option>';
    let html = '<option value="" selected disabled>Pilih satuan</option>';
    window.satuanList.forEach(function(satuan) {
      html += `<option value="${satuan.id}">${satuan.nama_detail_parameter}</option>`;
    });
    return html;
  }

  // Fungsi untuk menambahkan baris konversi satuan baru
  $('#tambahKonversi').on('click', function() {
    const optionsHtml = getSatuanOptionsFromList();
    const newRow = `
      <div class="row g-2 mb-2 align-items-center border p-2 rounded conversion-row">
        <div class="col-md-2">
          <input type="number" class="form-control form-control-sm" name="konversi_satuan_json[][dari]" value="1" min="0">
        </div>
        <div class="col-md-3">
          <select class="form-select form-select-sm" name="konversi_satuan_json[][satuan_dari]">
            ${optionsHtml}
          </select>
        </div>
        <div class="col-auto">
          <span>=</span>
        </div>
        <div class="col-md-2">
          <input type="number" class="form-control form-control-sm" name="konversi_satuan_json[][ke]" value="1" min="0">
        </div>
        <div class="col-md-3">
          <select class="form-select form-select-sm" name="konversi_satuan_json[][satuan_ke]">
            ${optionsHtml}
          </select>
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-outline-danger btn-sm delete-conversion-row"><i data-feather="trash" class="icon-sm"></i></button>
        </div>
      </div>
    `;
    $('#conversionUnitsContainer').append(newRow);
    feather.replace(); // Re-initialize feather icons for new elements
    updateNoConversionMessage();
  });

  // Event listener untuk menghapus baris konversi
  $('#conversionUnitsContainer').on('click', '.delete-conversion-row', function() {
    $(this).closest('.conversion-row').remove();
    updateNoConversionMessage();
  });

  // Perbarui opsi satuan saat kategori berubah
  $('#kategori').on('change', function() {
    const selectedKategori = $(this).val();
    updateStockUnitLabels();
  });

  // Fungsi untuk mempersiapkan data sebelum submit
  function prepareFormData(formData) {
    // Hapus format Rupiah dari harga terakhir
    const hargaTerakhir = formData.get('harga_terakhir');
    if (hargaTerakhir) {
      formData.set('harga_terakhir', hargaTerakhir.replace(/\./g, ''));
    }

    // Persiapkan data konversi satuan
    const konversiRows = [];
    $('.conversion-row').each(function() {
      const dari = $(this).find('input[name*="[dari]"]').val();
      const satuanDari = $(this).find('select[name*="[satuan_dari]"]').val();
      const ke = $(this).find('input[name*="[ke]"]').val();
      const satuanKe = $(this).find('select[name*="[satuan_ke]"]').val();

      if (dari && satuanDari && ke && satuanKe) {
        konversiRows.push({
          dari: parseFloat(dari),
          satuan_dari: satuanDari,
          ke: parseFloat(ke),
          satuan_ke: satuanKe
        });
      }
    });

    // Set konversi satuan sebagai JSON string dengan format yang diinginkan
    formData.set('konversi_satuan_json', JSON.stringify(konversiRows));

    // Set status_aktif dari dropdown
    const statusAktif = $('#statusAktif').val();
    formData.delete('status_aktif'); // Hapus nilai lama jika ada
    formData.append('status_aktif', statusAktif);

    // Tambahkan file foto_pendukung dan video_pendukung ke FormData
    selectedPhotos.forEach(file => {
      formData.append('foto_pendukung_new[]', file);
    });
    selectedVideos.forEach(file => {
      formData.append('video_pendukung_new[]', file);
    });

    // Tambahkan file dokumen ke FormData
    selectedDocuments.forEach(file => {
      formData.append('dokumen_pendukung_new[]', file);
    });

    // Tambahkan link pendukung ke FormData
    formData.set('link_pendukung_json', JSON.stringify(linkPendukung));

    return formData;
  }

  // Arrays untuk menyimpan file yang dipilih
  let selectedPhotos = [];
  let selectedVideos = [];
  let selectedDocuments = [];

  // Fungsi untuk memisahkan file ke foto/video
  function handleMediaFiles(files) {
    Array.from(files).forEach(file => {
      if (file.type.startsWith('image/')) {
        selectedPhotos.push(file);
      } else if (file.type.startsWith('video/')) {
        selectedVideos.push(file);
      }
    });
    renderPhotosPreview();
    renderVideosPreview();
  }

  // Event handler drag-and-drop area
  const mediaDropzone = document.getElementById('mediaDropzoneArea');
  const mediaInput = document.getElementById('mediaPendukungInput');
  if (mediaDropzone && mediaInput) {
    // Klik area = buka file picker
    mediaDropzone.addEventListener('click', function(e) {
      if (e.target === mediaDropzone || e.target.classList.contains('dz-message') || e.target.closest('.dz-message')) {
        mediaInput.click();
      }
    });
    // Drag events
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
      handleMediaFiles(e.dataTransfer.files);
    });
    // File input change
    mediaInput.addEventListener('change', function(e) {
      handleMediaFiles(e.target.files);
      mediaInput.value = '';
    });
  }

  // Fungsi untuk menampilkan pratinjau foto
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

  // Fungsi untuk menampilkan pratinjau video
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

  // Event listener untuk tombol "Tambah Foto"
  $('#tambahFoto').on('click', function() {
    $('#fotoPendukungInput').trigger('click');
  });

  // Event listener untuk input file foto
  $('#fotoPendukungInput').on('change', function(e) {
    Array.from(e.target.files).forEach(file => {
      selectedPhotos.push(file);
    });
    renderPhotosPreview();
    $(this).val(''); // Clear the input so same file can be selected again
  });

  // Event listener untuk tombol "Tambah Video"
  $('#tambahVideo').on('click', function() {
    $('#videoPendukungInput').trigger('click');
  });

  // Event listener untuk input file video
  $('#videoPendukungInput').on('change', function(e) {
    Array.from(e.target.files).forEach(file => {
      selectedVideos.push(file);
    });
    renderVideosPreview();
    $(this).val(''); // Clear the input
  });

  // Event listener untuk menghapus foto
  $(document).on('click', '.delete-photo', function() {
    const indexToDelete = $(this).data('index');
    selectedPhotos.splice(indexToDelete, 1);
    renderPhotosPreview();
  });

  // Event listener untuk menghapus video
  $(document).on('click', '.delete-video', function() {
    const indexToDelete = $(this).data('index');
    selectedVideos.splice(indexToDelete, 1);
    renderVideosPreview();
  });

  // Event listener untuk tombol "Tambah Dokumen"
  $('#tambahDokumen').on('click', function() {
    $('#dokumenPendukungInput').trigger('click');
  });

  // Event listener untuk input file dokumen
  $('#dokumenPendukungInput').on('change', function(e) {
    Array.from(e.target.files).forEach(file => {
      selectedDocuments.push(file);
    });
    renderDocumentsPreview();
    $(this).val(''); // Clear the input
  });

  // Fungsi untuk menampilkan pratinjau dokumen
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

  // Event listener untuk menghapus dokumen
  $(document).on('click', '.delete-document', function() {
    const indexToDelete = $(this).data('index');
    selectedDocuments.splice(indexToDelete, 1);
    renderDocumentsPreview();
  });

  // Reset form dan preview saat modal ditutup
  $('#addMaterialModal').on('hidden.bs.modal', function () {
    $('#addMaterialForm')[0].reset();
    selectedPhotos = [];
    selectedVideos = [];
    selectedDocuments = [];
    renderPhotosPreview();
    renderVideosPreview();
    renderDocumentsPreview();
    // Reset elemen dinamis lainnya jika diperlukan
    $('#conversionUnitsContainer').empty();
    updateNoConversionMessage();
    $('#dokumenPendukungBody').html(`
      <tr id="noDokumenMessage">
          <td colspan="4" class="text-center text-muted py-4">
              <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
          </td>
      </tr>
    `);
    feather.replace();
  });

  // Event listener untuk form submit
  $('#addMaterialForm').on('submit', function(e) {
    e.preventDefault();
    const submitButton = $(this).find('button[type="submit"]');
    const originalButtonText = submitButton.html();
    
    // Serialisasi spesifikasi teknis ke field hidden sebelum submit
    const spesifikasiArr = [];
    $('#detail_spesifikasi_container .detail-item').each(function() {
      const nama = $(this).find('input[name="spesifikasi_nama[]"]').val();
      const nilai = $(this).find('input[name="spesifikasi_nilai[]"]').val();
      const satuan = $(this).find('input[name="spesifikasi_satuan[]"]').val();
      if (nama && nilai) {
        spesifikasiArr.push({nama, nilai, satuan});
      }
    });
    $('#detail_spesifikasi_json').val(JSON.stringify(spesifikasiArr));

    // Tampilkan spinner dan nonaktifkan tombol
    submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);

    const formData = new FormData(this);
    // Panggil fungsi prepareFormData untuk memproses data sebelum dikirim
    prepareFormData(formData);
    // Tambahkan CSRF token ke FormData agar tidak terjadi CSRF token mismatch
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
      url: $('#addMaterialForm').attr('action') || '/backend/master-bahanbaku',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        // Kembalikan tombol ke kondisi awal
        submitButton.html(originalButtonText).prop('disabled', false);
        
        // Tampilkan SweetAlert dengan auto-close
        Swal.fire({
          title: 'Berhasil!',
          text: response.message,
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          $('#addMaterialModal').modal('hide');
          location.reload(); // Refresh halaman setelah sukses
        });
      },
      error: function(xhr) {
        // Kembalikan tombol ke kondisi awal
        submitButton.html(originalButtonText).prop('disabled', false);
        
        let errorMessage = 'Terjadi kesalahan!';
        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors;
          errorMessage = 'Mohon periksa kembali input Anda:<br>';
          for (let key in errors) {
            errorMessage += `- ${errors[key][0]}<br>`;
          }
        } else {
          errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan tak terduga.';
        }
        
        Swal.fire({
          title: 'Gagal!',
          html: errorMessage,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    });
  });

  // Array untuk menyimpan link pendukung
  let linkPendukung = [];

  // Fungsi untuk render daftar link pendukung
  function renderLinkPendukung() {
    const list = $('#daftarLinkPendukung');
    list.empty();
    if (linkPendukung.length === 0) {
      list.append('<li class="list-group-item text-muted text-center">Belum ada link yang ditambahkan.</li>');
      return;
    }
    linkPendukung.forEach((link, idx) => {
      list.append(`
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <a href="${link}" target="_blank" rel="noopener">${link}</a>
          <button type="button" class="btn btn-sm btn-danger hapus-link-pendukung" data-index="${idx}"><i data-feather="trash" class="icon-sm"></i></button>
        </li>
      `);
    });
    feather.replace();
  }

  // Event handler tambah link
  $('#tambahLinkPendukung').on('click', function() {
    const link = $('#inputLinkPendukung').val().trim();
    if (link && /^https?:\/\//.test(link) && !linkPendukung.includes(link)) {
      linkPendukung.push(link);
      $('#inputLinkPendukung').val('');
      renderLinkPendukung();
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Link tidak valid atau sudah ada',
        text: 'Pastikan link diawali http:// atau https:// dan belum ada di daftar.'
      });
    }
  });

  // Event handler hapus link
  $(document).on('click', '.hapus-link-pendukung', function() {
    const idx = $(this).data('index');
    linkPendukung.splice(idx, 1);
    renderLinkPendukung();
  });

  // Saat modal ditutup, reset link
  $('#addMaterialModal').on('hidden.bs.modal', function () {
    linkPendukung = [];
    renderLinkPendukung();
  });

  // Panggil renderLinkPendukung saat dokumen siap
  renderLinkPendukung();

  // === Spesifikasi Teknis Dinamis (Tambah) ===
  function renderSpesifikasiTeknis() {
    const container = $('#detail_spesifikasi_container');
    container.empty();
    if (window.detailSpesifikasi.length === 0) {
      container.append('<div class="text-muted text-center py-3" id="no_spesifikasi_message">Belum ada spesifikasi teknis. Klik tombol "Tambah Spesifikasi" untuk menambahkan.</div>');
      return;
    }
    window.detailSpesifikasi.forEach((item, idx) => {
      container.append(`
        <div class="row mb-2 detail-item align-items-center">
          <div class="col-md-4 mb-1"><input type="text" class="form-control form-control-sm spesifikasi-nama" data-index="${idx}" name="spesifikasi_nama[]" placeholder="Nama Spesifikasi" value="${item.nama || ''}"></div>
          <div class="col-md-4 mb-1"><input type="text" class="form-control form-control-sm spesifikasi-nilai" data-index="${idx}" name="spesifikasi_nilai[]" placeholder="Nilai" value="${item.nilai || ''}"></div>
          <div class="col-md-3 mb-1"><input type="text" class="form-control form-control-sm spesifikasi-satuan" data-index="${idx}" name="spesifikasi_satuan[]" placeholder="Satuan" value="${item.satuan || ''}"></div>
          <div class="col-md-1 mb-1 text-end"><button type="button" class="btn btn-outline-danger btn-sm remove-detail-spesifikasi" data-index="${idx}"><i data-feather="trash-2" class="icon-sm"></i></button></div>
        </div>
      `);
    });
    feather.replace();
    // Tambahkan event handler input agar array sinkron
    container.find('.spesifikasi-nama').on('input', function() {
      const idx = $(this).data('index');
      window.detailSpesifikasi[idx].nama = $(this).val();
    });
    container.find('.spesifikasi-nilai').on('input', function() {
      const idx = $(this).data('index');
      window.detailSpesifikasi[idx].nilai = $(this).val();
    });
    container.find('.spesifikasi-satuan').on('input', function() {
      const idx = $(this).data('index');
      window.detailSpesifikasi[idx].satuan = $(this).val();
    });
  }
  window.detailSpesifikasi = [];
  $('#tambah_detail_spesifikasi').on('click', function() {
    window.detailSpesifikasi.push({nama:'',nilai:'',satuan:''});
    renderSpesifikasiTeknis();
  });
  $(document).on('click', '.remove-detail-spesifikasi', function() {
    const idx = $(this).data('index');
    window.detailSpesifikasi.splice(idx, 1);
    renderSpesifikasiTeknis();
  });
  // Reset saat modal ditutup
  $('#addMaterialModal').on('hidden.bs.modal', function () {
    window.detailSpesifikasi = [];
    renderSpesifikasiTeknis();
  });
  // Inisialisasi awal
  renderSpesifikasiTeknis();

  // Event handler klik pada thumbnail foto di preview
  $(document).on('click', '#fotoPendukungPreview img', function(e) {
    // Cegah jika klik pada tombol hapus
    if ($(e.target).closest('.delete-photo').length > 0) return;
    const src = $(this).attr('src');
    const alt = $(this).attr('alt') || '';
    $('#mediaPreviewModalBody').html(`<img src="${src}" alt="${alt}" class="img-fluid rounded" style="max-height:70vh;">`);
    $('#mediaPreviewModal').modal('show');
  });
  // Event handler klik pada thumbnail video di preview (ikon video)
  $(document).on('click', '#videoPendukungPreview .preview-card', function(e) {
    // Cegah jika klik pada tombol hapus
    if ($(e.target).closest('.delete-video').length > 0) return;
    // Ambil nama file dari .text-truncate
    const fileName = $(this).find('.text-truncate').text().trim();
    // Cari file video di selectedVideos berdasarkan nama
    const fileObj = selectedVideos.find(f => f.name === fileName);
    if (fileObj) {
      const reader = new FileReader();
      reader.onload = function(ev) {
        $('#mediaPreviewModalBody').html(`<video src="${ev.target.result}" controls autoplay style="max-width:100%;max-height:70vh;"></video>`);
        $('#mediaPreviewModal').modal('show');
      };
      reader.readAsDataURL(fileObj);
    }
  });

  // Bersihkan konten modal preview media saat ditutup agar video berhenti total
  $('#mediaPreviewModal').on('hidden.bs.modal', function() {
    $('#mediaPreviewModalBody').html('');
  });
}); 
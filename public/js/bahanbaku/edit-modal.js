// Definisi satuan berdasarkan kategori (dipindahkan ke global scope)
const satuanByKategori = {
  'Bahan Lembaran': [
    { value: 'lembar', label: 'Lembar' },
    { value: 'pcs', label: 'Pcs' },
    { value: 'rim', label: 'Rim' },
    { value: 'pak', label: 'Pak' }
  ],
  'Bahan Roll': [
    { value: 'roll', label: 'Roll' },
    { value: 'meter', label: 'Meter' },
    { value: 'cm2', label: 'cm2' },
    { value: 'm2', label: 'm2' }
  ],
  'Bahan Cair': [
    { value: 'ml', label: 'ml' },
    { value: 'liter', label: 'Liter' },
    { value: 'galon', label: 'Galon' },
    { value: 'botol', label: 'Botol' }
  ],
  'Bahan Berat': [
    { value: 'gram', label: 'Gram' },
    { value: 'kg', label: 'Kg' },
    { value: 'ton', label: 'Ton' },
    { value: 'ball', label: 'Ball' }
  ],
  'Bahan Unit/Biji': [
    { value: 'pcs', label: 'Pcs' },
    { value: 'unit', label: 'Unit' },
    { value: 'biji', label: 'Biji' },
    { value: 'box', label: 'Box' }
  ],
  'Bahan Paket/Set': [
    { value: 'paket', label: 'Paket' },
    { value: 'set', label: 'Set' },
    { value: 'bundle', label: 'Bundle' }
  ],
  'Bahan Waktu/Jasa': [
    { value: 'detik', label: 'Detik' },
    { value: 'menit', label: 'Menit' },
    { value: 'jam', label: 'Jam' },
    { value: 'hari', label: 'Hari' }
  ]
};

// Fungsi untuk mendapatkan opsi satuan berdasarkan kategori (dipindahkan ke global scope)
function getEditSatuanOptions(kategori) {
  if (satuanByKategori[kategori]) {
    return satuanByKategori[kategori].map(satuan => `<option value="${satuan.value}">${satuan.label}</option>`).join('');
  }
  return '<option value="">Pilih satuan</option>';
}

// Fungsi untuk mengupdate label unit pada input stok edit (dipindahkan ke global scope)
function updateEditStockUnitLabels() {
  const satuanUtama = $('#edit_satuan_utama').val();
  const unitText = satuanUtama ? satuanUtama.charAt(0).toUpperCase() + satuanUtama.slice(1) : 'Unit';
  $('#editStokSaatIniUnit').text(unitText);
  $('#editStokMinimumUnit').text(unitText);
  $('#editStokMaksimumUnit').text(unitText);
}

// Fungsi untuk mengupdate informasi stok edit secara dinamis (dipindahkan ke global scope)
function updateEditStokInfo() {
  const stokSaatIni = parseInt($('#edit_stok_saat_ini').val()) || 0;
  const stokMinimum = parseInt($('#edit_stok_minimum').val()) || 0;
  const stokMaksimum = parseInt($('#edit_stok_maksimum').val()) || 0;
  const hargaTerakhir = parseFloat($('#edit_harga_terakhir').val()) || 0;
  const satuanUtama = $('#edit_satuan_utama').val();

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

  $('#editStatusStokText').text(statusText);
  $('#editStokProgressBar').css('width', `${progressBarWidth}%`).removeClass('bg-primary bg-danger bg-success').addClass(progressBarClass);
  $('#editStokSummary').text(`${stokSaatIni} / ${stokMaksimum} ${satuanUtama ? satuanUtama.charAt(0).toUpperCase() + satuanUtama.slice(1) : 'Unit'}`);
  $('#editStokAlert').removeClass('d-none').addClass(stokAlertClass);

  // Update Estimasi Nilai Stok
  const totalNilaiStok = stokSaatIni * hargaTerakhir;
  $('#editTotalNilaiStok').text(`Rp ${totalNilaiStok.toLocaleString('id-ID')}`);
}

// Arrays untuk menyimpan file yang dipilih (baru dan yang sudah ada)
let selectedEditPhotos = [];
let selectedEditVideos = [];
let selectedEditDocuments = [];

// Tambahkan array global untuk dokumen lama
let dokumenLamaEdit = [];
let fotoLamaEdit = [];
let videoLamaEdit = [];

// Array untuk menyimpan link pendukung di modal edit
let editLinkPendukung = [];

// Fungsi untuk menampilkan pratinjau foto di modal edit
function renderEditPhotosPreview() {
  const previewContainer = $('#editFotoPendukungPreview');
  previewContainer.empty();
  
  // Foto lama
  if (fotoLamaEdit.length > 0) {
    fotoLamaEdit.forEach((url, index) => {
      const previewItem = `
        <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
          <div class="card border-0 preview-card">
            <img src="${url}" class="card-img-top img-fluid rounded" style="height: 100px; object-fit: cover;" alt="foto lama">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-foto-lama" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
              <i data-feather="x" class="icon-sm"></i>
            </button>
            <div class="card-body p-1 text-truncate" style="font-size: 0.75rem;">
              ${url.split('/').pop()}
            </div>
          </div>
        </div>
      `;
      previewContainer.append(previewItem);
      feather.replace();
    });
  }
  // Foto baru
  if (selectedEditPhotos.length > 0) {
    selectedEditPhotos.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function(e) {
        const previewItem = `
          <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
            <div class="card border-0 preview-card">
              <img src="${e.target.result}" class="card-img-top img-fluid rounded" style="height: 100px; object-fit: cover;" alt="${file.name}">
              <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-edit-photo" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
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
  if (fotoLamaEdit.length === 0 && selectedEditPhotos.length === 0) {
    previewContainer.append(`
      <div class="col-12 text-center text-muted" id="noEditFotoMessage">
          <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang ditambahkan.
      </div>
    `);
    feather.replace();
  }
}

// Fungsi untuk menampilkan pratinjau video di modal edit
function renderEditVideosPreview() {
  const previewContainer = $('#editVideoPendukungPreview');
  previewContainer.empty();
  
  // Video lama
  if (videoLamaEdit.length > 0) {
    videoLamaEdit.forEach((url, index) => {
      const previewItem = `
        <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
          <div class="card border-0 preview-card d-flex flex-column align-items-center justify-content-center p-2" style="height: 100px; overflow: hidden;">
            <i data-feather="video" class="icon-lg text-primary mb-1"></i>
            <div class="text-truncate w-100 text-center" style="font-size: 0.75rem;">${url.split('/').pop()}</div>
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-video-lama" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
              <i data-feather="x" class="icon-sm"></i>
            </button>
          </div>
      </div>
      `;
      previewContainer.append(previewItem);
    feather.replace();
    });
  }
  // Video baru
  if (selectedEditVideos.length > 0) {
  selectedEditVideos.forEach((file, index) => {
    const previewItem = `
      <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
        <div class="card border-0 preview-card d-flex flex-column align-items-center justify-content-center p-2" style="height: 100px; overflow: hidden;">
          <i data-feather="video" class="icon-lg text-primary mb-1"></i>
            <div class="text-truncate w-100 text-center" style="font-size: 0.75rem;">${file.name}</div>
          <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-edit-video" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
            <i data-feather="x" class="icon-sm"></i>
          </button>
        </div>
      </div>
    `;
    previewContainer.append(previewItem);
    feather.replace();
  });
}
  if (videoLamaEdit.length === 0 && selectedEditVideos.length === 0) {
    previewContainer.append(`
      <div class="col-12 text-center text-muted" id="noEditVideoMessage">
          <i data-feather="video" class="icon-lg mb-2"></i><br>Belum ada video yang ditambahkan.
      </div>
    `);
    feather.replace();
  }
}

// Fungsi untuk menampilkan pratinjau dokumen di modal edit
function renderEditDocumentsPreview() {
  const previewContainer = $('#editDokumenPendukungBody');
  previewContainer.empty();
  // Dokumen lama
  const dokumenLamaValid = dokumenLamaEdit.filter(doc => doc && doc.nama && doc.path);
  if (dokumenLamaValid.length > 0) {
    dokumenLamaValid.forEach((doc, index) => {
      const docRow = `
        <tr>
            <td>${doc.nama}</td>
            <td>${doc.tipe}</td>
            <td>${(parseInt(doc.ukuran)/1024).toFixed(1)} KB</td>
            <td>
              <a href="${doc.path}" class="btn btn-sm btn-info me-1" target="_blank" title="Lihat/Download"><i data-feather="download" class="icon-sm"></i></a>
              <button type="button" class="btn btn-danger btn-sm delete-dokumen-row" data-index="${index}" data-lama="1"><i data-feather="trash" class="icon-sm"></i></button>
            </td>
        </tr>
      `;
      previewContainer.append(docRow);
      feather.replace();
    });
  }
  // Dokumen baru
  if (selectedEditDocuments.length > 0) {
    selectedEditDocuments.forEach((file, index) => {
      const row = `
        <tr>
          <td>${file.name}</td>
          <td>${file.type}</td>
          <td>${(file.size/1024).toFixed(1)} KB</td>
          <td><button type="button" class="btn btn-danger btn-sm delete-edit-document" data-index="${index}"><i data-feather="trash" class="icon-sm"></i></button></td>
        </tr>
      `;
      previewContainer.append(row);
    });
    feather.replace();
  }
  if (dokumenLamaValid.length === 0 && selectedEditDocuments.length === 0) {
    previewContainer.append(`
      <tr id="noEditDokumenMessage">
          <td colspan="4" class="text-center text-muted py-4">
              <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
          </td>
      </tr>
    `);
    feather.replace();
  }
}

// Fungsi untuk render daftar link pendukung di modal edit
function renderEditLinkPendukung() {
  const list = $('#editDaftarLinkPendukung');
  list.empty();
  if (editLinkPendukung.length === 0) {
    list.append('<li class="list-group-item text-muted text-center">Belum ada link yang ditambahkan.</li>');
    return;
  }
  editLinkPendukung.forEach((item, idx) => {
    list.append(`
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <a href="${item.url}" target="_blank" rel="noopener">${item.url}</a>
          <br><small class="text-muted">${item.keterangan ? item.keterangan : ''}</small>
        </div>
        <button type="button" class="btn btn-sm btn-danger edit-hapus-link-pendukung" data-index="${idx}"><i data-feather="trash" class="icon-sm"></i></button>
      </li>
    `);
  });
  feather.replace();
}

// Event handler tambah link di modal edit
$(document).on('click', '#editTambahLinkPendukung', function() {
  const link = $('#editInputLinkPendukung').val().trim();
  const ket = $('#editInputKeteranganLinkPendukung').val().trim();
  if (link && /^https?:\/\//.test(link) && !editLinkPendukung.some(item => item.url === link)) {
    editLinkPendukung.push({url: link, keterangan: ket});
    $('#editInputLinkPendukung').val('');
    $('#editInputKeteranganLinkPendukung').val('');
    renderEditLinkPendukung();
  } else {
    Swal.fire({
      icon: 'warning',
      title: 'Link tidak valid atau sudah ada',
      text: 'Pastikan link diawali http:// atau https:// dan belum ada di daftar.'
    });
  }
});

// Event handler hapus link di modal edit
$(document).on('click', '.edit-hapus-link-pendukung', function() {
  const idx = $(this).data('index');
  editLinkPendukung.splice(idx, 1);
  renderEditLinkPendukung();
});

// Saat modal edit ditutup, reset link
$('#editModal').on('hidden.bs.modal', function () {
  editLinkPendukung = [];
  renderEditLinkPendukung();
});

// Fungsi untuk memuat data bahan baku ke form edit
function loadBahanBakuData(id) {
  $.get(`/backend/master-bahanbaku/${id}`, function(data) {
    $('#editForm').attr('action', `/backend/master-bahanbaku/${id}`);
    
    // Isi form dengan data
    $('#edit_kode_bahan').val(data.kode_bahan);
    $('#edit_nama_bahan').val(data.nama_bahan);
    $('#edit_kategori').val(data.kategori).trigger('change');
    $('#edit_sub_kategori').val(data.sub_kategori);
    $('#edit_satuan_utama').val(data.satuan_utama);
    $('#edit_status_aktif').val(data.status_aktif ? '1' : '0');
    $('#edit_keterangan').val(data.keterangan);
    
    // Spesifikasi Teknis
    $('#edit_pilihan_warna').val(data.pilihan_warna);
    $('#edit_nama_warna_custom').val(data.nama_warna_custom);
    $('#edit_berat').val(data.berat);
    $('#edit_tinggi').val(data.tinggi);
    $('#edit_tebal').val(data.tebal);
    $('#edit_gramasi_densitas').val(data.gramasi_densitas);
    $('#edit_volume').val(data.volume);
    
    // Pemasok & Harga
    $('#edit_pemasok_utama_id').val(data.pemasok_utama_id);
    BahanBakuHelper.applyMoneyFormat($('#edit_harga_terakhir').val(data.harga_terakhir));
    
    // Informasi Stok
    $('#edit_stok_saat_ini').val(data.stok_saat_ini);
    $('#edit_stok_minimum').val(data.stok_minimum);
    $('#edit_stok_maksimum').val(data.stok_maksimum);
    
    // Konversi Satuan
    $('#editConversionUnitsContainer').empty();
    if (data.konversi_satuan_json) {
      let konversiData;
      try {
        // Jika data sudah dalam bentuk array, gunakan langsung
        konversiData = Array.isArray(data.konversi_satuan_json) ? 
          data.konversi_satuan_json : 
          JSON.parse(data.konversi_satuan_json);
      } catch (e) {
        console.error('Error parsing konversi_satuan_json:', e);
        konversiData = [];
      }

      const selectedKategori = $('#edit_kategori').val();
      const optionsHtml = getEditSatuanOptions(selectedKategori);

      konversiData.forEach(konversi => {
        const newRow = `
          <div class="row g-2 mb-2 align-items-center border p-2 rounded conversion-row">
            <div class="col-md-2">
              <input type="number" class="form-control form-control-sm" name="konversi_satuan_json[][dari]" value="${konversi.dari || konversi.from_value}" min="0">
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
              <input type="number" class="form-control form-control-sm" name="konversi_satuan_json[][ke]" value="${konversi.ke || konversi.to_value}" min="0">
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
        $('#editConversionUnitsContainer').append(newRow);

        // Set selected values for the new dropdowns
        $(`#editConversionUnitsContainer .conversion-row:last-child select[name*="[satuan_dari]"]`).val(konversi.satuan_dari || konversi.from_unit);
        $(`#editConversionUnitsContainer .conversion-row:last-child select[name*="[satuan_ke]"]`).val(konversi.satuan_ke || konversi.to_unit);
      });
      feather.replace();
    }
    updateEditNoConversionMessage();

    // Dokumen Pendukung
    $('#editDokumenPendukungBody').empty();
    dokumenLamaEdit = data.dokumen_pendukung_json ? [...data.dokumen_pendukung_json] : [];
    fotoLamaEdit = data.foto_pendukung_json ? [...data.foto_pendukung_json] : [];
    videoLamaEdit = data.video_pendukung_json ? [...data.video_pendukung_json] : [];
    // Tampilkan dokumen lama (dari database)
    if (dokumenLamaEdit && dokumenLamaEdit.length > 0) {
      dokumenLamaEdit.forEach((doc, index) => {
        const docRow = `
          <tr>
              <td>${doc.nama ? doc.nama : ''}</td>
              <td>${doc.tipe ? doc.tipe : ''}</td>
              <td>${doc.ukuran ? (parseInt(doc.ukuran)/1024).toFixed(1) + ' KB' : ''}</td>
              <td>
                <a href="${doc.path ? doc.path : '#'}" class="btn btn-sm btn-info me-1" target="_blank" ${doc.path ? '' : 'disabled'} title="Lihat/Download"><i data-feather="download" class="icon-sm"></i></a>
                <button type="button" class="btn btn-danger btn-sm delete-dokumen-row" data-index="${index}" data-lama="1"><i data-feather="trash" class="icon-sm"></i></button>
              </td>
          </tr>
        `;
        $('#editDokumenPendukungBody').append(docRow);
        feather.replace();
      });
    }
    // Tampilkan dokumen baru (belum tersimpan)
    if (selectedEditDocuments && selectedEditDocuments.length > 0) {
      selectedEditDocuments.forEach((file, index) => {
        const row = `
          <tr>
            <td>${file.name}</td>
            <td>${file.type}</td>
            <td>${(file.size/1024).toFixed(1)} KB</td>
            <td><button type="button" class="btn btn-danger btn-sm delete-edit-document" data-index="${index}"><i data-feather="trash" class="icon-sm"></i></button></td>
          </tr>
        `;
        $('#editDokumenPendukungBody').append(row);
      });
      feather.replace();
    }
    // Jika tidak ada dokumen sama sekali
    if (dokumenLamaEdit.length === 0 && (!selectedEditDocuments || selectedEditDocuments.length === 0)) {
      $('#editDokumenPendukungBody').html(`
        <tr id=\"noEditDokumenMessage\">
            <td colspan=\"4\" class=\"text-center text-muted py-4\">
                <i data-feather=\"file-text\" class=\"icon-lg mb-2\"></i><br>Belum ada dokumen yang ditambahkan.
            </td>
        </tr>
      `);
      feather.replace();
    }
    
    // Media & Foto
    selectedEditPhotos = [];
    selectedEditVideos = [];
    renderEditPhotosPreview();
    renderEditVideosPreview();

    // Tampilkan modal
    $('#editModal').modal('show');
    // Panggil fungsi update stok saat modal ditampilkan dan data dimuat
    updateEditStockUnitLabels();
    updateEditStokInfo();

    // Saat load data edit, isi array link dari backend
    editLinkPendukung = Array.isArray(data.link_pendukung_json) ? data.link_pendukung_json.map(item => {
      if (typeof item === 'string') return {url: item, keterangan: ''};
      return item;
    }) : [];
    renderEditLinkPendukung();

    // === Spesifikasi Teknis Dinamis (Edit) ===
    renderEditSpesifikasiTeknis();
    loadEditSpesifikasiTeknis(data.detail_spesifikasi_json);
  });
}

// Fungsi untuk memisahkan file ke foto/video
function handleEditMediaFiles(files) {
  Array.from(files).forEach(file => {
    if (file.type.startsWith('image/')) {
      selectedEditPhotos.push(file);
    } else if (file.type.startsWith('video/')) {
      selectedEditVideos.push(file);
    }
  });
  renderEditPhotosPreview();
  renderEditVideosPreview();
}

// Event handler drag-and-drop area
const editMediaDropzone = document.getElementById('editMediaDropzoneArea');
const editMediaInput = document.getElementById('editMediaPendukungInput');
if (editMediaDropzone && editMediaInput) {
  // Klik area = buka file picker
  editMediaDropzone.addEventListener('click', function(e) {
    if (e.target === editMediaDropzone || e.target.classList.contains('dz-message') || e.target.closest('.dz-message')) {
      editMediaInput.click();
    }
  });
  // Drag events
  editMediaDropzone.addEventListener('dragover', function(e) {
    e.preventDefault();
    editMediaDropzone.classList.add('dragover');
  });
  editMediaDropzone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    editMediaDropzone.classList.remove('dragover');
  });
  editMediaDropzone.addEventListener('drop', function(e) {
    e.preventDefault();
    editMediaDropzone.classList.remove('dragover');
    handleEditMediaFiles(e.dataTransfer.files);
  });
  // File input change
  editMediaInput.addEventListener('change', function(e) {
    handleEditMediaFiles(e.target.files);
    editMediaInput.value = '';
  });
}

// Event listener untuk tombol "Tambah Foto" di modal edit
$(document).on('click', '#editTambahFoto', function() {
  $('#editFotoPendukungInput').trigger('click');
});

// Event listener untuk input file foto di modal edit
$(document).on('change', '#editFotoPendukungInput', function(e) {
  Array.from(e.target.files).forEach(file => {
    selectedEditPhotos.push(file);
  });
  renderEditPhotosPreview();
  $(this).val(''); // Clear the input
});

// Event listener untuk tombol "Tambah Video" di modal edit
$(document).on('click', '#editTambahVideo', function() {
  $('#editVideoPendukungInput').trigger('click');
});

// Event listener untuk input file video di modal edit
$(document).on('change', '#editVideoPendukungInput', function(e) {
  Array.from(e.target.files).forEach(file => {
    selectedEditVideos.push(file);
  });
  renderEditVideosPreview();
  $(this).val(''); // Clear the input
});

// Event listener untuk tombol "Tambah Dokumen" di modal edit
$(document).on('click', '#editTambahDokumen', function() {
  $('#editDokumenPendukungInput').trigger('click');
});

// Event listener untuk input file dokumen di modal edit
$(document).on('change', '#editDokumenPendukungInput', function(e) {
  Array.from(e.target.files).forEach(file => {
    selectedEditDocuments.push(file);
  });
  renderEditDocumentsPreview();
  $(this).val(''); // Clear the input
});

// Event listener untuk menghapus foto di modal edit
$(document).on('click', '.delete-edit-photo', function() {
  const indexToDelete = $(this).data('index');
  selectedEditPhotos.splice(indexToDelete, 1);
  renderEditPhotosPreview();
});

// Event listener untuk menghapus video di modal edit
$(document).on('click', '.delete-edit-video', function() {
  const indexToDelete = $(this).data('index');
  selectedEditVideos.splice(indexToDelete, 1);
  renderEditVideosPreview();
});

// Event listener untuk menghapus dokumen di modal edit
$(document).on('click', '.delete-edit-document', function() {
  const indexToDelete = $(this).data('index');
  selectedEditDocuments.splice(indexToDelete, 1);
  renderEditDocumentsPreview();
});

// Event handler untuk hapus dokumen lama
$(document).on('click', '.delete-dokumen-row', function() {
  const index = $(this).data('index');
  const isLama = $(this).data('lama') == 1;
  if (isLama) {
    dokumenLamaEdit.splice(index, 1);
    // Render ulang tabel dokumen
    $('#editDokumenPendukungBody').empty();
    // Tampilkan dokumen lama
    if (dokumenLamaEdit.length > 0) {
      dokumenLamaEdit.forEach((doc, idx) => {
        const docRow = `
          <tr>
              <td>${doc.nama ? doc.nama : ''}</td>
              <td>${doc.tipe ? doc.tipe : ''}</td>
              <td>${doc.ukuran ? (parseInt(doc.ukuran)/1024).toFixed(1) + ' KB' : ''}</td>
              <td>
                <a href="${doc.path ? doc.path : '#'}" class="btn btn-sm btn-info me-1" target="_blank" ${doc.path ? '' : 'disabled'} title="Lihat/Download"><i data-feather="download" class="icon-sm"></i></a>
                <button type="button" class="btn btn-danger btn-sm delete-dokumen-row" data-index="${idx}" data-lama="1"><i data-feather="trash" class="icon-sm"></i></button>
              </td>
          </tr>
        `;
        $('#editDokumenPendukungBody').append(docRow);
        feather.replace();
      });
    }
    // Tampilkan dokumen baru
    if (selectedEditDocuments && selectedEditDocuments.length > 0) {
      selectedEditDocuments.forEach((file, idx) => {
        const row = `
          <tr>
            <td>${file.name}</td>
            <td>${file.type}</td>
            <td>${(file.size/1024).toFixed(1)} KB</td>
            <td><button type="button" class="btn btn-danger btn-sm delete-edit-document" data-index="${idx}"><i data-feather="trash" class="icon-sm"></i></button></td>
          </tr>
        `;
        $('#editDokumenPendukungBody').append(row);
      });
      feather.replace();
    }
    // Jika tidak ada dokumen sama sekali
    if (dokumenLamaEdit.length === 0 && (!selectedEditDocuments || selectedEditDocuments.length === 0)) {
      $('#editDokumenPendukungBody').html(`
        <tr id=\"noEditDokumenMessage\">
            <td colspan=\"4\" class=\"text-center text-muted py-4\">
                <i data-feather=\"file-text\" class=\"icon-lg mb-2\"></i><br>Belum ada dokumen yang ditambahkan.
            </td>
        </tr>
      `);
      feather.replace();
    }
  }
});

// Fungsi untuk update visibilitas pesan konversi satuan kosong di modal edit
function updateEditNoConversionMessage() {
  if ($('#editConversionUnitsContainer .conversion-row').length === 0) {
    if ($('#noEditConversionMessage').length === 0) {
      $('#editConversionUnitsContainer').append(`
        <div class="col-12 text-center text-muted py-4" id="noEditConversionMessage">
          <i data-feather="refresh-cw" class="icon-lg mb-2"></i><br>Belum ada konversi satuan yang ditambahkan.
        </div>
      `);
      feather.replace();
    } else {
      $('#noEditConversionMessage').show();
    }
  } else {
    $('#noEditConversionMessage').hide();
  }
}

// Modifikasi prepareFormData untuk mengirim link_pendukung_json
function prepareFormData(formData) {
    // Hapus format Rupiah dari harga terakhir
    const hargaTerakhir = formData.get('harga_terakhir');
    if (hargaTerakhir) {
        formData.set('harga_terakhir', hargaTerakhir.replace(/\./g, ''));
    }

    // Set status_aktif dari dropdown
    const statusAktif = $('#edit_status_aktif').val();
    formData.delete('status_aktif'); // Hapus nilai lama jika ada
    formData.append('status_aktif', statusAktif);

    // Persiapkan data konversi satuan
    const konversiRows = [];
    $('#editConversionUnitsContainer .conversion-row').each(function() {
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
    formData.set('konversi_satuan_json', JSON.stringify(konversiRows));

    // Filter dokumen lama agar tidak ada elemen kosong
    const dokumenLamaValid = dokumenLamaEdit.filter(doc => doc && doc.nama && doc.path);
    formData.set('dokumen_pendukung_json', JSON.stringify(dokumenLamaValid));

    // Foto lama
    formData.append('foto_pendukung_existing_json', JSON.stringify(fotoLamaEdit));
    // Video lama
    formData.append('video_pendukung_existing_json', JSON.stringify(videoLamaEdit));

    // Tambahkan file foto baru ke FormData
    selectedEditPhotos.forEach(file => {
      formData.append('foto_pendukung_new[]', file);
    });
    // Tambahkan file video baru ke FormData
    selectedEditVideos.forEach(file => {
      formData.append('video_pendukung_new[]', file);
    });

    // Tambahkan file dokumen ke FormData
    selectedEditDocuments.forEach(file => {
      formData.append('dokumen_pendukung_new[]', file);
    });

    // Tambahkan link pendukung ke FormData
    formData.set('link_pendukung_json', JSON.stringify(editLinkPendukung));

    // Pastikan pengambilan value sub-kategori menggunakan id
    const subKategoriId = $('#edit_sub_kategori_id').val();
    formData.append('sub_kategori_id', subKategoriId);

    return formData;
}

// === Spesifikasi Teknis Dinamis (Edit) ===
function renderEditSpesifikasiTeknis() {
  const container = $('#edit_detail_spesifikasi_container');
  container.empty();
  if (window.editDetailSpesifikasi.length === 0) {
    container.append('<div class="text-muted text-center py-3" id="edit_no_spesifikasi_message">Belum ada spesifikasi teknis. Klik tombol "Tambah Spesifikasi" untuk menambahkan.</div>');
    return;
  }
  window.editDetailSpesifikasi.forEach((item, idx) => {
    container.append(`
      <div class="row mb-2 detail-item align-items-center">
        <div class="col-md-4 mb-1"><input type="text" class="form-control form-control-sm" name="edit_spesifikasi_nama[]" placeholder="Nama Spesifikasi" value="${item.nama || ''}"></div>
        <div class="col-md-4 mb-1"><input type="text" class="form-control form-control-sm" name="edit_spesifikasi_nilai[]" placeholder="Nilai" value="${item.nilai || ''}"></div>
        <div class="col-md-3 mb-1"><input type="text" class="form-control form-control-sm" name="edit_spesifikasi_satuan[]" placeholder="Satuan" value="${item.satuan || ''}"></div>
        <div class="col-md-1 mb-1 text-end"><button type="button" class="btn btn-outline-danger btn-sm remove-edit-detail-spesifikasi" data-index="${idx}"><i data-feather="trash-2" class="icon-sm"></i></button></div>
      </div>
    `);
  });
  feather.replace();
}
window.editDetailSpesifikasi = [];
$('#edit_tambah_detail_spesifikasi').on('click', function() {
  window.editDetailSpesifikasi.push({nama:'',nilai:'',satuan:''});
  renderEditSpesifikasiTeknis();
});
$(document).on('click', '.remove-edit-detail-spesifikasi', function() {
  const idx = $(this).data('index');
  window.editDetailSpesifikasi.splice(idx, 1);
  renderEditSpesifikasiTeknis();
});
// Saat load data edit, isi field dinamis dari JSON
function loadEditSpesifikasiTeknis(json) {
  window.editDetailSpesifikasi = Array.isArray(json) ? [...json] : [];
  renderEditSpesifikasiTeknis();
}
// Serialisasi ke hidden sebelum submit
$('#editForm').on('submit', function() {
  // ... existing code ...
  // Ambil data dari form dinamis
  const spesifikasiArr = [];
  $('#edit_detail_spesifikasi_container .detail-item').each(function() {
    const nama = $(this).find('input[name="edit_spesifikasi_nama[]"]').val();
    const nilai = $(this).find('input[name="edit_spesifikasi_nilai[]"]').val();
    const satuan = $(this).find('input[name="edit_spesifikasi_satuan[]"]').val();
    if (nama && nilai) {
      spesifikasiArr.push({nama, nilai, satuan});
    }
  });
  $('#edit_detail_spesifikasi_json').val(JSON.stringify(spesifikasiArr));
  // ... existing code ...
});
// Reset saat modal ditutup
$('#editModal').on('hidden.bs.modal', function () {
  window.editDetailSpesifikasi = [];
  renderEditSpesifikasiTeknis();
});

$(document).ready(function() {
  feather.replace(); // Pastikan feather icons diinisialisasi untuk elemen statis juga
  updateEditStockUnitLabels();
  updateEditStokInfo();

  // Event listener untuk tombol tambah konversi pada modal edit
  $('#editTambahKonversi').on('click', function() {
    const selectedKategori = $('#edit_kategori').val();
    const optionsHtml = getEditSatuanOptions(selectedKategori);
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
    $('#editConversionUnitsContainer').append(newRow);
    feather.replace(); // Re-initialize feather icons for new elements
    updateEditNoConversionMessage();
  });

  // Event listener untuk menghapus baris konversi pada modal edit
  $('#editConversionUnitsContainer').on('click', '.delete-conversion-row', function() {
    $(this).closest('.conversion-row').remove();
    updateEditNoConversionMessage();
  });

  // Event listener untuk perubahan kategori di modal edit
  $('#edit_kategori').on('change', function() {
    const kategori = $(this).val();
    const satuanSelect = $('#edit_satuan_utama');
    
    // Reset dan disable satuan utama
    satuanSelect.empty().prop('disabled', true);
    
    if (kategori) {
      // Enable satuan utama
      satuanSelect.prop('disabled', false);
      
      // Tambahkan opsi berdasarkan kategori
      let options = [];
      switch(kategori) {
        case 'Bahan Lembaran':
          options = ['lembar', 'pcs', 'rim', 'pak'];
          break;
        case 'Bahan Roll':
          options = ['meter', 'yard', 'roll'];
          break;
        case 'Bahan Cair':
          options = ['ml', 'liter', 'galon', 'botol'];
          break;
        case 'Bahan Berat':
          options = ['gram', 'kg', 'ton'];
          break;
        case 'Bahan Unit/Biji':
          options = ['pcs', 'unit', 'buah'];
          break;
        case 'Bahan Paket/Set':
          options = ['paket', 'set', 'box'];
          break;
        case 'Bahan Waktu/Jasa':
          options = ['jam', 'hari', 'minggu'];
          break;
      }
      
      // Tambahkan opsi ke select
      options.forEach(option => {
        satuanSelect.append(`<option value="${option}">${option}</option>`);
      });
    } else {
      // Jika tidak ada kategori yang dipilih
      satuanSelect.append('<option value="">Pilih kategori terlebih dahulu</option>');
    }
    updateEditStockUnitLabels();
    updateEditStokInfo();

    // Perbarui dropdown satuan di setiap baris konversi yang sudah ada
    $('#editConversionUnitsContainer .conversion-row').each(function() {
      const fromUnitSelect = $(this).find('select[name*="[satuan_dari]"]');
      const toUnitSelect = $(this).find('select[name*="[satuan_ke]"]');
      fromUnitSelect.empty().append(getEditSatuanOptions(kategori));
      toUnitSelect.empty().append(getEditSatuanOptions(kategori));
    });
  });

  // Event listener untuk form submit
  $('#editForm').on('submit', function(e) {
    e.preventDefault();
    const submitButton = $(this).find('button[type="submit"]');
    const originalButtonText = submitButton.html();
    
    // Tampilkan spinner dan nonaktifkan tombol
    submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);

    const formData = new FormData(this);
    // Panggil fungsi prepareFormData untuk memproses data sebelum dikirim
    prepareFormData(formData);

    $.ajax({
      url: $('#editForm').attr('action'),
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
          $('#editModal').modal('hide');
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

  // Reset form dan preview saat modal ditutup
  $('#editModal').on('hidden.bs.modal', function () {
    $('#editForm')[0].reset();
    selectedEditPhotos = [];
    selectedEditVideos = [];
    selectedEditDocuments = [];
    renderEditPhotosPreview();
    renderEditVideosPreview();
    renderEditDocumentsPreview();
    // Reset elemen dinamis lainnya jika diperlukan
    $('#editConversionUnitsContainer').empty();
    updateEditNoConversionMessage();
    $('#editDokumenPendukungBody').html(`
      <tr id="noEditDokumenMessage">
          <td colspan="4" class="text-center text-muted py-4">
              <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
          </td>
      </tr>
    `);
    feather.replace();
  });

  // Event handler untuk hapus foto lama
  $(document).on('click', '.delete-foto-lama', function() {
    const index = $(this).data('index');
    fotoLamaEdit.splice(index, 1);
    renderEditPhotosPreview();
  });

  // Event handler untuk hapus video lama
  $(document).on('click', '.delete-video-lama', function() {
    const index = $(this).data('index');
    videoLamaEdit.splice(index, 1);
    renderEditVideosPreview();
  });

  // Event handler klik pada thumbnail foto di preview (edit)
  $(document).on('click', '#editFotoPendukungPreview img', function(e) {
    if ($(e.target).closest('.delete-edit-photo, .delete-foto-lama').length > 0) return;
    const src = $(this).attr('src');
    const alt = $(this).attr('alt') || '';
    $('#editMediaPreviewModalBody').html(`<img src="${src}" alt="${alt}" class="img-fluid rounded" style="max-height:70vh;">`);
    $('#editMediaPreviewModal').modal('show');
  });
  // Event handler klik pada thumbnail video di preview (edit)
  $(document).on('click', '#editVideoPendukungPreview .preview-card', function(e) {
    if ($(e.target).closest('.delete-edit-video, .delete-video-lama').length > 0) return;
    const fileName = $(this).find('.text-truncate').text().trim();
    // Cek di videoLamaEdit (string url) atau selectedEditVideos (File)
    let videoSrc = null;
    // Cek video lama (url)
    const idxLama = videoLamaEdit.findIndex(url => url.split('/').pop() === fileName);
    if (idxLama !== -1) {
      videoSrc = videoLamaEdit[idxLama];
      $('#editMediaPreviewModalBody').html(`<video src="${videoSrc}" controls autoplay style="max-width:100%;max-height:70vh;"></video>`);
      $('#editMediaPreviewModal').modal('show');
      return;
    }
    // Cek video baru (File)
    const fileObj = selectedEditVideos.find(f => f.name === fileName);
    if (fileObj) {
      const reader = new FileReader();
      reader.onload = function(ev) {
        $('#editMediaPreviewModalBody').html(`<video src="${ev.target.result}" controls autoplay style="max-width:100%;max-height:70vh;"></video>`);
        $('#editMediaPreviewModal').modal('show');
      };
      reader.readAsDataURL(fileObj);
    }
  });

  // Bersihkan konten modal preview media saat ditutup agar video berhenti total
  $('#editMediaPreviewModal').on('hidden.bs.modal', function() {
    $('#editMediaPreviewModalBody').html('');
  });
}); 
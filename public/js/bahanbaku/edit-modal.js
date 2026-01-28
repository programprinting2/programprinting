// Fungsi untuk mengupdate label unit pada input stok edit (dipindahkan ke global scope)
function updateEditStockUnitLabels() {
  // Selalu tampilkan 'Unit' pada label satuan
  $('#editStokSaatIniUnit').text('Unit');
  $('#editStokMinimumUnit').text('Unit');
  $('#editStokMaksimumUnit').text('Unit');
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
  $('#editStokSummary').text(`${stokSaatIni} / ${stokMaksimum} Unit`);
  $('#editStokAlert').removeClass('d-none').addClass(stokAlertClass);
}

// Arrays untuk menyimpan file yang dipilih (baru dan yang sudah ada)
let selectedEditPhotos = [];
let selectedEditVideos = [];
let selectedEditDocuments = [];

// Helper function untuk generate Supabase URL
function generateSupabaseUrl(path) {
  if (!path || path.startsWith('http')) {
    return path;
  }
  return `https://qwvqaxqhvcchrfegdjwo.supabase.co/storage/v1/object/public/qdesign-public/${path}`;
}

// Tambahkan array global untuk dokumen lama
let dokumenLamaEdit = [];
let fotoLamaEdit = [];
let videoLamaEdit = [];

// Array untuk menyimpan link pendukung di modal edit
let editLinkPendukung = [];
let subSatuanList = window.subSatuanList || [];

// Fungsi untuk menampilkan pratinjau foto di modal edit
function renderEditPhotosPreview() {
  const previewContainer = $('#editFotoPendukungPreview');
  previewContainer.empty();
  
  // Foto lama
  if (fotoLamaEdit.length > 0) {
    fotoLamaEdit.forEach((path, index) => {
      const fullUrl = generateSupabaseUrl(path);

      const previewItem = `
        <div class="col-md-4 col-lg-3 col-xl-2 position-relative mb-2">
          <div class="card border-0 preview-card">
            <img src="${fullUrl}" class="card-img-top img-fluid rounded" style="height: 100px; object-fit: cover;" alt="foto lama" onerror="this.src='/images/placeholder-image.png'">
            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-foto-lama" data-index="${index}" style="--bs-btn-padding-y: .15rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;">
              <i data-feather="x" class="icon-sm"></i>
            </button>
            <div class="card-body p-1 text-truncate" style="font-size: 0.75rem;">
              ${path.split('/').pop()}
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
    $('#edit_id').val(data.id);
    // Isi form dengan data
    $('#edit_kode_bahan').val(data.kode_bahan);
    $('#edit_nama_bahan').val(data.nama_bahan);
    $('#edit_kategori').val(data.kategori_id).trigger('change');
    updateEditSubKategoriOptions(data.kategori_id, data.sub_kategori_id);
    $('#edit_sub_kategori_id').val(data.sub_kategori_id);
    $('#edit_satuan_utama').val(data.satuan_utama_id);
    updateEditSubSatuanOptions(data.satuan_utama_id, data.sub_satuan_id);
    $('#edit_sub_satuan').val(data.sub_satuan_id);
    updateEditLabelSatuanHargaTerakhir();
    $('#edit_status_aktif').val(data.status_aktif ? '1' : '0');
    $('#edit_keterangan').val(data.keterangan);
    $('#edit_warna_id').val(data.warna_id);
    $('#edit_warna_id').trigger('change');
    
    // Spesifikasi Teknis
    // $('#edit_pilihan_warna').val(data.pilihan_warna);
    // $('#edit_nama_warna_custom').val(data.nama_warna_custom);
    // $('#edit_berat').val(data.berat);
    // $('#edit_tinggi').val(data.tinggi);
    // $('#edit_tebal').val(data.tebal);
    // $('#edit_gramasi_densitas').val(data.gramasi_densitas);
    // $('#edit_volume').val(data.volume);
    
    // Pemasok & Harga
    $('#edit_pemasok_utama_id').val(data.pemasok_utama_id);
    if (data.pemasok_utama_nama) {
      let nama = data.pemasok_utama_nama;
      if (data.pemasok_utama_kode) nama += ' [' + data.pemasok_utama_kode + ']';
      $('#editNamaPemasokUtama').val(nama);
      $('#editPemasokUtamaId').val(data.pemasok_utama_id);
    } else {
      $('#editNamaPemasokUtama').val('');
      $('#editPemasokUtamaId').val('');
    }
    $('#edit_harga_terakhir').val(data.harga_terakhir || 0);
    
    // Informasi Stok
    $('#edit_stok_saat_ini').val(data.stok_saat_ini);
    $('#edit_stok_minimum').val(data.stok_minimum);
    $('#edit_stok_maksimum').val(data.stok_maksimum);
    
    // Konversi Satuan
    $('#editConversionUnitsContainer').empty();
    if (data.konversi_satuan_json) {
      let konversiData;
      try {
        konversiData = Array.isArray(data.konversi_satuan_json) ? 
          data.konversi_satuan_json : 
          JSON.parse(data.konversi_satuan_json);
      } catch (e) {
        console.error('Error parsing konversi_satuan_json:', e);
        konversiData = [];
      }
      const optionsHtml = getSatuanOptionsFromList();
      konversiData.forEach(konversi => {
        const subSatuanUtamaId = data.sub_satuan_id;
        const subSatuanUtamaNama = getNamaSubSatuanById(subSatuanUtamaId);
        const newRow = `
          <div class="row g-2 mb-2 align-items-center border p-2 rounded conversion-row">
            <div class="col-md-3">
              <select class="form-select form-select-sm" name="konversi_satuan_json[][satuan_dari]">
                ${optionsHtml}
              </select>
            </div>
            <div class="col-auto">
              <span>=</span>
            </div>
            <div class="col-md-4">
              <div class="input-group">
                <input type="number" class="form-control form-control-sm jumlah-konversi" name="konversi_satuan_json[][jumlah]" value="${konversi.jumlah}" min="1" step="0.01">
                <span class="input-group-text">${subSatuanUtamaNama}</span>
              </div>
            </div>
            <div class="col-auto d-flex align-items-center gap-4">
              <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="text" class="form-control form-control-sm total-konversi-harga fw-bold ps-2 text-end" value="0" readonly disabled>
                <span class="input-group-text satuan-total-konversi"></span>
              </div>
              <button type="button" class="btn btn-outline-danger btn-sm delete-conversion-row"><i data-feather="trash" class="icon-sm"></i></button>
            </div>
          </div>
        `;
        $('#editConversionUnitsContainer').append(newRow);
        // Set selected value untuk dropdown satuan_dari
        $('#editConversionUnitsContainer .conversion-row:last-child select[name*="[satuan_dari]"]').val(konversi.satuan_dari);
      });
      feather.replace();
      updateEditConversionTotals();
      updateEditNoConversionMessage();
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
        const downloadUrl = generateSupabaseUrl(doc.path);

        const docRow = `
          <tr>
              <td>${doc.nama ? doc.nama : ''}</td>
              <td>${doc.tipe ? doc.tipe : ''}</td>
              <td>${doc.ukuran ? (parseInt(doc.ukuran)/1024).toFixed(1) + ' KB' : ''}</td>
              <td>
                <a href="${downloadUrl}" class="btn btn-sm btn-info me-1" target="_blank" ${downloadUrl && downloadUrl !== '#' ? '' : 'disabled'} title="Lihat/Download"><i data-feather="download" class="icon-sm"></i></a>
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
    $('#editModalLabel').text(`Edit Bahan Baku: ${data.nama_bahan}`);
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

    loadEditSpesifikasiBahanBaku('#edit_detail_spesifikasi_container', data.detail_spesifikasi_json);

  });
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

// Fungsi untuk update sub satuan options di edit modal
function updateEditSubSatuanOptions(selectedSatuanId, currentSubSatuanId = null) {
  const subSatuanSelect = $('#edit_sub_satuan');
  subSatuanSelect.empty();
  subSatuanSelect.prop('disabled', true);
  subSatuanSelect.append('<option value="" selected disabled>Pilih detail satuan</option>');

  if (selectedSatuanId && subSatuanList) {
    const filtered = subSatuanList.filter(sub => sub.detail_parameter_id == selectedSatuanId);
    filtered.forEach(sub => {
      subSatuanSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
    });
    if (filtered.length > 0) {
      subSatuanSelect.prop('disabled', false);
      if (currentSubSatuanId) {
        subSatuanSelect.val(currentSubSatuanId);
      }
    }
  }
}

// Fungsi untuk mendapatkan nama satuan utama dari id
function getNamaSatuanById(id) {
  if (!window.satuanList) return '';
  const satuan = window.satuanList.find(s => s.id == id);
  return satuan ? satuan.nama_detail_parameter : '';
}

function getNamaSubSatuanById(id) {
  if (!window.subSatuanList) return '';
  const subSatuan = window.subSatuanList.find(s => s.id == id);
  return subSatuan ? subSatuan.nama_sub_detail_parameter : '';
}

// Event listener untuk tombol tambah konversi pada modal edit
$('#editTambahKonversi').off('click').on('click', function() {
  const subSatuanId = $('#edit_sub_satuan').val();
  const subSatuanNama = getNamaSubSatuanById(subSatuanId);
  const optionsHtml = getSatuanOptionsFromList();
  const newRow = `
    <div class="row g-2 mb-2 align-items-center border p-2 rounded conversion-row">
      <div class="col-md-3">
        <select class="form-select form-select-sm" name="konversi_satuan_json[][satuan_dari]">
          ${optionsHtml}
        </select>
      </div>
      <div class="col-auto">
        <span>=</span>
      </div>
      <div class="col-md-4">
        <div class="input-group">
          <input type="number" class="form-control form-control-sm jumlah-konversi" name="konversi_satuan_json[][jumlah]" value="1" min="1" step="0.01">
          <span class="input-group-text">${subSatuanNama}</span>
        </div>
      </div>
      <div class="col-auto d-flex align-items-center gap-4">
        <div class="input-group">
          <span class="input-group-text">Rp</span>
          <input type="text" class="form-control form-control-sm total-konversi-harga fw-bold ps-2 text-end" value="0" readonly disabled>
          <span class="input-group-text satuan-total-konversi"></span>
        </div>
        <button type="button" class="btn btn-outline-danger btn-sm delete-conversion-row"><i data-feather="trash" class="icon-sm"></i></button>
      </div>
    </div>
  `;
  $('#editConversionUnitsContainer').append(newRow);
  feather.replace();
  updateEditConversionTotals();
  updateEditNoConversionMessage();
});

// Update semua satuan ke jika satuan utama berubah
$('#edit_sub_satuan').on('change', function() {
  const subSatuanId = $(this).val();
  const subSatuanNama = getNamaSubSatuanById(subSatuanId);
  $('#editConversionUnitsContainer .conversion-row .input-group .input-group-text').each(function(idx, el) {
    // Hanya update label satuan pada kolom jumlah (bukan label Rp)
    if ($(el).prev('input.jumlah-konversi').length > 0) {
      $(el).text(subSatuanNama);
    }
  });
  updateEditConversionTotals();
});

// Fungsi untuk mempersiapkan data sebelum submit
function prepareFormData(formData) {
  // Set status_aktif dari dropdown
  const statusAktif = $('#edit_status_aktif').val();
  formData.delete('status_aktif'); // Hapus nilai lama jika ada
  formData.append('status_aktif', statusAktif);

  // Persiapkan data konversi satuan
  const konversiRows = [];
  $('.conversion-row').each(function() {
    const subSatuanDari = $(this).find('select[name*="[satuan_dari]"]').val();
    const jumlah = $(this).find('input[name*="[jumlah]"]').val();
    if (subSatuanDari && jumlah) {
      konversiRows.push({
        satuan_dari: subSatuanDari,
        jumlah: parseFloat(jumlah)
      });
    }
  });
  // Tambahkan baris default satuan utama ke satuan utama (jumlah 1) di urutan pertama jika belum ada
  const subSatuanUtamaId = $('#edit_sub_satuan').val();
  if (subSatuanUtamaId) {
    // Cek apakah sudah ada baris default
    const sudahAda = konversiRows.some(row => String(row.satuan_dari) === String(subSatuanUtamaId) && Number(row.jumlah) === 1);
    if (!sudahAda) {
      // Hapus baris lain yang satuan_dari sama dan jumlah 1 (jika ada duplikat manual)
      const tanpaDuplikat = konversiRows.filter(row => !(String(row.satuan_dari) === String(subSatuanUtamaId) && Number(row.jumlah) === 1));
      konversiRows.length = 0;
      konversiRows.push({ satuan_dari: subSatuanUtamaId, jumlah: 1 }, ...tanpaDuplikat);
    } else {
      // Pastikan baris default ada di urutan pertama
      const urutanBaru = [];
      konversiRows.forEach(row => {
        if (String(row.satuan_dari) === String(subSatuanUtamaId) && Number(row.jumlah) === 1) {
          urutanBaru.unshift(row);
        } else {
          urutanBaru.push(row);
        }
      });
      konversiRows.length = 0;
      konversiRows.push(...urutanBaru);
    }
  }
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

// Serialisasi ke hidden sebelum submit
$('#editForm').on('submit', function() {
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
});
// Reset saat modal ditutup
$('#editModal').on('hidden.bs.modal', function () {
  window.editDetailSpesifikasi = [];
});

// === Spesifikasi Teknis Dinamis (Edit Bahan Baku) ===
$('#edit_tambah_detail_spesifikasi').on('click', function() {
  tambahEditSpesifikasiBahanBaku('#edit_detail_spesifikasi_container');
  $('#edit_no_spesifikasi_message').hide();
});

// Fungsi untuk menambah baris spesifikasi edit bahan baku
function tambahEditSpesifikasiBahanBaku(containerId) {
  var newSpesifikasi = `
      <div class="row mb-2 detail-item align-items-center">
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
              <button type="button" class="btn btn-outline-danger btn-sm remove-edit-detail-spesifikasi">
                  <i data-feather="trash-2" class="icon-sm"></i>
              </button>
          </div>
      </div>
  `;
  $(containerId).append(newSpesifikasi);
  feather.replace();
}

// Event handler untuk tombol hapus spesifikasi edit bahan baku
$(document).on('click', '.remove-edit-detail-spesifikasi', function() {
  $(this).closest('.detail-item').remove();
  
  // Cek container dan tampilkan pesan kosong jika perlu
  var container = $(this).closest('#edit_detail_spesifikasi_container, [id^="edit_detail_spesifikasi_container"]');
  if (container.find('.detail-item').length === 0) {
      if (container.attr('id') === 'edit_detail_spesifikasi_container') {
          $('#edit_no_spesifikasi_message').show();
      } else {
          var id = container.attr('id').replace('edit_detail_spesifikasi_container', '');
          $('#edit_no_spesifikasi_message' + id).show();
      }
  }
});

// Fungsi untuk memuat spesifikasi teknis dari data bahan baku yang akan diedit
function loadEditSpesifikasiBahanBaku(containerId, spesifikasiData = []) {
  const container = $(containerId);
  container.empty();
  
  if (!Array.isArray(spesifikasiData) || spesifikasiData.length === 0) {
      var messageId = containerId.replace('#edit_detail_spesifikasi_container', '#edit_no_spesifikasi_message');
      if (messageId === '#edit_no_spesifikasi_message') {
          $('#edit_no_spesifikasi_message').show();
      } else {
          var id = containerId.replace('#edit_detail_spesifikasi_container', '');
          $('#edit_no_spesifikasi_message' + id).show();
      }
      return;
  }
  
  spesifikasiData.forEach((item) => {
      var existingSpesifikasi = `
          <div class="row mb-2 detail-item align-items-center">
              <div class="col-md-4 mb-1">
                  <input type="text" class="form-control form-control-sm" 
                         name="edit_spesifikasi_nama[]" placeholder="Nama Spesifikasi" 
                         value="${item.nama || ''}">
              </div>
              <div class="col-md-4 mb-1">
                  <input type="text" class="form-control form-control-sm" 
                         name="edit_spesifikasi_nilai[]" placeholder="Nilai" 
                         value="${item.nilai || ''}">
              </div>
              <div class="col-md-3 mb-1">
                  <input type="text" class="form-control form-control-sm" 
                         name="edit_spesifikasi_satuan[]" placeholder="Satuan" 
                         value="${item.satuan || ''}">
              </div>
              <div class="col-md-1 mb-1 text-end">
                  <button type="button" class="btn btn-outline-danger btn-sm remove-edit-detail-spesifikasi">
                      <i data-feather="trash-2" class="icon-sm"></i>
                  </button>
              </div>
          </div>
      `;
      container.append(existingSpesifikasi);
  });
  
  feather.replace();
}

// Fungsi untuk generate <option> satuan dari window.satuanList
function getSatuanOptionsFromList() {
  if (!window.subSatuanList) return '<option value="" selected disabled>Pilih sub satuan</option>';
  let html = '<option value="" selected disabled>Pilih sub satuan</option>';
  window.subSatuanList.forEach(function(subSatuan) {
    html += `<option value="${subSatuan.id}">${subSatuan.nama_sub_detail_parameter}</option>`;
  });
  return html;
}

// Fungsi untuk menghitung dan update total harga per baris konversi satuan
function updateEditConversionTotals() {
  const hargaTerakhir = parseFloat($('#edit_harga_terakhir').val()) || 0;
  $('#editConversionUnitsContainer .conversion-row').each(function() {
    const jumlah = parseFloat($(this).find('.jumlah-konversi').val()) || 0;
    const subSatuanId = $(this).find('select[name*="[satuan_dari]"]').val();
    let subSatuanNama = '';
    if (window.subSatuanList && subSatuanId) {
      const subSatuan = window.subSatuanList.find(s => s.id == subSatuanId);
      subSatuanNama = subSatuan ? subSatuan.nama_sub_detail_parameter : '';
    }
    const total = jumlah * hargaTerakhir;
    $(this).find('.total-konversi-harga').val(total.toLocaleString('id-ID'));
    $(this).find('.satuan-total-konversi').text(subSatuanNama ? '/' + subSatuanNama : '');
  });
}

// Tambahkan pemanggilan updateEditConversionTotals pada event yang relevan
$(document).on('input', '#edit_harga_terakhir', updateEditConversionTotals);
$(document).on('input', '#editConversionUnitsContainer .jumlah-konversi', updateEditConversionTotals);
$(document).on('change', '#editConversionUnitsContainer select[name*="[satuan_dari]"]', updateEditConversionTotals);

function updateEditLabelSatuanHargaTerakhir() {
  const subSatuanId = $('#edit_sub_satuan').val();
  let satuanNama = '';
  if (window.subSatuanList && subSatuanId) {
    const subSatuan = window.subSatuanList.find(s => s.id == subSatuanId);
    satuanNama = subSatuan ? '/' + subSatuan.nama_sub_detail_parameter : '';
  }
  $('#editLabelSatuanHargaTerakhir').text(satuanNama);
}
// $('#edit_satuan_utama').on('change', updateEditLabelSatuanHargaTerakhir);
$('#edit_sub_satuan').on('change', updateEditLabelSatuanHargaTerakhir);
$('#edit_satuan_utama').on('change', function() {
  updateEditSubSatuanOptions($(this).val());
  updateEditStockUnitLabels();
  updateEditStokInfo();
  updateEditLabelSatuanHargaTerakhir();
});
$('#editModal').on('shown.bs.modal', updateEditLabelSatuanHargaTerakhir);

$(document).ready(function() {
  feather.replace(); 
  $('#edit_warna_id').on('change', function() {
    const selectedOption = $(this).find('option:selected');
    const hexCode = selectedOption.data('hex');
    updateWarnaPreviewModal(hexCode, 'editWarnaPreviewModal');
  });

  // Fungsi untuk update preview warna di modal
  function updateWarnaPreviewModal(hexCode, previewId) {
      const preview = document.getElementById(previewId);
      if (hexCode && /^#[0-9A-F]{6}$/i.test(hexCode)) {
          preview.style.backgroundColor = hexCode;
          preview.style.display = 'block';
          preview.title = `Warna: ${hexCode}`;
      } else {
          preview.style.display = 'none';
          preview.style.backgroundColor = '';
          preview.title = 'Tidak ada preview warna';
      }
  }

  // Saat modal edit dibuka, set preview warna
  $('#editModal').on('shown.bs.modal', function() {
      const selectedWarnaId = $('#edit_warna_id').val();
      if (selectedWarnaId) {
          const selectedOption = $('#edit_warna_id').find('option[value="' + selectedWarnaId + '"]');
          const hexCode = selectedOption.data('hex');
          updateWarnaPreviewModal(hexCode, 'editWarnaPreviewModal');
      } else {
          updateWarnaPreviewModal(null, 'editWarnaPreviewModal');
      }
  });

  updateEditStockUnitLabels();
  updateEditStokInfo();

  // Event listener untuk perubahan input stok dan harga di modal edit
  $('#edit_stok_saat_ini, #edit_stok_minimum, #edit_stok_maksimum, #edit_harga_terakhir').on('input', updateEditStokInfo);
  $('#edit_satuan_utama').on('change', function(){
    updateEditStockUnitLabels();
    updateEditStokInfo();
  });

  // Panggil saat modal edit ditampilkan (setelah data bahan baku dimuat jika edit)
  $('#editModal').on('shown.bs.modal', function () {
    updateEditStockUnitLabels();
    updateEditStokInfo();
  });

  // Event listener untuk menghapus baris konversi pada modal edit
  $('#editConversionUnitsContainer').on('click', '.delete-conversion-row', function() {
    $(this).closest('.conversion-row').remove();
    updateEditNoConversionMessage();
  });

  // Event listener untuk perubahan kategori di modal edit
  $('#edit_kategori').on('change', function() {
    updateEditSubKategoriOptions($(this).val());
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
    // Tambahkan CSRF token ke FormData agar tidak terjadi CSRF token mismatch
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

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
    $('#editModalLabel').text('Edit Bahan Baku');
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
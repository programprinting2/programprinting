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
    $('#edit_status_aktif').prop('checked', data.status_aktif);
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
    $('#edit_harga_terakhir').val(data.harga_terakhir);
    
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

    // Tampilkan modal
    $('#editModal').modal('show');
    // Panggil fungsi update stok saat modal ditampilkan dan data dimuat
    updateEditStockUnitLabels();
    updateEditStokInfo();
  });
}

// Inisialisasi tampilan stok saat dokumen siap (di dalam $(document).ready())
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
  });

  // Event listener untuk menghapus baris konversi pada modal edit
  $('#editConversionUnitsContainer').on('click', '.delete-conversion-row', function() {
    $(this).closest('.conversion-row').remove();
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

  // Handle submit form edit
  $('#editForm').on('submit', function(e) {
    e.preventDefault();
    
    // Tampilkan spinner dan nonaktifkan tombol
    const submitBtn = $(this).find('button[type="submit"]');
    const originalBtnText = submitBtn.html();
    submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);
    
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

    // Persiapkan data form
    const formData = new FormData(this);
    
    // Hapus format Rupiah dari harga terakhir
    const hargaTerakhir = formData.get('harga_terakhir');
    if (hargaTerakhir) {
      formData.set('harga_terakhir', hargaTerakhir.replace(/\./g, ''));
    }

    // Set konversi satuan sebagai JSON string dengan format yang diinginkan
    formData.set('konversi_satuan_json', JSON.stringify(konversiRows));

    // Set status_aktif sebagai boolean
    const statusAktif = $('#edit_status_aktif').is(':checked');
    formData.delete('status_aktif'); // Hapus nilai lama jika ada
    formData.append('status_aktif', statusAktif ? '1' : '0');

    // Tambahkan _method untuk Laravel
    formData.append('_method', 'PUT');
    
    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.success) {
          $('#editModal').modal('hide');
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: response.message,
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            window.location.reload();
          });
        }
      },
      error: function(xhr) {
        let errorMessage = 'Terjadi kesalahan saat menyimpan data';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          // Tampilkan semua error validasi
          const errors = xhr.responseJSON.errors;
          errorMessage = Object.values(errors).flat().join('\n');
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMessage = xhr.responseJSON.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Gagal!',
          text: errorMessage
        });
      },
      complete: function() {
        // Kembalikan tombol ke kondisi awal
        submitBtn.html(originalBtnText).prop('disabled', false);
      }
    });
  });
}); 
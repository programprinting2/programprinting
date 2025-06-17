$(document).ready(function() {
  feather.replace();

  // Definisi satuan berdasarkan kategori
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

  // Fungsi untuk mengupdate opsi satuan
  function updateSatuanOptions(kategori) {
    const satuanSelect = $('#satuanUtama');
    satuanSelect.empty();
    
    if (!kategori) {
      // Jika kategori belum dipilih
      satuanSelect.append('<option value="" selected disabled>Pilih kategori terlebih dahulu</option>');
      satuanSelect.prop('disabled', true);
    } else {
      // Jika kategori sudah dipilih
      satuanSelect.prop('disabled', false);
      satuanSelect.append('<option value="" selected disabled>Pilih satuan</option>');
      
      // Tambahkan opsi berdasarkan kategori
      if (satuanByKategori[kategori]) {
        satuanByKategori[kategori].forEach(satuan => {
          satuanSelect.append(`<option value="${satuan.value}">${satuan.label}</option>`);
        });
      }
    }
  }

  // Inisialisasi awal
  updateSatuanOptions(null);

  // Event listener untuk perubahan kategori
  $('#kategori').on('change', function() {
    const selectedKategori = $(this).val();
    updateSatuanOptions(selectedKategori);
    updateStockUnitLabels(selectedKategori);
  });

  // Fungsi untuk mengupdate label unit pada input stok
  function updateStockUnitLabels(kategori) {
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

  // Fungsi untuk mendapatkan opsi satuan berdasarkan kategori
  function getSatuanOptions(kategori) {
    if (satuanByKategori[kategori]) {
      return satuanByKategori[kategori].map(satuan => `<option value="${satuan.value}">${satuan.label}</option>`).join('');
    }
    return '<option value="">Pilih satuan</option>';
  }

  // Fungsi untuk menambahkan baris konversi satuan baru
  $('#tambahKonversi').on('click', function() {
    const selectedKategori = $('#kategori').val();
    const optionsHtml = getSatuanOptions(selectedKategori);
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
  });

  // Event listener untuk menghapus baris konversi
  $('#conversionUnitsContainer').on('click', '.delete-conversion-row', function() {
    $(this).closest('.conversion-row').remove();
  });

  // Perbarui opsi satuan saat kategori berubah
  $('#kategori').on('change', function() {
    const selectedKategori = $(this).val();
    // Perbarui dropdown satuan utama
    updateSatuanOptions(selectedKategori);
    updateStockUnitLabels(selectedKategori);

    // Perbarui dropdown satuan di setiap baris konversi yang sudah ada
    $('.conversion-row').each(function() {
      const dariSelect = $(this).find('select[name*="[dari]"]');
      const keSelect = $(this).find('select[name*="[ke]"]');
      dariSelect.empty().append(getSatuanOptions(selectedKategori));
      keSelect.empty().append(getSatuanOptions(selectedKategori));
    });
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

    // Tambahkan histori harga awal jika ada harga terakhir
    if (hargaTerakhir) {
      const historiHarga = [{
        tanggal: new Date().toISOString().split('T')[0],
        harga: parseFloat(hargaTerakhir.replace(/\./g, '')),
        pemasok: $('#pemasokUtama').find('option:selected').text()
      }];

      formData.set('histori_harga_json', JSON.stringify(historiHarga));
    }

    // Set status_aktif sebagai boolean
    const statusAktif = $('#statusAktif').is(':checked');
    formData.delete('status_aktif'); // Hapus nilai lama jika ada
    formData.append('status_aktif', statusAktif ? '1' : '0');

    return formData;
  }

  // Event listener untuk form submit
  $('#addMaterialForm').on('submit', function(e) {
    e.preventDefault();
    
    // Tampilkan spinner dan nonaktifkan tombol
    const submitBtn = $(this).find('button[type="submit"]');
    const originalBtnText = submitBtn.html();
    submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...').prop('disabled', true);
    
    const formData = new FormData(this);
    const preparedData = prepareFormData(formData);

    $.ajax({
      url: '/backend/master-bahanbaku',
      method: 'POST',
      data: preparedData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: response.message,
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            $('#addMaterialModal').modal('hide');
            location.reload();
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
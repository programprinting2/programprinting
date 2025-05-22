$(function() {
  // Setup AJAX CSRF token
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Inisialisasi format mata uang
  KaryawanHelper.initMoneyFormat();

  // Reset form dan state saat modal ditutup
  $('#modalKaryawan').on('hidden.bs.modal', function() {
    // Reset form
    $('#formKaryawan')[0].reset();
    
    // Reset tab ke awal
    currentTab = 0;
    showTab(0);
    
    // Reset alamat dan rekening
    $('#alamat-list').empty();
    $('#rekening-list').empty();
    $('#komponen-gaji-list').empty();
    
    // Tambahkan alamat dan rekening default
    $('#addAlamat').click();
    $('#addRekening').click();
    
    // Reset total gaji
    KaryawanHelper.hitungTotalGaji();
    
    // Reset error states
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('#karyawanTabs button').removeClass('text-danger');
  });

  // Inisialisasi saat modal dibuka
  $('#modalKaryawan').on('show.bs.modal', function() {
    // Pastikan form bersih
    $('#formKaryawan')[0].reset();
    
    // Reset tab ke awal
    currentTab = 0;
    showTab(0);
    
    // Reset alamat dan rekening
    $('#alamat-list').empty();
    $('#rekening-list').empty();
    $('#komponen-gaji-list').empty();
    
    // Tambahkan alamat dan rekening default
    $('#addAlamat').click();
    $('#addRekening').click();
    
    // Reset total gaji
    KaryawanHelper.hitungTotalGaji();
    
    // Reset error states
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('#karyawanTabs button').removeClass('text-danger');
  });

  // Persiapan form sebelum submit
  KaryawanHelper.prepareFormSubmit('#formKaryawan');

  // Tab navigation logic
  let tabIds = ['utama','alamat','pajak','rekening','gaji'];
  let currentTab = 0;
  
  function showValidationErrors(errors) {
    // Reset semua error state
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    // Tampilkan error untuk setiap field
    $.each(errors, function(field, messages) {
      const input = $(`[name="${field}"]`);
      input.addClass('is-invalid');
      
      // Tambahkan pesan error
      const errorDiv = $('<div>')
        .addClass('invalid-feedback')
        .text(messages[0]); // Ambil pesan error pertama
        
      input.after(errorDiv);
      
      // Jika field ada di tab lain, tampilkan indikator error pada tab tersebut
      const tabContent = input.closest('.tab-pane');
      const tabId = tabContent.attr('id');
      const tabIndex = tabIds.indexOf(tabId);
      
      if (tabIndex !== -1) {
        $(`#tab-${tabId}`).addClass('text-danger');
      }
    });
    
    // Pindah ke tab yang memiliki error pertama
    const firstErrorField = Object.keys(errors)[0];
    const firstErrorInput = $(`[name="${firstErrorField}"]`);
    const firstErrorTab = firstErrorInput.closest('.tab-pane').attr('id');
    const firstErrorTabIndex = tabIds.indexOf(firstErrorTab);
    
    if (firstErrorTabIndex !== -1) {
      currentTab = firstErrorTabIndex;
      showTab(currentTab);
    }
  }

  function showTab(idx) {
    $('#karyawanTabs button').removeClass('active');
    $('#karyawanTabs button').eq(idx).addClass('active');
    $('.tab-pane').removeClass('show active');
    $('#' + tabIds[idx]).addClass('show active');
    $('#nextTab').toggleClass('d-none', idx === tabIds.length-1);
    $('#submitKaryawan').toggleClass('d-none', idx !== tabIds.length-1);
  }
  $('#nextTab').click(function() {
    if(currentTab < tabIds.length-1) {
      currentTab++;
      showTab(currentTab);
    }
  });
  $('#karyawanTabs button').click(function() {
    currentTab = $(this).parent().index();
    showTab(currentTab);
  });
  showTab(0);

  // Alamat dinamis
  function renderAlamatSelect() {
    let options = '';
    $('#alamat-list .alamat-item').each(function(i) {
      let label = $(this).find('input[name^="alamat_label"]').val() || 'Alamat ' + (i+1);
      options += `<option value="${i}">${label}</option>`;
    });
    $('#alamat_utama').html(options);
  }

  function updateAlamatLabel(input) {
    let label = input.val() || 'Alamat ' + ($(input).closest('.alamat-item').index() + 1);
    $(input).closest('.alamat-item').find('.accordion-button .nomor-alamat').text(label);
  }

  $('#addAlamat').click(function() {
    let alamatCount = $('#alamat-list .alamat-item').length + 1;
    let html = `
      <div class="alamat-item accordion mb-3" id="accordionAlamat${alamatCount}">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingAlamat${alamatCount}">
            <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAlamat${alamatCount}" aria-expanded="true" aria-controls="collapseAlamat${alamatCount}">
              <span class="nomor-alamat fw-bold">Alamat ${alamatCount}</span>
            </button>
          </h2>
          <div id="collapseAlamat${alamatCount}" class="accordion-collapse collapse show" aria-labelledby="headingAlamat${alamatCount}" data-bs-parent="#accordionAlamat${alamatCount}">
            <div class="accordion-body">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Label Alamat <span class="text-danger">*</span></label>
                  <input type="text" class="form-control alamat-label-input" name="alamat_label[]" placeholder="Contoh: Rumah, Kost, dll" required>
                </div>
                <div class="col-md-8">
                  <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                  <textarea class="form-control" name="alamat_alamat[]" rows="2" placeholder="Isi alamat lengkap" required></textarea>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Kota/Kabupaten</label>
                  <input type="text" class="form-control" name="alamat_kota[]" placeholder="Kota/Kabupaten">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Provinsi</label>
                  <input type="text" class="form-control" name="alamat_provinsi[]" placeholder="Provinsi">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Kode Pos</label>
                  <input type="text" class="form-control" name="alamat_kode_pos[]" placeholder="Kode Pos">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nama Kontak Darurat</label>
                  <input type="text" class="form-control" name="alamat_kontak_darurat[]" placeholder="Nama kontak darurat">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nomor Telepon Darurat</label>
                  <input type="text" class="form-control" name="alamat_telepon_darurat[]" placeholder="Nomor telepon darurat">
                </div>
                <div class="col-12 text-end">
                  <button type="button" class="btn btn-outline-danger btn-sm removeAlamat">
                    <i data-feather="trash-2" class="icon-sm"></i> Hapus Alamat
                  </button>
              </div>
      </div>
      </div>
      </div>
      </div>`;
    $('#alamat-list').append(html);
    renderAlamatSelect();
    feather.replace();
  });

  // Event listener untuk perubahan label alamat
  $('#alamat-list').on('input', '.alamat-label-input', function() {
    updateAlamatLabel($(this));
    renderAlamatSelect();
  });

  $('#alamat-list').on('click', '.removeAlamat', function() {
    $(this).closest('.alamat-item').remove();
    // Update nomor alamat
    $('#alamat-list .alamat-item').each(function(i) {
      let label = $(this).find('input[name^="alamat_label"]').val() || 'Alamat ' + (i+1);
      $(this).find('.nomor-alamat').text(label);
    });
    renderAlamatSelect();
  });

  // Rekening dinamis
  function renderRekeningSelect() {
    let options = '';
    $('#rekening-list .rekening-item').each(function(i) {
      let label = $(this).find('input[name^="rekening_bank"]').val() || 'Rekening ' + (i+1);
      options += `<option value="${i}">${label}</option>`;
    });
    $('#rekening_utama').html(options);
  }
  $('#addRekening').click(function() {
    let rekeningCount = $('#rekening-list .rekening-item').length + 1;
    let html = `
      <div class="rekening-item accordion mb-3" id="accordionRekening${rekeningCount}">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingRekening${rekeningCount}">
            <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRekening${rekeningCount}" aria-expanded="true" aria-controls="collapseRekening${rekeningCount}">
              <span class="nomor-rekening fw-bold">Rekening ${rekeningCount}</span>
            </button>
          </h2>
          <div id="collapseRekening${rekeningCount}" class="accordion-collapse collapse show" aria-labelledby="headingRekening${rekeningCount}" data-bs-parent="#accordionRekening${rekeningCount}">
            <div class="accordion-body">
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Bank</label>
                  <input type="text" class="form-control rekening-bank-input" name="rekening_bank[]" placeholder="Nama Bank">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Cabang</label>
                  <input type="text" class="form-control" name="rekening_cabang[]" placeholder="Cabang Bank">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Nomor Rekening</label>
                  <input type="text" class="form-control" name="rekening_nomor[]" placeholder="Nomor Rekening">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Nama Pemilik</label>
                  <input type="text" class="form-control" name="rekening_nama_pemilik[]" placeholder="Nama Pemilik">
                </div>
                <div class="col-12 text-end">
                  <button type="button" class="btn btn-outline-danger btn-sm removeRekening">
                    <i data-feather="trash-2" class="icon-sm"></i> Hapus Rekening
                  </button>
                </div>
              </div>
            </div>
      </div>
      </div>
      </div>`;
    $('#rekening-list').append(html);
    renderRekeningSelect();
    feather.replace();
  });

  // Event listener untuk perubahan nama bank
  $('#rekening-list').on('input', '.rekening-bank-input', function() {
    let bank = $(this).val() || 'Rekening ' + ($(this).closest('.rekening-item').index() + 1);
    $(this).closest('.rekening-item').find('.accordion-button .nomor-rekening').text(bank);
    renderRekeningSelect();
  });

  $('#rekening-list').on('click', '.removeRekening', function() {
    $(this).closest('.rekening-item').remove();
    renderRekeningSelect();
  });

  // Komponen Gaji dinamis
  function createKomponenGajiItem(index) {
    return `
      <div class="komponen-gaji-item accordion mb-3" id="accordionKomponen${index}">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingKomponen${index}">
            <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKomponen${index}" aria-expanded="true" aria-controls="collapseKomponen${index}">
              <span class="nomor-komponen fw-bold">Komponen ${index}</span>
            </button>
          </h2>
          <div id="collapseKomponen${index}" class="accordion-collapse collapse show" aria-labelledby="headingKomponen${index}" data-bs-parent="#accordionKomponen${index}">
            <div class="accordion-body">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Tipe</label>
                  <select class="form-select" name="komponen_tipe[]">
                    <option value="tunjangan">Tunjangan</option>
                    <option value="potongan">Potongan</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nama Komponen</label>
                  <input type="text" class="form-control komponen-nama-input" name="komponen_nama[]" placeholder="Contoh: Tunjangan Transport">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label">Jumlah</label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control komponen-nilai money-format" name="komponen_nilai[]">
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Satuan</label>
                  <select class="form-select komponen-satuan" name="komponen_satuan[]">
                    <option value="hari">Per Hari</option>
                    <option value="bulan">Per Bulan</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Tanggal Dibuat</label>
                  <input type="date" class="form-control komponen-tanggal" name="komponen_tanggal[]" value="${new Date().toISOString().split('T')[0]}">
                </div>
              </div>
              <div class="row">
                <div class="col-12 text-end">
                  <button type="button" class="btn btn-outline-danger btn-sm hapus-komponen">
                    <i data-feather="trash-2" class="icon-sm"></i> Hapus Komponen
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // Event listener untuk tombol tambah komponen gaji
  $('#addKomponenGaji').click(function() {
    // Sembunyikan pesan kosong
    $('#empty-komponen-message').hide();
    
    let komponenCount = $('#komponen-gaji-list .komponen-gaji-item').length + 1;
    let html = createKomponenGajiItem(komponenCount);
    
    $('#komponen-gaji-list').append(html);
    
    // Inisialisasi format mata uang pada input nilai yang baru ditambahkan
    const $newKomponen = $('#komponen-gaji-list .komponen-gaji-item:last');
    const $nilaiInput = $newKomponen.find('input[name^="komponen_nilai"]');
    
    // Terapkan event handler untuk format uang
    $nilaiInput.on('input focus', function() {
      KaryawanHelper.applyMoneyFormat($(this));
    });
    
    // Perbarui total dan title
    KaryawanHelper.hitungTotalGaji();
    KaryawanHelper.updateKomponenTitle($newKomponen);
    feather.replace();
  });

  // Event listener untuk input numerik
  $('input[type="number"]').on('input', function() {
    KaryawanHelper.ensureInteger($(this));
  });

  // Event listener untuk perubahan komponen gaji
  $('#komponen-gaji-list').on('input', 
    'input[name^="komponen_nilai"], select[name^="komponen_tipe"], select[name^="komponen_satuan"], input[name^="komponen_nama"]',
    function() {
      KaryawanHelper.hitungTotalGaji();
      KaryawanHelper.updateKomponenTitle($(this).closest('.komponen-gaji-item'));
  });

  // Event listener untuk perubahan gaji pokok dan jam kerja
  $('#gaji_pokok, #estimasi_hari_kerja').on('input', function() {
    KaryawanHelper.hitungTotalGaji();
    KaryawanHelper.hitungGajiPerJam();
  });

  $('#jam_kerja_per_hari').on('input', function() {
    KaryawanHelper.hitungGajiPerJam();
  });

  // Event listener untuk menghapus komponen gaji
  $('#komponen-gaji-list').on('click', '.hapus-komponen', function() {
    $(this).closest('.komponen-gaji-item').remove();
    KaryawanHelper.hitungTotalGaji();
  });

  // Submit form via AJAX
  $('#formKaryawan').submit(function(e) {
    e.preventDefault();
    
    // Reset error states
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('#karyawanTabs button').removeClass('text-danger');

    let data = {
      nama_lengkap: $('#nama_lengkap').val(),
      posisi: $('#posisi').val(),
      departemen: $('#departemen').val(),
      tanggal_masuk: $('#tanggal_masuk').val(),
      tanggal_lahir: $('#tanggal_lahir').val(),
      jenis_kelamin: $('#jenis_kelamin').val(),
      status_pernikahan: $('#status_pernikahan').val(),
      nomor_telepon: $('#nomor_telepon').val(),
      email: $('#email').val(),
      gaji_pokok: $('#gaji_pokok').val(),
      status: $('#status').val(),
      npwp: $('#npwp').val(),
      status_pajak: $('#status_pajak').val(),
      tarif_pajak: $('#tarif_pajak').val(),
      estimasi_hari_kerja: $('#estimasi_hari_kerja').val(),
      jam_kerja_per_hari: $('#jam_kerja_per_hari').val(),
      alamat: [],
      rekening: [],
      alamat_utama: $('#alamat_utama').val(),
      rekening_utama: $('#rekening_utama').val(),
      komponen_gaji: []
    };

    // Collect alamat data
    $('#alamat-list .alamat-item').each(function() {
      data.alamat.push({
        label: $(this).find('input[name^="alamat_label"]').val(),
        alamat: $(this).find('textarea[name^="alamat_alamat"]').val(),
        kota: $(this).find('input[name^="alamat_kota"]').val(),
        provinsi: $(this).find('input[name^="alamat_provinsi"]').val(),
        kode_pos: $(this).find('input[name^="alamat_kode_pos"]').val(),
        kontak_darurat: $(this).find('input[name^="alamat_kontak_darurat"]').val(),
        telepon_darurat: $(this).find('input[name^="alamat_telepon_darurat"]').val()
      });
    });

    // Collect rekening data
    $('#rekening-list .rekening-item').each(function() {
      data.rekening.push({
        bank: $(this).find('input[name^="rekening_bank"]').val(),
        cabang: $(this).find('input[name^="rekening_cabang"]').val(),
        nomor: $(this).find('input[name^="rekening_nomor"]').val(),
        nama_pemilik: $(this).find('input[name^="rekening_nama_pemilik"]').val()
      });
    });

    // Collect komponen gaji data
    $('#komponen-gaji-list .komponen-gaji-item').each(function() {
      data.komponen_gaji.push({
        tipe: $(this).find('select[name^="komponen_tipe"]').val(),
        nama: $(this).find('input[name^="komponen_nama"]').val(),
        nilai: $(this).find('input[name^="komponen_nilai"]').val(),
        satuan: $(this).find('select[name^="komponen_satuan"]').val(),
        tanggal: $(this).find('input[name^="komponen_tanggal"]').val()
      });
    });

    $.ajax({
      url: karyawanStoreUrl,
      method: 'POST',
      data: data,
      success: function(res) {
        if(res.success) {
          // Tampilkan pesan sukses
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: res.message || 'Data karyawan berhasil ditambahkan',
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
          $('#modalKaryawan').modal('hide');
          location.reload();
          });
        } else {
          // Tampilkan pesan error umum
          Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: res.message || 'Terjadi kesalahan saat menyimpan data',
          });
        }
      },
      error: function(xhr) {
        if(xhr.status === 422) {
          // Tampilkan error validasi
          showValidationErrors(xhr.responseJSON.errors);
          
          // Tampilkan pesan error
          Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: 'Silakan periksa kembali data yang Anda masukkan',
          });
        } else {
          // Tampilkan error umum
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
          });
        }
      }
    });
  });

  // Event listener untuk perubahan nama komponen
  $('#komponen-gaji-list').on('input', '.komponen-nama-input, .komponen-nilai, .komponen-satuan', function() {
    KaryawanHelper.updateKomponenTitle($(this).closest('.komponen-gaji-item'));
  });

  // Inisialisasi awal
  $('#addAlamat').click();
  $('#addRekening').click();
  feather.replace();
  KaryawanHelper.hitungTotalGaji();

  // Event listener untuk perubahan input rekening
  $('#rekening-list').on('input', 'input[name^="rekening_bank"], input[name^="rekening_nomor"]', function() {
    renderRekeningSelect();
  });

  // Update judul komponen gaji saat ada perubahan
  function updateKomponenGajiTitles() {
    $('#komponen-gaji-list .komponen-gaji-item').each(function() {
      KaryawanHelper.updateKomponenTitle($(this));
    });
  }

  // Event listener untuk perubahan nilai komponen gaji
  $('#komponen-gaji-list').on('input', 'input[name^="komponen_nilai"], input[name^="komponen_nama"]', function() {
    updateKomponenGajiTitles();
  });

  $('#komponen-gaji-list').on('change', 'select[name^="komponen_satuan"], select[name^="komponen_tipe"]', function() {
    updateKomponenGajiTitles();
  });

  // Event listener untuk perubahan estimasi hari kerja
  $('#estimasi_hari_kerja').on('input', function() {
    updateKomponenGajiTitles();
    KaryawanHelper.hitungGajiPerJam();
  });

  // Event listener untuk perubahan jam kerja per hari
  $('#jam_kerja_per_hari').on('input', function() {
    KaryawanHelper.hitungGajiPerJam();
  });

  // Event listener untuk perubahan gaji pokok
  $('#gaji_pokok').on('input', function() {
    KaryawanHelper.hitungGajiPerJam();
    KaryawanHelper.hitungTotalGaji();
  });

  // Event listener untuk hapus komponen gaji
  $('#komponen-gaji-list').on('click', '.removeKomponenGaji', function() {
    $(this).closest('.komponen-gaji-item').remove();
    updateKomponenGajiTitles();
    KaryawanHelper.hitungTotalGaji();
  });

  // Update judul komponen gaji saat form dibuka
  $('#modalKaryawan').on('shown.bs.modal', function() {
    updateKomponenGajiTitles();
  });
});

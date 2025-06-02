$(function() {
  // Setup AJAX CSRF token
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Inisialisasi format mata uang
  KaryawanHelper.initMoneyFormat('edit_');

  // Persiapan form sebelum submit
  KaryawanHelper.prepareFormSubmit('#formEditKaryawan');

  // Tab navigation logic
  let editTabIds = ['edit-utama','edit-alamat','edit-pajak','edit-rekening','edit-gaji'];
  let currentEditTab = 0;
  
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
      const tabIndex = editTabIds.indexOf(tabId);
      
      if (tabIndex !== -1) {
        $(`#tab-${tabId}`).addClass('text-danger');
      }
    });
    
    // Pindah ke tab yang memiliki error pertama
    const firstErrorField = Object.keys(errors)[0];
    const firstErrorInput = $(`[name="${firstErrorField}"]`);
    const firstErrorTab = firstErrorInput.closest('.tab-pane').attr('id');
    const firstErrorTabIndex = editTabIds.indexOf(firstErrorTab);
    
    if (firstErrorTabIndex !== -1) {
      currentEditTab = firstErrorTabIndex;
      showEditTab(currentEditTab);
    }
  }

  function showEditTab(idx) {
    $('#editKaryawanTabs button').removeClass('active');
    $('#editKaryawanTabs button').eq(idx).addClass('active');
    $('.tab-pane').removeClass('show active');
    $('#' + editTabIds[idx]).addClass('show active');
    $('#editNextTab').toggleClass('d-none', idx === editTabIds.length-1);
    $('#submitEditKaryawan').toggleClass('d-none', idx !== editTabIds.length-1);
  }

  $('#editNextTab').click(function() {
    if(currentEditTab < editTabIds.length-1) {
      currentEditTab++;
      showEditTab(currentEditTab);
    }
  });

  $('#editKaryawanTabs button').click(function() {
    currentEditTab = $(this).parent().index();
    showEditTab(currentEditTab);
  });

  // Alamat dinamis untuk edit
  function renderEditAlamatSelect() {
    let options = '';
    $('#edit-alamat-list .alamat-item').each(function(i) {
      let label = $(this).find('input[name^="alamat_label"]').val() || 'Alamat ' + (i+1);
      options += `<option value="${i}">${label}</option>`;
    });
    $('#edit_alamat_utama').html(options);
  }

  function updateEditAlamatLabel(input) {
    let label = input.val() || 'Alamat ' + ($(input).closest('.alamat-item').index() + 1);
    $(input).closest('.alamat-item').find('.accordion-button .nomor-alamat').text(label);
  }

  $('#editAddAlamat').click(function() {
    let alamatCount = $('#edit-alamat-list .alamat-item').length + 1;
    let html = `
      <div class="alamat-item accordion mb-3" id="editAccordionAlamat${alamatCount}">
        <div class="accordion-item">
          <h2 class="accordion-header" id="editHeadingAlamat${alamatCount}">
            <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#editCollapseAlamat${alamatCount}" aria-expanded="true" aria-controls="editCollapseAlamat${alamatCount}">
              <span class="nomor-alamat fw-bold">Alamat ${alamatCount}</span>
            </button>
          </h2>
          <div id="editCollapseAlamat${alamatCount}" class="accordion-collapse collapse show" aria-labelledby="editHeadingAlamat${alamatCount}" data-bs-parent="#editAccordionAlamat${alamatCount}">
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
        </div>
      </div>`;
    $('#edit-alamat-list').append(html);
    renderEditAlamatSelect();
    feather.replace();
  });

  // Event listener untuk perubahan label alamat di form edit
  $('#edit-alamat-list').on('input', '.alamat-label-input', function() {
    updateEditAlamatLabel($(this));
    renderEditAlamatSelect();
  });

  $('#edit-alamat-list').on('click', '.removeAlamat', function() {
    $(this).closest('.alamat-item').remove();
    // Update nomor alamat
    $('#edit-alamat-list .alamat-item').each(function(i) {
      let label = $(this).find('input[name^="alamat_label"]').val() || 'Alamat ' + (i+1);
      $(this).find('.nomor-alamat').text(label);
    });
    renderEditAlamatSelect();
  });

  // Update label alamat saat data dimuat
  function updateAlamatLabels() {
    $('#edit-alamat-list .alamat-item').each(function() {
      let input = $(this).find('input[name^="alamat_label"]');
      updateEditAlamatLabel(input);
    });
    renderEditAlamatSelect();
  }

  // Rekening dinamis
  function renderEditRekeningSelect() {
    let options = '';
    $('#edit-rekening-list .rekening-item').each(function(i) {
      let bank = $(this).find('input[name^="rekening_bank"]').val();
      let nomor = $(this).find('input[name^="rekening_nomor"]').val();
      
      // Jika bank kosong, gunakan nomor rekening sebagai label
      let label = bank ? `${bank} - ${nomor || ''}` : `Rekening ${i+1}`;
      
      options += `<option value="${i}">${label}</option>`;
    });
    $('#edit_rekening_utama').html(options);
  }

  $('#editAddRekening').click(function() {
    let rekeningCount = $('#edit-rekening-list .rekening-item').length + 1;
    let html = `
      <div class="rekening-item accordion mb-3" id="editAccordionRekening${rekeningCount}">
        <div class="accordion-item">
          <h2 class="accordion-header" id="editHeadingRekening${rekeningCount}">
            <button class="accordion-button bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#editCollapseRekening${rekeningCount}" aria-expanded="true" aria-controls="editCollapseRekening${rekeningCount}">
              <span class="nomor-rekening fw-bold">Rekening ${rekeningCount}</span>
            </button>
          </h2>
          <div id="editCollapseRekening${rekeningCount}" class="accordion-collapse collapse show" aria-labelledby="editHeadingRekening${rekeningCount}" data-bs-parent="#editAccordionRekening${rekeningCount}">
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
                  <button type="button" class="btn btn-outline-danger btn-sm removeEditRekening">
                    <i data-feather="trash-2" class="icon-sm"></i> Hapus Rekening
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>`;
    $('#edit-rekening-list').append(html);
    renderEditRekeningSelect();
    feather.replace();
  });

  // Event listener untuk perubahan nama bank
  $('#edit-rekening-list').on('input', '.rekening-bank-input', function() {
    let bank = $(this).val() || 'Rekening ' + ($(this).closest('.rekening-item').index() + 1);
    $(this).closest('.rekening-item').find('.accordion-button .nomor-rekening').text(bank);
    renderEditRekeningSelect();
  });

  $('#edit-rekening-list').on('click', '.removeEditRekening', function() {
    $(this).closest('.rekening-item').remove();
    renderEditRekeningSelect();
  });

  // Event listener untuk perubahan input rekening
  $('#edit-rekening-list').on('input', 'input[name^="rekening_bank"], input[name^="rekening_nomor"]', function() {
    renderEditRekeningSelect();
  });

  // Komponen Gaji dinamis
  function createKomponenGajiItem(index) {
    return `
      <div class="komponen-gaji-item accordion mb-3" id="accordionKomponen${index}">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingKomponen${index}">
            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKomponen${index}" aria-expanded="false" aria-controls="collapseKomponen${index}">
              <span class="nomor-komponen fw-bold">Komponen ${index}</span>
            </button>
          </h2>
          <div id="collapseKomponen${index}" class="accordion-collapse collapse" aria-labelledby="headingKomponen${index}" data-bs-parent="#accordionKomponen${index}">
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
  $('#editAddKomponenGaji').click(function() {
    // Sembunyikan pesan kosong
    $('#edit-empty-komponen-message').hide();
    
    let komponenCount = $('#edit-komponen-gaji-list .komponen-gaji-item').length + 1;
    let html = createKomponenGajiItem(komponenCount);
    
    $('#edit-komponen-gaji-list').append(html);
    
    // Inisialisasi format mata uang pada input komponen gaji yang baru ditambahkan
    const $newKomponen = $('#edit-komponen-gaji-list .komponen-gaji-item:last');
    const $nilaiInput = $newKomponen.find('input[name^="komponen_nilai"]');
    
    // Terapkan event handler untuk format uang
    $nilaiInput.on('input focus', function() {
      KaryawanHelper.applyMoneyFormat($(this));
    });
    
    // Hitung total gaji setelah menambahkan komponen baru
    KaryawanHelper.hitungTotalGaji('edit_');
    feather.replace();
  });

  // Event listener untuk input numerik
  $('input[type="number"]').on('input', function() {
    KaryawanHelper.ensureInteger($(this));
  });

  // Event listener untuk perubahan komponen gaji
  $('#edit-komponen-gaji-list').on('input', 
    'input[name^="komponen_nilai"], select[name^="komponen_tipe"], select[name^="komponen_satuan"], input[name^="komponen_nama"]',
    function() {
      KaryawanHelper.hitungTotalGaji('edit_');
      KaryawanHelper.updateKomponenTitle($(this).closest('.komponen-gaji-item'));
  });

  // Event listener untuk perubahan gaji pokok dan jam kerja
  $('#edit_gaji_pokok, #edit_estimasi_hari_kerja').on('input', function() {
    KaryawanHelper.hitungTotalGaji('edit_');
    KaryawanHelper.hitungGajiPerJam('edit_');
  });

  $('#edit_jam_kerja_per_hari').on('input', function() {
    KaryawanHelper.hitungGajiPerJam('edit_');
  });

  // Event listener untuk menghapus komponen gaji
  $('#edit-komponen-gaji-list').on('click', '.hapus-komponen', function() {
    $(this).closest('.komponen-gaji-item').remove();
    KaryawanHelper.hitungTotalGaji('edit_');
  });

  // Update judul komponen gaji saat ada perubahan
  function updateKomponenGajiTitles() {
    $('#edit-komponen-gaji-list .komponen-gaji-item').each(function() {
      KaryawanHelper.updateKomponenTitle($(this), 'edit_');
    });
  }

  // Event listener untuk perubahan nilai komponen gaji
  $('#edit-komponen-gaji-list').on('input', 'input[name^="komponen_nilai"], input[name^="komponen_nama"]', function() {
    updateKomponenGajiTitles();
  });

  $('#edit-komponen-gaji-list').on('change', 'select[name^="komponen_satuan"], select[name^="komponen_tipe"]', function() {
    updateKomponenGajiTitles();
  });

  // Event listener untuk perubahan estimasi hari kerja
  $('#edit_estimasi_hari_kerja').on('input', function() {
    updateKomponenGajiTitles();
    KaryawanHelper.hitungGajiPerJam('edit_');
  });

  // Event listener untuk perubahan jam kerja per hari
  $('#edit_jam_kerja_per_hari').on('input', function() {
    KaryawanHelper.hitungGajiPerJam('edit_');
  });

  // Event listener untuk perubahan gaji pokok
  $('#edit_gaji_pokok').on('input', function() {
    KaryawanHelper.hitungGajiPerJam('edit_');
    KaryawanHelper.hitungTotalGaji('edit_');
  });

  // Load data karyawan untuk edit
  window.loadKaryawanData = function(id) {
    $.ajax({
      url: `/api/karyawan/${id}`,
      method: 'GET',
      success: function(data) {
        // Reset form
        $('#formEditKaryawan')[0].reset();
        $('#edit-alamat-list').empty();
        $('#edit-rekening-list').empty();
        $('#edit-komponen-gaji-list').empty();
        
        // Set ID
        $('#edit_id').val(data.id);
        
        // Data Utama
        $('#edit_nama_lengkap').val(data.nama_lengkap);
        $('#edit_departemen').val(data.departemen);
        $('#edit_posisi').val(data.posisi);
        
        // Format tanggal
        if (data.tanggal_lahir) {
          $('#edit_tanggal_lahir').val(data.tanggal_lahir.split('T')[0]);
        }
        if (data.tanggal_masuk) {
          $('#edit_tanggal_masuk').val(data.tanggal_masuk.split('T')[0]);
        }
        
        $('#edit_jenis_kelamin').val(data.jenis_kelamin);
        $('#edit_status_pernikahan').val(data.status_pernikahan);
        $('#edit_nomor_telepon').val(data.nomor_telepon);
        $('#edit_email').val(data.email);
        let gajiPokokInput = $('#edit_gaji_pokok');
        gajiPokokInput.val(data.gaji_pokok || 0);
        // Format nilai gaji pokok
        KaryawanHelper.applyMoneyFormat(gajiPokokInput);
        
        $('#edit_status').val(data.status);
        
        // Alamat
        if (data.alamat && data.alamat.length > 0) {
          data.alamat.forEach(function(alamat) {
            $('#editAddAlamat').click();
            let $lastAlamat = $('#edit-alamat-list .alamat-item:last');
            let $labelInput = $lastAlamat.find('[name="alamat_label[]"]');
            $labelInput.val(alamat.label || '');
            $lastAlamat.find('[name="alamat_alamat[]"]').val(alamat.alamat || '');
            $lastAlamat.find('[name="alamat_kota[]"]').val(alamat.kota || '');
            $lastAlamat.find('[name="alamat_provinsi[]"]').val(alamat.provinsi || '');
            $lastAlamat.find('[name="alamat_kode_pos[]"]').val(alamat.kode_pos || '');
            $lastAlamat.find('[name="alamat_kontak_darurat[]"]').val(alamat.kontak_darurat || '');
            $lastAlamat.find('[name="alamat_telepon_darurat[]"]').val(alamat.telepon_darurat || '');
            
            // Update label accordion
            updateEditAlamatLabel($labelInput);
          });
          $('#edit_alamat_utama').val(data.alamat_utama || 0);
        }
        
        // Pajak
        $('#edit_npwp').val(data.npwp || '');
        $('#edit_status_pajak').val(data.status_pajak || '');
        $('#edit_tarif_pajak').val(data.tarif_pajak || 0);
        
        // Rekening
        if (data.rekening && data.rekening.length > 0) {
          data.rekening.forEach(function(rekening) {
            $('#editAddRekening').click();
            let $lastRekening = $('#edit-rekening-list .rekening-item:last');
            $lastRekening.find('[name="rekening_bank[]"]').val(rekening.bank || '');
            $lastRekening.find('[name="rekening_cabang[]"]').val(rekening.cabang || '');
            $lastRekening.find('[name="rekening_nomor[]"]').val(rekening.nomor || '');
            $lastRekening.find('[name="rekening_nama_pemilik[]"]').val(rekening.nama_pemilik || '');
          });
          $('#edit_rekening_utama').val(data.rekening_utama || 0);
        }
        
        // Gaji dan Jam Kerja
        $('#edit_estimasi_hari_kerja').val(data.estimasi_hari_kerja || 0);
        $('#edit_jam_kerja_per_hari').val(data.jam_kerja_per_hari || 0);
        
        // Trigger perhitungan jam kerja
        let hari = parseInt($('#edit_estimasi_hari_kerja').val()) || 0;
        let jam = parseInt($('#edit_jam_kerja_per_hari').val()) || 0;
        let gaji = parseFloat($('#edit_gaji_pokok').val()) || 0;
        let total_jam = hari * jam;
        let gaji_per_jam = total_jam > 0 ? (gaji / total_jam) : 0;
        
        $('#edit_total_jam_kerja').text(total_jam + ' jam/bulan');
        $('#edit_gaji_per_jam').text('Rp ' + gaji_per_jam.toLocaleString('id-ID', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }));
        
        // Komponen Gaji
        if (data.komponen_gaji && data.komponen_gaji.length > 0) {
          data.komponen_gaji.forEach(function(komponen) {
            $('#editAddKomponenGaji').click();
            let $lastKomponen = $('#edit-komponen-gaji-list .komponen-gaji-item:last');
            $lastKomponen.find('[name="komponen_tipe[]"]').val(komponen.tipe || 'tunjangan');
            $lastKomponen.find('[name="komponen_nama[]"]').val(komponen.nama || '');
            
            // Format nilai uang
            let nilaiInput = $lastKomponen.find('[name="komponen_nilai[]"]');
            nilaiInput.val(komponen.nilai || 0);
            KaryawanHelper.applyMoneyFormat(nilaiInput);
            
            $lastKomponen.find('[name="komponen_satuan[]"]').val(komponen.satuan || 'hari');
            $lastKomponen.find('[name="komponen_tanggal[]"]').val(komponen.tanggal || new Date().toISOString().split('T')[0]);
            
            // Update komponen title
            KaryawanHelper.updateKomponenTitle($lastKomponen);
          });
        }
        
        // Hitung ulang total gaji
        KaryawanHelper.hitungTotalGaji('edit_');
        KaryawanHelper.hitungGajiPerJam('edit_');
        
        // Reset tab ke awal
        currentEditTab = 0;
        showEditTab(0);
        
        // Tampilkan modal
        $('#modalEditKaryawan').modal('show');
      },
      error: function(xhr) {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Gagal memuat data karyawan',
        });
      }
    });
  };

  // Submit form edit via AJAX
  $('#formEditKaryawan').submit(function(e) {
    e.preventDefault();
    
    // Reset error states
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('#editKaryawanTabs button').removeClass('text-danger');

    // Nonaktifkan tombol simpan
    const submitButton = $(this).find('button[type="submit"]');
    const originalText = submitButton.html();
    submitButton.prop('disabled', true);
    submitButton.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');

    let id = $('#edit_id').val();
    let url = karyawanUpdateUrl.replace(':id', id);

    let data = {
      nama_lengkap: $('#edit_nama_lengkap').val(),
      posisi: $('#edit_posisi').val(),
      departemen: $('#edit_departemen').val(),
      tanggal_masuk: $('#edit_tanggal_masuk').val(),
      tanggal_lahir: $('#edit_tanggal_lahir').val(),
      jenis_kelamin: $('#edit_jenis_kelamin').val(),
      status_pernikahan: $('#edit_status_pernikahan').val(),
      nomor_telepon: $('#edit_nomor_telepon').val(),
      email: $('#edit_email').val(),
      gaji_pokok: $('#edit_gaji_pokok').val(),
      status: $('#edit_status').val(),
      npwp: $('#edit_npwp').val(),
      status_pajak: $('#edit_status_pajak').val(),
      tarif_pajak: $('#edit_tarif_pajak').val(),
      estimasi_hari_kerja: $('#edit_estimasi_hari_kerja').val(),
      jam_kerja_per_hari: $('#edit_jam_kerja_per_hari').val(),
      alamat: [],
      rekening: [],
      alamat_utama: $('#edit_alamat_utama').val(),
      rekening_utama: $('#edit_rekening_utama').val(),
      komponen_gaji: []
    };

    // Collect alamat data
    $('#edit-alamat-list .alamat-item').each(function() {
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
    $('#edit-rekening-list .rekening-item').each(function() {
      data.rekening.push({
        bank: $(this).find('input[name^="rekening_bank"]').val(),
        cabang: $(this).find('input[name^="rekening_cabang"]').val(),
        nomor: $(this).find('input[name^="rekening_nomor"]').val(),
        nama_pemilik: $(this).find('input[name^="rekening_nama_pemilik"]').val()
      });
    });

    // Collect komponen gaji data
    $('#edit-komponen-gaji-list .komponen-gaji-item').each(function() {
      data.komponen_gaji.push({
        tipe: $(this).find('select[name^="komponen_tipe"]').val(),
        nama: $(this).find('input[name^="komponen_nama"]').val(),
        nilai: $(this).find('input[name^="komponen_nilai"]').val(),
        satuan: $(this).find('select[name^="komponen_satuan"]').val(),
        tanggal: $(this).find('input[name^="komponen_tanggal"]').val()
      });
    });

    $.ajax({
      url: url,
      method: 'PUT',
      data: data,
      success: function(res) {
        if(res.success) {
          // Tampilkan pesan sukses
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: res.message || 'Data karyawan berhasil diperbarui',
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            $('#modalEditKaryawan').modal('hide');
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
        // Aktifkan kembali tombol simpan
        submitButton.prop('disabled', false);
        submitButton.html(originalText);
      }
    });
  });

  // Delete confirmation
  $('.btn-delete-karyawan').click(function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    let nama = $(this).data('nama');
    
    Swal.fire({
      title: 'Hapus Data Karyawan?',
      text: `Anda akan menghapus data karyawan "${nama}". Data yang sudah dihapus tidak dapat dikembalikan.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        // Submit form delete
        $(`#formDeleteKaryawan${id}`).submit();
      }
    });
  });

  // Update judul komponen gaji saat form dibuka
  $('#modalEditKaryawan').on('shown.bs.modal', function() {
    updateKomponenGajiTitles();
  });
}); 
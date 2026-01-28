/**
 * BahanBaku Helper - Fungsi-fungsi umum untuk manajemen bahan baku
 */
if (typeof BahanBakuHelper === 'undefined') {
    window.BahanBakuHelper = {
        /**
         * Format angka menjadi format mata uang Rupiah
         * @param {number} amount - Jumlah uang
         * @returns {string} Format Rupiah
         */
        formatCurrency: function(amount) {
            return amount.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        },

        /**
         * Format angka dengan pemisah ribuan
         * @param {number} number - Angka yang akan diformat
         * @returns {string} Format angka dengan pemisah ribuan
         */
        formatNumber: function(number) {
            return number.toString().replace(/,/g, '');
        },

        getNumericValue: function($input) {
          let value = $input.val() || '';

          // Hapus prefix seperti "Rp " jika ada
          value = value.replace(/^Rp\s*/, '');

          value = value.replace(/,/g, '');

          return parseFloat(value) || 0;
        },
        /**
         * Inisialisasi format mata uang untuk semua input uang
         */
        initMoneyFormat: function() {
            // Format saat focus
            $(document).on('focus', '.money-format', (e) => {
                this.applyMoneyFormat($(e.target));
            });
            
            // Format saat input
            $(document).on('input', '.money-format', (e) => {
                this.applyMoneyFormat($(e.target));
            });
        },

        /**
         * Persiapan data form sebelum submit
         * @param {string} formSelector - Selector form
         */
        prepareFormSubmit: function(formSelector) {
            $(formSelector).on('submit', function() {
                // Hapus format dari semua input uang sebelum submit
                $(this).find('input[type="text"].money-format').each(function() {
                    const rawValue = $(this).val().replace(/\./g, '');
                    $(this).val(rawValue);
                });
            });
        }
    };
}

// Fungsi untuk mengupdate opsi sub-kategori di form modal
function updateSubKategoriOptions(selectedKategoriId) {
  const subKategoriSelect = $('#sub_kategori_id');
  subKategoriSelect.empty();
  subKategoriSelect.prop('disabled', true);
  subKategoriSelect.append('<option value="" selected disabled>Pilih sub-kategori</option>');

  if (selectedKategoriId && window.subKategoriList) {
    const filtered = window.subKategoriList.filter(sub => sub.detail_parameter_id == selectedKategoriId);
    filtered.forEach(sub => {
      subKategoriSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
      });
    if (filtered.length > 0) subKategoriSelect.prop('disabled', false);
  }
}

// Fungsi untuk mengupdate opsi sub-kategori di edit modal
function updateEditSubKategoriOptions(selectedKategoriId, currentSubKategoriId = null) {
  const subKategoriSelect = $('#edit_sub_kategori_id');
  subKategoriSelect.empty();
  subKategoriSelect.prop('disabled', true);
  subKategoriSelect.append('<option value="" selected disabled>Pilih sub-kategori</option>');

  if (selectedKategoriId && window.subKategoriList) {
    const filtered = window.subKategoriList.filter(sub => sub.detail_parameter_id == selectedKategoriId);
    filtered.forEach(sub => {
      subKategoriSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
      });
    if (filtered.length > 0) subKategoriSelect.prop('disabled', false);
    if (currentSubKategoriId) {
      subKategoriSelect.val(currentSubKategoriId);
    }
  }
} 

// Fungsi untuk mengupdate opsi sub-satuan di form modal
function updateSubSatuanOptions(selectedSatuanId, selector) {
  const subSatuanSelect = $(selector);
  subSatuanSelect.empty();
  subSatuanSelect.prop('disabled', true);
  subSatuanSelect.append('<option value="" selected disabled>Pilih detail satuan</option>');

  if (selectedSatuanId && window.subSatuanList) {
    const filtered = window.subSatuanList.filter(sub => sub.detail_parameter_id == selectedSatuanId);
    filtered.forEach(sub => {
      subSatuanSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
    });
    if (filtered.length > 0) subSatuanSelect.prop('disabled', false);
  }
}

// Fungsi untuk mengupdate opsi sub-satuan di edit modal
function updateEditSubSatuanOptions(selectedSatuanId, currentSubSatuanId = null) {
  const subSatuanSelect = $('#edit_sub_satuan');
  subSatuanSelect.empty();
  subSatuanSelect.prop('disabled', true);
  subSatuanSelect.append('<option value="" selected disabled>Pilih detail satuan</option>');

  if (selectedSatuanId && window.subSatuanList) {
    const filtered = window.subSatuanList.filter(sub => sub.detail_parameter_id == selectedSatuanId);
    filtered.forEach(sub => {
      subSatuanSelect.append(`<option value="${sub.id}">${sub.nama_sub_detail_parameter}</option>`);
    });
    if (filtered.length > 0) subSatuanSelect.prop('disabled', false);
    if (currentSubSatuanId) {
      subSatuanSelect.val(currentSubSatuanId);
    }
  }
}
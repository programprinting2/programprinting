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
         * Konversi nilai metric unit
         * @param {number} value - Nilai yang akan dikonversi
         * @param {string} fromUnit - Satuan asal ('cm', 'mm', 'm')
         * @param {string} toUnit - Satuan tujuan ('cm', 'mm', 'm')
         * @param {boolean} isArea - Apakah konversi untuk luas (default: false)
         * @returns {number} Nilai hasil konversi
         */
        convertMetricUnit: function(value, fromUnit, toUnit, isArea = false) {
          const conversionFactors = {
              'cm_to_mm': 10,    // 1 cm = 10 mm
              'cm_to_m': 0.01,   // 1 cm = 0.01 m
              'mm_to_cm': 0.1,   // 1 mm = 0.1 cm
              'mm_to_m': 0.001,  // 1 mm = 0.001 m
              'm_to_cm': 100,    // 1 m = 100 cm
              'm_to_mm': 1000    // 1 m = 1000 mm
          };
      
          const key = `${fromUnit}_to_${toUnit}`;
      
          if (conversionFactors[key]) {
              if (isArea) {
                  // Untuk luas, kuadratkan faktor konversi
                  return value * (conversionFactors[key] * conversionFactors[key]);
              } else {
                  return value * conversionFactors[key];
              }
          }
          return value;
        },

        /**
        * Update dimensi berdasarkan perubahan satuan metric
        * @param {string} newUnit - Satuan baru ('cm', 'mm', 'm')
        * @param {string} oldUnit - Satuan lama ('cm', 'mm', 'm')
        * @param {Object} selectors - Object berisi selector untuk input dimensi
        * @param {string} selectors.lebar - Selector input lebar
        * @param {string} selectors.panjang - Selector input panjang
        * @param {string} selectors.luas - Selector input luas
        * @param {string} selectors.labelLebar - Selector label lebar
        * @param {string} selectors.labelPanjang - Selector label panjang
        * @param {string} selectors.labelLuas - Selector label luas
        */
        updateMetricDimensions: function(newUnit, oldUnit, selectors) {
          // Update labels
          const unitMap = {
              'cm': { label: 'cm', area: 'cm²' },
              'mm': { label: 'mm', area: 'mm²' },
              'm': { label: 'm', area: 'm²' }
          };

          $(selectors.labelLebar).text(unitMap[newUnit].label);
          $(selectors.labelPanjang).text(unitMap[newUnit].label);
          $(selectors.labelLuas).text(unitMap[newUnit].area);

          // Konversi nilai dimensi jika satuan berbeda
          if (oldUnit && oldUnit !== newUnit) {
              // Konversi lebar
              const currentLebar = parseFloat($(selectors.lebar).val()) || 0;
              if (currentLebar > 0) {
                  const newLebar = this.convertMetricUnit(currentLebar, oldUnit, newUnit);
                  $(selectors.lebar).val(newLebar.toFixed(2));
              }

              // Konversi panjang
              const currentPanjang = parseFloat($(selectors.panjang).val()) || 0;
              if (currentPanjang > 0) {
                  const newPanjang = this.convertMetricUnit(currentPanjang, oldUnit, newUnit);
                  $(selectors.panjang).val(newPanjang.toFixed(2));
              }

              // Hitung ulang luas setelah konversi
              const newLebar = parseFloat($(selectors.lebar).val()) || 0;
              const newPanjang = parseFloat($(selectors.panjang).val()) || 0;
              const newLuas = newLebar * newPanjang;
              $(selectors.luas).val(newLuas.toFixed(2));
          }
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
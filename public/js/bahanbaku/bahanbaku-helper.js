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
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        /**
         * Mengaplikasikan format mata uang pada input
         * @param {jQuery} $input - Element input yang akan diformat
         */
        applyMoneyFormat: function($input) {
            // Simpan posisi kursor
            const cursorPos = $input[0].selectionStart;
            const cursorEnd = $input[0].selectionEnd;
            
            // Ambil nilai tanpa format
            let value = $input.val().replace(/\./g, '');
            
            if (value !== '') {
                // Hitung perbedaan panjang sebelum dan sesudah format
                const lengthBefore = $input.val().length;
                
                // Format angka
                const formattedValue = this.formatNumber(value);
                $input.val(formattedValue);
                
                // Hitung perbedaan panjang
                const lengthAfter = formattedValue.length;
                const lengthDiff = lengthAfter - lengthBefore;
                
                // Sesuaikan posisi kursor
                if (cursorPos !== undefined) {
                    $input[0].setSelectionRange(cursorPos + lengthDiff, cursorEnd + lengthDiff);
                }
            }
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

// Deklarasi variabel global untuk data sub-kategori
let subKategoriParametersData = {};

// Mapping kategori ke nama parameter sub-kategori
const kategoriToSubKategoriMap = {
  'Bahan Lembaran': 'SUB KATEGORI BAHAN LEMBARAN',
  'Bahan Roll': 'SUB KATEGORI BAHAN ROLL',
  'Bahan Cair': 'SUB KATEGORI BAHAN CAIR',
  'Bahan Berat': 'SUB KATEGORI BAHAN BERAT',
  'Bahan Unit/Biji': 'SUB KATEGORI BAHAN UNIT/BIJI',
  'Bahan Paket/Set': 'SUB KATEGORI BAHAN PAKET/SET',
  'Bahan Waktu/Jasa': 'SUB KATEGORI BAHAN WAKTU/JASA',
};

// Fungsi untuk menginisialisasi data sub-kategori
function initSubKategoriData(data) {
  subKategoriParametersData = data;
}

// Fungsi untuk mengupdate opsi sub-kategori di form modal
function updateSubKategoriOptions(selectedKategori) {
  const subKategoriSelect = $('#subKategori');
  subKategoriSelect.empty();
  subKategoriSelect.prop('disabled', true);
  subKategoriSelect.append('<option value="" selected disabled>Pilih sub-kategori</option>');

  if (selectedKategori && kategoriToSubKategoriMap[selectedKategori]) {
    const parameterName = kategoriToSubKategoriMap[selectedKategori];
    if (subKategoriParametersData[parameterName] && subKategoriParametersData[parameterName].details) {
      subKategoriParametersData[parameterName].details.forEach(detail => {
        subKategoriSelect.append(`<option value="${detail.nama_detail_parameter}">${detail.nama_detail_parameter}</option>`);
      });
      subKategoriSelect.prop('disabled', false);
    }
  }
}

// Fungsi untuk mengupdate opsi sub-kategori di edit modal
function updateEditSubKategoriOptions(selectedKategori, currentSubKategori = null) {
  const subKategoriSelect = $('#edit_sub_kategori');
  subKategoriSelect.empty();
  subKategoriSelect.prop('disabled', true);
  subKategoriSelect.append('<option value="" selected disabled>Pilih sub-kategori</option>');

  if (selectedKategori && kategoriToSubKategoriMap[selectedKategori]) {
    const parameterName = kategoriToSubKategoriMap[selectedKategori];
    if (subKategoriParametersData[parameterName] && subKategoriParametersData[parameterName].details) {
      subKategoriParametersData[parameterName].details.forEach(detail => {
        subKategoriSelect.append(`<option value="${detail.nama_detail_parameter}">${detail.nama_detail_parameter}</option>`);
      });
      subKategoriSelect.prop('disabled', false);

      // Set nilai yang sudah ada jika ada
      if (currentSubKategori) {
        subKategoriSelect.val(currentSubKategori);
      }
    }
  }
} 
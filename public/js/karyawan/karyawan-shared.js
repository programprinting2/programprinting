/**
 * Karyawan Helper - Fungsi-fungsi umum untuk manajemen karyawan
 */
if (typeof KaryawanHelper === 'undefined') {
  window.KaryawanHelper = {
    /**
     * Menghitung total gaji berdasarkan gaji pokok dan komponen-komponennya
     * @param {string} prefix - Prefix untuk ID elemen ('edit_' untuk form edit, '' untuk form tambah)
     * @returns {number} Total gaji dalam bentuk integer
     */
    hitungTotalGaji: function(prefix = '') {
      
      // Pastikan kita mendapatkan nilai numerik dari input gaji pokok
      const gajiPokokInput = $(`#${prefix}gaji_pokok`).val();
      let total = Math.round(parseFloat(gajiPokokInput ? gajiPokokInput.replace(/\./g, '') : 0)) || 0;
      let estimasiHari = parseInt($(`#${prefix}estimasi_hari_kerja`).val()) || 0;
      
      
      // Hitung komponen gaji
      const komponenListSelector = prefix === 'edit_' ? '#edit-komponen-gaji-list' : '#komponen-gaji-list';
     
      $(`${komponenListSelector} .komponen-gaji-item`).each(function() {
        let tipe = $(this).find('select[name^="komponen_tipe"]').val();
        // Pastikan kita mendapatkan nilai numerik dari input komponen gaji
        let nilaiInput = $(this).find('input[name^="komponen_nilai"]').val();
        let nominal = Math.round(parseFloat(nilaiInput ? nilaiInput.replace(/\./g, '') : 0)) || 0;
        let satuan = $(this).find('select[name^="komponen_satuan"]').val();
        
        if (satuan === 'hari') {
          nominal = nominal * estimasiHari;
        }
        
        if (tipe === 'potongan') {
          total -= nominal;
        } else {
          total += nominal;
        }
      });

      // Update tampilan
      $(`#${prefix}total_gaji_bulan`).text(this.formatCurrency(total));
      
      // Toggle pesan kosong
      const emptyMsgSelector = prefix === 'edit_' ? '#edit-empty-komponen-message' : '#empty-komponen-message';
      const komponenCount = $(`${komponenListSelector} .komponen-gaji-item`).length;
      $(emptyMsgSelector).toggle(komponenCount === 0);
      
      return total;
    },

    /**
     * Menghitung gaji per jam berdasarkan gaji pokok dan jam kerja
     * @param {string} prefix - Prefix untuk ID elemen
     */
    hitungGajiPerJam: function(prefix = '') {
      let hari = parseInt($(`#${prefix}estimasi_hari_kerja`).val()) || 0;
      let jam = parseInt($(`#${prefix}jam_kerja_per_hari`).val()) || 0;
      let gaji = Math.round(parseFloat($(`#${prefix}gaji_pokok`).val().replace(/\./g, ''))) || 0;
      let total_jam = hari * jam;
      let gaji_per_jam = total_jam > 0 ? Math.round(gaji / total_jam) : 0;
      
      $(`#${prefix}total_jam_kerja`).text(`${total_jam} jam/bulan`);
      $(`#${prefix}gaji_per_jam`).text(this.formatCurrency(gaji_per_jam));
    },

    /**
     * Memperbarui judul komponen gaji pada accordion
     * @param {jQuery} $komponen - Element komponen gaji
     * @param {string} prefix - Prefix untuk ID elemen ('edit_' untuk form edit, '' untuk form tambah)
     */
    updateKomponenTitle: function($komponen, prefix = '') {
      let nama = $komponen.find('input[name^="komponen_nama"]').val() || 
                 'Komponen ' + ($komponen.index() + 1);
      let nilai = Math.round(parseFloat($komponen.find('input[name^="komponen_nilai"]').val().replace(/\./g, ''))) || 0;
      let satuan = $komponen.find('select[name^="komponen_satuan"]').val();
      let tipe = $komponen.find('select[name^="komponen_tipe"]').val();
      let estimasiHari = parseInt($(`#${prefix}estimasi_hari_kerja`).val()) || 0;
      
      // Hitung konversi nilai
      let nilaiPerHari = satuan === 'hari' ? nilai : Math.round(nilai / estimasiHari);
      let nilaiPerBulan = satuan === 'bulan' ? nilai : nilai * estimasiHari;
      
      // Format teks
      let nilaiText = '';
      if (satuan === 'hari') {
        nilaiText = `${this.formatCurrency(nilai)}/hari, ${this.formatCurrency(nilaiPerBulan)}/bulan`;
      } else {
        nilaiText = `${this.formatCurrency(nilaiPerHari)}/hari, ${this.formatCurrency(nilai)}/bulan`;
      }
      
      let tipeText = tipe === 'potongan' ? '(Potongan)' : '';
      
      $komponen.find('.accordion-button .nomor-komponen').text(
        `${nama} - ${nilaiText} ${tipeText}`
      );
    },

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
     * Memastikan input numerik selalu integer
     * @param {jQuery} $input - Element input
     */
    ensureInteger: function($input) {
      let value = $input.val();
      if (value) {
        $input.val(Math.round(parseFloat(value.replace(/\./g, ''))));
      }
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
     * @param {string} prefix - Prefix untuk ID elemen
     */
    initMoneyFormat: function(prefix = '') {
      // Inisialisasi format untuk gaji pokok
      const gajiPokokSelector = `#${prefix}gaji_pokok`;
      
      // Format saat focus
      $(document).on('focus', gajiPokokSelector, (e) => {
        this.applyMoneyFormat($(e.target));
      });
      
      // Format saat input
      $(document).on('input', gajiPokokSelector, (e) => {
        this.applyMoneyFormat($(e.target));
      });
      
      // Format untuk komponen gaji
      const komponenNilaiSelector = `#${prefix}komponen-gaji-list input[name^="komponen_nilai"]`;
      
      // Format saat focus
      $(document).on('focus', komponenNilaiSelector, (e) => {
        this.applyMoneyFormat($(e.target));
      });
      
      // Format saat input
      $(document).on('input', komponenNilaiSelector, (e) => {
        this.applyMoneyFormat($(e.target));
      });
    },

    /**
     * Mengambil nilai numerik murni dari input berformat
     * @param {jQuery} $input - Element input
     * @returns {number} Nilai numerik
     */
    getNumericValue: function($input) {
      return parseInt($input.val().replace(/\./g, '')) || 0;
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
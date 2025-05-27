/**
 * Pelanggan Helper - Fungsi-fungsi umum untuk manajemen pelanggan
 */
if (typeof PelangganHelper === 'undefined') {
    window.PelangganHelper = {
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
         * Inisialisasi format mata uang untuk input total piutang
         * @param {string} prefix - Prefix untuk ID elemen ('edit_' untuk form edit, '' untuk form tambah)
         */
        initMoneyFormat: function(prefix = '') {
            const selector = `#${prefix}batas_total_piutang_nilai`;
            
            // Format saat focus
            $(document).on('focus', selector, (e) => {
                this.applyMoneyFormat($(e.target));
            });
            
            // Format saat input
            $(document).on('input', selector, (e) => {
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
                // Hapus format dari input total piutang sebelum submit
                const $input = $(this).find('#batas_total_piutang_nilai, #edit_batas_total_piutang_nilai');
                if ($input.length) {
                    const rawValue = $input.val().replace(/\./g, '');
                    $input.val(rawValue);
                }
            });
        }
    };
} 
/**
 * Pembelian Helper - Fungsi-fungsi umum untuk manajemen pembelian
 */
if (typeof PembelianHelper === 'undefined') {
    window.PembelianHelper = {
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
         * Format angka decimal dengan pemisah ribuan dan desimal
         * @param {number} number - Angka yang akan diformat
         * @param {number} decimals - Jumlah digit desimal
         * @returns {string} Format angka dengan desimal
         */
        formatDecimal: function(number, decimals = 2) {
            return number.toLocaleString('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
            });
        },
        
        /**
         * Format mata uang Rupiah dengan desimal
         * @param {number} amount - Jumlah uang
         * @returns {string} Format Rupiah dengan desimal
         */
        formatCurrencyDecimal: function(amount) {
            return amount.toLocaleString('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
            });
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
         * Inisialisasi format mata uang untuk semua input uang di form pembelian
         */
        initMoneyFormat: function() {
            // Format untuk input harga bahan baku
            // const hargaInputSelector = '#hargaInput';
            
            // // Format saat focus
            // $(document).on('focus', hargaInputSelector, (e) => {
            //     this.applyMoneyFormat($(e.target));
            // });
            
            // // Format saat input
            // $(document).on('input', hargaInputSelector, (e) => {
            //     this.applyMoneyFormat($(e.target));
            // });

            // Format untuk input biaya tambahan
            const biayaInputSelectors = [
                'input[name="jumlah_diskon"]',
                'input[name="biaya_pengiriman"]',
                'input[name="biaya_lain"]',
                'input[name="nota_kredit"]'
            ];

            // biayaInputSelectors.forEach(selector => {
            //     // Format saat focus
            //     $(document).on('focus', selector, (e) => {
            //         this.applyMoneyFormat($(e.target));
            //     });
                
            //     // Format saat input
            //     $(document).on('input', selector, (e) => {
            //         this.applyMoneyFormat($(e.target));
            //     });
            // });

            // Format untuk input edit item (dinamis)
            // $(document).on('focus', '.input-edit-harga', (e) => {
            //     this.applyMoneyFormat($(e.target));
            // });
            
            // $(document).on('input', '.input-edit-harga', (e) => {
            //     this.applyMoneyFormat($(e.target));
            // });

            // Inisialisasi format untuk input yang sudah ada nilai (edit mode)
            $(document).ready(() => {
                // Format input harga bahan baku
                // const $hargaInput = $(hargaInputSelector);
                // if ($hargaInput.length && $hargaInput.val() !== '0') {
                //     this.applyMoneyFormat($hargaInput);
                // }

                // Format input biaya tambahan
                biayaInputSelectors.forEach(selector => {
                    const $input = $(selector);
                    if ($input.length && $input.val() !== '0') {
                        this.applyMoneyFormat($input);
                    }
                });
            });
        },

        getNumericValue: function($input) {
            const inputType = $input.attr('type');
            const inputId = $input.attr('id')

            if (inputId === 'hargaInput') {
                return parseFloat($input.val()) || 0;
            }
            
            if (inputType === 'number') {
                return parseFloat($input.val()) || 0;
            }
            return parseFloat($input.val().replace(/\./g, '').replace(',', '.')) || 0;
        },

        /**
         * Persiapan data form sebelum submit
         * @param {string} formSelector - Selector form
         */
        prepareFormSubmit: function(formSelector) {
            $(formSelector).on('submit', function() {
                // Hapus format dari input harga bahan baku
                // const $hargaInput = $('#hargaInput');
                // if ($hargaInput.length) {
                //     const rawValue = $hargaInput.val().replace(/\./g, '');
                //     $hargaInput.val(rawValue);
                // }

                // Hapus format dari input biaya tambahan
                const biayaInputs = [
                    'input[name="jumlah_diskon"]',
                    'input[name="biaya_pengiriman"]',
                    'input[name="biaya_lain"]',
                    'input[name="nota_kredit"]'
                ];

                biayaInputs.forEach(selector => {
                    const $input = $(this).find(selector);
                    if ($input.length) {
                        const rawValue = $input.val().replace(/\./g, '');
                        $input.val(rawValue);
                    }
                });

                $(this).find('input[type="text"].money-format').each(function() {
                    const rawValue = $(this).val().replace(/\./g, '');
                    $(this).val(rawValue);
                });

                // Hapus format dari input edit item yang sedang aktif
                $(this).find('.input-edit-harga').each(function() {
                    const rawValue = $(this).val().replace(/\./g, '');
                    $(this).val(rawValue);
                });
            });
        },

        /**
         * Inisialisasi semua fitur helper pembelian
         */
        init: function() {
            this.initMoneyFormat();
            this.prepareFormSubmit('#addForm, #editForm');
        }
    };
} 
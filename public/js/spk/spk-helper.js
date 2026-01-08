/**
 * SPK Helper - Fungsi-fungsi umum untuk manajemen SPK
 */
if (typeof SPKHelper === 'undefined') {
    window.SPKHelper = {
        /**
         * Format angka menjadi format mata uang Rupiah
         * @param {number} amount - Jumlah uang
         * @returns {string} Format Rupiah
         */
        formatCurrency: function(amount) {
            if (!amount || isNaN(amount)) return 'Rp 0';
            return 'Rp ' + amount.toLocaleString('id-ID');
        },

        /**
         * Format angka dengan pemisah ribuan
         * @param {number} number - Angka yang akan diformat
         * @returns {string} Format angka dengan pemisah ribuan
         */
        formatNumber: function(number) {
            if (!number || isNaN(number)) return '0';
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        /**
         * Ambil nilai numerik dari input (dengan atau tanpa format)
         * @param {string|number} value - Nilai input
         * @returns {number} Nilai numerik
         */
        getNumericValue: function(value) {
            if (typeof value === 'number') return value;
            if (typeof value === 'string') {
                // Hapus semua karakter non-digit kecuali titik dan koma
                const cleanValue = value.replace(/[^\d.,]/g, '');
                // Ganti koma dengan titik untuk parsing
                const normalizedValue = cleanValue.replace(',', '.');
                const parsed = parseFloat(normalizedValue);
                return isNaN(parsed) ? 0 : parsed;
            }
            return 0;
        },

        /**
         * Format ukuran file dalam bytes
         * @param {number} bytes - Ukuran file dalam bytes
         * @returns {string} Format ukuran file
         */
        formatBytes: function(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        /**
         * Validasi input numerik
         * @param {string} value - Nilai input
         * @param {number} min - Nilai minimum
         * @param {number} max - Nilai maksimum
         * @returns {boolean} Valid atau tidak
         */
        validateNumericInput: function(value, min = 0, max = Infinity) {
            const num = this.getNumericValue(value);
            return num >= min && num <= max;
        },

        /**
         * Generate ID unik untuk item
         * @returns {string} ID unik
         */
        generateUniqueId: function() {
            return Date.now().toString(36) + Math.random().toString(36).substr(2);
        },

        /**
         * Deep clone object/array
         * @param {*} obj - Object yang akan di-clone
         * @returns {*} Clone dari object
         */
        deepClone: function(obj) {
            if (obj === null || typeof obj !== 'object') return obj;
            if (obj instanceof Date) return new Date(obj.getTime());
            if (obj instanceof Array) return obj.map(item => this.deepClone(item));
            if (typeof obj === 'object') {
                const cloned = {};
                for (const key in obj) {
                    if (obj.hasOwnProperty(key)) {
                        cloned[key] = this.deepClone(obj[key]);
                    }
                }
                return cloned;
            }
        },

        /**
         * Debounce function untuk optimasi performance
         * @param {Function} func - Function yang akan di-debounce
         * @param {number} wait - Waktu tunggu dalam ms
         * @returns {Function} Function yang sudah di-debounce
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Show notification dengan SweetAlert
         * @param {string} title - Judul notifikasi
         * @param {string} message - Pesan notifikasi
         * @param {string} type - Tipe notifikasi (success, error, warning, info)
         */
        showNotification: function(title, message, type = 'info') {
            if (typeof Swal !== 'undefined') {
                Swal.fire(title, message, type);
            } else {
                // Fallback ke alert biasa jika SweetAlert tidak tersedia
                alert(`${title}: ${message}`);
            }
        },

        /**
         * Confirm dialog dengan SweetAlert
         * @param {string} title - Judul konfirmasi
         * @param {string} message - Pesan konfirmasi
         * @param {string} confirmText - Teks tombol konfirmasi
         * @param {string} cancelText - Teks tombol batal
         * @returns {Promise<boolean>} Promise yang resolve ke boolean
         */
        confirmDialog: function(title, message, confirmText = 'Ya', cancelText = 'Batal') {
            if (typeof Swal !== 'undefined') {
                return Swal.fire({
                    title: title,
                    text: message,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: cancelText,
                    reverseButtons: true
                }).then((result) => {
                    return result.isConfirmed;
                });
            } else {
                // Fallback ke confirm biasa
                return Promise.resolve(confirm(`${title}: ${message}`));
            }
        },

        /**
         * Inisialisasi helper
         */
        init: function() {
        }
    };
} 
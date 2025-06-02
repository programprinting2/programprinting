// Cek apakah PemasokHelper sudah ada
if (typeof PemasokHelper === 'undefined') {
    const PemasokHelper = {
        // Inisialisasi format mata uang
        initMoneyFormat: function(prefix = '') {
            $(`.money-format`).each(function() {
                let value = $(this).val() || '0';
                value = parseFloat(value.replace(/[^\d]/g, '')).toLocaleString('id-ID');
                $(this).val(value);
            });

            $(document).on('input', '.money-format', function() {
                let value = $(this).val().replace(/[^\d]/g, '');
                value = parseFloat(value || 0).toLocaleString('id-ID');
                $(this).val(value);
            });
        },

        // Persiapan form sebelum submit
        prepareFormSubmit: function(formSelector) {
            $(formSelector).on('submit', function() {
                // Format ulang nilai mata uang sebelum submit
                $('.money-format').each(function() {
                    let value = $(this).val().replace(/[^\d]/g, '');
                    $(this).val(value);
                });
            });
        },

        // Fungsi untuk menampilkan error validasi
        showValidationErrors: function(errors) {
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
                
                if (tabId) {
                    $(`#tab-${tabId}`).addClass('text-danger');
                }
            });
            
            // Pindah ke tab yang memiliki error pertama
            const firstErrorField = Object.keys(errors)[0];
            const firstErrorInput = $(`[name="${firstErrorField}"]`);
            const firstErrorTab = firstErrorInput.closest('.tab-pane').attr('id');
            
            if (firstErrorTab) {
                $(`#tab-${firstErrorTab}`).tab('show');
            }
        }
    };

    // Ekspos PemasokHelper ke global scope
    window.PemasokHelper = PemasokHelper;
} 
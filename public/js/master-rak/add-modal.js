// Handle form submission untuk modal tambah rak
$(document).ready(function() {
    $('#formRak').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnSubmitRak');
        const spinner = $('#spinnerRak');
        
        // Disable button dan show spinner
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                spinner.addClass('d-none');
                btn.prop('disabled', false);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#tambahRak').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                spinner.addClass('d-none');
                btn.prop('disabled', false);
                let errorMessage = 'Gagal menambahkan rak';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });
    // Reset form when modal is hidden
    $('#tambahRak').on('hidden.bs.modal', function() {
        $('#formRak')[0].reset();
        $('#btnSubmitRak').prop('disabled', false);
        $('#spinnerRak').addClass('d-none');
        $('#infoVolume').text('0 m³');
        $('#infoDimensi').text('0m × 0m × 0m');
    });
    // Enable submit button when form is valid
    $('#formRak input, #formRak select, #formRak textarea').on('input change', function() {
        const form = $('#formRak')[0];
        const submitBtn = $('#btnSubmitRak');
        if (form.checkValidity()) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    });
    // Hitung volume otomatis
    $('#lebar, #tinggi, #kedalaman').on('input', function() {
        const lebar = parseFloat($('#lebar').val()) || 0;
        const tinggi = parseFloat($('#tinggi').val()) || 0;
        const kedalaman = parseFloat($('#kedalaman').val()) || 0;
        const volume = lebar * tinggi * kedalaman;
        $('#infoVolume').text(volume.toLocaleString('id-ID') + ' m³');
        $('#infoDimensi').text(`${lebar}m × ${kedalaman}m × ${tinggi}m`);
    });
}); 
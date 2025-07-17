// Handle form submission untuk modal tambah gudang
$(document).ready(function() {
    $('#formGudang').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnSubmitGudang');
        const spinner = $('#spinnerGudang');
        
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
                // Hide spinner dan enable button
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
                        $('#tambahGudang').modal('hide');
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
                // Hide spinner dan enable button
                spinner.addClass('d-none');
                btn.prop('disabled', false);
                
                let errorMessage = 'Gagal menambahkan gudang';
                
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
    $('#tambahGudang').on('hidden.bs.modal', function() {
        $('#formGudang')[0].reset();
        // Reset button state
        $('#btnSubmitGudang').prop('disabled', false);
        $('#spinnerGudang').addClass('d-none');
    });
    
    // Enable submit button when form is valid
    $('#formGudang input, #formGudang select, #formGudang textarea').on('input change', function() {
        const form = $('#formGudang')[0];
        const submitBtn = $('#btnSubmitGudang');
        
        if (form.checkValidity()) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    });
}); 
// Fungsi untuk membuka modal edit dan load data gudang
function editGudang(id) {
    // Get data from button
    const button = event.target.closest('button');
    const gudangData = JSON.parse(button.getAttribute('data-gudang'));
    
    // Reset form
    $('#editGudangForm')[0].reset();
    
    // Set form action
    $('#editGudangForm').attr('action', `/backend/master-gudang/${id}`);
    
    // Fill form fields
    $('#edit_kode_gudang').val(gudangData.kode_gudang);
    $('#edit_nama_gudang').val(gudangData.nama_gudang);
    $('#edit_manager').val(gudangData.manager);
    $('#edit_kapasitas').val(gudangData.kapasitas);
    $('#edit_status').val(gudangData.status);
    $('#edit_deskripsi').val(gudangData.deskripsi);
    $('#edit_alamat').val(gudangData.alamat);
    $('#edit_kota').val(gudangData.kota);
    $('#edit_provinsi').val(gudangData.provinsi);
    $('#edit_kode_pos').val(gudangData.kode_pos);
    $('#edit_no_telepon').val(gudangData.no_telepon);
    $('#edit_email').val(gudangData.email);
    
    // Enable submit button since form is pre-filled
    $('#btnSubmitEditGudang').prop('disabled', false);
    
    // Show modal
    $('#editGudangModal').modal('show');
}

// Handle form submission
$(document).ready(function() {
    $('#editGudangForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnSubmitEditGudang');
        const spinner = $('#spinnerEditGudang');
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
                        $('#editGudangModal').modal('hide');
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
                let errorMessage = 'Gagal memperbarui gudang';
                
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
    $('#editGudangModal').on('hidden.bs.modal', function() {
        $('#editGudangForm')[0].reset();
        // Reset button state
        $('#btnSubmitEditGudang').prop('disabled', false);
        $('#spinnerEditGudang').addClass('d-none');
    });
    
    // Enable submit button when form is valid (for edit modal)
    $('#editGudangForm input, #editGudangForm select, #editGudangForm textarea').on('input change', function() {
        const form = $('#editGudangForm')[0];
        const submitBtn = $('#btnSubmitEditGudang');
        
        if (form.checkValidity()) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    });
}); 
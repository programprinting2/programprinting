// Fungsi untuk membuka modal edit dan load data rak
function editRak(id) {
    const button = event.target.closest('button');
    const rakData = JSON.parse(button.getAttribute('data-rak'));
    // Reset form
    $('#editRakForm')[0].reset();
    // Set form action
    $('#editRakForm').attr('action', `/backend/master-rak/${id}`);
    // Fill form fields
    $('#edit_gudang_id').val(rakData.gudang_id);
    $('#edit_kode_rak').val(rakData.kode_rak);
    $('#edit_nama_rak').val(rakData.nama_rak);
    $('#edit_kapasitas').val(rakData.kapasitas);
    $('#edit_jumlah_level').val(rakData.jumlah_level);
    $('#edit_status').val(rakData.status);
    $('#edit_lebar').val(rakData.lebar);
    $('#edit_tinggi').val(rakData.tinggi);
    $('#edit_kedalaman').val(rakData.kedalaman);
    $('#edit_deskripsi').val(rakData.deskripsi);
    // Enable submit button since form is pre-filled
    $('#btnSubmitEditRak').prop('disabled', false);
    // Show modal
    $('#editRakModal').modal('show');
}
// Handle form submission
$(document).ready(function() {
    $('#editRakForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btnSubmitEditRak');
        const spinner = $('#spinnerEditRak');
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
                        $('#editRakModal').modal('hide');
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
                let errorMessage = 'Gagal memperbarui rak';
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
    $('#editRakModal').on('hidden.bs.modal', function() {
        $('#editRakForm')[0].reset();
        $('#btnSubmitEditRak').prop('disabled', false);
        $('#spinnerEditRak').addClass('d-none');
    });
    // Enable submit button when form is valid
    $('#editRakForm input, #editRakForm select, #editRakForm textarea').on('input change', function() {
        const form = $('#editRakForm')[0];
        const submitBtn = $('#btnSubmitEditRak');
        if (form.checkValidity()) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    });
}); 
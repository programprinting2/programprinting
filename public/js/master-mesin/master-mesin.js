$(document).ready(function() {
    // Inisialisasi feather icons
    feather.replace();
    
    // Inisialisasi ikon status khusus dengan ukuran yang lebih kecil
    feather.replace('.icon-status', {
        width: 14,
        height: 14
    });
    
    // Toggle view (Card/Table)
    $('#cardViewBtn').click(function() {
        $(this).addClass('active');
        $('#tableViewBtn').removeClass('active');
        $('#cardView').removeClass('d-none');
        $('#tableView').addClass('d-none');
        
        // Reinisialisasi ikon status
        feather.replace('.icon-status', {
            width: 14,
            height: 14
        });
    });
    
    $('#tableViewBtn').click(function() {
        $(this).addClass('active');
        $('#cardViewBtn').removeClass('active');
        $('#tableView').removeClass('d-none');
        $('#cardView').addClass('d-none');
        
        // Reinisialisasi ikon status
        feather.replace('.icon-status', {
            width: 14,
            height: 14
        });
    });
    
    // Handler untuk tombol hapus
    $('.btn-delete-mesin').click(function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        
        Swal.fire({
            title: 'Hapus Mesin?',
            text: `Apakah Anda yakin ingin menghapus mesin "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formDeleteMesin' + id).submit();
            }
        });
    });
    
    // Handler untuk form tambah mesin
    $('#formTambahMesin').submit(function(e) {
        e.preventDefault();
        
        // Reset error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Nonaktifkan tombol submit dan simpan teks aslinya
        let submitBtn = $('#submitFormMesin');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        let formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Data mesin berhasil ditambahkan',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#tambahMesinModal').modal('hide');
                        location.reload();
                    });
                } else {
                    // Aktifkan kembali tombol submit
                    submitBtn.prop('disabled', false).html(originalText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message || 'Terjadi kesalahan saat menyimpan data',
                    });
                }
            },
            error: function(xhr) {
                // Aktifkan kembali tombol submit
                submitBtn.prop('disabled', false).html(originalText);
                
                if(xhr.status === 422) {
                    // Tampilkan error validasi
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        let input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        
                        // Tambahkan pesan error
                        let errorDiv = $('<div>')
                            .addClass('invalid-feedback')
                            .text(messages[0]);
                            
                        input.after(errorDiv);
                    });
                    
                    // Tampilkan pesan error
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Silakan periksa kembali data yang Anda masukkan',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                    });
                }
            }
        });
    });
    
    // Handler untuk form edit mesin
    $('.btn-submit-edit').click(function() {
        let id = $(this).data('id');
        let form = $('#formEditMesin' + id);
        let submitBtn = $(this);
        let originalText = submitBtn.html();
        
        // Reset error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Nonaktifkan tombol submit
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
        
        let formData = new FormData(form[0]);
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Data mesin berhasil diperbarui',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#editMesinModal' + id).modal('hide');
                        location.reload();
                    });
                } else {
                    // Aktifkan kembali tombol submit
                    submitBtn.prop('disabled', false).html(originalText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message || 'Terjadi kesalahan saat memperbarui data',
                    });
                }
            },
            error: function(xhr) {
                // Aktifkan kembali tombol submit
                submitBtn.prop('disabled', false).html(originalText);
                
                if(xhr.status === 422) {
                    // Tampilkan error validasi
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        let input = $(`[name="${field}"]`);
                        input.addClass('is-invalid');
                        
                        // Tambahkan pesan error
                        let errorDiv = $('<div>')
                            .addClass('invalid-feedback')
                            .text(messages[0]);
                            
                        input.after(errorDiv);
                    });
                    
                    // Tampilkan pesan error
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Silakan periksa kembali data yang Anda masukkan',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.',
                    });
                }
            }
        });
    });
    
    // Trigger perubahan tipe mesin saat halaman dimuat
    if ($('#tipe_mesin').val()) {
        $('#tipe_mesin').trigger('change');
    }
    
    // Trigger perubahan tipe mesin saat modal edit dibuka
    $('.edit-tipe-mesin').each(function() {
        $(this).trigger('change');
    });
}); 

// Fungsi untuk preview gambar
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
    }
} 
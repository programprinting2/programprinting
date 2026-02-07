$(document).ready(function() {
    $(document).off('click', '.pagination .page-link');
    $(document).off('click', '.pagination a');
    $(document).off('click', '.page-link');
    // Helper function untuk build URL halaman saat ini
    function getCurrentPageUrl() {
        const url = new URL(window.location.origin + '/backend/master-mesin');
        url.searchParams.set('page', 1); // Default ke page 1 untuk redirect
        const search = $('#searchInput').val();
        const type = $('#filterTipeMesin').val();
        const status = $('#filterStatus').val();
        
        if (search && search !== '') {
            url.searchParams.set('search', search);
        }
        if (type && type !== 'semua') {
            url.searchParams.set('type', type);
        }
        if (status && status !== 'semua') {
            url.searchParams.set('status', status);
        }
        return url.toString();
    }

    // Fungsi untuk menginisialisasi event handler button
    function initializeButtonHandlers() {
        $(document).off('click', '.btn-detail-mesin');
        $(document).off('click', '.btn-edit-mesin');
        $(document).off('click', '.btn-delete-mesin');

        // Event handler untuk tombol detail
        $(document).on('click', '.btn-detail-mesin', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const el = document.getElementById('detailMesinModal' + id);
            if (!el) {
                // Modal tidak ada di DOM, redirect ke halaman yang benar
                window.location.href = getCurrentPageUrl();
                return;
            }
            // Bootstrap 5: pakai native API
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(el).show();
            }
        });

        $(document).on('click', '.btn-edit-mesin', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const el = document.getElementById('editMesinModal' + id);
            if (!el) {
                // Modal tidak ada di DOM, redirect ke halaman yang benar
                window.location.href = getCurrentPageUrl();
                return;
            }
            // Bootstrap 5: pakai native API
            if (typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(el).show();
            }
        });

        // Event handler untuk tombol delete
        $(document).on('click', '.btn-delete-mesin', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus mesin "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#formDeleteMesin${id}`).submit();
                }
            });
        });
    }

    // Event handler untuk tombol toggle view (card/table)
    $('#cardViewBtn').on('click', function() {
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

    $('#tableViewBtn').on('click', function() {
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

    // Inisialisasi button handlers saat halaman dimuat
    $(document).on('click', '.pagination a', function(e) {
        // Jangan preventDefault - biarkan link bekerja normal
        // console.log('Pagination clicked:', $(this).attr('href')); // untuk debug
    });
    
    // Pastikan tidak ada event delegation yang menginterferensi
    $(document).on('click', '.page-link', function(e) {
        // Biarkan default behavior untuk pagination
    });
    initializeButtonHandlers();
});
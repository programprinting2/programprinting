$(document).ready(function() {
    // Variabel untuk menyimpan state filter
    let currentFilters = {
        search: '',
        type: 'semua',
        status: 'semua'
    };

    // Fungsi untuk menginisialisasi event handler button
    function initializeButtonHandlers() {
        // Event handler untuk tombol detail
        $('.btn-detail-mesin').on('click', function() {
            const id = $(this).data('id');
            $('#detailMesinModal' + id).modal('show');
        });

        // Event handler untuk tombol edit
        $('.btn-edit-mesin').on('click', function() {
            const id = $(this).data('id');
            $('#editMesinModal' + id).modal('show');
        });

        // Event handler untuk tombol delete
        $('.btn-delete-mesin').on('click', function() {
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

    // Fungsi untuk memuat data dengan filter
    function loadData(page = 1) {
        const url = new URL(window.location.origin + '/backend/master-mesin');
        url.searchParams.set('page', page);
        
        // Tambahkan parameter filter hanya jika memiliki nilai
        if (currentFilters.search && currentFilters.search !== '') {
            url.searchParams.set('search', currentFilters.search);
        }
        if (currentFilters.type && currentFilters.type !== 'semua') {
            url.searchParams.set('type', currentFilters.type);
        }
        if (currentFilters.status && currentFilters.status !== 'semua') {
            url.searchParams.set('status', currentFilters.status);
        }

        // Tampilkan loading state
        $('#cardView').html('<div class="col-12 text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#tableView tbody').html('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"></div></td></tr>');

        $.ajax({
            url: url.toString(),
            type: 'GET',
            success: function(response) {
                // Update card view
                $('#cardView').html(response.html);
                
                // Update table view
                $('#tableView tbody').html($(response.table_html).find('tbody').html());
                
                // Update pagination
                updatePagination(response.pagination);
                
                // Update jumlah mesin
                $('.text-muted.mb-0').text(`Menampilkan ${response.total_count} mesin.`);
                
                // Reinitialize Feather icons
                feather.replace();
                
                // Initialize button handlers
                initializeButtonHandlers();
                
                // Tampilkan pesan jika tidak ada hasil
                if (response.total_count === 0) {
                    const noResultsHtml = `
                        <div class="col-12 text-center py-4">
                            <div class="alert alert-info mb-0">
                                <i data-feather="info" class="me-2"></i>
                                Tidak ada mesin yang ditemukan dengan filter yang dipilih.
                            </div>
                        </div>
                    `;
                    $('#cardView').html(noResultsHtml);
                    $('#tableView tbody').html(`
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="alert alert-info mb-0">
                                    <i data-feather="info" class="me-2"></i>
                                    Tidak ada mesin yang ditemukan dengan filter yang dipilih.
                                </div>
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(xhr) {
                console.error('Error loading data:', xhr);
                const errorHtml = `
                    <div class="col-12 text-center py-4">
                        <div class="alert alert-danger mb-0">
                            <i data-feather="alert-circle" class="me-2"></i>
                            Terjadi kesalahan saat memuat data
                        </div>
                    </div>
                `;
                $('#cardView').html(errorHtml);
                $('#tableView tbody').html(`
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="alert alert-danger mb-0">
                                <i data-feather="alert-circle" class="me-2"></i>
                                Terjadi kesalahan saat memuat data
                            </div>
                        </td>
                    </tr>
                `);
                feather.replace();
            }
        });
    }

    // Fungsi untuk memperbarui tampilan pagination
    function updatePagination(pagination) {
        const $pagination = $('.pagination');
        if (!$pagination.length) return;

        let html = '';
        
        // Previous button
        html += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i data-feather="chevron-left" class="icon-sm"></i>
                </a>
            </li>
        `;

        // Page numbers
        let startPage = Math.max(1, pagination.current_page - 2);
        let endPage = Math.min(pagination.last_page, startPage + 4);
        
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        if (startPage > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>
                ${startPage > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
            `;
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        if (endPage < pagination.last_page) {
            html += `
                ${endPage < pagination.last_page - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                <li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a>
                </li>
            `;
        }

        // Next button
        html += `
            <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    <i data-feather="chevron-right" class="icon-sm"></i>
                </a>
            </li>
        `;

        $pagination.html(html);
        feather.replace();
    }

    // Debounce function untuk input pencarian
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Filter pencarian dengan debounce
    $('#searchInput').on('keyup', debounce(function() {
        currentFilters.search = $(this).val();
        loadData(1); // Reset ke halaman pertama
    }, 300));

    // Filter tipe mesin
    $('#filterTipeMesin').change(function() {
        currentFilters.type = $(this).val();
        loadData(1); // Reset ke halaman pertama
    });
    
    // Filter status
    $('#filterStatus').change(function() {
        currentFilters.status = $(this).val();
        loadData(1); // Reset ke halaman pertama
    });

    // Reset semua filter
    $('#resetFilters').click(function() {
        currentFilters = {
            search: '',
            type: 'semua',
            status: 'semua'
        };
        
        // Reset input dan select
        $('#searchInput').val('');
        $('#filterTipeMesin').val('semua');
        $('#filterStatus').val('semua');
        
        // Muat ulang data
        loadData(1);
    });

    // Handle pagination click
    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && !$(this).parent().hasClass('disabled')) {
            loadData(page);
        }
    });

    // Event handler untuk tombol toggle view
    $('#cardViewBtn').on('click', function() {
        $(this).addClass('active');
        $('#tableViewBtn').removeClass('active');
        $('#cardView').removeClass('d-none');
        $('#tableView').addClass('d-none');
    });

    $('#tableViewBtn').on('click', function() {
        $(this).addClass('active');
        $('#cardViewBtn').removeClass('active');
        $('#tableView').removeClass('d-none');
        $('#cardView').addClass('d-none');
    });

    // Inisialisasi filter saat halaman dimuat
    loadData();

    // Inisialisasi button handlers saat halaman dimuat
    initializeButtonHandlers();
}); 
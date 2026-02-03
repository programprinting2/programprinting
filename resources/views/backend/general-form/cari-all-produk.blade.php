@php
  $modalId = $modalId ?? 'modalCariAllProduk';
  $inputId = $inputId ?? 'searchAllProduk';
  $tableId = $tableId ?? 'tabelCariAllProduk';
  $paginationId = $paginationId ?? 'paginationAllProduk';
  $clearBtnId = $clearBtnId ?? 'clearSearchAllProduk';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border border-primary">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">Cari Semua Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="{{ $inputId }}" class="form-label">Cari Produk</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i data-feather="search" class="icon-sm"></i></span>
            <input type="text" class="form-control" id="{{ $inputId }}" placeholder="Cari berdasarkan kode, nama produk..." tabindex="1" autocomplete="off">
            <span class="input-group-text input-group-addon" id="{{ $clearBtnId }}" style="cursor:pointer;"><i data-feather="delete"></i></span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0" id="{{ $tableId }}">
            <thead class="table-light">
              <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Satuan</th> 
                <th>Total Modal</th>
              </tr>
            </thead>
            <tbody><!-- Data AJAX --></tbody>
          </table>
        </div>
        <div id="{{ $paginationId }}" class="mt-3 d-flex justify-content-center"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@push('custom-scripts')
<style>
  .pilih-semua-produk:hover {
    background: #f0f6ff !important;
    cursor: pointer;
  }
  .modal-stack {
    z-index: 1060 !important;
  }
  .modal-backdrop-stack {
    z-index: 1055 !important;
  }
</style>
<script>
(function() {
  const modalId = @json($modalId);
  const inputId = @json($inputId);
  const tableId = @json($tableId);
  const paginationId = @json($paginationId);
  const clearBtnId = @json($clearBtnId);

  // Fungsi untuk render pagination
  function renderPagination(data, search) {
      let html = '';
      if (data.last_page > 1) {
          html += '<nav><ul class="pagination pagination-sm">';
          
          // Previous button dengan &laquo; dan disabled state
          if (data.current_page > 1) {
              html += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}" data-search="${search}">&laquo;</a></li>`;
          } else {
              html += '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
          }
          
          // Loop penuh dari 1 sampai last_page (bukan range)
          for (let i = 1; i <= data.last_page; i++) {
              if (i === data.current_page) {
                  html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
              } else {
                  html += `<li class="page-item"><a class="page-link" href="#" data-page="${i}" data-search="${search}">${i}</a></li>`;
              }
          }
          
          // Next button dengan &raquo; dan disabled state
          if (data.current_page < data.last_page) {
              html += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page + 1}" data-search="${search}">&raquo;</a></li>`;
          } else {
              html += '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
          }
          
          html += '</ul></nav>';
      }
      $('#' + paginationId).html(html);
  }

  // Fungsi utama load data
  function loadSemuaProduk(page = 1) {
      const searchTerm = $('#' + inputId).val();
      const tbody = $('#' + tableId + ' tbody');
      
      tbody.html('<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Memuat...</td></tr>');

      $.ajax({
          url: '{{ route('backend.cari-semua-produk') }}',
          method: 'GET',
          data: {
              search: searchTerm,
              page: page
          },
          success: function(response) {
              if (response.data && response.data.length > 0) {
                  let html = '';
                  response.data.forEach(function(produk) {
                      html += `
                          <tr class="pilih-semua-produk" 
                              data-id="${produk.id}" 
                              data-kode="${produk.kode_produk}"
                              data-nama="${produk.nama_produk}"
                              data-modal="${produk.total_modal_keseluruhan || 0}"
                              data-panjang="${produk.panjang || 0}"
                              data-lebar="${produk.lebar || 0}"
                              data-panjangLocked="${produk.panjang_locked || false}"
                              data-lebarLocked="${produk.lebar_locked || false}"
                              data-kategori="${produk.kategori_nama || '-'}"
                              data-jenis="${produk.jenis_produk || 'produk'}"
                              data-satuan="${produk.satuan_nama || 'pcs'}">
                              <td>${produk.kode_produk ?? '-'}</td>
                              <td>${produk.nama_produk ?? '-'}</td>
                              <td>
                                  <span class="badge bg-${produk.jenis_produk === 'produk' ? 'primary' : 'secondary'}">
                                      ${produk.jenis_produk === 'produk' ? 'Produk' : 'Jasa'}
                                  </span>
                              </td>
                              <td>${produk.kategori_nama ?? '-'}</td>
                              <td>${produk.satuan_nama ?? '-'}</td>    
                              <td class="text-end">Rp ${produk.total_modal_keseluruhan ? parseFloat(produk.total_modal_keseluruhan).toLocaleString('id-ID') : '0'}</td>
                          </tr>
                      `;
                  });
                  tbody.html(html);

                  // Render pagination
                  renderPagination(response, searchTerm);

                  // Re-initialize feather icons
                  if (typeof feather !== 'undefined') feather.replace();
              } else {
                  tbody.html('<tr><td colspan="6" class="text-center text-muted">Tidak ada produk ditemukan</td></tr>');
                  $('#' + paginationId).html('');
              }
          },
          error: function(xhr) {
            $('#' + tableId + ' tbody').html('<tr><td colspan="6" class="text-center">Gagal memuat data</td></tr>');
            $('#' + paginationId).html('');
          }
      });
  }

  // Event listeners
  $(document).on('input', '#' + inputId, function() {
      clearTimeout(window.searchTimeout);
      window.searchTimeout = setTimeout(function() {
          loadSemuaProduk();
      }, 300);
  });

  $(document).on('click', '#' + clearBtnId, function() {
      $('#' + inputId).val('');
      loadSemuaProduk();
  });

  $(document).on('click', '#' + paginationId + ' .page-link', function(e) {
      e.preventDefault();
      if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
      const page = $(this).data('page');
      const search = $('#' + inputId).val();
      loadSemuaProduk(search, page);
  });

  $(document).on('click', '.pilih-semua-produk', function() {
        const panjangLockedRaw = $(this).attr('data-panjang-locked') || $(this).attr('data-panjangLocked');
        const lebarLockedRaw = $(this).attr('data-lebar-locked') || $(this).attr('data-lebarLocked');
        const data = {
          id: parseInt($(this).data('id')) || 0,
          kode_produk: $(this).data('kode'),
          nama_produk: $(this).data('nama'),
          total_modal_keseluruhan: parseFloat($(this).data('modal')) || 0,
          panjang: parseFloat($(this).data('panjang')) || 0,
          lebar: parseFloat($(this).data('lebar')) || 0,
          panjang_locked: panjangLockedRaw === 'true' || panjangLockedRaw === '1' || panjangLockedRaw === true,
          lebar_locked: lebarLockedRaw === 'true' || lebarLockedRaw === '1' || lebarLockedRaw === true,
          kategori_nama: $(this).data('kategori'),
          jenis_produk: $(this).data('jenis'),
          satuan_nama: $(this).data('satuan') || 'pcs',
          sourceModal: 'spk'
      };

      // Close modal
      window.dispatchEvent(new CustomEvent('produkDipilih', { detail: data }));
      $('#' + modalId).modal('hide');
  });

  // Load initial data when modal is shown
  $(document).on('shown.bs.modal', '#' + modalId, function() {
      loadSemuaProduk();
  });
})();
</script>
@endpush
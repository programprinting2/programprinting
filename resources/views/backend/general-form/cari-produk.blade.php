@php
  $modalId = $modalId ?? 'modalCariProdukRakitan';
  $inputId = $inputId ?? 'searchProdukRakitan';
  $tableId = $tableId ?? 'tabelCariProdukRakitan';
  $paginationId = $paginationId ?? 'paginationProdukRakitan';
  $clearBtnId = $clearBtnId ?? 'clearSearchProdukRakitan';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-primary">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">Cari Produk Komponen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="{{ $inputId }}" class="form-label">Cari Produk Komponen</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i data-feather="search" class="icon-sm"></i></span>
            <input type="text" class="form-control" id="{{ $inputId }}" placeholder="Cari berdasarkan kode, nama produk...">
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
  .pilih-produk-komponen:hover {
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
          html += '<nav><ul class="pagination pagination-sm">';  // Tambah pagination-sm
          
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
  function loadProdukKomponen(page = 1) {
      const searchTerm = $('#' + inputId).val();
      const tbody = $('#' + tableId + ' tbody');
      
      tbody.html('<tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Memuat...</td></tr>');

      $.ajax({
          url: '{{ route('backend.cari-produk') }}',
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
                          <tr class="pilih-produk-komponen" 
                              data-id="${produk.id}" 
                              data-kode="${produk.kode_produk}"
                              data-nama="${produk.nama_produk}"
                              data-modal="${produk.total_modal_keseluruhan || 0}">
                              <td>${produk.kode_produk ?? '-'}</td>
                              <td>${produk.nama_produk ?? '-'}</td>
                              <td>
                                  <span class="badge bg-${produk.jenis_produk === 'produk' ? 'primary' : 'secondary'}">
                                      ${produk.jenis_produk === 'produk' ? 'Produk' : 'Jasa'}
                                  </span>
                              </td>
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
                  tbody.html('<tr><td colspan="5" class="text-center text-muted">Tidak ada produk komponen ditemukan</td></tr>');
                  $('#' + paginationId).html('');
              }
          },
          error: function(xhr) {
            $('#' + tableId + ' tbody').html('<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>');
            $('#' + paginationId).html('');
          }
      });
  }

  // Event listeners
  $(document).on('input', '#' + inputId, function() {
      clearTimeout(window.searchTimeout);
      window.searchTimeout = setTimeout(function() {
          loadProdukKomponen();
      }, 300);
  });

  $(document).on('click', '#' + clearBtnId, function() {
      $('#' + inputId).val('');
      loadProdukKomponen();
  });

  $(document).on('click', '#' + paginationId + ' .page-link', function(e) {
      e.preventDefault();
      if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
      const page = $(this).data('page');
      const search = $('#' + inputId).val();
      loadProdukKomponen(search, page);
  });

  $(document).on('click', '.pilih-produk-komponen', function() {
      const data = {
          id: parseInt($(this).data('id')) || 0,
          kode_produk: $(this).data('kode'),
          nama_produk: $(this).data('nama'),
          total_modal_keseluruhan: parseFloat($(this).data('modal')) || 0 ,
          sourceModal: $('#editProdukModal').hasClass('show') ? 'edit' : 'add'
      };

      // Cek apakah ini dari modal edit atau add
      // const isEditModal = $('#editProdukModal').hasClass('show');
      
      
      // Close modal
      window.dispatchEvent(new CustomEvent('produkKomponenDipilih', { detail: data }));
      $('#' + modalId).modal('hide');
  });

  // Load initial data when modal is shown
  $(document).on('shown.bs.modal', '#' + modalId, function() {
      loadProdukKomponen();
  });
})();
</script>
@endpush
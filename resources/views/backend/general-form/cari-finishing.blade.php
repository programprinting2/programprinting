@php
  $modalId = $modalId ?? 'modalCariFinishing';
  $inputId = $inputId ?? 'searchFinishing';
  $tableId = $tableId ?? 'tabelCariFinishing';
  $paginationId = $paginationId ?? 'paginationFinishing';
  $clearBtnId = $clearBtnId ?? 'clearSearchFinishing';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-primary">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">
          <i data-feather="search" class="me-2"></i>Cari Produk Finishing
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="{{ $inputId }}" class="form-label">Cari Produk Finishing</label>
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
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th>Harga Modal</th>
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
  .pilih-finishing:hover {
    background: #f0fff0 !important;
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
          
          if (data.current_page > 1) {
              html += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}" data-search="${search}">&laquo;</a></li>`;
          } else {
              html += '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
          }
          
          for (let i = 1; i <= data.last_page; i++) {
              if (i === data.current_page) {
                  html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
              } else {
                  html += `<li class="page-item"><a class="page-link" href="#" data-page="${i}" data-search="${search}">${i}</a></li>`;
              }
          }
          
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
  function loadFinishing(page = 1) {
      const searchTerm = $('#' + inputId).val();
      const tbody = $('#' + tableId + ' tbody');
      
      tbody.html('<tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Memuat...</td></tr>');

      $.ajax({
          url: '{{ route('backend.cari-finishing') }}',
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
                          <tr class="pilih-finishing" 
                              data-id="${produk.id}" 
                              data-kode="${produk.kode_produk}"
                              data-nama="${produk.nama_produk}"
                              data-harga="${produk.harga_modal}">
                              <td>${produk.kode_produk ?? '-'}</td>
                              <td>${produk.nama_produk ?? '-'}</td>
                              <td>${produk.kategori ?? '-'}</td>
                              <td>${produk.sub_kategori ?? '-'}</td>
                              <td class="text-end">Rp ${produk.harga_modal ? parseFloat(produk.harga_modal).toLocaleString('id-ID') : '0'}</td>
                          </tr>
                      `;
                  });
                  tbody.html(html);
                  renderPagination(response, searchTerm);
                  if (typeof feather !== 'undefined') feather.replace();
              } else {
                  tbody.html('<tr><td colspan="5" class="text-center text-muted">Tidak ada produk finishing ditemukan</td></tr>');
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
          loadFinishing();
      }, 300);
  });

  $(document).on('click', '#' + clearBtnId, function() {
      $('#' + inputId).val('');
      loadFinishing();
  });

  $(document).on('click', '#' + paginationId + ' .page-link', function(e) {
      e.preventDefault();
      if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
      const page = $(this).data('page');
      const search = $('#' + inputId).val();
      loadFinishing(search, page);
  });

  $(document).on('click', '.pilih-finishing', function() {
      const data = {
          id: parseInt($(this).data('id')) || 0,
          kode_produk: $(this).data('kode'),
          nama_produk: $(this).data('nama'),
          harga_modal: parseFloat($(this).data('harga')) || 0,
          sourceModal: $('#editProdukModal').hasClass('show') ? 'edit' : 'add'
      };

      // Dispatch event
      window.dispatchEvent(new CustomEvent('finishingDipilih', { detail: data }));
      $('#' + modalId).modal('hide');
  });

  // Load initial data when modal is shown
  $(document).on('shown.bs.modal', '#' + modalId, function() {
      loadFinishing();
  });
})();
</script>
@endpush
@php
  $modalId = $modalId ?? 'modalCariPemasok';
  $inputId = $inputId ?? 'searchPemasok';
  $tableId = $tableId ?? 'tabelCariPemasok';
  $paginationId = $paginationId ?? 'paginationCariPemasok';
  $clearBtnId = $clearBtnId ?? 'clearSearchPemasok';
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-primary">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">Cari Pemasok</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="{{ $inputId }}" class="form-label">Cari Pemasok</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i data-feather="search" class="icon-sm"></i></span>
            <input type="text" class="form-control" id="{{ $inputId }}" placeholder="Cari berdasarkan kode, nama, kontak, email...">
            <span class="input-group-text input-group-addon" id="{{ $clearBtnId }}" style="cursor:pointer;"><i data-feather="delete"></i></span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0" id="{{ $tableId }}">
            <thead class="table-light">
              <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Handphone</th>
                <th>Email</th>
                <th>Status</th>
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
  .pilih-pemasok:hover {
    background: #f0f6ff !important;
    cursor: pointer;
  }
</style>
<script>
(function() {
  const modalId = @json($modalId);
  const inputId = @json($inputId);
  const tableId = @json($tableId);
  const paginationId = @json($paginationId);
  const clearBtnId = @json($clearBtnId);

  function loadPemasok(search = '', page = 1) {
    $('#' + tableId + ' tbody').html('<tr><td colspan="5" class="text-center">Memuat data...</td></tr>');
    $.ajax({
      url: '/backend/cari-pemasok',
      data: { search: search, page: page },
      dataType: 'json',
      success: function(data) {
        let html = '';
        if (data.data && data.data.length > 0) {
          data.data.forEach(item => {
            html += `<tr class="pilih-pemasok" 
              data-id="${item.id}" 
              data-kode="${item.kode}" 
              data-nama="${item.nama}" 
              data-handphone="${item.handphone ?? ''}" 
              data-email="${item.email ?? ''}" 
              data-status="${item.status}">
              <td>${item.kode}</td>
              <td>${item.nama}</td>
              <td>${item.handphone ?? '-'}</td>
              <td>${item.email ?? '-'}</td>
              <td>${item.status == 1 ? 'Aktif' : 'Tidak Aktif'}</td>
            </tr>`;
          });
        } else {
          html = '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
        }
        $('#' + tableId + ' tbody').html(html);
        renderPagination(data, search);
      },
      error: function() {
        $('#' + tableId + ' tbody').html('<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>');
        $('#' + paginationId).html('');
      }
    });
  }
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
  $(document).on('click', '#' + clearBtnId, function() {
    $('#' + inputId).val('');
    loadPemasok('', 1);
  });
  $(document).on('shown.bs.modal', '#' + modalId, function () {
    loadPemasok($('#' + inputId).val(), 1);
  });
  $(document).on('keypress', '#' + inputId, function(event) {
    if (event.key === 'Enter') {
      loadPemasok(this.value, 1);
      event.preventDefault();
    }
  });
  $(document).on('click', '#' + paginationId + ' .page-link', function(e) {
    e.preventDefault();
    if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
    const page = $(this).data('page');
    const search = $('#' + inputId).val();
    loadPemasok(search, page);
  });
  $(document).on('click', '#' + tableId + ' .pilih-pemasok', function() {
    const data = {
      id: $(this).data('id'),
      kode: $(this).data('kode'),
      nama: $(this).data('nama'),
      handphone: $(this).data('handphone'),
      email: $(this).data('email'),
      status: $(this).data('status')
    };
    window.dispatchEvent(new CustomEvent('pemasokDipilih', { detail: data }));
    $('#' + modalId).modal('hide');
  });
})();
</script>
@endpush 
@php
  $modalId = $modalId ?? 'modalCariMesin';
  $inputId = $inputId ?? 'searchMesin';
  $tableId = $tableId ?? 'tabelCariMesin';
  $paginationId = $paginationId ?? 'paginationCariMesin';
  $clearBtnId = $clearBtnId ?? 'clearSearchMesin';
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-primary">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">Cari Mesin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="{{ $inputId }}" class="form-label">Cari Mesin</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i data-feather="search" class="icon-sm"></i></span>
            <input type="text" class="form-control" id="{{ $inputId }}" placeholder="Cari berdasarkan nama, tipe, merek mesin...">
            <span class="input-group-text input-group-addon" id="{{ $clearBtnId }}" style="cursor:pointer;"><i data-feather="delete"></i></span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0" id="{{ $tableId }}">
            <thead class="table-light">
              <tr>
                <th>Nama Mesin</th>
                <th>Tipe Mesin</th>
                <th>Merek</th>
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
  .pilih-mesin:hover {
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

  function loadMesin(search = '', page = 1) {
    $('#' + tableId + ' tbody').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');
    $.ajax({
      url: '/backend/cari-mesin',
      data: { search: search, page: page },
      dataType: 'json',
      success: function(data) {
        let html = '';
        if (data.data && data.data.length > 0) {
          data.data.forEach(item => {
            const merekDisplay = item.merek && item.merek.trim() !== '' ? item.merek : '-';
            html += `<tr class="pilih-mesin" 
              data-id="${item.id}" 
              data-nama="${item.nama_mesin}" 
              data-tipe="${item.tipe_mesin ?? ''}"
              data-merek="${item.merek ?? ''}"
              data-status="${item.status}"
              data-biaya_perhitungan_profil='${JSON.stringify(item.biaya_perhitungan_profil ?? "")}'>
              <td>${item.nama_mesin ?? '-'}</td>
              <td>${item.tipe_mesin ?? '-'}</td>
              <td>${merekDisplay}</td>
              <td>${item.status == 'Aktif' ? 'Aktif' : 'Tidak Aktif'}</td>
            </tr>`;
          });
        } else {
          html = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
        }
        $('#' + tableId + ' tbody').html(html);
        renderPagination(data, search);
      },
      error: function() {
        $('#' + tableId + ' tbody').html('<tr><td colspan="4" class="text-center">Gagal memuat data</td></tr>');
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
    loadMesin('', 1);
  });
  $(document).on('shown.bs.modal', '#' + modalId, function () {
    loadMesin($('#' + inputId).val(), 1);
  });
  $(document).on('keypress', '#' + inputId, function(event) {
    if (event.key === 'Enter') {
      loadMesin(this.value, 1);
      event.preventDefault();
    }
  });
  $(document).on('click', '#' + paginationId + ' .page-link', function(e) {
    e.preventDefault();
    if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
    const page = $(this).data('page');
    const search = $('#' + inputId).val();
    loadMesin(search, page);
  });
  $(document).on('click', '#' + tableId + ' .pilih-mesin', function() {
    const data = {
      id: $(this).data('id'),
      nama: $(this).data('nama'),
      tipe: $(this).data('tipe'),
      merek: $(this).data('merek'),
      status: $(this).data('status'),
      biaya_perhitungan_profil: $(this).data('biaya_perhitungan_profil')
    };
    window.dispatchEvent(new CustomEvent('mesinDipilih', { detail: data }));
    $('#' + modalId).modal('hide');
  });
  // Tambahkan class stackable saat modal cari mesin dibuka
  $(document).on('show.bs.modal', '#' + modalId, function () {
    $(this).addClass('modal-stack');
    // Tambah backdrop stack
    setTimeout(function() {
      $('.modal-backdrop').last().addClass('modal-backdrop-stack');
    }, 10);
  });
  // Hapus class stackable saat modal cari mesin ditutup
  $(document).on('hidden.bs.modal', '#' + modalId, function () {
    $(this).removeClass('modal-stack');
    $('.modal-backdrop-stack').removeClass('modal-backdrop-stack');
  });
})();
</script>
@endpush 
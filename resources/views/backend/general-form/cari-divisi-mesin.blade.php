@php
  $modalId = $modalId ?? 'modalCariDivisiMesin';
  $inputId = $inputId ?? 'searchDivisiMesin';
  $tableId = $tableId ?? 'tabelCariDivisiMesin';
  $paginationId = $paginationId ?? 'paginationCariDivisiMesin';
  $clearBtnId = $clearBtnId ?? 'clearSearchDivisiMesin';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-primary">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">Cari Divisi Mesin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="{{ $inputId }}" class="form-label">Cari Divisi Mesin</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i data-feather="search" class="icon-sm"></i></span>
            <input type="text" class="form-control" id="{{ $inputId }}" placeholder="Cari berdasarkan nama divisi mesin...">
            <span class="input-group-text input-group-addon" id="{{ $clearBtnId }}" style="cursor:pointer;"><i data-feather="delete"></i></span>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0" id="{{ $tableId }}">
            <thead class="table-light">
              <tr>
                <th>Divisi Mesin</th>
                <th>Keterangan</th>
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
  .pilih-divisi-mesin:hover {
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

  function loadDivisiMesin(search = '', page = 1) {
    $('#' + tableId + ' tbody').html('<tr><td colspan="3" class="text-center">Memuat data...</td></tr>');
    $.ajax({
      url: '{{ route('cari-divisi-mesin') }}',
      data: { search: search, page: page },
      dataType: 'json',
      success: function(data) {
        let html = '';
        if (data.data && data.data.length > 0) {
          data.data.forEach(item => {
            const statusBadge = item.aktif ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
            html += `<tr class="pilih-divisi-mesin" 
              data-id="${item.id}" 
              data-nama="${item.nama_detail_parameter}"
              data-keterangan="${item.keterangan ?? ''}">
              <td>${item.nama_detail_parameter ?? '-'}</td>
              <td>${item.keterangan ?? '-'}</td>
              <td>${statusBadge}</td>
            </tr>`;
          });
        } else {
          html = '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>';
        }
        $('#' + tableId + ' tbody').html(html);
        renderPagination(data, search);
      },
      error: function() {
        $('#' + tableId + ' tbody').html('<tr><td colspan="3" class="text-center">Gagal memuat data</td></tr>');
        $('#' + paginationId).html('');
      }
    });
  }

  function renderPagination(data, search) {
    let paginationHtml = '';
    if (data.last_page > 1) {
      paginationHtml += '<nav><ul class="pagination pagination-sm justify-content-center">';
      
      // Previous button
      if (data.current_page > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}">‹</a></li>`;
      }
      
      // Page numbers
      for (let i = Math.max(1, data.current_page - 2); i <= Math.min(data.last_page, data.current_page + 2); i++) {
        const activeClass = i === data.current_page ? ' active' : '';
        paginationHtml += `<li class="page-item${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
      }
      
      // Next button
      if (data.current_page < data.last_page) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page + 1}">›</a></li>`;
      }
      
      paginationHtml += '</ul></nav>';
    }
    $('#' + paginationId).html(paginationHtml);
  }

  // Event listeners
  $(document).on('click', '#' + clearBtnId, function() {
    $('#' + inputId).val('');
    loadDivisiMesin('', 1);
  });

  $(document).on('input', '#' + inputId, function() {
    const search = $(this).val();
    loadDivisiMesin(search, 1);
  });

  $(document).on('click', '#' + paginationId + ' .page-link', function(e) {
    e.preventDefault();
    const page = $(this).data('page');
    const search = $('#' + inputId).val();
    loadDivisiMesin(search, page);
  });

  // Listen for modal show event
  $('#' + modalId).on('show.bs.modal', function() {
    loadDivisiMesin('', 1);
  });

  // Handle row click
  $(document).on('click', '.pilih-divisi-mesin', function() {
    const data = {
      id: $(this).data('id'),
      nama: $(this).data('nama'),
      keterangan: $(this).data('keterangan')
    };
    
    // Dispatch custom event
    window.dispatchEvent(new CustomEvent('divisiMesinDipilih', { detail: data }));
    
    // Close modal
    $('#' + modalId).modal('hide');
  });

})();
</script>
@endpush
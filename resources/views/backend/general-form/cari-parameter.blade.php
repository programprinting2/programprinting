<!-- Modal Cari Parameter -->
@php
  $modalId = $modalId ?? 'modalCariParameter';
  $inputId = $inputId ?? 'searchParameter';
  $tableId = $tableId ?? 'tabelCariParameter';
  $paginationId = $paginationId ?? 'paginationParameter';
  $clearBtnId = $clearBtnId ?? 'clearSearchParameter';
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-primary" style="box-shadow: 0 0 0 1px #000000; border-width: 1px; border-style: solid; border-width: thin;">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">Cari Parameter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Konten modal untuk mencari parameter -->
        <div class="mb-3">
          <label for="{{ $inputId }}" class="form-label">Cari Parameter</label>
          <div class="input-group">
            <span class="input-group-text bg-light">
                <i data-feather="search" class="icon-sm"></i>
            </span>
            <input type="text" class="form-control" name="searchParameter" id="{{ $inputId }}" placeholder="Cari berdasarkan nama parameter..." value="{{ request('searchParameter') }}">
            <script>
              document.addEventListener('shown.bs.modal', function(event) {
                if (event.target.id === '{{ $modalId }}') {
                  const input = document.getElementById('{{ $inputId }}');
                  input.focus();
                  if (input.value) {
                    input.select();
                  }
                }
              });
            </script>
            <script>
              document.addEventListener('shown.bs.modal', function(event) {
                if (event.target.id === '{{ $modalId }}') {
                  document.getElementById('{{ $inputId }}').focus();
                }
              });
            </script>
            <span class="input-group-text input-group-addon" id="{{ $clearBtnId }}" style="cursor:pointer;">
              <i data-feather="delete"></i>
            </span>
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                const clearBtn = document.getElementById('{{ $clearBtnId }}');
                const searchInput = document.getElementById('{{ $inputId }}');
                clearBtn.addEventListener('click', function() {
                  searchInput.value = '';
                  searchInput.focus();
                });
              });
            </script>
          </div>
            </div>


        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0" id="{{ $tableId }}">
        <thead class="table-light">
          <tr>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Parameter</th>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Kategori</th>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Deskripsi</th>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Status</th>
          </tr>
        </thead>
        <tbody>
          <!-- Data parameter akan dimuat di sini melalui AJAX -->
        </tbody>
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
  .pilih-parameter:hover {
    background: #f0f6ff !important;
    cursor: pointer;
  }
</style>
<script>
(function() {
  // Ambil id dinamis dari blade
  const modalId = @json($modalId);
  const inputId = @json($inputId);
  const tableId = @json($tableId);
  const paginationId = @json($paginationId);
  const clearBtnId = @json($clearBtnId);

function loadParameter(search = '', page = 1) {
    $('#' + tableId + ' tbody').html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');
    $.ajax({
        url: '{{ route('backend.cari-parameter') }}',
        data: { searchParameter: search, page: page },
        dataType: 'json',
        success: function(data) {
            let html = '';
            if (data.data && data.data.length > 0) {
                data.data.forEach(item => {
                    html += `<tr class="pilih-parameter"
                        data-id="${item.id}"
                        data-nama="${item.nama ?? '-'}"
                        data-keterangan="${item.keterangan ?? '-'}"
                        data-aktif="${item.aktif}"
                        data-master-parameter="${item.master_parameter ?? '-'}"
                        data-isi-parameter="${item.isi_parameter ?? ''}"
                        >
                        <td>${item.nama ?? '-'}</td>
                        <td>${item.master_parameter ?? '-'}</td>
                        <td>${item.keterangan ?? '-'}</td>
                        <td><span class="badge bg-${item.aktif ? 'primary' : 'secondary'}">${item.aktif ? 'Aktif' : 'Nonaktif'}</span></td>
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

// Event: clear search
  $(document).on('click', '#' + clearBtnId, function() {
    $('#' + inputId).val('');
    loadParameter('', 1);
});

// Load data saat modal dibuka (langsung tampil walau input kosong)
  $(document).on('shown.bs.modal', '#' + modalId, function () {
    loadParameter($('#' + inputId).val(), 1);
});

// Event: handle tombol Enter
  $(document).on('keypress', '#' + inputId, function(event) {
  if (event.key === 'Enter') {
    loadParameter(this.value, 1);
    event.preventDefault();
  }
});

// Event: klik pagination
  $(document).on('click', '#' + paginationId + ' .page-link', function(e) {
    e.preventDefault();
    if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
    const page = $(this).data('page');
    const search = $('#' + inputId).val();
    loadParameter(search, page);
});

// Event: klik parameter, emit event custom agar bisa di-handle di file pemanggil
  $(document).on('click', '#' + tableId + ' .pilih-parameter', function() {
    const data = {
        id: $(this).data('id'),
        nama: $(this).data('nama'),
        keterangan: $(this).data('keterangan'),
        aktif: $(this).data('aktif'),
        master_parameter: $(this).data('master-parameter'),
        isi_parameter: $(this).data('isi-parameter')
    };
    // Emit event custom ke window
    window.dispatchEvent(new CustomEvent('parameterDipilih', { detail: data }));
    // Tutup modal
    $('#' + modalId).modal('hide');
  });
})();
</script>
@endpush


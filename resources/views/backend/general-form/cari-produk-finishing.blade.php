@php
  $modalId = $modalId ?? 'modalCariProdukFinishing';
  $inputId = $inputId ?? 'searchProdukFinishing';
  $tableId = $tableId ?? 'tabelCariProdukFinishing';
  $paginationId = $paginationId ?? 'paginationProdukFinishing';
  $clearBtnId = $clearBtnId ?? 'clearSearchProdukFinishing';
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-primary">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">Cari Produk Finishing</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="{{ $inputId }}" class="form-label">Cari Produk Finishing</label>
          <div class="input-group">
            <span class="input-group-text bg-light"><i data-feather="search" class="icon-sm"></i></span>
            <input type="text" class="form-control" id="{{ $inputId }}" placeholder="Cari berdasarkan kode, nama produk...">
            <span class="input-group-text input-group-addon" id="{{ $clearBtnId }}" style="cursor:pointer;"><i data-feather="delete"></i></span>
          </div>
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="{{ $modalId }}ShowAll">
            <label class="form-check-label" for="{{ $modalId }}ShowAll">
              <small>Tampilkan semua produk finishing</small>
            </label>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0" id="{{ $tableId }}">
            <thead class="table-light">
              <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Harga</th>
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
  .pilih-produk-finishing:hover {
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
  function loadProdukFinishing(page = 1) {
      const searchTerm = $('#' + inputId).val();
      const showAll = $('#' + modalId + 'ShowAll').is(':checked');
      const tbody = $('#' + tableId + ' tbody');
      
      tbody.html('<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Memuat...</td></tr>');

      $.ajax({
          url: '{{ route('backend.cari-produk-finishing') }}',
          method: 'GET',
          data: {
              search: searchTerm,
              page: page,
              produk_id: window.SPKCurrentProdukIdForFinishing || null,
              show_all: showAll ? 1 : 0 
          },
          success: function (response) {
              if (response.data && response.data.length > 0) {
                  let html = '';

                  const getCustomerKategoriHarga = () => {
                      const el = document.getElementById('customerKategoriHarga');
                      return (el?.value ?? 'Umum').toString().trim().toLowerCase();
                  };

                  const getHargaByTier = (tiers, qty) => {
                      if (!Array.isArray(tiers) || tiers.length === 0) return 0;

                      const sorted = [...tiers]
                          .filter(t => t && t.harga != null)
                          .map(t => ({
                              min_qty: Number(t.min_qty ?? 0),
                              max_qty: Number(t.max_qty ?? 0),
                              harga: Number(t.harga ?? 0),
                          }))
                          .sort((a, b) => a.min_qty - b.min_qty);

                      if (sorted.length === 0) return 0;

                      const q = Number(qty ?? 0);

                      const matched = sorted.find(h => q >= h.min_qty && q <= h.max_qty);
                      if (matched) return matched.harga;

                      const last = sorted[sorted.length - 1];
                      if (q > last.max_qty) return last.harga;

                      return sorted[0].harga;
                  };

                  response.data.forEach(function (produk) {
                      const kategori = getCustomerKategoriHarga();
                      const isReseller = kategori === 'Reseller';

                      const hargaTiers = isReseller
                          ? (produk.harga_reseller_json || [])
                          : (produk.harga_bertingkat_json || []);

                      const hargaJual = getHargaByTier(hargaTiers, 1);

                      html += `
                          <tr class="pilih-produk-finishing"
                              data-id="${produk.id}"
                              data-kode="${produk.kode_produk}"
                              data-nama="${produk.nama_produk}"
                              data-satuan="${produk.satuan_nama || 'pcs'}"
                              data-harga="${hargaJual || 0}"
                              data-kategori="${produk.kategori_nama || '-'}"
                              data-jenis="${produk.jenis_produk || 'produk'}"
                              data-panjang="${produk.panjang || 0}"
                              data-lebar="${produk.lebar || 0}"
                              data-panjangLocked="${produk.panjang_locked || false}"
                              data-lebarLocked="${produk.lebar_locked || false}"
                              data-is_metric="${produk.is_metric || false}"
                              data-metric_unit="${produk.metric_unit || '-'}"
                              data-harga-bertingkat-json='${JSON.stringify(produk.harga_bertingkat_json || [])}'
                              data-harga-reseller-json='${JSON.stringify(produk.harga_reseller_json || [])}'>
                              <td>${produk.kode_produk ?? '-'}</td>
                              <td>${produk.nama_produk ?? '-'}</td>
                              <td>${produk.kategori_nama ?? '-'}</td>
                              <td>${produk.satuan_nama ?? 'pcs'}</td>
                              <td class="text-end">Rp ${(hargaJual ? Number(hargaJual) : 0).toLocaleString('id-ID')}</td>
                          </tr>
                      `;
                  });

                  tbody.html(html);
                  renderPagination(response, searchTerm);
                  if (typeof feather !== 'undefined') feather.replace();
              } else {
                  tbody.html('<tr><td colspan="6" class="text-center text-muted">Tidak ada produk finishing ditemukan</td></tr>');
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
          loadProdukFinishing();
      }, 300);
  });

  $(document).on('click', '#' + clearBtnId, function() {
      $('#' + inputId).val('');
      loadProdukFinishing();
  });

  $(document).on('click', '#' + paginationId + ' .page-link', function(e) {
      e.preventDefault();
      if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
      const page = $(this).data('page');
      const search = $('#' + inputId).val();
      loadProdukFinishing(search, page);
  });

  $(document).on('click', '.pilih-produk-finishing', function() {
      const panjangLockedRaw = $(this).attr('data-panjang-locked') || $(this).attr('data-panjangLocked');
      const lebarLockedRaw = $(this).attr('data-lebar-locked') || $(this).attr('data-lebarLocked');
      const data = {
          id: parseInt($(this).data('id')) || 0,
          kode_produk: $(this).data('kode'),
          nama_produk: $(this).data('nama'),
          satuan_nama: $(this).data('satuan') || 'pcs',
          harga_satuan: parseFloat($(this).data('harga')) || 0,
          kategori_nama: $(this).data('kategori'),
          is_metric: $(this).data('is_metric') === 'true' || $(this).data('is_metric') === true,
          panjang_locked: panjangLockedRaw === 'true' || panjangLockedRaw === '1' || panjangLockedRaw === true,
          lebar_locked: lebarLockedRaw === 'true' || lebarLockedRaw === '1' || lebarLockedRaw === true,
          panjang: parseFloat($(this).data('panjang')) || 0,
          lebar: parseFloat($(this).data('lebar')) || 0, 
          metric_unit: $(this).data('metric_unit') || 'cm',
          harga_bertingkat_json: JSON.parse($(this).attr('data-harga-bertingkat-json').replace(/&#39;/g, "'") || '[]'),
          harga_reseller_json: JSON.parse($(this).attr('data-harga-reseller-json').replace(/&#39;/g, "'") || '[]'),
          sourceModal: 'spk'
      };

      window.dispatchEvent(new CustomEvent('produkFinishingDipilih', { detail: data }));
      $('#' + modalId).modal('hide');
  });

  // Load initial data when modal is shown
  $(document).on('shown.bs.modal', '#' + modalId, function() {
      loadProdukFinishing();
  });
  $(document).on('change', '#' + modalId + 'ShowAll', function() {
    loadProdukFinishing();
  });
})();
</script>
@endpush
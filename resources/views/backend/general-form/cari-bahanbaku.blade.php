<!-- Modal Cari Bahan Baku (pastikan ada di file ini atau include) -->
<div class="modal fade" id="modalCariBahanBaku" tabindex="-1" aria-labelledby="modalCariBahanBakuLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border border-primary" style="box-shadow: 0 0 0 1px #000000; border-width: 1px; border-style: solid; border-width: thin;">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCariBahanBakuLabel">Cari Bahan Baku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Konten modal untuk mencari bahan baku -->
        <div class="mb-3">
          <label for="searchBahanBaku" class="form-label">Cari Bahan Baku</label>
          <div class="input-group">
             
            <span class="input-group-text bg-light">
                <i data-feather="search" class="icon-sm"></i>
            </span>
            <input type="text" class="form-control" name="searchBahanBaku" id="searchBahanBaku" placeholder="Cari berdasarkan kode, nama produk..." value="{{ request('searchBahanBaku') }}">
            <script>
              document.addEventListener('shown.bs.modal', function(event) {
                if (event.target.id === 'modalCariBahanBaku') {
                  const input = document.getElementById('searchBahanBaku');
                  input.focus();
                  if (input.value) {
                    input.select();
                  }
                }
              });
            </script>
            <script>
              document.addEventListener('shown.bs.modal', function(event) {
                if (event.target.id === 'modalCariBahanBaku') {
                  document.getElementById('searchBahanBaku').focus();
                }
              });
            </script>
            <span class="input-group-text input-group-addon" id="clearSearchBahanBaku" style="cursor:pointer;">
              <i data-feather="delete"></i>
            </span>
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                const clearBtn = document.getElementById('clearSearchBahanBaku');
                const searchInput = document.getElementById('searchBahanBaku');
                clearBtn.addEventListener('click', function() {
                  searchInput.value = '';
                  searchInput.focus();
                });
              });
            </script>
          </div>
            </div>
       
       
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0" id="tabelCariBahanBaku">
        <thead class="table-light">
          <tr>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Kode Bahan</th>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Bahan</th>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Kategori</th>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Sub-Kategori</th>
            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Satuan Utama</th>
          </tr>
        </thead>
        <tbody>
          <!-- Data bahan baku akan dimuat di sini melalui AJAX -->
        </tbody>
          </table>
        </div>
        <div id="paginationBahanBaku" class="mt-3 d-flex justify-content-center"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@push('custom-scripts')
<script>
function loadBahanBaku(search = '', page = 1) {
    $('#tabelCariBahanBaku tbody').html('<tr><td colspan="5" class="text-center">Memuat data...</td></tr>');
    $.ajax({
        url: '{{ route('backend.cari-bahanbaku') }}',
        data: { searchBahanBaku: search, page: page },
        dataType: 'json',
        success: function(data) {
            let html = '';
            if (data.data && data.data.length > 0) {
                data.data.forEach(item => {
                    html += `<tr class="pilih-bahan-baku" 
                        data-id="${item.id}" 
                        data-nama="${item.nama_bahan ?? '-'}" 
                        data-satuan="${item.satuan_utama ?? '-'}"
                        data-harga="${item.harga_terakhir ?? 0}">
                        <td>${item.kode_bahan ?? '-'}</td>
                        <td>${item.nama_bahan ?? '-'}</td>
                        <td>${item.kategori ?? '-'}</td>
                        <td>${item.sub_kategori ?? '-'}</td>
                        <td>${item.satuan_utama ?? '-'}</td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
            }
            $('#tabelCariBahanBaku tbody').html(html);
            renderPagination(data, search);
        },
        error: function() {
            $('#tabelCariBahanBaku tbody').html('<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>');
            $('#paginationBahanBaku').html('');
        }
    });
}

function renderPagination(data, search) {
    let html = '';
    if (data.last_page > 1) {
        html += '<nav><ul class="pagination pagination-sm">';
        // Tombol prev
        if (data.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page - 1}" data-search="${search}">&laquo;</a></li>`;
        } else {
            html += '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
        }
        // Nomor halaman
        for (let i = 1; i <= data.last_page; i++) {
            if (i === data.current_page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${i}" data-search="${search}">${i}</a></li>`;
            }
        }
        // Tombol next
        if (data.current_page < data.last_page) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${data.current_page + 1}" data-search="${search}">&raquo;</a></li>`;
        } else {
            html += '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
        }
        html += '</ul></nav>';
    }
    $('#paginationBahanBaku').html(html);
}

// Event: clear search
$(document).on('click', '#clearSearchBahanBaku', function() {
    $('#searchBahanBaku').val('');
    loadBahanBaku('', 1);
});

// Load data saat modal dibuka
$('#modalCariBahanBaku').on('shown.bs.modal', function () {
    loadBahanBaku($('#searchBahanBaku').val(), 1);
});

// Event: handle tombol Enter
$(document).on('keypress', '#searchBahanBaku', function(event) {
  if (event.key === 'Enter') {
    loadBahanBaku(this.value, 1);
    event.preventDefault();
  }
});

// Event: klik pagination
$(document).on('click', '#paginationBahanBaku .page-link', function(e) {
    e.preventDefault();
    if ($(this).parent().hasClass('disabled') || $(this).parent().hasClass('active')) return;
    const page = $(this).data('page');
    const search = $('#searchBahanBaku').val();
    loadBahanBaku(search, page);
});

// Event: klik bahan baku, emit event custom agar bisa di-handle di file pemanggil
$(document).on('click', '.pilih-bahan-baku', function() {
    const data = {
        id: $(this).data('id'),
        nama: $(this).data('nama'),
        satuan: $(this).data('satuan'),
        harga: $(this).data('harga') || 0
    };
    // Emit event custom ke window
    window.dispatchEvent(new CustomEvent('bahanBakuDipilih', { detail: data }));
    // Tutup modal
    $('#modalCariBahanBaku').modal('hide');
});

// Hitung total saat harga/jumlah berubah
// $(document).on('input', '.harga-bahan, .jumlah-bahan', function() {
//     const row = $(this).closest('tr');
//     const harga = parseInt(row.find('.harga-bahan').val()) || 0;
//     const jumlah = parseInt(row.find('.jumlah-bahan').val()) || 0;
//     const total = harga * jumlah;
//     row.find('.total-bahan').text('Rp ' + total.toLocaleString('id-ID'));
//     hitungTotalModalBahan();
// });

// // Hapus baris bahan baku
// $(document).on('click', '.btn-hapus-bahan', function() {
//     $(this).closest('tr').remove();
//     hitungTotalModalBahan();
// });

// // Hitung total semua bahan
// function hitungTotalModalBahan() {
//     let total = 0;
//     $('#tabelBahanBaku tbody tr').each(function() {
//         const harga = parseInt($(this).find('.harga-bahan').val()) || 0;
//         const jumlah = parseInt($(this).find('.jumlah-bahan').val()) || 0;
//         total += harga * jumlah;
//     });
//     $('#totalModalBahan').text('Rp ' + total.toLocaleString('id-ID'));
// }
</script>
@endpush
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
            {{-- @forelse ($bahanBaku as $item)
              <tr>
                <td>{{ $item->kode_bahan }}</td>
                <td>{{ $item->nama_bahan }}</td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->sub_kategori }}</td>
                <td>{{ $item->satuan_utama }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center">Tidak ada data</td>
              </tr>
            @endforelse --}}
        </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
        </div>
  </div>
</div>

@push('custom-scripts')
<script>
/**
 * Agar modal utama tidak tertutup saat modal kedua dibuka (Bootstrap 5)
 * - Modal utama: #tambahProduk
 * - Modal kedua: #modalCariBahanBaku
 * 
 * Kunci: intercept event hide pada modal utama saat modal kedua dibuka.
 */
$(function() {
    // Cegah modal utama tertutup saat modal kedua dibuka
    var preventClose = false;

    $('#modalCariBahanBaku').on('show.bs.modal', function () {
        preventClose = true;
    });

    $('#tambahProduk').on('hide.bs.modal', function (e) {
        if (preventClose) {
            e.preventDefault();
        }
    });

    $('#modalCariBahanBaku').on('hidden.bs.modal', function () {
        preventClose = false;
        if ($('#tambahProduk').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });
});
</script>
@endpush

@push('custom-scripts')
<script>
// Nested modal: buka modalCariBahanBaku tanpa menutup modal parent
$(document).on('click', '#btnTambahBahan', function() {
    var modalBahan = new bootstrap.Modal(document.getElementById('modalCariBahanBaku'), {
        backdrop: 'static',
        keyboard: false,
        focus: true
    });
    modalBahan.show();

    // Pastikan body tetap punya class modal-open agar modal parent tidak tertutup
    setTimeout(function() {
        if ($('#tambahProduk').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    }, 200);
});

function loadBahanBaku(search = '') {
    fetch(`/backend/cari-bahanbaku?search=${encodeURIComponent(search)}`)
        .then(res => res.json())
        .then(data => {
            let html = '';
            if (data.length > 0) {
                data.forEach(item => {
                    html += `<tr>
                        <td>${item.kode_bahan}</td>
                        <td>${item.nama_bahan}</td>
                        <td>${item.kategori}</td>
                        <td>${item.sub_kategori}</td>
                        <td>${item.satuan_utama}</td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>';
            }
            $('#tabelCariBahanBaku tbody').html(html);
        })
        .catch(() => {
            $('#tabelCariBahanBaku tbody').html('<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>');
        });
}

// Event: search realtime saat ketik
$(document).on('input', '#searchBahanBaku', function() {
    loadBahanBaku(this.value);
});

// Event: clear search
$(document).on('click', '#clearSearchBahanBaku', function() {
    $('#searchBahanBaku').val('');
    loadBahanBaku('');
});

// Load data saat modal dibuka
$('#modalCariBahanBaku').on('shown.bs.modal', function () {
    loadBahanBaku($('#searchBahanBaku').val());
});

// Event: handle tombol Enter
$(document).on('keypress', '#searchBahanBaku', function(event) {
  if (event.key === 'Enter') {
    loadBahanBaku(this.value);
    event.preventDefault(); // Mencegah form submit saat enter ditekan
  }
});
</script>
@endpush
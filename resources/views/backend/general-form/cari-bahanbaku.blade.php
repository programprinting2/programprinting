
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

</script>
@endpush
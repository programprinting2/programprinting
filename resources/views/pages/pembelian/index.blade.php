@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
@endpush

@section('content')

<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pembelian</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title">Data Pembelian</h6>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-dark d-flex align-items-center gap-1" type="button" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa fa-plus"></i> Tambah Pembelian
                </button>
            </div>
        </div>
        <p class="text-muted mb-3">Kelola data pembelian bahan baku dan barang lainnya</p>
        <form class="search-form">
          <div class="input-group">
            <div class="input-group-text">
              <i data-feather="search" class="icon-md cursor-pointer"></i>
            </div>
            <input type="text" class="form-control" id="searchForm" placeholder="Cari data pembelian..." onkeyup="filterContent()">
            <button type="button" class="btn btn-outline-secondary" onclick="clearSearchForm()" title="Clear">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </form>
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between flex-wrap">
          <div class="d-flex align-items-center gap-2">
            <label class="me-2">Filter:</label>
            <select class="form-select form-select-sm" style="width: 150px;" aria-label="Filter Status">
              <option selected disabled>Status</option>
              <option value="lunas">Lunas</option>
              <option value="belum_lunas">Belum Lunas</option>
            </select>
            <select class="form-select form-select-sm" style="width: 150px;" aria-label="Filter Supplier">
              <option selected disabled>Supplier</option>
              <option value="supplier1">Supplier 1</option>
              <option value="supplier2">Supplier 2</option>
            </select>
            <select class="form-select form-select-sm" style="width: 150px;" aria-label="Filter Periode">
              <option selected disabled>Periode</option>
              <option value="bulan_ini">Bulan Ini</option>
              <option value="bulan_lalu">Bulan Lalu</option>
              <option value="tahun_ini">Tahun Ini</option>
            </select>
          </div>
          <div class="pagination-controls d-flex align-items-center gap-2">
            <button class="btn btn-outline-dark btn-sm" id="prevPageButton">
                <i class="fas fa-chevron-left"></i>
            </button>
            <span class="text-primary" id="currentPage">1/1</span>
            <button class="btn btn-outline-dark btn-sm" id="nextPageButton">
                <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
        <br>
        <div class="table-responsive">
          <table id="data-source-1" class="table">
            <thead>
                <tr class="table-responsive">
                    <th style="width: 5%;">No</th>      
                    <th style="width: 15%;">Nomor Faktur</th>                                                                                                    
                    <th style="width: 15%;">Tanggal</th> 
                    <th style="width: 20%;">Supplier</th>  
                    <th style="width: 10%;">Total</th>                                        
                    <th style="width: 10%;">Status</th>
                    <th style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data akan ditampilkan di sini -->
            </tbody>
        </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Data - UI Baru -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <div>
          <h4 class="modal-title fw-bold" id="addModalLabel">Buat Pembelian Baru</h4>
          <div class="text-muted small">Buat transaksi pembelian bahan baku baru.</div>
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" form="addForm" class="btn btn-primary"><i class="fa fa-save me-1"></i> Simpan Pembelian</button>
        </div>
      </div>
      <div class="modal-body pt-0">
        <form id="addForm" action="{{ route('pembelian.create') }}" method="POST">
          @csrf
          <div class="row">
            <!-- Kiri: Info Pemasok, Catatan, Ringkasan -->
            <div class="col-md-3">
              <div class="mb-4 p-3 border rounded bg-light">
                <div class="fw-semibold mb-2"><i class="fa fa-user me-1"></i> Informasi Pemasok</div>
                <div class="mb-3">
                  <label class="form-label">Pemasok</label>
                  <div class="input-group">
                    <select class="form-select" name="supplier_id" required>
                      <option value="" selected disabled>Pilih pemasok...</option>
                      <option value="1">PT. Sumber Makmur</option>
                      <option value="2">CV. Bahan Jaya</option>
                      <option value="3">UD. Kertas Abadi</option>
                    </select>
                    <button class="btn btn-outline-secondary" type="button"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Tanggal Pembelian</label>
                  <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Status Pembelian</label>
                  <select class="form-select" name="status">
                    <option value="dipesan">Dipesan</option>
                    <option value="diterima">Diterima</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Gunakan Nomor Form</label>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="useFormNumber" name="use_form_number" onchange="toggleNomorForm()">
                    <label class="form-check-label" for="useFormNumber"></label>
                  </div>
                  <input type="text" class="form-control mt-2" id="nomorFormInput" name="nomor_form" placeholder="Nomor form" style="display:none;">
                </div>
                <div class="mb-3">
                  <label class="form-label">Tanggal Jatuh Tempo <span class="text-muted small">(Opsional)</span></label>
                  <input type="date" class="form-control" name="jatuh_tempo">
                </div>
              </div>
              <div class="mb-4 p-3 border rounded bg-light">
                <div class="fw-semibold mb-2"><i class="fa fa-sticky-note me-1"></i> Catatan</div>
                <textarea class="form-control" name="catatan" rows="3" placeholder="Tambahkan catatan untuk pembelian ini"></textarea>
              </div>
              <div class="p-3 border rounded bg-light">
                <div class="fw-semibold mb-2"><i class="fa fa-dollar-sign me-1"></i> Ringkasan Biaya</div>
                <div class="d-flex justify-content-between small mb-1"><span>Subtotal:</span><span class="ringkasan-subtotal">Rp 0</span></div>
                <div class="d-flex justify-content-between small mb-1"><span>Diskon:</span><span class="ringkasan-diskon">- Rp 0</span></div>
                <div class="d-flex justify-content-between small mb-1"><span>Pengiriman:</span><span class="ringkasan-pengiriman">Rp 0</span></div>
                <div class="d-flex justify-content-between small mb-1"><span>Pajak (11%):</span><span class="ringkasan-pajak">Rp 0</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold"><span>Total:</span><span class="ringkasan-total">Rp 0</span></div>
              </div>
            </div>
            <!-- Kanan: Item Pembelian & Biaya Tambahan -->
            <div class="col-md-9">
              <div class="card border-0 shadow-none">
                <div class="card-body p-0">
                  <ul class="nav nav-tabs mb-3" id="tabPembelian" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="item-tab" data-bs-toggle="tab" data-bs-target="#itemPembelian" type="button" role="tab">Item Pembelian</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="biaya-tab" data-bs-toggle="tab" data-bs-target="#biayaTambahan" type="button" role="tab">Biaya Tambahan</button>
                    </li>
                  </ul>
                  <div class="tab-content" id="tabPembelianContent">
                    <div class="tab-pane fade show active" id="itemPembelian" role="tabpanel">
                      <div class="mb-3">
                        <label class="form-label">Material</label>
                        <div class="input-group mb-2">
                          <select class="form-select" id="materialSelect" name="material_id">
                            <option value="" selected disabled>Pilih material...</option>
                            <option value="1" data-nama="Kertas HVS A4" data-harga="25000">Kertas HVS A4</option>
                            <option value="2" data-nama="Tinta Printer Epson" data-harga="50000">Tinta Printer Epson</option>
                            <option value="3" data-nama="Lem Kertas" data-harga="10000">Lem Kertas</option>
                          </select>
                          <button class="btn btn-outline-secondary" type="button"><i class="fa fa-search"></i></button>
                        </div>
                        <div class="row g-2 mb-2">
                          <div class="col-md-3">
                            <input type="number" class="form-control" id="jumlahInput" placeholder="Jumlah" min="1" value="1">
                          </div>
                          <div class="col-md-3">
                            <input type="number" class="form-control" id="hargaInput" placeholder="Harga" min="0" value="0">
                          </div>
                          <div class="col-md-3">
                            <input type="number" class="form-control" id="diskonInput" placeholder="Diskon" min="0" value="0">
                          </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary" id="btnTambahItem"><i class="fa fa-plus"></i> Tambah Item</button>
                      </div>
                      <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="tabelItemPembelian">
                          <thead class="table-light">
                            <tr>
                              <th>Kode</th>
                              <th>Material</th>
                              <th>Jumlah</th>
                              <th>Harga</th>
                              <th>Diskon</th>
                              <th>Total</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody id="itemBody">
                            <tr>
                              <td colspan="7" class="text-center text-muted">Belum ada item yang ditambahkan</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="biayaTambahan" role="tabpanel">
                      <div class="row g-3 mb-3">
                        <div class="col-md-3">
                          <label class="form-label">Diskon (%)</label>
                          <input type="number" class="form-control" value="0" name="diskon_persen">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Tarif Pajak (%)</label>
                          <input type="number" class="form-control" value="0" name="tarif_pajak">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Jumlah Diskon (Rp)</label>
                          <input type="number" class="form-control" value="0" name="jumlah_diskon">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Nota Kredit (Rp)</label>
                          <input type="number" class="form-control" value="0" name="nota_kredit">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Biaya Pengiriman (Rp)</label>
                          <input type="number" class="form-control" value="0" name="biaya_pengiriman">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Label Biaya Lain</label>
                          <input type="text" class="form-control" placeholder="Biaya admin, dll." name="label_biaya_lain">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Jumlah (Rp)</label>
                          <input type="number" class="form-control" value="0" name="jumlah_biaya_lain">
                        </div>
                      </div>
                      <div class="p-3 border rounded bg-light mt-3">
                        <div class="fw-bold mb-2">Ringkasan Biaya</div>
                        <div class="d-flex justify-content-between small mb-1"><span>Subtotal:</span><span class="ringkasan-subtotal">Rp 0</span></div>
                        <div class="d-flex justify-content-between small mb-1"><span>Diskon:</span><span class="ringkasan-diskon">- Rp 0</span></div>
                        <div class="d-flex justify-content-between small mb-1"><span>Biaya Pengiriman:</span><span class="ringkasan-pengiriman">Rp 0</span></div>
                        <div class="d-flex justify-content-between small mb-1"><span>Pajak (%):</span><span class="ringkasan-pajak">Rp 0</span></div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold"><span>Total:</span><span class="ringkasan-total">Rp 0</span></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Simpan Pembelian</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Modal Tambah Data - UI Baru -->

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
    let dataTableInstance;
    
    document.addEventListener('DOMContentLoaded', function () {
        try {
            dataTableInstance = $('#data-source-1').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                dom: 'lrtip'
            });
            
            updatePaginationControls();
        } catch (error) {
            console.error('Error initializing DataTables:', error);
        }
    });
    
    function updatePaginationControls() {
        if (!dataTableInstance) return;
        
        const pageInfo = dataTableInstance.page.info();
        document.getElementById('currentPage').textContent = `${pageInfo.page + 1}/${pageInfo.pages || 1}`;
        
        document.getElementById('prevPageButton').disabled = pageInfo.page === 0;
        document.getElementById('nextPageButton').disabled = pageInfo.page === pageInfo.pages - 1 || pageInfo.pages === 0;
    }
    
    document.getElementById('prevPageButton').addEventListener('click', function () {
        if (!dataTableInstance || !dataTableInstance.page.info().page) return;
        dataTableInstance.page('previous').draw('page');
        updatePaginationControls();
    });
    
    document.getElementById('nextPageButton').addEventListener('click', function () {
        if (!dataTableInstance || dataTableInstance.page.info().page === dataTableInstance.page.info().pages - 1) return;
        dataTableInstance.page('next').draw('page');
        updatePaginationControls();
    });
    
    function filterContent() {
        if (!dataTableInstance) return;
        dataTableInstance.search(document.getElementById('searchForm').value).draw();
    }
    
    function clearSearchForm() {
        document.getElementById('searchForm').value = '';
        filterContent();
    }

    function toggleNomorForm() {
      const checkbox = document.getElementById('useFormNumber');
      const input = document.getElementById('nomorFormInput');
      input.style.display = checkbox.checked ? 'block' : 'none';
    }

    // Tambah item ke tabel
    let itemIndex = 1;
    document.getElementById('btnTambahItem').addEventListener('click', function() {
      const materialSelect = document.getElementById('materialSelect');
      const jumlahInput = document.getElementById('jumlahInput');
      const hargaInput = document.getElementById('hargaInput');
      const diskonInput = document.getElementById('diskonInput');
      const itemBody = document.getElementById('itemBody');

      const materialId = materialSelect.value;
      const materialNama = materialSelect.options[materialSelect.selectedIndex]?.getAttribute('data-nama') || '';
      const harga = parseInt(hargaInput.value) || 0;
      const jumlah = parseInt(jumlahInput.value) || 1;
      const diskon = parseInt(diskonInput.value) || 0;
      if (!materialId) return alert('Pilih material terlebih dahulu!');
      if (jumlah < 1) return alert('Jumlah minimal 1!');
      if (harga < 1) return alert('Harga minimal 1!');
      if (diskon < 0) return alert('Diskon tidak boleh negatif!');
      if (diskon > harga * jumlah) return alert('Diskon tidak boleh lebih besar dari total harga!');

      const total = (harga * jumlah) - diskon;

      // Hapus row kosong jika ada
      if (itemBody.children.length === 1 && itemBody.children[0].children.length === 1) {
        itemBody.innerHTML = '';
      }

      // Tambah baris baru
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${materialId}</td>
        <td>${materialNama}</td>
        <td class="item-jumlah">${jumlah}</td>
        <td class="item-harga">${harga}</td>
        <td class="item-diskon">${diskon}</td>
        <td class="item-total">${total}</td>
        <td>
          <button type="button" class="btn btn-sm btn-warning btn-edit-item me-1"><i class="fa fa-edit"></i></button>
          <button type="button" class="btn btn-sm btn-danger btn-hapus-item"><i class="fa fa-trash"></i></button>
        </td>
      `;
      itemBody.appendChild(row);

      // Reset input
      materialSelect.value = '';
      jumlahInput.value = 1;
      hargaInput.value = 0;
      diskonInput.value = 0;

      updateRingkasanBiaya();
    });

    // Hapus & Edit item dari tabel
    document.getElementById('itemBody').addEventListener('click', function(e) {
      if (e.target.closest('.btn-hapus-item')) {
        e.target.closest('tr').remove();
        if (itemBody.children.length === 0) {
          itemBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Belum ada item yang ditambahkan</td></tr>';
        }
        updateRingkasanBiaya();
      }
      if (e.target.closest('.btn-edit-item')) {
        const row = e.target.closest('tr');
        // Ambil data lama
        const jumlah = row.querySelector('.item-jumlah').textContent;
        const harga = row.querySelector('.item-harga').textContent;
        const diskon = row.querySelector('.item-diskon').textContent;
        // Ganti sel menjadi input
        row.querySelector('.item-jumlah').innerHTML = `<input type='number' class='form-control form-control-sm input-edit-jumlah' value='${jumlah}' min='1'>`;
        row.querySelector('.item-harga').innerHTML = `<input type='number' class='form-control form-control-sm input-edit-harga' value='${harga}' min='1'>`;
        row.querySelector('.item-diskon').innerHTML = `<input type='number' class='form-control form-control-sm input-edit-diskon' value='${diskon}' min='0'>`;
        // Ganti tombol
        row.querySelector('td:last-child').innerHTML = `
          <button type="button" class="btn btn-sm btn-success btn-simpan-edit me-1"><i class="fa fa-check"></i></button>
          <button type="button" class="btn btn-sm btn-secondary btn-batal-edit"><i class="fa fa-times"></i></button>
        `;
      }
      if (e.target.closest('.btn-batal-edit')) {
        const row = e.target.closest('tr');
        // Kembalikan ke nilai sebelum edit
        const jumlah = row.querySelector('.input-edit-jumlah').defaultValue;
        const harga = row.querySelector('.input-edit-harga').defaultValue;
        const diskon = row.querySelector('.input-edit-diskon').defaultValue;
        row.querySelector('.item-jumlah').textContent = jumlah;
        row.querySelector('.item-harga').textContent = harga;
        row.querySelector('.item-diskon').textContent = diskon;
        // Update total
        const total = (parseInt(harga) || 0) * (parseInt(jumlah) || 0) - (parseInt(diskon) || 0);
        row.querySelector('.item-total').textContent = total;
        // Kembalikan tombol
        row.querySelector('td:last-child').innerHTML = `
          <button type="button" class="btn btn-sm btn-warning btn-edit-item me-1"><i class="fa fa-edit"></i></button>
          <button type="button" class="btn btn-sm btn-danger btn-hapus-item"><i class="fa fa-trash"></i></button>
        `;
        updateRingkasanBiaya();
      }
      if (e.target.closest('.btn-simpan-edit')) {
        const row = e.target.closest('tr');
        // Ambil nilai baru
        const jumlah = row.querySelector('.input-edit-jumlah').value;
        const harga = row.querySelector('.input-edit-harga').value;
        const diskon = row.querySelector('.input-edit-diskon').value;
        // Validasi
        if (jumlah < 1) return alert('Jumlah minimal 1!');
        if (harga < 1) return alert('Harga minimal 1!');
        if (diskon < 0) return alert('Diskon tidak boleh negatif!');
        if (parseInt(diskon) > parseInt(harga) * parseInt(jumlah)) return alert('Diskon tidak boleh lebih besar dari total harga!');
        // Update sel
        row.querySelector('.item-jumlah').textContent = jumlah;
        row.querySelector('.item-harga').textContent = harga;
        row.querySelector('.item-diskon').textContent = diskon;
        // Update total
        const total = (parseInt(harga) || 0) * (parseInt(jumlah) || 0) - (parseInt(diskon) || 0);
        row.querySelector('.item-total').textContent = total;
        // Kembalikan tombol
        row.querySelector('td:last-child').innerHTML = `
          <button type="button" class="btn btn-sm btn-warning btn-edit-item me-1"><i class="fa fa-edit"></i></button>
          <button type="button" class="btn btn-sm btn-danger btn-hapus-item"><i class="fa fa-trash"></i></button>
        `;
        updateRingkasanBiaya();
      }
    });

    // Update ringkasan biaya otomatis
    function updateRingkasanBiaya() {
      // Ambil data item
      let subtotal = 0;
      let totalDiskon = 0;
      let total = 0;
      const itemBody = document.getElementById('itemBody');
      for (let row of itemBody.children) {
        if (row.children.length < 7) continue;
        const harga = parseInt(row.querySelector('.item-harga').textContent) || 0;
        const jumlah = parseInt(row.querySelector('.item-jumlah').textContent) || 0;
        const diskon = parseInt(row.querySelector('.item-diskon').textContent) || 0;
        subtotal += harga * jumlah;
        totalDiskon += diskon;
      }

      // Ambil biaya tambahan
      const diskonPersen = parseFloat(document.querySelector('[name="diskon_persen"]').value) || 0;
      const jumlahDiskon = parseInt(document.querySelector('[name="jumlah_diskon"]').value) || 0;
      const biayaPengiriman = parseInt(document.querySelector('[name="biaya_pengiriman"]').value) || 0;
      const tarifPajak = parseFloat(document.querySelector('[name="tarif_pajak"]').value) || 0;
      const notaKredit = parseInt(document.querySelector('[name="nota_kredit"]').value) || 0;
      const jumlahBiayaLain = parseInt(document.querySelector('[name="jumlah_biaya_lain"]').value) || 0;

      // Hitung diskon total
      let diskonTotal = totalDiskon + jumlahDiskon + Math.round(subtotal * (diskonPersen/100));
      // Hitung pajak
      let dpp = subtotal - diskonTotal;
      let pajak = Math.round(dpp * (tarifPajak/100));
      // Hitung total
      total = dpp + pajak + biayaPengiriman + jumlahBiayaLain - notaKredit;

      // Update tampilan
      document.querySelectorAll('.ringkasan-subtotal').forEach(e => e.textContent = 'Rp ' + subtotal.toLocaleString());
      document.querySelectorAll('.ringkasan-diskon').forEach(e => e.textContent = '- Rp ' + diskonTotal.toLocaleString());
      document.querySelectorAll('.ringkasan-pengiriman').forEach(e => e.textContent = 'Rp ' + biayaPengiriman.toLocaleString());
      document.querySelectorAll('.ringkasan-pajak').forEach(e => e.textContent = 'Rp ' + pajak.toLocaleString());
      document.querySelectorAll('.ringkasan-total').forEach(e => e.textContent = 'Rp ' + total.toLocaleString());
    }

    // Trigger update saat input biaya tambahan berubah
    ['diskon_persen','jumlah_diskon','biaya_pengiriman','tarif_pajak','nota_kredit','jumlah_biaya_lain'].forEach(function(name) {
      document.querySelector(`[name="${name}"]`).addEventListener('input', updateRingkasanBiaya);
    });
</script>
@endpush 
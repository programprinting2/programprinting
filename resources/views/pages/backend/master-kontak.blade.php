@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
@endpush

@section('content')

<style>
  .custom-tabs .nav-tabs {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa; /* Light background for tabs */
    border-radius: 0.25rem; /* Rounded corners */
  }
  .custom-tabs .nav-link {
    border: none;
    color: #6c757d; /* Default text color */
    padding: 10px 15px;
    transition: color 0.2s, background-color 0.2s;
  }
  .custom-tabs .nav-link.active {
    background-color: #fff; /* White background for active tab */
    color: #000; /* Black text for active tab */
    font-weight: bold;
    border: 1px solid #e9ecef;
    border-bottom: none; /* Remove bottom border for active tab */
    border-radius: 0.25rem 0.25rem 0 0; /* Rounded top corners */
  }
  .fixed-modal-body {
    height: 450px; /* Adjust height to fit content */
    overflow-y: auto; /* Enable scrolling if content overflows */
    padding-left: 20px; /* Adjust padding to move content to the left */
    padding-right: 20px; /* Optional: Add right padding for symmetry */
  }
  .tab-content > .tab-pane {
    height: 100%; /* Ensure consistent height for tab content */
  }
  .modal-footer-container {
    position: sticky; /* Make the footer container fixed */
    bottom: 0; /* Stick to the bottom of the modal */
    background-color: #fff; /* Match the modal background */
    z-index: 10; /* Ensure it stays above the content */
    padding: 15px; /* Add padding for spacing */
    border-top: 1px solid #ddd; /* Add a border for separation */
    display: flex; /* Align buttons horizontally */
    justify-content: flex-end; /* Align buttons to the right */
  }
</style>

<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Tables</a></li>
    <li class="breadcrumb-item active" aria-current="page">Master Kontak</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title">Data Master Kontak</h6>
            <div class="d-flex align-items-center gap-3">
                <div class="btn-group" role="group" aria-label="View toggle">
                    <button type="button" class="btn btn-primary active" onclick="showTab('table')" id="tableViewButton">
                        <i class="fas fa-list"></i> Table
                    </button>
                    <button type="button" class="btn btn-light" onclick="showTab('grid')" id="gridViewButton">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                </div>
                <button class="btn btn-dark d-flex align-items-center gap-1" type="button" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa fa-plus"></i> Tambah Kontak
                </button>
            </div>
        </div>
        <p class="text-muted mb-3">Kelola data kontak seperti staff, customer, atau supplier.</p>
        <hr style="border: 0,5px solid #5e5e5e; margin: 10px 0;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <!-- Search and Filter Section -->
            <div class="d-flex align-items-center" style="margin-right: 2px;">
                <div class="input-group" style="width: 250px; margin-right: 10px;">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control form-control-sm" id="searchForm" placeholder="Cari..." onkeyup="filterContent()">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSearchForm()" title="Clear">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="input-group" style="width: 200px; margin-right: 10px;">
                    <span class="input-group-text">
                        <i class="fas fa-filter"></i>
                    </span>
                    <select class="form-select form-select-sm" aria-label="Filter Tipe">
                        <option selected>Semua Tipe</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                        <option value="supplier">Supplier</option>
                    </select>
                </div>
                <div class="input-group" style="width: 200px;">
                    <select class="form-select form-select-sm" aria-label="Filter Tipe">
                        <option selected>Semua Tipe</option>
                        <option value="staff">Staff</option>
                        <option value="customer">Customer</option>
                        <option value="supplier">Supplier</option>
                    </select>
                </div>
            </div>

            <ul class="pagination pagination-rounded d-flex align-items-center justify-content-end mb-0">
                <li class="page-item">
                    <button class="page-link btn-sm text-secondary" id="prevPageButton" aria-label="Previous">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </li>
                <li class="page-item">
                    <span class="page-link text-secondary btn-sm" id="currentPage">1/1</span>
                </li>
                <li class="page-item">
                    <button class="page-link btn-sm text-secondary" id="nextPageButton" aria-label="Next">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </li>
            </ul>

        </div>
        <hr style="border: 0,5px solid #5e5e5e; margin: 10px 0;">
        <div class="table-responsive" id="table-view">
          <table id="data-source-1" class="table">
            <thead>
                <tr>
                    <th style="width: 15%;">Tipe</th>
                    <th style="width: 20%;">Nama</th>
                    <th style="width: 20%;">HP</th>
                    <th style="width: 25%;">Alamat</th>
                    <th style="width: 10%;">Catatan</th>
                    <th style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_kontak as $kontak)
                <tr ondblclick="openEditModal({{ $kontak->id }})">
                    <td>{{ ucfirst($kontak->tipe) }}</td>
                    <td>{{ $kontak->nama }}</td>
                    <td>{{ $kontak->HP }}</td>
                    <td>{{ $kontak->alamat }}</td>
                    <td>{{ $kontak->catatan }}</td>
                    <td class="text-center">
                        <button class="btn btn-outline-primary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#editDataModal{{ $kontak->id }}" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDataModal{{ $kontak->id }}" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>

        <div id="grid-view" class="row" style="display: none;">
          <!-- Grid items will be dynamically updated -->
        </div>

        <script>
          let currentPage = 1;
          const itemsPerPage = 6;

          function renderGridView(data) {
            const gridView = document.getElementById('grid-view');
            gridView.innerHTML = ''; // Clear existing content

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedData = data.slice(startIndex, endIndex);

            if (paginatedData.length === 0) {
              gridView.innerHTML = '<p class="text-center">No data available in grid view.</p>';
              return;
            }

            paginatedData.forEach(kontak => {
              const card = `
                <div class="col-md-4 mb-4">
                  <div class="card border">
                    <div class="card-body">
                      <h5 class="card-title">${kontak.nama}</h5>
                      <p class="mb-1"><strong>Tipe:</strong> ${kontak.tipe}</p>
                      <p class="mb-1"><strong>HP:</strong> ${kontak.HP}</p>
                      <p class="mb-1"><strong>Alamat:</strong> ${kontak.alamat}</p>
                      <p class="mb-1"><strong>Catatan:</strong> ${kontak.catatan || 'Tidak ada catatan'}</p>
                      <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editDataModal${kontak.id}">
                          <i class="fa fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDataModal${kontak.id}">
                          <i class="fa fa-trash"></i> Delete
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              `;
              gridView.insertAdjacentHTML('beforeend', card);
            });

            updateGridPaginationControls(data.length);
          }

          function updateGridPaginationControls(totalItems) {
            const totalPages = Math.ceil(totalItems / itemsPerPage);
            document.getElementById('currentPage').textContent = `${currentPage}/${totalPages}`;

            document.getElementById('prevPageButton').disabled = currentPage === 1;
            document.getElementById('nextPageButton').disabled = currentPage === totalPages;
          }

          document.getElementById('prevPageButton').addEventListener('click', function () {
            if (currentPage > 1) {
              currentPage--;
              renderGridView(filteredGridData);
            }
          });

          document.getElementById('nextPageButton').addEventListener('click', function () {
            const totalPages = Math.ceil(filteredGridData.length / itemsPerPage);
            if (currentPage < totalPages) {
              currentPage++;
              renderGridView(filteredGridData);
            }
          });

          function showTab(view) {
            const tableButton = document.getElementById('tableViewButton');
            const gridButton = document.getElementById('gridViewButton');
            const tableView = document.getElementById('table-view');
            const gridView = document.getElementById('grid-view');

            if (view === 'table') {
              tableButton.classList.add('btn-primary');
              tableButton.classList.remove('btn-light');
              gridButton.classList.add('btn-light');
              gridButton.classList.remove('btn-primary');
              tableView.style.display = 'block'; // Show table view
              gridView.style.display = 'none'; // Hide grid view
            } else if (view === 'grid') {
              gridButton.classList.add('btn-primary');
              gridButton.classList.remove('btn-light');
              tableButton.classList.add('btn-light');
              tableButton.classList.remove('btn-primary');
              tableView.style.display = 'none'; // Hide table view
              gridView.style.display = 'flex'; // Show grid view
              renderGridView(filteredGridData); // Ensure grid view is rendered on first switch
            }
          }

          const gridData = @json($data_kontak);
          let filteredGridData = [...gridData]; // Initialize filtered data with all data
        </script>



      </div>
    </div>
  </div>
</div>


        <!-- Modal Tambah Data -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Kontak Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body fixed-modal-body">
                        <form id="addForm" action="#" method="POST">
                            @csrf
                            <div class="custom-tabs">
                                <ul class="nav nav-tabs" id="addContactTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="umum-tab" data-bs-toggle="tab" data-bs-target="#umum" type="button" role="tab">Umum</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="alamat-tab" data-bs-toggle="tab" data-bs-target="#alamat" type="button" role="tab">Alamat</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="penjualan-tab" data-bs-toggle="tab" data-bs-target="#penjualan" type="button" role="tab">Penjualan</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content mt-3">
                                <!-- Umum Tab -->
                                <div class="tab-pane fade show active" id="umum" role="tabpanel">
                                    <div class="row mb-3">
                                        <label for="addNama" class="col-sm-4 col-form-label text-end">Nama</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="addNama" name="nama" placeholder="Nama perusahaan atau individu" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="addTipe" class="col-sm-4 col-form-label text-end">Tipe</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" id="addTipe" name="tipe" required>
                                                <option value="" disabled selected>Pilih tipe</option>
                                                <option value="pelanggan">Pelanggan</option>
                                                <option value="supplier">Supplier</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="addKontakPerson" class="col-sm-4 col-form-label text-end">Kontak Person</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="addKontakPerson" name="kontak_person" placeholder="Nama kontak person" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="addEmail" class="col-sm-4 col-form-label text-end">Email</label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control" id="addEmail" name="email" placeholder="Email kontak" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="addTelepon" class="col-sm-4 col-form-label text-end">Telepon</label>
                                        <div class="col-sm-8">
                                            <input type="tel" class="form-control" id="addTelepon" name="telepon" placeholder="Nomor telepon" pattern="[0-9]+" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="addStatus" class="col-sm-4 col-form-label text-end">Status</label>
                                        <div class="col-sm-8">
                                            <select class="form-select" id="addStatus" name="status" required>
                                                <option value="" disabled selected>Pilih status</option>
                                                <option value="aktif">Aktif</option>
                                                <option value="nonaktif">Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- Alamat Tab -->
                                <div class="tab-pane fade" id="alamat" role="tabpanel">
                                    <div class="row mb-3">
                                        <label for="addAlamatLengkap" class="col-sm-2 col-form-label text-end">Alamat</label>
                                        <div class="col-sm-10">
                                            <textarea class="form-control" id="addAlamatLengkap" name="alamat_lengkap" placeholder="Masukkan alamat lengkap"></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="addKota" class="col-sm-2 col-form-label text-end">Kota</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="addKota" name="kota" placeholder="Jakarta, Bandung, dsb">
                                        </div>
                                        <label for="addProvinsi" class="col-sm-2 col-form-label text-end">Provinsi</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="addProvinsi" name="provinsi" placeholder="DKI Jakarta, Jawa Barat, dsb">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="addKodePos" class="col-sm-2 col-form-label text-end">Kode Pos</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="addKodePos" name="kode_pos" placeholder="12345">
                                        </div>
                                    </div>
                                </div>
                                <!-- Penjualan Tab -->
                                <div class="tab-pane fade" id="penjualan" role="tabpanel">
                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <label for="kategoriHarga" class="col-sm-4 col-form-label text-end">Kategori Harga <span class="text-danger">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="form-select form-select-sm" id="kategoriHarga" name="kategori_harga">
                                                        <option value="umum">Umum</option>
                                                        <option value="khusus">Khusus</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="kategoriDiskon" class="col-sm-4 col-form-label text-end">Kategori Diskon</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control" id="kategoriDiskon" name="kategori_diskon" placeholder="Cari/Pilih...">
                                                        <button class="btn btn-outline-secondary" type="button">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="syaratPembayaran" class="col-sm-4 col-form-label text-end">Syarat Pembayaran</label>
                                                <div class="col-sm-8">
                                                    <select class="form-select form-select-sm" id="syaratPembayaran" name="syarat_pembayaran">
                                                        <option value="cod">C.O.D</option>
                                                        <option value="net30">Net 30</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="defaultPenjual" class="col-sm-4 col-form-label text-end">Default Penjual</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control" id="defaultPenjual" name="default_penjual" placeholder="Cari/Pilih...">
                                                        <button class="btn btn-outline-secondary" type="button">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="defaultDiskon" class="col-sm-4 col-form-label text-end">Default Diskon (%)</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control form-control-sm" id="defaultDiskon" name="default_diskon" placeholder="%">
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="defaultDeskripsi" class="col-sm-4 col-form-label text-end">Default Deskripsi</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control form-control-sm" id="defaultDeskripsi" name="default_deskripsi" rows="2"></textarea>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Akun Penjualan</h6>
                                            <div class="row mb-2">
                                                <label for="akunPiutang" class="col-sm-4 col-form-label text-end">Akun Piutang</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control" id="akunPiutang" name="akun_piutang" placeholder="Cari/Pilih...">
                                                        <button class="btn btn-outline-secondary" type="button">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="akunUangMuka" class="col-sm-4 col-form-label text-end">Akun Uang Muka</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control" id="akunUangMuka" name="akun_uang_muka" placeholder="Cari/Pilih...">
                                                        <button class="btn btn-outline-secondary" type="button">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4"></div>
                                                <div class="col-sm-8">
                                                    <small class="text-danger">
                                                        [Opsional] Diisikan jika anda ingin membedakan jurnal akun piutang/uang muka pelanggan ini dengan default akun piutang/uang muka yg ada pada Mata Uang. Untuk dapat terpilih ke dalam transaksi, pastikan pengguna memiliki hak akses ke akun-akun ini.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer-container">
                        <button type="submit" class="btn btn-primary">Tambah Kontak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush
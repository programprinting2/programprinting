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
    <li class="breadcrumb-item active" aria-current="page">Master Parameter</li>
  </ol>
</nav>

<div class="row">
  <!-- Left Column: Category List -->
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="card-title">Kategori Parameter</h6>
          <button class="btn btn-dark btn-sm d-flex align-items-center" type="button" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa fa-plus"></i>
          </button>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <!-- Search and Filter Section -->
          <div class="d-flex align-items-center">
            <div class="input-group" style="width: 250px; margin-right: 10px;">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" class="form-control form-control-sm" id="searchCategoryForm" placeholder="Cari..." onkeyup="filterCategoryContent()">
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearCategorySearchForm()" title="Clear">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <div class="input-group" style="width: 200px;">
              <span class="input-group-text">
                <i class="fas fa-filter"></i>
              </span>
              <select class="form-select form-select-sm" aria-label="Filter Status">
                <option selected>Semua Data</option>
                <option value="aktif">Aktif</option>
                <option value="tidak_aktif">Tidak Aktif</option>
              </select>
            </div>
          </div>
        </div>
        <hr>
        <div class="table-responsive">
          <table id="category-table" class="table">
            <thead>
              <tr>
                <th style="width: 70%;">Kategori</th>
                <th style="width: 30%;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Satuan Ukuran</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>Kategori Bahan</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>print_type</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>finishing_option</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>paper_dimension</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>customer_category</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>Jenis Kertas</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>Jenis Finishing</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>work_status</td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                  <button class="btn btn-outline-success btn-xs" title="Tambah">
                    <i class="fa fa-plus"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <ul class="pagination pagination-rounded d-flex align-items-center justify-content-end mb-0">
          <li class="page-item">
            <button class="page-link btn-sm text-secondary" id="prevCategoryPageButton" aria-label="Previous">
              <i class="fas fa-chevron-left"></i>
            </button>
          </li>
          <li class="page-item">
            <span class="page-link text-secondary btn-sm" id="currentCategoryPage">1/1</span>
          </li>
          <li class="page-item">
            <button class="page-link btn-sm text-secondary" id="nextCategoryPageButton" aria-label="Next">
              <i class="fas fa-chevron-right"></i>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Right Column: Details -->
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="card-title">Detail Parameter</h6>
          <button class="btn btn-dark btn-sm d-flex align-items-center" type="button" data-bs-toggle="modal" data-bs-target="#addDetailModal">
            <i class="fa fa-plus"></i>
          </button>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <!-- Search and Filter Section -->
          <div class="d-flex align-items-center">
            <div class="input-group" style="width: 250px; margin-right: 10px;">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input type="text" class="form-control form-control-sm" id="searchDetailForm" placeholder="Cari..." onkeyup="filterDetailContent()">
              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearDetailSearchForm()" title="Clear">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <div class="input-group" style="width: 200px;">
              <span class="input-group-text">
                <i class="fas fa-filter"></i>
              </span>
              <select class="form-select form-select-sm" aria-label="Filter Status">
                <option selected>Semua Data</option>
                <option value="aktif">Aktif</option>
                <option value="tidak_aktif">Tidak Aktif</option>
              </select>
            </div>
          </div>
        </div>
        <hr>
        <div class="table-responsive">
          <table id="data-source-1" class="table">
            <thead>
              <tr>
                <th style="width: 10%;">Simbol</th>
                <th style="width: 20%;">Nama</th>
                <th style="width: 40%;">Deskripsi</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 20%;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- Dynamic rows based on selected category --}}
              <tr>
                <td>kg</td>
                <td>Kilogram</td>
                <td>Satuan berat dalam kilogram</td>
                <td><span class="badge bg-primary">Aktif</span></td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>m2</td>
                <td>Meter Persegi</td>
                <td>Satuan luas dalam meter persegi</td>
                <td><span class="badge bg-primary">Aktif</span></td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>m3</td>
                <td>Meter Kubik</td>
                <td>Satuan volume dalam meter kubik</td>
                <td><span class="badge bg-primary">Aktif</span></td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>liter</td>
                <td>Liter</td>
                <td>Satuan volume dalam liter</td>
                <td><span class="badge bg-primary">Aktif</span></td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>box</td>
                <td>Box</td>
                <td>Satuan dalam kotak</td>
                <td><span class="badge bg-primary">Aktif</span></td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>pcs</td>
                <td>Pieces</td>
                <td>Satuan dalam buah/pcs</td>
                <td><span class="badge bg-primary">Aktif</span></td>
                <td>
                  <button class="btn btn-outline-primary btn-xs" title="Edit">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-xs" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <ul class="pagination pagination-rounded d-flex align-items-center justify-content-end mb-0">
          <li class="page-item">
            <button class="page-link btn-sm text-secondary" id="prevDetailPageButton" aria-label="Previous">
              <i class="fas fa-chevron-left"></i>
            </button>
          </li>
          <li class="page-item">
            <span class="page-link text-secondary btn-sm" id="currentDetailPage">1/1</span>
          </li>
          <li class="page-item">
            <button class="page-link btn-sm text-secondary" id="nextDetailPageButton" aria-label="Next">
              <i class="fas fa-chevron-right"></i>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Modals -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCategoryModalLabel">Tambah Kategori Parameter Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row mb-3">
            <label for="categoryName" class="col-sm-4 col-form-label text-end">Nama Kategori</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="categoryName" placeholder="Nama kategori parameter" required>
            </div>
          </div>
          <div class="row mb-3">
            <label for="categoryDescription" class="col-sm-4 col-form-label text-end">Deskripsi</label>
            <div class="col-sm-8">
              <textarea class="form-control" id="categoryDescription" placeholder="Deskripsi kategori parameter (opsional)" rows="3"></textarea>
            </div>
          </div>
          <div class="row mb-3">
            <label for="categoryStatus" class="col-sm-4 col-form-label text-end">Status Aktif</label>
            <div class="col-sm-8 d-flex align-items-center">
              <input class="form-check-input" type="checkbox" id="categoryStatus" checked>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addDetailModal" tabindex="-1" aria-labelledby="addDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDetailModalLabel">Tambah Parameter Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row mb-3">
            <label for="categoryParameter" class="col-sm-4 col-form-label text-end">Kategori Parameter</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="categoryParameter" value="Satuan Ukuran" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <label for="parameterName" class="col-sm-4 col-form-label text-end">Nama</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="parameterName" placeholder="Nama parameter" required>
            </div>
          </div>
          <div class="row mb-3">
            <label for="parameterSymbol" class="col-sm-4 col-form-label text-end">Simbol</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="parameterSymbol" placeholder="Masukkan simbol satuan (misal: kg, m, cm)" required>
            </div>
          </div>
          <div class="row mb-3">
            <label for="parameterDescription" class="col-sm-4 col-form-label text-end">Deskripsi</label>
            <div class="col-sm-8">
              <textarea class="form-control" id="parameterDescription" placeholder="Deskripsi parameter (opsional)" rows="3"></textarea>
            </div>
          </div>
          <div class="row mb-3">
            <label for="parameterStatus" class="col-sm-4 col-form-label text-end">Status Aktif</label>
            <div class="col-sm-8 d-flex align-items-center">
              <input class="form-check-input" type="checkbox" id="parameterStatus" checked>
            </div>
          </div>
          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script>
    $(document).ready(function() {
      $('#category-table').DataTable({
        searching: false,
        paging: false,
        info: false
      });

      $('#data-source-1').DataTable({
        searching: false,
        paging: false,
        info: false
      });

      // Add custom pagination logic here if needed
    });

    function selectCategory(category) {
      console.log('Selected category:', category);
      // Add logic to filter the right table based on the selected category
    }
  </script>
@endpush
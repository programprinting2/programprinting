@extends('layout.master')

@push('plugin-styles')
  <!-- <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" /> -->
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
    <li class="breadcrumb-item"><a href="#">Master</a></li>
    <li class="breadcrumb-item active" aria-current="page">Kontak</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title">Data Master Kontak</h6>
            <button class="btn btn-dark d-flex align-items-center gap-1" type="button" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa fa-plus"></i> Tambah Kontak
            </button>
        </div>
        <p class="text-muted mb-3">Kelola data kontak seperti staff, customer, atau supplier.</p>
        <form class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
                <input type="text" class="form-control" id="searchKontak" placeholder="Cari kontak..." onkeyup="filterKontakTable()">
                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('searchKontak').value='';filterKontakTable();"><i class="fas fa-times"></i></button>
            </div>
        </form>
        
        <div class="table-responsive">
          <table id="dataTable" class="table">
            <thead>
                <tr>
                    <th>Tipe</th>
                    <th>Nama</th>
                    <th>HP</th>
                    <th>Alamat</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_kontak as $kontak)
                <tr>
                    <td>{{ ucfirst($kontak->tipe) }}</td>
                    <td>{{ $kontak->nama }}</td>
                    <td>{{ $kontak->HP }}</td>
                    <td>{{ $kontak->alamat }}</td>
                    <td>{{ $kontak->catatan }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $kontak->id }}" data-bs-toggle="modal" data-bs-target="#editModal{{ $kontak->id }}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $kontak->id }}" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $kontak->id }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal{{ $kontak->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Kontak</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('backend.master-kontak.update', $kontak->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Tipe</label>
                                        <select class="form-select" name="tipe" required>
                                            <option value="staff" {{ $kontak->tipe == 'staff' ? 'selected' : '' }}>Staff</option>
                                            <option value="customer" {{ $kontak->tipe == 'customer' ? 'selected' : '' }}>Customer</option>
                                            <option value="supplier" {{ $kontak->tipe == 'supplier' ? 'selected' : '' }}>Supplier</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nama</label>
                                        <input type="text" class="form-control" name="nama" value="{{ $kontak->nama }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">HP</label>
                                        <input type="text" class="form-control" name="HP" value="{{ $kontak->HP }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" name="alamat" rows="3">{{ $kontak->alamat }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Catatan</label>
                                        <textarea class="form-control" name="catatan" rows="3">{{ $kontak->catatan }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Delete -->
                <div class="modal fade" id="deleteModal{{ $kontak->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Hapus Kontak</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah Anda yakin ingin menghapus kontak "{{ $kontak->nama }}"?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('backend.master-kontak.delete', $kontak->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{ $data_kontak->links('pagination::bootstrap-5') }}

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kontak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('backend.master-kontak.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select class="form-select" name="tipe" required>
                            <option value="">Pilih Tipe</option>
                            <option value="staff">Staff</option>
                            <option value="customer">Customer</option>
                            <option value="supplier">Supplier</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">HP</label>
                        <input type="text" class="form-control" name="HP">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
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

@push('custom-scripts')
<script>
function filterKontakTable() {
    var input = document.getElementById('searchKontak');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('dataTable');
    var trs = table.getElementsByTagName('tr');
    for (var i = 1; i < trs.length; i++) { // Mulai dari 1 agar skip thead
        var tds = trs[i].getElementsByTagName('td');
        var found = false;
        for (var j = 0; j < tds.length; j++) {
            if (tds[j] && tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        trs[i].style.display = found ? '' : 'none';
    }
}
</script>
@endpush
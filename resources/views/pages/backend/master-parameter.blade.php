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
  
  /* Tambahan style untuk list kategori */
  #kategoriList .list-group-item {
    border: none;
    border-bottom: 1px solid #eee;
    padding: 12px 15px;
    transition: all 0.2s ease;
    cursor: pointer;
  }
  
  #kategoriList .list-group-item:hover {
    background-color: #f8f9fa;
  }
  
  #kategoriList .list-group-item.active {
    background-color: #e9ecef;
    border-left: 4px solid #0d6efd;
    color: #0d6efd;
    font-weight: 500;
  }
  
  #kategoriList .list-group-item .btn-group {
    opacity: 0;
    transition: opacity 0.2s ease;
  }
  
  #kategoriList .list-group-item:hover .btn-group {
    opacity: 1;
  }
  
  #kategoriList .btn-xs {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
  }
</style>

<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Master</a></li>
    <li class="breadcrumb-item active" aria-current="page">Parameter</li>
  </ol>
</nav>

<div class="container-fluid">
  <div class="row">
    <!-- Kategori Parameter -->
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title">Kategori Parameter</h6>
            <button class="btn btn-dark btn-sm" id="btnAddKategori"><i class="fa fa-plus"></i></button>
          </div>
          <input type="text" class="form-control mb-2" id="searchKategori" placeholder="Cari kategori...">
          <ul class="list-group" id="kategoriList">
            <!-- Kategori akan diisi via JS -->
          </ul>
        </div>
      </div>
    </div>
    <!-- Detail Parameter -->
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title" id="detailTitle">Detail Parameter</h6>
            <button class="btn btn-dark btn-sm" id="btnAddDetail" disabled><i class="fa fa-plus"></i></button>
          </div>
          <input type="text" class="form-control mb-2" id="searchDetail" placeholder="Cari detail...">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Simbol</th>
                  <th>Nama</th>
                  <th>Deskripsi</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="detailList">
                <tr><td colspan="5" class="text-center text-muted">Pilih kategori parameter</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Kategori -->
<div class="modal fade" id="modalKategori" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalKategoriTitle">Tambah Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formKategori">
          <input type="hidden" id="kategoriId">
          <div class="mb-3">
            <label>Nama Kategori</label>
            <input type="text" class="form-control" id="kategoriNama" required>
          </div>
          <div class="mb-3">
            <label>Deskripsi</label>
            <input type="text" class="form-control" id="kategoriKeterangan">
          </div>
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="kategoriAktif" checked>
            <label class="form-check-label" for="kategoriAktif">Aktif</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnSaveKategori">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetailTitle">Tambah Detail Parameter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formDetail">
          <input type="hidden" id="detailId">
          <div class="mb-3">
            <label>Nama</label>
            <input type="text" class="form-control" id="detailNama" required>
          </div>
          <div class="mb-3">
            <label>Simbol</label>
            <input type="text" class="form-control" id="detailIsi">
          </div>
          <div class="mb-3">
            <label>Deskripsi</label>
            <input type="text" class="form-control" id="detailKeterangan">
          </div>
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="detailAktif" checked>
            <label class="form-check-label" for="detailAktif">Aktif</label>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnSaveDetail">Simpan</button>
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

@push('custom-scripts')
<script>
let kategoriAktif = null;

function loadKategori() {
  fetch('/backend/master-parameter')
    .then(res => res.text())
    .then(html => {
      let data = @json($data_parameter);
      let list = '';
      data.forEach(kat => {
        list += `<li class="list-group-item d-flex justify-content-between align-items-center ${kategoriAktif==kat.id?'active':''}" data-id="${kat.id}">
          <span>${kat.nama_parameter}</span>
          <div class="btn-group">
            <button class="btn btn-xs btn-outline-primary btn-edit-kat"><i class="fa fa-edit"></i></button>
            <button class="btn btn-xs btn-outline-danger btn-del-kat"><i class="fa fa-trash"></i></button>
          </div>
        </li>`;
      });
      document.getElementById('kategoriList').innerHTML = list;
    });
}

function loadDetail(id) {
  fetch(`/backend/master-parameter/${id}/detail`)
    .then(res => res.json())
    .then(data => {
      let list = '';
      if(data.length==0) list = `<tr><td colspan="5" class="text-center text-muted">Belum ada detail</td></tr>`;
      data.forEach(det => {
        list += `<tr data-id="${det.id}">
          <td>${det.isi_parameter||''}</td>
          <td>${det.nama_detail_parameter}</td>
          <td>${det.keterangan||''}</td>
          <td><span class="badge bg-${det.aktif?'primary':'secondary'}">${det.aktif?'Aktif':'Nonaktif'}</span></td>
          <td>
            <button class="btn btn-xs btn-outline-primary btn-edit-detail"><i class="fa fa-edit"></i></button>
            <button class="btn btn-xs btn-outline-danger btn-del-detail"><i class="fa fa-trash"></i></button>
          </td>
        </tr>`;
      });
      document.getElementById('detailList').innerHTML = list;
    });
}

// Kategori event
$(document).on('click', '#btnAddKategori', function() {
  $('#modalKategoriTitle').text('Tambah Kategori');
  $('#formKategori')[0].reset();
  $('#kategoriId').val('');
  $('#modalKategori').modal('show');
});
$(document).on('click', '.btn-edit-kat', function(e) {
  e.stopPropagation();
  let id = $(this).closest('li').data('id');
  let kat = @json($data_parameter).find(k=>k.id==id);
  $('#modalKategoriTitle').text('Edit Kategori');
  $('#kategoriId').val(kat.id);
  $('#kategoriNama').val(kat.nama_parameter);
  $('#kategoriKeterangan').val(kat.keterangan);
  $('#kategoriAktif').prop('checked', kat.aktif);
  $('#modalKategori').modal('show');
});
$(document).on('click', '.btn-del-kat', function(e) {
  e.stopPropagation();
  if(!confirm('Hapus kategori ini?')) return;
  let id = $(this).closest('li').data('id');
  $.ajax({url:`/backend/master-parameter/${id}`,type:'DELETE',data:{_token:'{{csrf_token()}}'},success:()=>{loadKategori();if(kategoriAktif==id){kategoriAktif=null;$('#detailList').html('<tr><td colspan="5" class="text-center text-muted">Pilih kategori parameter</td></tr>');}}});
});
$(document).on('click', '#btnSaveKategori', function() {
  let id = $('#kategoriId').val();
  let data = {
    nama_parameter: $('#kategoriNama').val(),
    keterangan: $('#kategoriKeterangan').val(),
    aktif: $('#kategoriAktif').is(':checked')?1:0,
    _token: '{{csrf_token()}}'
  };
  if(!data.nama_parameter) return alert('Nama kategori wajib diisi!');
  if(id) {
    $.ajax({url:`/backend/master-parameter/${id}`,type:'PUT',data,success:()=>{loadKategori();$('#modalKategori').modal('hide');}});
  } else {
    $.post('/backend/master-parameter',data,()=>{loadKategori();$('#modalKategori').modal('hide');});
  }
});
$(document).on('click', '#kategoriList li', function() {
  $('#kategoriList li').removeClass('active');
  $(this).addClass('active');
  kategoriAktif = $(this).data('id');
  $('#btnAddDetail').prop('disabled',false);
  loadDetail(kategoriAktif);
});

// Detail event
$(document).on('click', '#btnAddDetail', function() {
  $('#modalDetailTitle').text('Tambah Detail Parameter');
  $('#formDetail')[0].reset();
  $('#detailId').val('');
  $('#modalDetail').modal('show');
});
$(document).on('click', '.btn-edit-detail', function() {
  let id = $(this).closest('tr').data('id');
  $.get(`/backend/master-parameter/${kategoriAktif}/detail`, function(data) {
    let det = data.find(d=>d.id==id);
    $('#modalDetailTitle').text('Edit Detail Parameter');
    $('#detailId').val(det.id);
    $('#detailNama').val(det.nama_detail_parameter);
    $('#detailIsi').val(det.isi_parameter);
    $('#detailKeterangan').val(det.keterangan);
    $('#detailAktif').prop('checked', det.aktif);
    $('#modalDetail').modal('show');
  });
});
$(document).on('click', '.btn-del-detail', function() {
  if(!confirm('Hapus detail ini?')) return;
  let id = $(this).closest('tr').data('id');
  $.ajax({url:`/backend/master-parameter/${kategoriAktif}/detail/${id}`,type:'DELETE',data:{_token:'{{csrf_token()}}'},success:()=>loadDetail(kategoriAktif)});
});
$(document).on('click', '#btnSaveDetail', function() {
  let id = $('#detailId').val();
  let data = {
    nama_detail_parameter: $('#detailNama').val(),
    isi_parameter: $('#detailIsi').val(),
    keterangan: $('#detailKeterangan').val(),
    aktif: $('#detailAktif').is(':checked')?1:0,
    _token: '{{csrf_token()}}'
  };
  if(!data.nama_detail_parameter) return alert('Nama detail wajib diisi!');
  if(id) {
    $.ajax({url:`/backend/master-parameter/${kategoriAktif}/detail/${id}`,type:'PUT',data,success:()=>{loadDetail(kategoriAktif);$('#modalDetail').modal('hide');}});
  } else {
    $.post(`/backend/master-parameter/${kategoriAktif}/detail`,data,()=>{loadDetail(kategoriAktif);$('#modalDetail').modal('hide');});
  }
});

// Initial load
$(function(){
  loadKategori();
});
</script>
@endpush
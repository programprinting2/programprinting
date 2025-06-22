@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')

  <style>
    .custom-tabs .nav-tabs {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
    /* Light background for tabs */
    border-radius: 0.25rem;
    /* Rounded corners */
    }

    .custom-tabs .nav-link {
    border: none;
    color: #6c757d;
    /* Default text color */
    padding: 10px 15px;
    transition: color 0.2s, background-color 0.2s;
    }

    .custom-tabs .nav-link.active {
    background-color: #fff;
    /* White background for active tab */
    color: #000;
    /* Black text for active tab */
    font-weight: bold;
    border: 1px solid #e9ecef;
    border-bottom: none;
    /* Remove bottom border for active tab */
    border-radius: 0.25rem 0.25rem 0 0;
    /* Rounded top corners */
    }

    .fixed-modal-body {
    height: 450px;
    /* Adjust height to fit content */
    overflow-y: auto;
    /* Enable scrolling if content overflows */
    padding-left: 20px;
    /* Adjust padding to move content to the left */
    padding-right: 20px;
    /* Optional: Add right padding for symmetry */
    }

    .tab-content>.tab-pane {
    height: 100%;
    /* Ensure consistent height for tab content */
    }

    .modal-footer-container {
    position: sticky;
    /* Make the footer container fixed */
    bottom: 0;
    /* Stick to the bottom of the modal */
    background-color: #fff;
    /* Match the modal background */
    z-index: 10;
    /* Ensure it stays above the content */
    padding: 15px;
    /* Add padding for spacing */
    border-top: 1px solid #ddd;
    /* Add a border for separation */
    display: flex;
    /* Align buttons horizontally */
    justify-content: flex-end;
    /* Align buttons to the right */
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

    .loading-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(255, 255, 255, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .loading-spinner {
      width: 3rem;
      height: 3rem;
    }

    .table-container {
      position: relative;
      min-height: 200px;
    }

    .btn:disabled {
      cursor: not-allowed;
      opacity: 0.7;
    }

    .btn-loading {
      position: relative;
      color: transparent !important;
      pointer-events: none;
    }

    .btn-loading:after {
      content: "";
      position: absolute;
      width: 1rem;
      height: 1rem;
      top: 50%;
      left: 50%;
      margin: -0.5rem 0 0 -0.5rem;
      border: 2px solid #fff;
      border-right-color: transparent;
      border-radius: 50%;
      animation: button-loading-spinner 0.75s linear infinite;
    }

    @keyframes button-loading-spinner {
      from {
        transform: rotate(0turn);
      }
      to {
        transform: rotate(1turn);
      }
    }

    .btn-loading.btn-outline-primary:after {
      border-color: #0d6efd;
      border-right-color: transparent;
    }

    .btn-loading.btn-outline-danger:after {
      border-color: #dc3545;
      border-right-color: transparent;
    }

    .btn-loading.btn-dark:after {
      border-color: #fff;
      border-right-color: transparent;
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
        </div>
        <input type="text" class="form-control mb-2" id="searchDetail" placeholder="Cari detail...">
        <div class="table-responsive table-container">
        <table class="table table-bordered">
          <thead>
          <tr>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
          </thead>
          <tbody id="detailList">
          <tr>
            <td colspan="5" class="text-center text-muted">Pilih kategori parameter</td>
          </tr>
          </tbody>
        </table>
        <div id="loadingDetail" class="loading-overlay" style="display: none;">
          <div class="spinner-border text-primary loading-spinner" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
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
    <div class="modal-dialog modal-lg">
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
        <label>Deskripsi</label>
        <input type="text" class="form-control" id="detailKeterangan">
        </div>
        <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="detailAktif" checked>
        <label class="form-check-label" for="detailAktif">Aktif</label>
        </div>
        
        <!-- Section Sub Detail Parameter -->
        <hr>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6>Sub Detail Parameter</h6>
          <button type="button" class="btn btn-dark btn-sm" id="btnAddSubDetail"><i class="fa fa-plus"></i> Tambah Baris</button>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="subDetailList">
              <tr>
                <td colspan="4" class="text-center text-muted">Belum ada sub detail parameter</td>
              </tr>
            </tbody>
          </table>
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
@endpush

@push('custom-scripts')
  <script>
    let kategoriAktif = null;
    let namaKategoriAktif = '';
    let detailAktif = null;
    let isSubmitting = false;

    // Data lokal untuk sub detail
    let subDetailData = [];

    function showLoading() {
      $('#loadingDetail').show();
    }

    function hideLoading() {
      $('#loadingDetail').hide();
    }

    function setLoadingState(isLoading) {
      isSubmitting = isLoading;
      
      // Set loading state untuk button kategori
      $('#btnAddKategori').prop('disabled', isLoading);
      if (isLoading) {
        $('#btnAddKategori').addClass('btn-loading');
      } else {
        $('#btnAddKategori').removeClass('btn-loading');
      }
      
      // Set loading state untuk button detail
      $('#btnAddDetail').prop('disabled', isLoading);
      if (isLoading) {
        $('#btnAddDetail').addClass('btn-loading');
      } else {
        $('#btnAddDetail').removeClass('btn-loading');
      }
      
      // Set loading state untuk button sub detail
      $('#btnAddSubDetail').prop('disabled', isLoading);
      if (isLoading) {
        $('#btnAddSubDetail').addClass('btn-loading');
      } else {
        $('#btnAddSubDetail').removeClass('btn-loading');
      }
      
      // Set loading state untuk button edit dan delete
      $('.btn-edit-kat, .btn-del-kat, .btn-edit-detail, .btn-del-detail, .btn-edit-sub-detail, .btn-del-sub-detail').prop('disabled', isLoading);
      if (isLoading) {
        $('.btn-edit-kat, .btn-edit-detail, .btn-edit-sub-detail').addClass('btn-loading');
        $('.btn-del-kat, .btn-del-detail, .btn-del-sub-detail').addClass('btn-loading');
      } else {
        $('.btn-edit-kat, .btn-edit-detail, .btn-edit-sub-detail').removeClass('btn-loading');
        $('.btn-del-kat, .btn-del-detail, .btn-del-sub-detail').removeClass('btn-loading');
      }
      
      // Set loading state untuk button simpan di modal
      $('#btnSaveKategori, #btnSaveDetail').prop('disabled', isLoading);
      if (isLoading) {
        $('#btnSaveKategori, #btnSaveDetail').addClass('btn-loading');
      } else {
        $('#btnSaveKategori, #btnSaveDetail').removeClass('btn-loading');
      }
    }

    function loadKategori() {
      fetch('/backend/master-parameter')
        .then(res => res.text())
        .then(html => {
          let data = @json($data_parameter);
          renderKategoriList(data);
        });
    }

    function renderKategoriList(data) {
      let keyword = $('#searchKategori').val()?.toLowerCase() || '';
      let list = '';
      data.filter(kat => {
        return kat.nama_parameter.toLowerCase().includes(keyword) || (kat.keterangan || '').toLowerCase().includes(keyword);
      }).forEach(kat => {
        list += `<li class="list-group-item d-flex justify-content-between align-items-center ${kategoriAktif == kat.id ? 'active' : ''}" data-id="${kat.id}">
          <div class="d-flex flex-column">
            <span>${kat.nama_parameter}</span>
            <span class="text-muted">${kat.keterangan || ''}</span>
          </div>
          <div class="btn-group">
            <button class="btn btn-xs btn-outline-primary btn-edit-kat" ${isSubmitting ? 'disabled' : ''}><i class="fa fa-edit"></i></button>
            <button class="btn btn-xs btn-outline-danger btn-del-kat" ${isSubmitting ? 'disabled' : ''}><i class="fa fa-trash"></i></button>
            <button class="btn btn-xs btn-dark btn-add-detail" title="Tambah Detail Parameter" ${isSubmitting ? 'disabled' : ''}><i class="fa fa-plus"></i></button>
          </div>
        </li>`;
      });
      document.getElementById('kategoriList').innerHTML = list;
    }

    // Search kategori event
    $(document).on('input', '#searchKategori', function() {
      let data = @json($data_parameter);
      renderKategoriList(data);
    });

    function loadDetail(id) {
      showLoading();
      fetch(`/backend/master-parameter/${id}/detail`)
        .then(res => res.json())
        .then(data => {
          window._detailData = data; // simpan data detail untuk pencarian
          renderDetailList(data);
          hideLoading();
        })
        .catch(error => {
          console.error('Error:', error);
          hideLoading();
          Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat memuat data',
            icon: 'error'
          });
        });
    }

    function renderDetailList(data) {
      let keyword = $('#searchDetail').val()?.toLowerCase() || '';
      let list = '';
      let filtered = data.filter(det => {
        return det.nama_detail_parameter.toLowerCase().includes(keyword) || (det.keterangan || '').toLowerCase().includes(keyword);
      });
      if (filtered.length == 0) {
        list = `<tr><td colspan="5" class="text-center text-muted">Tidak ada data detail ditemukan</td></tr>`;
      } else {
        filtered.forEach(det => {
          list += `<tr data-id="${det.id}">
            <td>${det.nama_detail_parameter}</td>
            <td>${det.keterangan || ''}</td>
            <td><span class="badge bg-${det.aktif ? 'primary' : 'secondary'}">${det.aktif ? 'Aktif' : 'Nonaktif'}</span></td>
            <td>
              <button class="btn btn-xs btn-outline-primary btn-edit-detail" ${isSubmitting ? 'disabled' : ''}><i class="fa fa-edit"></i></button>
              <button class="btn btn-xs btn-outline-danger btn-del-detail" ${isSubmitting ? 'disabled' : ''}><i class="fa fa-trash"></i></button>
            </td>
          </tr>`;
        });
      }
      document.getElementById('detailList').innerHTML = list;
    }

    // Search detail event
    $(document).on('input', '#searchDetail', function() {
      if (window._detailData) {
        renderDetailList(window._detailData);
      }
    });

    // Render sub detail ke tabel
    function renderSubDetailTable() {
      let list = '';
      if (subDetailData.length === 0) {
        list = '<tr><td colspan="4" class="text-center text-muted">Belum ada sub detail parameter</td></tr>';
      } else {
        subDetailData.forEach((sub, idx) => {
          list += `<tr data-idx="${idx}">
            <td><input type="text" class="form-control form-control-sm sub-nama" value="${sub.nama_sub_detail_parameter || ''}" placeholder="Nama sub detail" /></td>
            <td><input type="text" class="form-control form-control-sm sub-keterangan" value="${sub.keterangan || ''}" placeholder="Keterangan" /></td>
            <td class="text-center">
              <input type="checkbox" class="form-check-input sub-aktif" ${sub.aktif ? 'checked' : ''} />
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-xs btn-outline-danger btn-del-sub-detail"><i class="fa fa-trash"></i></button>
            </td>
          </tr>`;
        });
      }
      $('#subDetailList').html(list);
    }

    // Tambah baris sub detail
    $(document).on('click', '#btnAddSubDetail', function() {
      subDetailData.push({ nama_sub_detail_parameter: '', keterangan: '', aktif: true });
      renderSubDetailTable();
    });

    // Hapus baris sub detail
    $(document).on('click', '.btn-del-sub-detail', function() {
      let idx = $(this).closest('tr').data('idx');
      subDetailData.splice(idx, 1);
      renderSubDetailTable();
    });

    // Edit inline sub detail (nama/keterangan/aktif)
    $(document).on('input', '.sub-nama', function() {
      let idx = $(this).closest('tr').data('idx');
      subDetailData[idx].nama_sub_detail_parameter = $(this).val();
    });
    $(document).on('input', '.sub-keterangan', function() {
      let idx = $(this).closest('tr').data('idx');
      subDetailData[idx].keterangan = $(this).val();
    });
    $(document).on('change', '.sub-aktif', function() {
      let idx = $(this).closest('tr').data('idx');
      subDetailData[idx].aktif = $(this).is(':checked');
    });

    // Saat modal detail dibuka, load sub detail ke data lokal
    function loadSubDetail(detailId) {
      if (!detailId) {
        subDetailData = [];
        renderSubDetailTable();
        return;
      }
      fetch(`/backend/master-parameter/${kategoriAktif}/detail/${detailId}/sub-detail`)
        .then(res => res.json())
        .then(data => {
          subDetailData = data.map(sub => ({
            id: sub.id,
            nama_sub_detail_parameter: sub.nama_sub_detail_parameter,
            keterangan: sub.keterangan,
            aktif: sub.aktif
          }));
          renderSubDetailTable();
        })
        .catch(error => {
          subDetailData = [];
          renderSubDetailTable();
        });
    }

    // Kategori event
    $(document).on('click', '#btnAddKategori', function () {
    $('#modalKategoriTitle').text('Tambah Kategori');
    $('#formKategori')[0].reset();
    $('#kategoriId').val('');
    $('#modalKategori').modal('show');
    });
    $(document).on('click', '.btn-edit-kat', function (e) {
    e.stopPropagation();
    let id = $(this).closest('li').data('id');
    let kat = @json($data_parameter).find(k => k.id == id);
    $('#modalKategoriTitle').text('Edit Kategori');
    $('#kategoriId').val(kat.id);
    $('#kategoriNama').val(kat.nama_parameter);
    $('#kategoriKeterangan').val(kat.keterangan);
    $('#kategoriAktif').prop('checked', kat.aktif);
    $('#modalKategori').modal('show');
    });
    $(document).on('click', '.btn-del-kat', function (e) {
    e.stopPropagation();
    let id = $(this).closest('li').data('id');
    let nama = $(this).closest('li').find('span').first().text();
    
    confirmDelete(
      'Hapus Kategori?',
      `Apakah Anda yakin ingin menghapus kategori "${nama}"?`,
      function() {
        setLoadingState(true);
        $.ajax({ 
          url: `/backend/master-parameter/${id}`, 
          type: 'DELETE', 
          data: { _token: '{{csrf_token()}}' }, 
          success: () => { 
            setLoadingState(false);
            Swal.fire({
              title: 'Berhasil!',
              text: 'Kategori berhasil dihapus',
              icon: 'success',
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              window.location.href = '{{ route("backend.master-parameter") }}';
            });
          },
          error: function(xhr) {
            setLoadingState(false);
            Swal.fire({
              title: 'Error!',
              text: 'Terjadi kesalahan saat menghapus kategori',
              icon: 'error'
            });
          }
        });
      }
    );
    });
    $(document).on('click', '#btnSaveKategori', function () {
    if (isSubmitting) return;
    
    let id = $('#kategoriId').val();
    let data = {
      nama_parameter: $('#kategoriNama').val(),
      keterangan: $('#kategoriKeterangan').val(),
      aktif: $('#kategoriAktif').is(':checked') ? 1 : 0,
      _token: '{{csrf_token()}}'
    };

    if (!data.nama_parameter) return alert('Nama kategori wajib diisi!');
    
    setLoadingState(true);
    
    if (id) {
      $.ajax({ 
        url: `/backend/master-parameter/${id}`, 
        type: 'PUT', 
        data, 
        success: () => { 
          // Redirect ke halaman yang sama untuk refresh data
          window.location.href = '{{ route("backend.master-parameter") }}';
        },
        error: function(xhr) {
          alert('Terjadi kesalahan: ' + xhr.responseText);
          setLoadingState(false);
        }
      });
    } else {
      $.ajax({
        url: '/backend/master-parameter',
        type: 'POST',
        data,
        success: () => { 
          // Redirect ke halaman yang sama untuk refresh data
          window.location.href = '{{ route("backend.master-parameter") }}';
        },
        error: function(xhr) {
          alert('Terjadi kesalahan: ' + xhr.responseText);
          setLoadingState(false);
        }
      });
    }
    });
    $(document).on('click', '#kategoriList li', function () {
    $('#kategoriList li').removeClass('active');
    $(this).addClass('active');
    kategoriAktif = $(this).data('id');
    namaKategoriAktif = $(this).find('span').first().text();
    $('#btnAddDetail').prop('disabled', false);
    loadDetail(kategoriAktif);
    });

    // Event baru untuk tombol tambah detail di kategori
    $(document).on('click', '.btn-add-detail', function (e) {
      e.stopPropagation();
      let li = $(this).closest('li');
      kategoriAktif = li.data('id');
      namaKategoriAktif = li.find('span').first().text();
      detailAktif = null; // Reset detail aktif
      let title = 'Tambah Detail Parameter';
      if (namaKategoriAktif) {
        title += ' untuk ' + namaKategoriAktif;
      }
      $('#modalDetailTitle').text(title);
      $('#formDetail')[0].reset();
      $('#detailId').val('');
      loadSubDetail(null); // Reset sub detail list
      $('#modalDetail').modal('show');
    });

    // Detail event
    $(document).on('click', '.btn-edit-detail', function () {
      let id = $(this).closest('tr').data('id');
      $.get(`/backend/master-parameter/${kategoriAktif}/detail`, function (data) {
        let det = data.find(d => d.id == id);
        detailAktif = det.id; // Set detail aktif
        let title = 'Edit Detail Parameter';
        if (namaKategoriAktif) {
          title += ' untuk ' + namaKategoriAktif;
        }
        $('#modalDetailTitle').text(title);
        $('#detailId').val(det.id);
        $('#detailNama').val(det.nama_detail_parameter);
        $('#detailKeterangan').val(det.keterangan);
        $('#detailAktif').prop('checked', det.aktif);
        loadSubDetail(det.id); // Load sub detail untuk detail ini
        $('#modalDetail').modal('show');
      });
    });
    $(document).on('click', '.btn-del-detail', function () {
    let id = $(this).closest('tr').data('id');
    let nama = $(this).closest('tr').find('td').first().text();
    
    confirmDelete(
      'Hapus Detail?',
      `Apakah Anda yakin ingin menghapus detail "${nama}"?`,
      function() {
        setLoadingState(true);
        $.ajax({ 
          url: `/backend/master-parameter/${kategoriAktif}/detail/${id}`, 
          type: 'DELETE', 
          data: { _token: '{{csrf_token()}}' }, 
          success: () => { 
            setLoadingState(false);
            Swal.fire({
              title: 'Berhasil!',
              text: 'Detail berhasil dihapus',
              icon: 'success',
              timer: 1500,
              showConfirmButton: false
            }).then(() => {
              loadDetail(kategoriAktif);
            });
          },
          error: function(xhr) {
            setLoadingState(false);
            Swal.fire({
              title: 'Error!',
              text: 'Terjadi kesalahan saat menghapus detail',
              icon: 'error'
            });
          }
        });
      }
    );
    });
    $(document).on('click', '#btnSaveDetail', function () {
      if (isSubmitting) return;
      
      let id = $('#detailId').val();
      let data = {
        nama_detail_parameter: $('#detailNama').val(),
        keterangan: $('#detailKeterangan').val(),
        aktif: $('#detailAktif').is(':checked') ? 1 : 0,
        sub_details: JSON.stringify(subDetailData),
        _token: '{{csrf_token()}}'
      };

      if (!data.nama_detail_parameter) return alert('Nama detail wajib diisi!');
      
      // Validasi sub detail
      for (let sub of subDetailData) {
        if (!sub.nama_sub_detail_parameter) {
          alert('Nama sub detail wajib diisi!');
          return;
        }
      }
      
      setLoadingState(true);
      
      if (id) {
        $.ajax({ 
          url: `/backend/master-parameter/${kategoriAktif}/detail/${id}`, 
          type: 'PUT', 
          data, 
          success: () => { 
            loadDetail(kategoriAktif); 
            detailAktif = id;
            loadSubDetail(id);
            $('#modalDetail').modal('hide');
            setLoadingState(false);
            Swal.fire({
              title: 'Berhasil!',
              text: 'Detail parameter dan sub detail berhasil disimpan',
              icon: 'success',
              timer: 1500,
              showConfirmButton: false
            });
          },
          error: function(xhr) {
            alert('Terjadi kesalahan: ' + xhr.responseText);
            setLoadingState(false);
          }
        });
      } else {
        $.ajax({
          url: `/backend/master-parameter/${kategoriAktif}/detail`,
          type: 'POST',
          data,
          success: (response) => { 
            loadDetail(kategoriAktif); 
            if (response && response.id) {
              detailAktif = response.id;
              loadSubDetail(response.id);
            }
            $('#modalDetail').modal('hide');
            setLoadingState(false);
            Swal.fire({
              title: 'Berhasil!',
              text: 'Detail parameter dan sub detail berhasil disimpan',
              icon: 'success',
              timer: 1500,
              showConfirmButton: false
            });
          },
          error: function(xhr) {
            alert('Terjadi kesalahan: ' + xhr.responseText);
            setLoadingState(false);
          }
        });
      }
    });

    // Initial load
    $(function () {
    loadKategori();
    });

    // Fungsi untuk konfirmasi delete
    function confirmDelete(title, text, callback) {
      Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          callback();
        }
      });
    }
  </script>
@endpush
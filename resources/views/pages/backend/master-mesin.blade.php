@extends('layout.master')

@push('plugin-styles')
  <!-- <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" /> -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" /> <!-- Add this line -->
@endpush

@section('content')


<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Master</a></li>
    <li class="breadcrumb-item active" aria-current="page">Mesin</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="card-title">Data Master Mesin ku</h6>
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
                    <i class="fa fa-plus"></i> Tambah Mesin
                </button>
            </div>
        </div>
        <p class="text-muted mb-3">Kelola inventaris mesin dan jadwal pemeliharaan </p>
        <form class="search-form">
          <div class="input-group">
            <div class="input-group-text">
              <i data-feather="search" class="icon-md cursor-pointer"></i>
            </div>
            <input type="text" class="form-control" id="searchForm" placeholder="Search here..." onkeyup="filterContent()">
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
              <option value="aktif">Aktif</option>
              <option value="nonaktif">Nonaktif</option>
            </select>
            <select class="form-select form-select-sm" style="width: 150px;" aria-label="Filter Produsen">
              <option selected disabled>Produsen</option>
              <option value="produsen1">Produsen 1</option>
              <option value="produsen2">Produsen 2</option>
            </select>
            <select class="form-select form-select-sm" style="width: 150px;" aria-label="Filter Lokasi">
              <option selected disabled>Lokasi</option>
              <option value="lokasi1">Lokasi 1</option>
              <option value="lokasi2">Lokasi 2</option>
            </select>
          </div>
        </div>
        <br>
        <div class="table-responsive">
          <table id="data-source-1" class="table">
            <thead>
                <tr class="table-responsive">
                    <th style="width: 5%;">Aktif</th>      
                    <th style="width: 5%;">Preview</th>                                                                                                    
                    <th style="width: 20%;">Nama Mesin</th> 
                    <th style="width: 15%;">Jenis Mesin</th>  
                    <th style="width: 20%;">Keterangan</th>                                        
                    <th style="width: 10%;">Jenis produksi</th>
                    <th style="width: 10%;">Tgl Beli</th>
                    <th style="width: 10%;">Nomor Seri</th>
                    <th style="width: 10%;">Pabrikan</th>
                    <th style="width: 10%;">Lokasi Pemeliharaan</th>
                    <th style="width: 10%;">Tgl Pemeliharaan</th>
                    <th style="width: 10%;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_mesin as $mesin)
                <tr class="custom-row-height" ondblclick="openEditModal({{ $mesin->id }})">
                    <td style="width: 5%;" class="text-center">
                        <input type="checkbox" {{ $mesin->aktif ? 'checked' : '' }} 
                            style="accent-color: {{ $mesin->aktif ? 'green' : 'red' }};" onclick="return false;">
                    </td>
                    <td style="width: 5%;" class="text-center">
                        @if ($mesin->gambar)
                            <img src="{{ asset('storage/images/' . $mesin->gambar) }}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; object-fit: contain;">
                        @else
                            <span>-</span>
                        @endif
                    </td>                                        
                    <td style="width: 20%;">{{ $mesin->nama_mesin }}</td> 
                    <td style="width: 15%;">{{ $mesin->jenis_mesin }}</td>  
                    <td style="width: 20%;">{{ $mesin->keterangan }}</td>
                    <td style="width: 10%;">{{ $mesin->non_produksi ? 'Produksi' : 'Non Produksi' }}</td>
                    <td style="width: 10%;">{{ \Carbon\Carbon::parse($mesin->tanggal_beli)->format('d-m-Y') }}</td>
                    <td style="width: 10%;">{{ $mesin->nomor_seri }}</td>
                    <td style="width: 10%;">{{ $mesin->pabrikan }}</td>
                    <td style="width: 10%;">{{ $mesin->lokasi_pemeliharaan }}</td>
                    <td style="width: 10%;">
                        @if($mesin->tanggal_pemeliharaan_terakhir)
                            {{ \Carbon\Carbon::parse($mesin->tanggal_pemeliharaan_terakhir)->format('d-m-Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="width: 10%;" class="text-center">
                        <button class="btn btn-outline-primary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#editDataModal{{ $mesin->id }}" title="Edit">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDataModal{{ $mesin->id }}" title="Delete">
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
        <div id="grid-search" class="mb-3" style="display: none;">
            <form class="search-form">
                <div class="input-group">
                    <div class="input-group-text">
                        <i data-feather="search" class="icon-md cursor-pointer"></i>
                    </div>
                    <input type="text" class="form-control" id="gridSearchForm" placeholder="Search in grid..." onkeyup="filterGrid()">
                </div>
            </form>
        </div>

        <!-- <script>
            let currentPage = 1;
            const itemsPerPage = 6;

            async function fetchFilteredData(searchQuery) {
                try {
                    const response = await fetch(`{{ route('backend.master-mesin.filter') }}?search=${encodeURIComponent(searchQuery)}`);
                    if (!response.ok) {
                        throw new Error('Failed to fetch filtered data');
                    }
                    const data = await response.json();
                    return data;
                } catch (error) {
                    console.error('Error fetching filtered data:', error);
                    return [];
                }
            }

            async function filterContent() {
                const searchInput = document.getElementById('searchForm').value.toLowerCase();

                if (document.getElementById('tableViewButton').classList.contains('btn-primary')) {
                    // Filter table view
                    if (dataTableInstance) {
                        dataTableInstance.search(searchInput).draw();
                    }
                } else {
                    // Filter grid view
                    const filteredData = await fetchFilteredData(searchInput);
                    filteredGridData = filteredData; // Update the global filtered data
                    currentPage = 1; // Reset to the first page
                    renderGridView(filteredGridData);
                }
            }

            // Format date function for grid view
            function formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                if (isNaN(date)) return '-';
                
                const day = date.getDate().toString().padStart(2, '0');
                const month = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][date.getMonth()];
                const year = date.getFullYear();
                
                return `${day} ${month} ${year}`;
            }

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

                paginatedData.forEach(mesin => {
                    const card = `
                        <div class="col-md-4 mb-4">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="card-title mb-0">${mesin.nama_mesin}</h5>
                                        <span class="badge bg-${mesin.aktif ? 'success' : 'danger'}">
                                            ${mesin.aktif ? 'Active' : 'Inactive'}
                                        </span>
                                    </div>
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="mb-1 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Jenis Mesin:</strong>
                                                <span class="text-start" style="width: 65%;">${mesin.jenis_mesin}</span>
                                            </p>
                                            <p class="mb-1 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Jenis Produksi:</strong>
                                                <span class="text-start" style="width: 65%;">${mesin.non_produksi ? 'Produksi' : 'Non Produksi'}</span>
                                            </p>
                                            <p class="mb-1 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Tanggal Beli:</strong>
                                                <span class="text-start" style="width: 65%;">${formatDate(mesin.tanggal_beli)}</span>
                                            </p>
                                            <p class="mb-1 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Nomor Seri:</strong>
                                                <span class="text-start" style="width: 65%;">${mesin.nomor_seri || '-'}</span>
                                            </p>
                                            <p class="mb-1 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Pabrikan:</strong>
                                                <span class="text-start" style="width: 65%;">${mesin.pabrikan || '-'}</span>
                                            </p>
                                            <p class="mb-1 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Lokasi:</strong>
                                                <span class="text-start" style="width: 65%;">${mesin.lokasi_pemeliharaan || '-'}</span>
                                            </p>
                                            <p class="mb-1 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Pemeliharaan:</strong>
                                                <span class="text-start" style="width: 65%;">
                                                    ${mesin.tanggal_pemeliharaan_terakhir ? 'Terakhir: ' + formatDate(mesin.tanggal_pemeliharaan_terakhir) : '-'}
                                                </span>
                                            </p>
                                            <p class="mb-1 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Pemeliharaan:</strong>
                                                <span class="text-start" style="width: 65%;">
                                                    ${mesin.tanggal_pemeliharaan_selanjutnya ? 'Selanjutnya: ' + formatDate(mesin.tanggal_pemeliharaan_selanjutnya) : '-'}
                                                </span>
                                            </p>
                                            <p class="mb-3 d-flex">
                                                <strong class="text-end me-1" style="width: 35%;">Keterangan:</strong>
                                                <span class="text-start" style="width: 65%;">${mesin.keterangan || 'Tidak ada keterangan'}</span>
                                            </p>
                                        </div>
                                        <div>
                                            ${mesin.gambar ? `<img src="{{ asset('storage/images/') }}/${mesin.gambar}" alt="Preview" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ccc;">` : '<span>No Image</span>'}
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editDataModal${mesin.id}">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDataModal${mesin.id}">
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
                const tableView = document.getElementById('data-source-1').parentElement; // Ensure the parent container is hidden
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

            // Initialize grid data and filtered data
            const gridData = @json($data_mesin);
            let filteredGridData = [...gridData]; // Initialize filtered data with all data

            function clearSearchForm() {
              const searchForm = document.getElementById('searchForm');
              searchForm.value = '';
              filterContent(); // Trigger filtering with an empty input
            }
        </script> -->

      </div>
    </div>
  </div>
</div>


    <!-- Modal Tambah Data -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="addModalLabel">Tambah Data Mesin</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <form id="addForm" action="{{ route('backend.master-mesin.create') }}" method="POST" enctype="multipart/form-data">
                      <div class="row">
                          <div class="col-md-8" style="border-right: 1px solid #dee2e6;">
                              @csrf
                              <div class="row mb-3 align-items-center">
                                  <label for="addKode" class="col-sm-4 col-form-label text-end">Kode Mesin</label>
                                  <div class="col-sm-4">
                                      <input type="text" class="form-control" id="addKode" name="kode_mesin" value="auto" readonly>
                                  </div>
                                  <label for="addAktif" class="col-sm-2 col-form-label text-end">Aktif</label>
                                  <div class="col-sm-2">
                                      <div class="form-check">
                                          <input type="hidden" name="aktif" value="0"> <!-- Hidden input ensures unchecked checkbox sends value -->
                                          <input type="checkbox" id="addAktif" name="aktif" value="1" class="form-check-input" checked>
                                      </div>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addNama" class="col-sm-4 col-form-label text-end">Nama Mesin</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" id="addNama" name="nama_mesin" required>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addModel" class="col-sm-4 col-form-label text-end">Model</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" id="addModel" name="model_mesin" required>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addJenis" class="col-sm-4 col-form-label text-end">Jenis Mesin</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" id="addJenis" name="jenis_mesin" required>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addKeterangan" class="col-sm-4 col-form-label text-end">Keterangan</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" id="addKeterangan" name="keterangan">
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addNonProduksi" class="col-sm-4 col-form-label text-end">Jenis Produksi</label>
                                  <div class="col-sm-8">
                                      <select class="form-select" id="addNonProduksi" name="non_produksi">
                                          <option value="1">Produksi</option>
                                          <option value="0">Non Produksi</option>
                                      </select>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addTglBeli" class="col-sm-4 col-form-label text-end">Tanggal Beli</label>
                                  <div class="col-sm-8">
                                      <input type="date" class="form-control" id="addTglBeli" name="tanggal_beli">
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addNomorSeri" class="col-sm-4 col-form-label text-end">Nomor Seri</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" id="addNomorSeri" name="nomor_seri">
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addPabrikan" class="col-sm-4 col-form-label text-end">Pabrikan</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" id="addPabrikan" name="pabrikan">
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addLokasiPemeliharaan" class="col-sm-4 col-form-label text-end">Lokasi Pemeliharaan</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" id="addLokasiPemeliharaan" name="lokasi_pemeliharaan">
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addTglPemeliharaanTerakhir" class="col-sm-4 col-form-label text-end">Tanggal Pemeliharaan Terakhir</label>
                                  <div class="col-sm-8">
                                      <input type="date" class="form-control" id="addTglPemeliharaanTerakhir" name="tanggal_pemeliharaan_terakhir">
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addTglPemeliharaanSelanjutnya" class="col-sm-4 col-form-label text-end">Tanggal Pemeliharaan Selanjutnya</label>
                                  <div class="col-sm-8">
                                      <input type="date" class="form-control" id="addTglPemeliharaanSelanjutnya" name="tanggal_pemeliharaan_selanjutnya">
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label for="addCatatan" class="col-sm-4 col-form-label text-end">Catatan</label>
                                  <div class="col-sm-8">
                                      <textarea class="form-control" id="addCatatan" name="catatan" rows="3"></textarea>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-4">
                              <label for="">Gambar</label>
                              <div class="d-flex flex-column align-items-center">
                                  <div style="width: 100%; height: 200px; border: 1px solid #ccc; display: flex; justify-content: center; align-items: center; overflow: hidden; margin-bottom: 15px;">
                                      <img id="previewImage" src="#" alt="Preview" style="max-height: 100%; max-width: 100%; object-fit: contain; display: none;">
                                  </div>
                                  <div class="d-flex flex-column w-100 gap-2">
                                      <button type="button" class="btn btn-secondary w-100" onclick="document.getElementById('addGambar').click();">Pilih Gambar</button>
                                      <button type="button" class="btn btn-danger w-100" onclick="clearAddImagePreview();">Bersihkan</button>
                                  </div>
                                  <input type="file" class="form-control d-none" id="addGambar" name="gambar" accept="image/*" onchange="previewSelectedImage(event)">
                                  <input type="hidden" id="clearAddGambar" name="clear_gambar" value="0">
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary">Simpan</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <!-- Modal Edit Data -->
  @foreach ($data_mesin as $mesin)
  <div class="modal fade" id="editDataModal{{ $mesin->id }}" tabindex="-1" aria-labelledby="editDataModalLabel{{ $mesin->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="editDataModalLabel{{ $mesin->id }}">Edit Data Mesin</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <form action="/backend/master-mesin/{{ $mesin->id }}/update" method="POST" enctype="multipart/form-data">
                  <div class="row">
                      <div class="col-md-8" style="border-right: 1px solid #dee2e6;">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="old_gambar" value="{{ $mesin->gambar }}">
                      <div class="row mb-3 align-items-center">
                          <label for="editKode_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Kode Mesin</label>
                          <div class="col-sm-4">
                              <input type="text" class="form-control" id="editKode_{{ $mesin->id }}" name="kode_mesin" value="{{ $mesin->kode_mesin }}" readonly>
                          </div>
                          <label for="editAktif_{{ $mesin->id }}" class="col-sm-2 col-form-label text-end">Aktif</label>
                          <div class="col-sm-2">
                              <div class="form-check">
                                  <input type="hidden" name="aktif" value="0"> <!-- Hidden input ensures unchecked value sends 0 -->
                                  <input type="checkbox" id="editAktif_{{ $mesin->id }}" name="aktif" value="1" class="form-check-input" {{ $mesin->aktif ? 'checked' : '' }}>
                              </div>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editNama_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Nama Mesin</label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" id="editNama_{{ $mesin->id }}" name="nama_mesin" value="{{ $mesin->nama_mesin }}" required>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editJenis_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Jenis Mesin</label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" id="editJenis_{{ $mesin->id }}" name="jenis_mesin" value="{{ $mesin->jenis_mesin }}" required>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editKeterangan_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Keterangan</label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" id="editKeterangan_{{ $mesin->id }}" name="keterangan" value="{{ $mesin->keterangan }}">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editNonProduksi_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Jenis Produksi</label>
                          <div class="col-sm-8">
                              <select class="form-select" id="editNonProduksi_{{ $mesin->id }}" name="non_produksi">
                                  <option value="1" {{ $mesin->non_produksi ? 'selected' : '' }}>Produksi</option>
                                  <option value="0" {{ !$mesin->non_produksi ? 'selected' : '' }}>Non Produksi</option>
                              </select>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editTanggalBeli_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Tanggal Beli</label>
                          <div class="col-sm-8">
                              <input type="date" class="form-control" id="editTanggalBeli_{{ $mesin->id }}" name="tanggal_beli" value="{{ \Carbon\Carbon::parse($mesin->tanggal_beli)->format('Y-m-d') }}">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editNomorSeri_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Nomor Seri</label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" id="editNomorSeri_{{ $mesin->id }}" name="nomor_seri" value="{{ $mesin->nomor_seri }}">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editPabrikan_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Pabrikan</label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" id="editPabrikan_{{ $mesin->id }}" name="pabrikan" value="{{ $mesin->pabrikan }}">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editLokasiPemeliharaan_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Lokasi Pemeliharaan</label>
                          <div class="col-sm-8">
                              <input type="text" class="form-control" id="editLokasiPemeliharaan_{{ $mesin->id }}" name="lokasi_pemeliharaan" value="{{ $mesin->lokasi_pemeliharaan }}">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editTglPemeliharaanTerakhir_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Tanggal Pemeliharaan Terakhir</label>
                          <div class="col-sm-8">
                              <input type="date" class="form-control" id="editTglPemeliharaanTerakhir_{{ $mesin->id }}" name="tanggal_pemeliharaan_terakhir" value="{{ $mesin->tanggal_pemeliharaan_terakhir ? \Carbon\Carbon::parse($mesin->tanggal_pemeliharaan_terakhir)->format('Y-m-d') : '' }}">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editTglPemeliharaanSelanjutnya_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Tanggal Pemeliharaan Selanjutnya</label>
                          <div class="col-sm-8">
                              <input type="date" class="form-control" id="editTglPemeliharaanSelanjutnya_{{ $mesin->id }}" name="tanggal_pemeliharaan_selanjutnya" value="{{ $mesin->tanggal_pemeliharaan_selanjutnya ? \Carbon\Carbon::parse($mesin->tanggal_pemeliharaan_selanjutnya)->format('Y-m-d') : '' }}">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="editCatatan_{{ $mesin->id }}" class="col-sm-4 col-form-label text-end">Catatan</label>
                          <div class="col-sm-8">
                              <textarea class="form-control" id="editCatatan_{{ $mesin->id }}" name="catatan" rows="3">{{ $mesin->catatan }}</textarea>
                          </div>
                      </div>
                      </div>
                      <div class="col-md-4">
                          <label for="">Gambar</label>
                          <div class="d-flex flex-column align-items-center">
                              <div style="width: 100%; height: 200px; border: 1px solid #ccc; display: flex; justify-content: center; align-items: center; overflow: hidden; margin-bottom: 15px;">
                                  <img id="previewImageEdit_{{ $mesin->id }}" 
                                       src="{{ $mesin->gambar ? asset('storage/images/' . $mesin->gambar) : '#' }}" 
                                       alt="Preview" 
                                       style="max-height: 100%; max-width: 100%; object-fit: contain; {{ $mesin->gambar ? '' : 'display: none;' }}">
                              </div>
                              <div class="d-flex flex-column w-100 gap-2">
                                  <button type="button" class="btn btn-secondary w-100" onclick="document.getElementById('editGambar_{{ $mesin->id }}').click();">Pilih Gambar</button>
                                  <button type="button" class="btn btn-danger w-100" onclick="clearImagePreview({{ $mesin->id }});">Bersihkan</button>
                              </div>
                              <input type="file" class="form-control d-none" id="editGambar_{{ $mesin->id }}" name="gambar" accept="image/*" onchange="previewSelectedImageEdit(event, {{ $mesin->id }})">
                              <input type="hidden" id="clearGambar_{{ $mesin->id }}" name="clear_gambar" value="0">
                          </div>
                      </div>
                  </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary">Simpan</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  @endforeach

  <!-- Modal Konfirmasi Hapus Data -->
  @foreach ($data_mesin as $mesin)
  <div class="modal fade" id="deleteDataModal{{ $mesin->id }}" tabindex="-1" aria-labelledby="deleteDataModalLabel{{ $mesin->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="deleteDataModalLabel{{ $mesin->id }}">Konfirmasi Hapus Data</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-md-8" style="border-right: 1px solid #dee2e6;">
                          <form>
                              <div class="row mb-3 align-items-center">
                                  <label class="col-sm-4 col-form-label text-end">Kode Mesin</label>
                                  <div class="col-sm-4">
                                      <input type="text" class="form-control" value="{{ $mesin->kode_mesin }}" disabled>
                                  </div>
                                  <label class="col-sm-2 col-form-label text-end">Aktif</label>
                                  <div class="col-sm-2 d-flex align-items-center">
                                      <input type="checkbox" {{ $mesin->aktif ? 'checked' : '' }} disabled>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label class="col-sm-4 col-form-label text-end">Nama Mesin</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" value="{{ $mesin->nama_mesin }}" disabled>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label class="col-sm-4 col-form-label text-end">Jenis Mesin</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" value="{{ $mesin->jenis_mesin }}" disabled>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label class="col-sm-4 col-form-label text-end">Keterangan</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" value="{{ $mesin->keterangan }}" disabled>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label class="col-sm-4 col-form-label text-end">Jenis Produksi</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" value="{{ $mesin->non_produksi ? 'Produksi' : 'Non Produksi' }}" disabled>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label class="col-sm-4 col-form-label text-end">Tanggal Beli</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($mesin->tanggal_beli)->format('d-m-Y') }}" disabled>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label class="col-sm-4 col-form-label text-end">Nomor Seri</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" value="{{ $mesin->nomor_seri ?: '-' }}" disabled>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label class="col-sm-4 col-form-label text-end">Pabrikan</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" value="{{ $mesin->pabrikan ?: '-' }}" disabled>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <label class="col-sm-4 col-form-label text-end">Lokasi Pemeliharaan</label>
                                  <div class="col-sm-8">
                                      <input type="text" class="form-control" value="{{ $mesin->lokasi_pemeliharaan ?: '-' }}" disabled>
                                  </div>
                              </div>
                          </form>
                      </div>
                      <div class="col-md-4">
                          <label>Gambar</label>
                          <div class="d-flex flex-column align-items-center">
                              <div style="width: 100%; height: 200px; border: 1px solid #ccc; display: flex; justify-content: center; align-items: center; overflow: hidden; margin-bottom: 15px;">
                                  @if ($mesin->gambar)
                                      <img src="{{ asset('storage/images/' . $mesin->gambar) }}" alt="Preview" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                  @else
                                      <span>Tidak ada gambar</span>
                                  @endif
                              </div>
                          </div>
                      </div>
                  </div>
                  <p class="text-center mt-3">Apakah Anda yakin ingin menghapus data ini?</p>
              </div>
              <div class="modal-footer">
                  <form action="{{ route('backend.master-mesin.delete', $mesin->id) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <input type="hidden" name="gambar_path" value="{{ $mesin->gambar }}">
                      <button type="submit" class="btn btn-danger">Delete</button>
                  </form>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              </div>
          </div>
      </div>
  </div>
  @endforeach

  {{ $data_mesin->links('pagination::bootstrap-5') }}

@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
function showTab(view) {
    const tableButton = document.getElementById('tableViewButton');
    const gridButton = document.getElementById('gridViewButton');
    const tableView = document.getElementById('data-source-1').parentElement;
    const gridView = document.getElementById('grid-view');

    if (view === 'table') {
        tableButton.classList.add('btn-primary');
        tableButton.classList.remove('btn-light');
        gridButton.classList.add('btn-light');
        gridButton.classList.remove('btn-primary');
        tableView.style.display = 'block';
        gridView.style.display = 'none';
    } else if (view === 'grid') {
        gridButton.classList.add('btn-primary');
        gridButton.classList.remove('btn-light');
        tableButton.classList.add('btn-light');
        tableButton.classList.remove('btn-primary');
        tableView.style.display = 'none';
        gridView.style.display = 'block';
        // AJAX load grid
        gridView.innerHTML = '<div class="text-center w-100 py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        fetch("{{ route('backend.master-mesin.grid') }}")
            .then(response => response.text())
            .then(html => {
                // Ambil hanya isi <div class="d-flex flex-wrap gap-3">...</div> dari partial
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const gridContent = doc.querySelector('.d-flex.flex-wrap.gap-3');
                if (gridContent) {
                    gridView.innerHTML = '';
                    gridView.appendChild(gridContent);
                } else {
                    gridView.innerHTML = '<div class="alert alert-danger">Gagal memuat grid.</div>';
                }
            })
            .catch(() => {
                gridView.innerHTML = '<div class="alert alert-danger">Gagal memuat grid.</div>';
            });
    }
}
</script>
@endpush
@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
      <li class="breadcrumb-item"><a href="{{ route('spk.index') }}">SPK</a></li>
      <li class="breadcrumb-item active" aria-current="page">Buat SPK Baru</li>
    </ol>
  </nav>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger">
      {{ session('error') }}
    </div>
  @endif
  @if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  <h4 class="fw-bold">Buat SPK Baru</h4>
  <p class="text-muted mb-4">Buat Surat Perintah Kerja untuk pelanggan.</p>

  <form id="formTambahSPK" action="{{ route('spk.store') }}" method="POST">
    @csrf
    <input type="hidden" name="items" id="itemsInput">
    <input type="hidden" name="tugas_produksi" id="tugasProduksiInput">
    <div class="row">
      <!-- Kiri: Item Pekerjaan -->
      <div class="col-md-9">
        <div class="card border-0 shadow-none">
          <div class="card-body p-0">
            <ul class="nav nav-tabs mb-3" id="tabSPK" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="item-tab" data-bs-toggle="tab" data-bs-target="#itemPekerjaan"
                  type="button" role="tab">Item Pekerjaan</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tugas-tab" data-bs-toggle="tab" data-bs-target="#tugasProduksi" type="button"
                  role="tab">Tugas Produksi</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="file-tab" data-bs-toggle="tab" data-bs-target="#filePendukung" type="button"
                  role="tab">File Pendukung</button>
              </li>
            </ul>
            <div class="tab-content" id="tabSPKContent">
              <div class="tab-pane fade show active" id="itemPekerjaan" role="tabpanel">
                <div class="row">
                  <!-- Form Input Item -->
                  <div class="col-md-8">
                    <div class="card mb-3 border-primary">
                      <div class="card-body">
                        <div class="mb-2">
                          <!-- <label class="form-label mb-0">Tambah Item Pekerjaan ke SPK</label> -->
                          <small class="text-muted d-block mb-2">Tambahkan detail item pekerjaan yang akan dikerjakan.</small>
                        </div>
                        <div class="row g-2 mb-2 align-items-end">
                          <div class="col-md-4">
                            <label class="form-label small mb-1">Nama Produk</label>
                            <input type="text" class="form-control" id="namaProdukInput" placeholder="Masukkan nama produk...">
                          </div>
                          <div class="col-md-2">
                            <label for="jumlahInput" class="form-label small mb-1">Jumlah</label>
                            <input type="number" class="form-control text-end" id="jumlahInput" placeholder="0" min="0" step="0.01">
                          </div>
                          <div class="col-md-2">
                            <label for="satuanInput" class="form-label small mb-1">Satuan</label>
                            <select class="form-select" id="satuanInput">
                              <option value="pcs">Pcs</option>
                              <option value="lembar">Lembar</option>
                              <option value="meter">Meter</option>
                              <option value="set">Set</option>
                              <option value="desain">Desain</option>
                            </select>
                          </div>
                          <div class="col-md-2">
                            <label for="biayaDesainInput" class="form-label small mb-1">Biaya Desain (Rp)</label>
                            <input type="number" class="form-control text-end" id="biayaDesainInput" placeholder="0" min="0" value="0">
                          </div>
                          <div class="col-md-2">
                            <label for="biayaFinishingInput" class="form-label small mb-1">Biaya Finishing (Rp)</label>
                            <input type="number" class="form-control text-end" id="biayaFinishingInput" placeholder="0" min="0" value="0">
                          </div>
                        </div>
                        <div class="row g-2 mb-2">
                          <div class="col-md-4">
                            <label class="form-label small mb-1">Bahan baku</label>
                            <div class="input-group">
                              <input type="text" class="form-control" id="namaBahanInput" placeholder="Pilih bahan..." readonly>
                              <input type="hidden" id="bahanIdInput">
                              <button type="button" class="btn btn-outline-secondary" id="btnCariBahan"><i class="fa fa-search"></i></button>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <label for="lebarInput" class="form-label small mb-1">Lebar (cm)</label>
                            <input type="number" class="form-control text-end" id="lebarInput" placeholder="0" min="0" step="0.1" value="0">
                          </div>
                          <div class="col-md-2">
                            <label for="panjangInput" class="form-label small mb-1">Panjang (cm)</label>
                            <input type="number" class="form-control text-end" id="panjangInput" placeholder="0" min="0" step="0.1" value="0">
                          </div>
                          <div class="col-md-4">
                            <label for="keteranganInput" class="form-label small mb-1">Keterangan</label>
                            <input type="text" class="form-control" id="keteranganInput" placeholder="Tambahkan keterangan...">
                          </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary" id="btnTambahItem"><i class="fa fa-plus"></i> Tambah Item</button>
                      </div>
                    </div>
                  </div>

                  <!-- Preview Image -->
                  <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                      <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fa fa-eye me-1"></i> Preview</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnRefreshPreview" title="Refresh Preview">
                          <i data-feather="refresh-cw" class="icon-sm"></i>
                        </button>
                      </div>
                      <div class="card-body">
                        <!-- Image Preview Box -->
                        <div class="border-2 border-dashed rounded p-3 text-center mb-3" id="imagePreviewBox" style="min-height: 180px; background: #f8f9fa; position:relative;">
                          <input type="file" id="inputPreviewGambar" accept="image/*" style="display:none;">
                          <!-- <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">←→ <span id="previewWidth">0</span> cm</small>
                            <small class="text-muted"><span id="previewHeight">0</span> cm ↑</small>
                          </div> -->
                          <div class="d-flex justify-content-center align-items-center" style="height: 120px;">
                            <div class="text-center" id="previewImageContainer">
                              <i class="fa fa-file-image-o fa-3x text-muted mb-2" id="iconNoPreview"></i>
                              <img id="previewImage" src="" alt="Preview" style="max-width:100%;max-height:110px;display:none;object-fit:contain;" />
                              <p class="text-muted mb-2 small" id="previewText">Tidak ada preview</p>
                              <button type="button" class="btn btn-outline-primary btn-sm" id="btnUploadGambar">
                                <i class="fa fa-upload me-1"></i> Upload Gambar
                              </button>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Image Properties -->
                        <div class="mb-3">
                          <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">DPI</span>
                            <span id="imageDPI">0 DPI</span>
                          </div>
                          <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Color Mode</span>
                            <span id="imageColorMode">CMYK</span>
                          </div>
                          <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">File Size</span>
                            <span id="imageFileSize">0 MB</span>
                          </div>
                        </div>
                        
                        <!-- Finishing Options -->
                        <div class="mb-3">
                          <label class="form-label small fw-semibold mb-2">Opsi Finishing</label>
                          <div class="d-flex flex-wrap gap-1" id="finishingOptions">
                            <button type="button" class="btn btn-outline-secondary btn-sm option-btn" data-option="laminating">
                              <i class="fa fa-bookmark me-1"></i> Laminating
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm option-btn" data-option="cutting">
                              <i class="far fa-hand-scissors me-1"></i> Cutting
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm option-btn" data-option="binding">
                              <i class="fa fa-book me-1"></i> Binding
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm option-btn" data-option="a3+">
                              <i class="far fa-file me-1"></i> A3+
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tabel Item SPK - Diganti dengan Card Layout -->
                <div class="card border-0 shadow-sm">
                  <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="fa fa-list me-2"></i>Daftar Item Pekerjaan</h6>
                  </div>
                  <div class="card-body p-0">
                    <!-- Header Table-like -->
                    <div class="row g-0 border-bottom bg-light fw-semibold small">
                      <div class="col-3 p-3">Nama Item</div>
                      <div class="col-2 p-3">Jumlah</div>
                      <div class="col-2 p-3">Satuan</div>
                      <div class="col-2 p-3">Bahan Baku</div>
                      <div class="col-2 p-3">Keterangan</div>
                      <div class="col-1 p-3 text-center">Aksi</div>
                    </div>
                    
                    <!-- Container untuk item cards -->
                    <div id="itemCardsContainer">
                      <div class="text-center text-muted py-4" id="noItemsMessage">
                        <i class="fa fa-list-alt fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada item yang ditambahkan</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Tab Tugas Produksi -->
              <div class="tab-pane fade px-3" id="tugasProduksi" role="tabpanel">
                <div id="emptyTugasState" class="d-flex flex-column align-items-center justify-content-center py-5" style="min-height:320px; border:1.5px dashed #e3e6ea; border-radius:12px; background:#f8fafc;">
                  <div class="mb-3">
                    <svg width="64" height="64" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="32" cy="32" r="30" stroke="#cfd8dc" stroke-width="4" fill="#f4f6f8"/>
                      <path d="M32 18v14l8 4" stroke="#b0bec5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <h5 class="fw-semibold text-secondary mb-1">Belum ada tugas</h5>
                  <div class="text-muted mb-3">Tambahkan tugas produksi untuk item ini</div>
                  <button type="button" class="btn btn-outline-primary btn-lg px-4 py-2" id="btnTambahTugasPertama">
                    <i class="fa fa-plus me-2"></i>Tambah Tugas Pertama
                  </button>
                </div>
              </div>
              <!-- Modal Tambah/Edit Tugas Produksi -->
              <div class="tab-pane fade" id="filePendukung" role="tabpanel">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Upload File Pendukung</label>
                  <div id="dropZone" class="border-2 border-dashed rounded p-4 text-center bg-light mb-3" style="cursor:pointer;">
                    <i class="fa fa-cloud-upload fa-2x text-primary mb-2"></i>
                    <div class="mb-2">Drag & drop file di sini atau <span class="text-primary" style="text-decoration:underline;cursor:pointer;" id="btnBrowseFile">klik untuk pilih file</span></div>
                    <small class="text-muted">Maksimal 10MB per file. Tipe: PDF, JPG, PNG, DOCX, XLSX, ZIP, dll.</small>
                    <input type="file" id="inputFilePendukung" multiple style="display:none;">
                  </div>
                </div>
                <div class="table-responsive mb-3">
                  <table class="table table-bordered align-middle mb-0" id="tabelFilePendukung">
                    <thead class="table-light">
                      <tr>
                        <th>Nama File</th>
                        <th>Tipe</th>
                        <th>Ukuran</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="filePendukungBody">
                      <tr><td colspan="4" class="text-center text-muted">Belum ada file pendukung</td></tr>
                    </tbody>
                  </table>
                </div>
                <input type="hidden" name="file_pendukung" id="filePendukungInput">
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
          <button type="button" class="btn btn-light" id="btnBatalSPK">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSimpanSPK">
            <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerSimpanSPK" role="status" aria-hidden="true"></span>
            <span class="label-simpan">Simpan SPK</span>
          </button>
        </div>
      </div>

      <!-- Sidebar kanan: informasi pelanggan -->
            <div class="col-md-3">
                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="fw-semibold mb-2"><i class="fa fa-user me-1"></i> Informasi Pelanggan</div>
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <div class="input-group">
              <input type="text" class="form-control" id="namaCustomerInput" placeholder="Pilih pelanggan..." readonly style="background:#fff;cursor:pointer;">
              <input type="hidden" id="customerIdInput" name="customer_id">
              <button class="btn btn-outline-secondary" type="button" id="btnCariCustomer"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal SPK</label>
            <input type="date" class="form-control" name="tanggal_spk" id="tanggalSPK" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="fw-semibold mb-2"><i class="fa fa-flag me-1"></i> Status & Prioritas</div>
                    <div class="mb-3">
                        <label class="form-label">Status SPK</label>
            <select class="form-select" name="status" id="statusSPK">
              <option value="draft" selected>Draft</option>
              <option value="menunggu persetujuan">Menunggu Persetujuan</option>
              <option value="disetujui">Disetujui</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioritas</label>
            <select class="form-select" name="prioritas" id="prioritasSPK">
              <option value="normal" selected>Normal</option>
              <option value="rendah">Rendah</option>
              <option value="tinggi">Tinggi</option>
              <option value="mendesak">Mendesak</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="fw-semibold mb-2"><i class="fa fa-sticky-note me-1"></i> Catatan</div>
          <textarea class="form-control" name="catatan" rows="3" placeholder="Tambahkan catatan untuk SPK ini"></textarea>
                </div>
        <div class="p-3 border rounded bg-light">
          <div class="fw-semibold mb-2"><i class="fa fa-dollar-sign me-1"></i> Ringkasan Biaya</div>
          <div class="d-flex justify-content-between small mb-1"><span>Total Item:</span><span class="ringkasan-total-item">0</span></div>
          <div class="d-flex justify-content-between small mb-1"><span>Total Biaya:</span><span class="ringkasan-total-biaya">Rp 0</span></div>
          <hr class="my-2">
          <div class="d-flex justify-content-between fw-bold"><span>Total SPK:</span><span class="ringkasan-total-spk">Rp 0</span></div>
                </div>
            </div>
        </div>
    </form>

  <!-- Modal Tambah/Edit Tugas Produksi -->
  <div class="modal fade" id="modalTugasProduksi" tabindex="-1" aria-labelledby="modalTugasProduksiLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTugasProduksiLabel">Tambah Tugas Produksi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formTugasProduksi">
            <div class="mb-3">
              <label class="form-label">Nama Tugas <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="inputNamaTugas" placeholder="Cetak, Finishing, Desain, dll" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Ditugaskan Kepada <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="text" class="form-control" id="inputDitugaskan" placeholder="Pilih karyawan..." readonly required>
                <input type="hidden" id="inputDitugaskanId">
                <button type="button" class="btn btn-outline-secondary" id="btnCariKaryawan"><i class="fa fa-search"></i></button>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Mesin (opsional)</label>
              <select class="form-select" id="inputMesin">
                <option value="">Tidak menggunakan mesin</option>
                <option value="Printer XYZ-2000">Printer XYZ-2000</option>
                <option value="Mesin Potong A">Mesin Potong A</option>
                <option value="Mesin Laminasi B">Mesin Laminasi B</option>
              </select>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label class="form-label">Estimasi Waktu (jam) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="inputWaktu" min="1" value="1" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" class="form-control" id="inputHarga" min="0" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea class="form-control" id="inputDeskripsi" rows="2" placeholder="Masukkan deskripsi tugas..."></textarea>
                            </div>
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary" id="btnSimpanTugas">Tambah Tugas</button>
                            </div>
          </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Item Pesanan -->
            <div class="modal fade" id="modalTambahItemPesanan" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalTambahItemPesananLabel" aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="modalTambahItemPesananLabel">
                      <i class="fa fa-plus-circle me-2"></i>Tambah Item Pesanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body p-0">
                    <div class="row g-0">
                      <!-- Left Panel: Form Sections -->
                      <div class="col-lg-8 border-end">
                        <div class="p-4">
                          <!-- Section 1: Detail Produk -->
                          <div class="section-item mb-4" id="sectionDetailPesanan">
                            <div class="d-flex align-items-center mb-3">
                              <span class="section-number me-2">1</span>
                              <h6 class="mb-0 fw-bold">Detail Produk</h6>
                            </div>
                            <div class="ps-4">
                              <!-- ... isi Detail Pesanan sama seperti sebelumnya ... -->
                              <div class="row g-3">
                                <div class="col-md-12">
                                  <label class="form-label">Produk <span class="text-danger">*</span></label>
                                  <div class="input-group">
                                    <input type="text" class="form-control" id="modalProdukSelect" placeholder="Pilih produk..." readonly style="cursor: pointer; background-color: #fff;">
                                    <input type="hidden" id="modalProdukId">
                                    <button type="button" class="btn btn-outline-secondary" id="modalBtnCariProduk" title="Cari Produk">
                                      <i class="fa fa-search"></i>
                                    </button>
                                  </div>
                                </div>
                                <div class="col-md-12">
                                  <label class="form-label">Keterangan</label>
                                  <textarea class="form-control" id="modalKeteranganInput" rows="2" placeholder="Masukkan keterangan pesanan..."></textarea>
                                </div>
                                <div class="col-md-4">
                                  <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                  <div class="input-group">
                                    <input type="number" class="form-control" id="modalJumlahInput" min="0" step="0.01" placeholder="0">
                                    <span class="input-group-text" id="modalSatuanDisplay">pcs</span>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <label class="form-label">Deadline <span class="text-danger">*</span></label>
                                  <input type="datetime-local" class="form-control" id="modalDeadlineInput">
                                </div>
                                <div class="col-md-4">
                                  <label class="form-label">Urgent</label>
                                  <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="modalUrgentToggle">
                                    <label class="form-check-label" for="modalUrgentToggle" id="modalUrgentLabel">
                                      <span class="urgent-status">Tidak</span>
                                    </label>
                                  </div>
                                  <input type="hidden" id="modalUrgentValue" value="false">
                                </div>
                                <div id="sectionUkuran" style="display: none;">
                                  <div class="col-12">
                                    <label class="form-label">Ukuran</label>
                                    <div class="row g-2">
                                      <div class="col-4">
                                        <div class="input-group">
                                          <span class="input-group-text">L</span>
                                          <input type="number" class="form-control" id="modalLebarInput" min="0" step="0.1" value="0" placeholder="Lebar">
                                          <span class="input-group-text" id="modalSatuanLebar">-</span>
                                        </div>
                                        <small class="text-muted mt-1" id="lebarStatus" style="display:none;"><i class="fa fa-lock"></i> Lebar terkunci sesuai produk</small>
                                      </div>
                                      <div class="col-4">
                                        <div class="input-group">
                                          <span class="input-group-text">P</span>
                                          <input type="number" class="form-control" id="modalPanjangInput" min="0" step="0.1" value="0" placeholder="Panjang">
                                          <span class="input-group-text" id="modalSatuanPanjang">-</span>
                                        </div>
                                        <small class="text-muted mt-1" id="panjangStatus" style="display:none;"><i class="fa fa-lock"></i> Panjang terkunci sesuai produk</small>
                                      </div>
                                      <div class="col-4">
                                        <div class="input-group">
                                          <span class="input-group-text">Luas</span>
                                          <input type="text" class="form-control" id="modalLuasInput" readonly>
                                          <span class="input-group-text" id="modalSatuanLuas">cm²</span>
                                        </div>
                                        <!-- <small class="text-muted mt-1">Panjang × Lebar</small> -->
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="mt-3" id="sectionRelasiProduk" style="display:none;">
                                  <div class="accordion" id="accordionRelasiProduk">
                                    <!-- Panel: Bahan Baku Terkait -->
                                    <div class="accordion-item">
                                      <h2 class="accordion-header" id="headingBahanBaku">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBahanBaku" aria-expanded="false" aria-controls="collapseBahanBaku">
                                          Bahan Baku Terkait
                                        </button>
                                      </h2>
                                      <div id="collapseBahanBaku" class="accordion-collapse collapse" aria-labelledby="headingBahanBaku" data-bs-parent="#accordionRelasiProduk">
                                        <div class="accordion-body" id="relasiBahanBakuBody">
                                          <p class="text-muted mb-0 small">Belum ada informasi bahan baku untuk produk ini.</p>
                                        </div>
                                      </div>
                                    </div>

                                    <!-- Panel: Produk Komponen (Rakitan) -->
                                    <div class="accordion-item">
                                      <h2 class="accordion-header" id="headingKomponen">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKomponen" aria-expanded="false" aria-controls="collapseKomponen">
                                          Produk Komponen (Rakitan)
                                        </button>
                                      </h2>
                                      <div id="collapseKomponen" class="accordion-collapse collapse" aria-labelledby="headingKomponen" data-bs-parent="#accordionRelasiProduk">
                                        <div class="accordion-body" id="relasiKomponenBody">
                                          <p class="text-muted mb-0 small">Belum ada informasi komponen rakitan untuk produk ini.</p>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <hr class="my-4">

                          <!-- Section 2: Finishing  -->
                          <div class="section-item mb-4" id="sectionFinishing">
                            <div class="d-flex align-items-center mb-3">
                              <span class="section-number me-2">2</span>
                              <h6 class="mb-0 fw-bold">Finishing</h6>
                            </div>
                            <div class="ps-4">
                              <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="text-muted small mb-0">Tambahkan finishing yang diperlukan untuk pesanan ini</p>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="modalBtnTambahFinishing">
                                  <i class="fa fa-plus me-1"></i>Tambah Finishing
                                </button>
                              </div>
                              
                              <!-- Table Finishing -->
                              <!-- <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0" id="modalTabelFinishing">
                                  <thead class="table-light">
                                    <tr>
                                      <th>Nama Finishing</th>
                                      <th>Jumlah</th>
                                      <th>Harga Satuan</th>
                                      <th>Total</th>
                                      <th>Aksi</th>
                                    </tr>
                                  </thead>
                                  <tbody id="modalFinishingBody">
                                    <tr>
                                      <td colspan="5" class="text-center text-muted py-3">
                                        <i class="fa fa-cogs me-2"></i>Belum ada finishing yang ditambahkan
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div> -->

                              <!-- Accordion Finishing Items -->
                              <div class="accordion mt-3" id="accordionFinishingItems">
                                <div class="text-center text-muted py-3" id="noFinishingMessage">
                                  <i class="fa fa-cogs me-2"></i>Belum ada finishing yang ditambahkan
                                </div>
                              </div>

                              <!-- Total Finishing -->
                              <div class="bg-light rounded p-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                  <span class="fw-semibold">Total Biaya Finishing:</span>
                                  <span class="fw-bold text-primary" id="modalTotalFinishing">Rp 0</span>
                                </div>
                              </div>
                            </div>
                          </div>

                          <hr class="my-4">

                          <!-- Section 3: Files -->
                          <div class="section-item mb-4" id="sectionFiles">
                            <div class="d-flex align-items-center mb-3">
                              <span class="section-number me-2">3</span>
                              <h6 class="mb-0 fw-bold">Files</h6>
                            </div>
                            <div class="ps-4">
                              <!-- Tab untuk pilih sumber file -->
                              <ul class="nav nav-tabs nav-tabs-sm mb-3" id="fileSourceTabs">
                                <li class="nav-item">
                                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabFileLokal">
                                    <i class="fa fa-folder me-1"></i> File Lokal
                                  </button>
                                </li>
                                <li class="nav-item">
                                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabFileLAN">
                                    <i class="fa fa-network-wired me-1"></i> LAN Location
                                  </button>
                                </li>
                              </ul>
                              <div class="tab-content">
                                <!-- Tab File Lokal -->
                                <div class="tab-pane fade show active" id="tabFileLokal">
                                  <div class="dropzone-area border-2 border-dashed rounded p-4 text-center mb-3" id="modalDropZone">
                                    <i class="fa fa-cloud-upload fa-2x text-primary mb-2"></i>
                                    <p class="mb-1">Drag & drop file di sini atau <span class="text-primary fw-semibold" style="cursor:pointer;" id="modalBtnBrowseFile">klik untuk pilih file</span></p>
                                    <small class="text-muted">Maksimal 10MB per file. Format: PDF, JPG, PNG, AI, PSD, CDR</small>
                                    <input type="file" id="modalInputFiles" multiple accept=".pdf,.jpg,.jpeg,.png,.ai,.psd,.cdr" style="display:none;">
                                  </div>
                                </div>
                                <!-- Tab LAN Location -->
                                <div class="tab-pane fade" id="tabFileLAN">
                                  <div class="mb-3">
                                    <label class="form-label">Path LAN</label>
                                    <div class="input-group">
                                      <span class="input-group-text"><i class="fa fa-server"></i></span>
                                      <input type="text" class="form-control" id="modalLANPathInput" placeholder="\\192.168.1.100\shared\designs\file.pdf">
                                      <button type="button" class="btn btn-outline-primary" id="modalBtnAddLANFile">
                                        <i class="fa fa-plus"></i> Tambah
                                      </button>
                                    </div>
                                    <small class="text-muted">Contoh: \\SERVER\folder\namafile.pdf</small>
                                  </div>
                                </div>
                              </div>
                              <!-- Daftar File yang Diupload -->
                              <div class="uploaded-files-list" id="modalUploadedFilesList">
                                <!-- File items akan ditambahkan di sini via JS -->
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Right Panel: Section 4 - Detail Orderan (Summary) -->
                      <div class="col-lg-4 bg-light">
                        <div class="p-4 sticky-top" style="top: 0;">
                          <div class="d-flex align-items-center mb-3">
                            <span class="section-number me-2">4</span>
                            <h6 class="mb-0 fw-bold">Detail Orderan</h6>
                          </div>
                          
                          <div class="order-summary">
                            <!-- Produk Info -->
                            <div class="summary-item mb-3 pb-3 border-bottom">
                              <small class="text-muted text-uppercase">Produk</small>
                              <p class="mb-0 fw-semibold" id="summaryProduk">-</p>
                            </div>

                            <!-- Jumlah & Ukuran -->
                            <div class="summary-item mb-3 pb-3 border-bottom">
                              <div class="row">
                                <div class="col-6">
                                  <small class="text-muted text-uppercase">Jumlah</small>
                                  <p class="mb-0 fw-semibold" id="summaryJumlah">0 pcs</p>
                                </div>
                                <div class="col-6" id="summaryUkuranContainer" style="display: none;">
                                  <small class="text-muted text-uppercase">Ukuran</small>
                                  <p class="mb-0 fw-semibold" id="summaryUkuran">0 x 0 cm</p>
                                </div>
                              </div>
                            </div>

                            <!-- Deadline -->
                            <div class="summary-item mb-3 pb-3 border-bottom">
                              <small class="text-muted text-uppercase">Deadline</small>
                              <p class="mb-0 fw-semibold" id="summaryDeadline">-</p>
                            </div>

                            <!-- Finishing -->
                            <div class="summary-item mb-3 pb-3 border-bottom">
                              <small class="text-muted text-uppercase">Finishing</small>
                              <div id="summaryFinishingList" class="mt-1">
                                <span class="text-muted">-</span>
                              </div>
                            </div>

                            <!-- Files -->
                            <div class="summary-item mb-3 pb-3 border-bottom">
                              <small class="text-muted text-uppercase">Files</small>
                              <p class="mb-0 fw-semibold" id="summaryFiles">0 file</p>
                            </div>

                            <!-- Rincian Harga -->
                            <div class="summary-pricing mt-4">
                              <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal Cetak</span>
                                <span id="summarySubtotalCetak">Rp 0</span>
                              </div>
                              <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Biaya Finishing</span>
                                <span id="summaryBiayaFinishing">Rp 0</span>
                              </div>
                              <hr>
                              <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total</span>
                                <span class="fw-bold text-primary fs-5" id="summaryTotalAkhir">Rp 0</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                      <i class="fa fa-times me-1"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="modalBtnSimpanItem">
                      <i class="fa fa-plus me-1"></i> Tambah Item
                    </button>
                  </div>
                </div>
              </div>
            </div>

  <style>
    /* Style untuk input field yang sedang difokuskan */
    .form-control:focus {
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Style untuk preview image */
    .border-dashed {
      border-style: dashed !important;
      border-color: #dee2e6 !important;
    }

    /* Style untuk finishing options */
    .option-btn {
      transition: all 0.2s ease;
      font-size: 0.8rem;
      padding: 0.25rem 0.5rem;
    }

    .option-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .option-btn.active {
      background-color: #007bff !important;
      color: white !important;
      border-color: #007bff !important;
      box-shadow: 0 2px 4px rgba(0,123,255,0.3);
    }

    /* Style untuk preview box */
    #imagePreviewBox {
      transition: all 0.3s ease;
    }

    #imagePreviewBox:hover {
      border-color: #007bff !important;
      background-color: #f8f9fa !important;
    }

    /* Style untuk item cards */
    .item-card {
      transition: all 0.2s ease;
      border-color: #e9ecef !important;
    }

    .item-card:hover {
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-color: #007bff !important;
    }

    .item-card .btn-toggle-tugas {
      transition: all 0.2s ease;
    }

    .item-card .btn-toggle-tugas:hover {
      background-color: #007bff !important;
      color: white !important;
    }

    .item-card .collapse {
      border-top: 1px solid #e9ecef;
    }

    .item-card .collapse .bg-light {
      background-color: #f8f9fa !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .col-md-4 {
        margin-top: 1rem;
      }
    }

    /* Animation untuk collapse */
    .collapse {
      transition: all 0.3s ease;
    }

    /* Style untuk table dalam collapse */
    .item-card .table-sm th,
    .item-card .table-sm td {
      padding: 0.5rem;
      font-size: 0.875rem;
    }

    .item-card .table-sm .btn-sm {
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
    }

    /* Style untuk button aksi */
    .item-card .d-flex.gap-1 .btn-sm {
      padding: 0.375rem 0.5rem;
      font-size: 0.75rem;
      min-width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .item-card .d-flex.gap-1 .btn-sm i {
      font-size: 0.75rem;
    }

    /* Responsive untuk button aksi */
    @media (max-width: 768px) {
      .item-card .col-2:last-child {
        flex: 0 0 auto;
        width: auto;
        min-width: 120px;
      }
      
      .item-card .d-flex.gap-1 {
        gap: 0.25rem !important;
      }
      
      .item-card .d-flex.gap-1 .btn-sm {
        padding: 0.25rem 0.375rem;
        min-width: 28px;
        height: 28px;
      }
    }

    .section-number {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      background: #007bff;
      color: white;
      border-radius: 50%;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .dropzone-area {
      background: #f8fafc;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .dropzone-area:hover,
    .dropzone-area.dragover {
      background: #e8f4ff;
      border-color: #007bff !important;
    }

    .finishing-option {
      background: #fff;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 10px 15px;
      margin: 0 !important;
      transition: all 0.2s ease;
    }

    .finishing-option:hover {
      border-color: #007bff;
      box-shadow: 0 2px 4px rgba(0,123,255,0.1);
    }

    .finishing-option .form-check-input:checked ~ .form-check-label .badge {
      background: #007bff !important;
      color: white !important;
    }

    .uploaded-files-list .file-item {
      display: flex;
      align-items: center;
      padding: 10px 12px;
      background: #fff;
      border: 1px solid #e9ecef;
      border-radius: 6px;
      margin-bottom: 8px;
    }

    .uploaded-files-list .file-item .file-icon {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f0f4f8;
      border-radius: 6px;
      margin-right: 12px;
    }

    .uploaded-files-list .file-item .file-info {
      flex: 1;
    }

    .uploaded-files-list .file-item .file-name {
      font-weight: 500;
      font-size: 0.9rem;
    }

    .uploaded-files-list .file-item .file-meta {
      font-size: 0.75rem;
      color: #6c757d;
    }

    .uploaded-files-list .file-item .btn-remove-file {
      opacity: 0.5;
      transition: opacity 0.2s;
    }

    .uploaded-files-list .file-item:hover .btn-remove-file {
      opacity: 1;
    }

    .order-summary .summary-item small {
      font-size: 0.7rem;
      letter-spacing: 0.5px;
    }

    #modalTambahItemPesanan .modal-body {
      max-height: calc(100vh - 200px);
      overflow-y: auto;
    }

    #modalUrgentToggle.form-check-input:checked {
      background-color: #dc3545 !important; 
      border-color: #dc3545 !important;
    }

    #modalUrgentToggle.form-check-input:not(:checked) {
      background-color: #198754 !important; 
      border-color: #198754 !important;
    }

    .urgent-status {
        font-weight: 500;
        transition: color 0.3s ease;
        margin-left: 8px;
    }

    .form-check-input:checked ~ .form-check-label .urgent-status {
        color: #dc3545; 
    }

    .form-check-input:not(:checked) ~ .form-check-label .urgent-status {
        color: #198754; 
    }
  </style>
@endsection

@push('custom-scripts')
@include('backend.general-form.cari-all-produk', [
  'modalId' => 'modalCariProdukSPK',
  'inputId' => 'searchProdukSPK',
  'tableId' => 'tabelCariProdukSPK',
  'paginationId' => 'paginationCariProdukSPK',
  'clearBtnId' => 'clearSearchProdukSPK',
])
@include('backend.general-form.cari-pelanggan', [
  'modalId' => 'modalCariPelangganSPK',
  'inputId' => 'searchPelangganSPK',
  'tableId' => 'tabelCariPelangganSPK',
  'paginationId' => 'paginationCariPelangganSPK',
  'clearBtnId' => 'clearSearchPelangganSPK',
])
@include('backend.general-form.cari-produk-finishing', [
  'modalId' => 'modalCariProdukFinishingSPK',
  'inputId' => 'searchProdukFinishingSPK',
  'tableId' => 'tabelCariProdukFinishingSPK',
  'paginationId' => 'paginationProdukFinishingSPK',
  'clearBtnId' => 'clearSearchProdukFinishingSPK',
])
@include('backend.general-form.cari-karyawan', [
  'modalId' => 'modalCariKaryawanSPK',
  'inputId' => 'searchKaryawanSPK',
  'tableId' => 'tabelCariKaryawanSPK',
  'paginationId' => 'paginationCariKaryawanSPK',
  'clearBtnId' => 'clearSearchKaryawanSPK',
])
<script src="{{ asset('js/spk/spk-helper.js') }}"></script>
<script src="{{ asset('js/spk/form-create.js') }}"></script>
@endpush
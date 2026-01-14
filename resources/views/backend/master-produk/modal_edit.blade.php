<!-- Modal Edit Produk -->
<div class="modal fade" id="editProdukModal" tabindex="-1" aria-labelledby="editProdukModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProdukModalLabel">Edit Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editProdukForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_produk_id" name="produk_id">
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3" id="EditProdukTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="edit-tab-umum" data-bs-toggle="tab" data-bs-target="#edit-umum" type="button" role="tab">
                <i data-feather="user" class="me-1 icon-sm"></i> Detail Produk
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-tab-spesifikasi-teknis" data-bs-toggle="tab" data-bs-target="#edit-spesifikasi-teknis" type="button" role="tab">
                  <i data-feather="tool" class="me-1 icon-sm"></i> Spesifikasi Teknis
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-tab-harga" data-bs-toggle="tab" data-bs-target="#edit-harga" type="button" role="tab">
                <i data-feather="map-pin" class="me-1 icon-sm"></i> Harga
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-tab-alur-produksi" data-bs-toggle="tab" data-bs-target="#edit-alur-produksi" type="button" role="tab">
                <i data-feather="shopping-cart" class="me-1 icon-sm"></i> Alur Produksi
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-tab-media-dokumen" data-bs-toggle="tab" data-bs-target="#edit-media-dokumen" type="button" role="tab">
                <i data-feather="shopping-cart" class="me-1 icon-sm"></i> Media & Dokumen
              </button>
            </li>
          </ul>
          <div class="tab-content" id="EditProdukTabContent">
            <!-- Tab Detail Produk -->
            <div class="tab-pane fade show active" id="edit-umum" role="tabpanel">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_nama_produk" class="form-label">Nama Produk <span class="text-danger"> *</span></label>
                      <input type="text" class="form-control" id="edit_nama_produk" name="nama_produk" required>
                    </div>
                    <div class="col-md-6">
                      <label for="edit_kode_produk" class="form-label">Kode Produk</label>
                      <input type="text" class="form-control" id="edit_kode_produk" name="kode_produk" readonly>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_kategori_utama" class="form-label">Kategori Utama <span class="text-danger"> *</span></label>
                      <div class="input-group">
                        <select class="form-select" id="edit_kategori_utama" name="kategori_utama_id" required>
                          <option value="" selected disabled>Pilih Kategori Utama</option>
                          @foreach($kategoriProdukList as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama_detail_parameter }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label for="edit_sub_kategori_id_produk" class="form-label">Sub-Kategori</label>
                      <select class="form-select" id="edit_sub_kategori_id_produk" name="sub_kategori_id">
                        <option value="" selected disabled>Pilih sub-kategori</option>
                        <!-- Options diisi dinamis oleh JS -->
                      </select>
                      <small class="text-muted">Pengelompokan lebih detail dalam kategori yang sama</small>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="edit_satuanBarang" class="form-label">Satuan<span class="text-danger"> *</span></label>
                      <select class="form-select" id="edit_satuanBarang" name="satuan_id" required>
                        <option value="" selected disabled>Pilih satuan</option>
                        @foreach($satuanList as $detail)
                          <option value="{{ $detail->id }}">{{ $detail->nama_detail_parameter }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Dimensi</label>
                      <div class="row">
                        <div class="col-md-6">
                          <input type="number" class="form-control" id="edit_lebar" name="lebar" min="0" placeholder="Lebar (cm)">
                          <small class="text-muted">Lebar (cm)</small>
                        </div>
                        <div class="col-md-6">
                          <input type="number" class="form-control" id="edit_panjang" name="panjang" min="0" placeholder="Panjang (cm)">
                          <small class="text-muted">Panjang (cm)</small>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Metode Penjualan</label>
                        <div class="card p-2">
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="metode_penjualan" id="edit_jual_per_m2" value="m2">
                            <label class="form-check-label" for="edit_jual_per_m2">Dijual per m<sup>2</sup></label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input" type="radio" name="metode_penjualan" id="edit_jual_per_meter_lari" value="meter_lari">
                            <label class="form-check-label" for="edit_jual_per_meter_lari">Dijual per meter lari</label>
                          </div>
                          <small class="text-muted">Produk dijual berdasarkan luas total (panjang × lebar)</small>
                        </div>
                      </div>
                    
                  </div>
                  <div class="row mb-3">
                    <!-- HIDDEN INPUT UNTUK JSON -->
                    <input type="hidden" name="bahan_baku_json" id="edit_bahan_baku_json">
                    <input type="hidden" name="harga_bertingkat_json" id="edit_harga_bertingkat_json">
                    <input type="hidden" name="harga_reseller_json" id="edit_harga_reseller_json">
                    <input type="hidden" name="foto_pendukung_json" id="edit_foto_pendukung_json">
                    <input type="hidden" name="video_pendukung_json" id="edit_video_pendukung_json">
                    <input type="hidden" name="dokumen_pendukung_json" id="edit_dokumen_pendukung_json">
                    <input type="hidden" name="alur_produksi_json" id="edit_alur_produksi_json">
                    <input type="hidden" name="foto_pendukung_existing_json" id="edit_foto_pendukung_existing_json">
                    <input type="hidden" name="video_pendukung_existing_json" id="edit_video_pendukung_existing_json">
                    <input type="hidden" name="dokumen_pendukung_existing_json" id="edit_dokumen_pendukung_existing_json">
                    <input type="hidden" name="parameter_modal_json" id="edit_parameter_modal_json">
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-4">
                      <div class="form-check form-switch">
                        <input type="hidden" name="status_aktif" value="0">
                        <input class="form-check-input" type="checkbox" id="edit_status_aktif" name="status_aktif" value="1">
                        <label class="form-check-label" for="edit_status_aktif">Status Aktif</label>
                        <div><small class="text-muted">Produk akan tampil di daftar produk aktif</small></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Tab Harga -->
            <div class="tab-pane fade" id="edit-harga" role="tabpanel">
              <div class="card mb-0">
                <div class="card-body">
                  <ul class="nav nav-tabs nav-tabs-line mb-3" id="editLineTab" role="tablist">
                    <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="edit-modal-tab" data-bs-toggle="tab" data-bs-target="#edit-modal-tab-pane" type="button" role="tab">Modal</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="edit-harga-jual-tab" data-bs-toggle="tab" data-bs-target="#edit-harga-jual-tab-pane" type="button" role="tab">Harga Jual</button>
                    </li>
                  </ul>
                  <div class="tab-content" id="editHargaTabContent">
                    <!-- Total Modal Keseluruhan -->
                    <div class="alert alert-primary d-flex align-items-center mb-3" role="alert">
                        <div>
                          <div class="fw-semibold mb-1">
                            <i data-feather="info" class="me-2"></i>
                            Total Modal Keseluruhan
                            <span class="badge bg-light text-primary ms-2" id="editTotalItemModal">0 item</span>
                          </div>
                          <div class="fs-4 fw-bold" id="editTotalModalKeseluruhan">Rp 0</div>
                          <div class="small text-muted mt-1">
                            Bahan Baku: <span id="editTotalBahanBakuText" class="fw-bold">Rp 0</span>
                            &nbsp;|&nbsp;
                            Parameter: <span id="editTotalParameterText" class="fw-bold">Rp 0</span>
                          </div>
                        </div>
                    </div> 
                    <!-- Tab Modal -->
                    <div class="tab-pane fade show active" id="edit-modal-tab-pane" role="tabpanel">
                      <!-- Bahan Baku -->
                      <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="fw-semibold">Bahan Baku</div>
                          <button type="button" class="btn btn-sm btn-outline-primary" id="editBtnTambahBahan">
                            + Tambah Bahan
                          </button>
                        </div>
                        <div class="table-responsive">
                          <table class="table table-bordered align-middle mb-0" id="editTabelBahanBaku">
                            <thead class="table-light">
                              <tr>
                                <th>Nama Bahan</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Aksi</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada bahan baku ditambahkan</td>
                              </tr>
                            </tbody>
                            <tfoot>
                              <tr>
                                <td colspan="4" class="text-end fw-bold">Total Modal Bahan:</td>
                                <td colspan="2" class="fw-bold" id="editTotalModalBahan">Rp 0</td>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                      <!-- Parameter Modal -->
                      <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div class="fw-semibold">Parameter Modal</div>
                          <button type="button" class="btn btn-sm btn-outline-primary" id="editBtnTambahParameter">
                            + Tambah Parameter
                          </button>
                        </div>
                        <div class="table-responsive">
                          <table class="table table-bordered align-middle mb-0" id="editTabelParameterModal">
                            <thead class="table-light">
                              <tr>
                                <th>Nama Mesin</th>
                                <th>Nama Parameter</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Aksi</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="5" class="text-center text-muted">Pilih kategori parameter</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      
                    </div>
                    <!-- Tab Harga Jual Dinamis -->
                    <div class="tab-pane fade" id="edit-harga-jual-tab-pane" role="tabpanel">
                      <div class="card mb-3 border-2 border-primary-subtle" style="background: #f8f5ff;">
                        <div class="card-body">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                              <i data-feather="tag" class="me-2 text-primary"></i>
                              <span class="fw-semibold">Harga Umum (Bertingkat)</span>
                              <div class="small text-muted">Atur harga berdasarkan quantity minimum</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="editBtnTambahHargaBertingkat">+ Tambah Tingkat</button>
                          </div>
                          <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="editTabelHargaBertingkat">
                              <thead class="table-light">
                                <tr>
                                  <th>Min. Qty</th>
                                  <th>Max. Qty</th>
                                  <th>Harga (Rp)</th>
                                  <th>Profit Rp</th>
                                  <th>Profit %</th>
                                  <th></th>
                                </tr>
                              </thead>
                              <tbody>
                                <!-- Baris harga bertingkat akan diisi dinamis oleh JS -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="card mb-3 border-2 border-warning-subtle" style="background: #fffbe7;">
                        <div class="card-body">
                          <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                              <i data-feather="users" class="me-2 text-warning"></i>
                              <span class="fw-semibold">Harga Reseller (Bertingkat)</span>
                              <div class="small text-muted">Harga khusus untuk partner reseller</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-warning" id="editBtnTambahHargaReseller">+ Tambah Tingkat</button>
                          </div>
                          <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="editTabelHargaReseller">
                              <thead class="table-light">
                                <tr>
                                  <th>Min. Qty</th>
                                  <th>Max. Qty</th>
                                  <th>Harga (Rp)</th>
                                  <th>Profit Rp</th>
                                  <th>Profit %</th>
                                  <th></th>
                                </tr>
                              </thead>
                              <tbody>
                                <!-- Baris harga reseller akan diisi dinamis oleh JS -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Alur Produksi -->
            <div class="tab-pane fade" id="edit-alur-produksi" role="tabpanel">
              <div class="card mb-0">
                <div class="card-body">
                  <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <span class="fw-semibold">Alur Produksi</span>
                        <div class="small text-muted">Tentukan mesin yang digunakan untuk memproduksi item ini</div>
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-primary" id="editBtnTambahMesin">
                        + Tambah Mesin
                      </button>
                    </div>
                  </div>
                  <div id="editDaftarMesin">
                    <!-- Mesin akan ditambahkan secara dinamis di sini -->
                  </div>
                </div>
              </div>
            </div>
            <!-- Media & Dokumen Tab -->
            <div class="tab-pane fade" id="edit-media-dokumen" role="tabpanel" aria-labelledby="edit-media-dokumen-tab">
              <div class="card mb-3">
                <div class="card-body">
                  <h6 class="mb-3">Media (Foto & Video)</h6>
                  <div class="dropzone-area-media mb-3 text-center p-4 border-2 border-dashed rounded bg-light position-relative" id="editMediaDropzoneArea" style="cursor:pointer;">
                    <input type="file" class="d-none" id="editMediaPendukungInput" name="media_pendukung_new[]" multiple accept="image/*,video/*">
                    <div class="dz-message text-muted">
                      <i data-feather="upload-cloud" class="icon-lg mb-2"></i><br>
                      <span>Seret & lepas foto/video di sini atau klik untuk memilih file</span>
                      <div style="font-size:0.85rem;">Maksimal 10 file, format gambar/video didukung</div>
                    </div>
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-12 mb-1 text-start"><strong>Foto</strong></div>
                    <div class="col-12" id="editFotoPendukungPreview">
                      <div class="text-muted text-center" id="noEditFotoMessage">
                        <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang ditambahkan.
                      </div>
                    </div>
                  </div>
                  <div class="row g-2 mb-4">
                    <div class="col-12 mb-1 text-start"><strong>Video</strong></div>
                    <div class="col-12" id="editVideoPendukungPreview">
                      <div class="text-muted text-center" id="noEditVideoMessage">
                        <i data-feather="video" class="icon-lg mb-2"></i><br>Belum ada video yang ditambahkan.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card mb-0">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Dokumen Pendukung</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="editTambahDokumen"><i data-feather="plus" class="me-1 icon-sm"></i> Tambah Dokumen</button>
                  </div>
                  <p class="text-muted mb-3" style="font-size: 0.85rem;">Tambahkan dokumen pendukung seperti spesifikasi teknis, sertifikat, atau laporan uji.</p>
                  <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="editDokumenPendukungTable">
                      <thead>
                        <tr>
                          <th style="width: 25%;">Nama Dokumen</th>
                          <th style="width: 20%;">Jenis</th>
                          <th style="width: 15%;">Ukuran</th>
                          <th style="width: 10%;">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="editDokumenPendukungBody">
                        <tr id="noEditDokumenMessage">
                          <td colspan="5" class="text-center text-muted py-4">
                            <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <input type="file" class="d-none" id="editDokumenPendukungInput" name="dokumen_pendukung_new[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.csv,.jpg,.jpeg,.png,.gif">
                </div>
              </div>
            </div>

            <!-- Tab Spesifikasi Teknis -->
            <div class="tab-pane fade" id="edit-spesifikasi-teknis" role="tabpanel">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                            <i data-feather="info" class="me-2"></i>
                            <div>
                                <strong>Petunjuk:</strong>
                                <ul class="mb-0">
                                    <li>Tambahkan spesifikasi teknis sesuai kebutuhan produk</li>
                                    <li>Contoh: Dimensi, Berat, Warna, Material, dll</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Spesifikasi Teknis</h6>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="edit_tambah_spesifikasi_produk">
                                    <i data-feather="plus" class="icon-sm"></i> Tambah Spesifikasi
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="edit_spesifikasi_produk_container">
                                    <div class="text-muted text-center py-3" id="edit_no_spesifikasi_message">
                                        Belum ada spesifikasi teknis. Klik tombol "Tambah Spesifikasi" untuk menambahkan.
                                    </div>
                                </div>
                                <input type="hidden" name="spesifikasi_teknis_json" id="edit_spesifikasi_teknis_json" value="[]">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

<!-- Modal Preview Media (Foto/Video) -->
<div class="modal fade" id="editMediaPreviewModal" tabindex="-1" aria-labelledby="editMediaPreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editMediaPreviewModalLabel">Preview Media</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center" id="editMediaPreviewModalBody">
        <!-- Konten media akan diisi via JS -->
      </div>
    </div>
  </div>
</div> 

<!-- Modal Cari Bahan Baku untuk Edit -->
@include('backend.general-form.cari-bahanbaku', [
  'modalId' => 'modalCariBahanBakuProdukEdit',
  'inputId' => 'searchBahanBakuProdukEdit',
  'tableId' => 'tabelCariBahanBakuProdukEdit',
  'paginationId' => 'paginationBahanBakuProdukEdit',
  'clearBtnId' => 'clearSearchBahanBakuProdukEdit',
])
<!-- Modal Cari Mesin untuk Edit -->
@include('backend.general-form.cari-mesin', [
  'modalId' => 'modalCariMesinProdukEdit',
  'inputId' => 'searchMesinProdukEdit',
  'tableId' => 'tabelCariMesinProdukEdit',
  'paginationId' => 'paginationCariMesinProdukEdit',
  'clearBtnId' => 'clearSearchMesinProdukEdit',
]) 
@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Transaksi</a></li>
      <li class="breadcrumb-item"><a href="{{ route('spk.index') }}">SPK</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit SPK</li>
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

  <h4 class="fw-bold">Edit SPK ({{ $spk->nomor_spk ?? '-' }})</h4>
  <p class="text-muted mb-4">Ubah Surat Perintah Kerja untuk pelanggan.</p>

  <form id="formTambahSPK" action="{{ route('spk.update', $spk) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="items" id="itemsInput">
    <input type="hidden" name="invoice_groups" id="invoiceGroupsInput">
    <div class="row">
      <!-- Sidebar kiri: informasi pelanggan -->
      <div class="col-md-3">
        <div class="mb-4 p-3 border rounded bg-light">
          <div class="fw-semibold mb-2"><i class="fa fa-user me-1"></i> Informasi Pelanggan</div>
          <div class="mb-3">
            <label class="form-label">Pelanggan</label>
            <div class="input-group">
              <input type="text" class="form-control" id="namaCustomerInput" value="{{ old('customer_display', optional($spk->pelanggan)->nama . (optional($spk->pelanggan)->kode ? ' ['.optional($spk->pelanggan)->kode.']' : '')) }}" placeholder="Pilih pelanggan..." readonly
                style="background:#fff;cursor:pointer;">
              <input type="hidden" id="customerIdInput" name="customer_id"  value="{{ old('customer_id', $spk->pelanggan_id) }}">
              <input type="hidden" id="customerKategoriHarga" name="customer_kategori_harga" value="{{ old('customer_kategori_harga', optional($spk->pelanggan)->kategori_harga ?? 'Umum') }}">
              <button class="btn btn-outline-secondary" type="button" id="btnCariCustomer"><i
                  class="fa fa-search"></i></button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Tanggal SPK</label>
            <input type="date" class="form-control" name="tanggal_spk" id="tanggalSPK" value="{{ old('tanggal_spk', $spk->tanggal_spk->format('Y-m-d')) }}"
              required>
          </div>
        </div>
        <div class="mb-4 p-3 border rounded bg-light">
          <div class="fw-semibold mb-2"><i class="fa fa-sticky-note me-1"></i> Catatan</div>
          <textarea class="form-control" name="catatan" rows="3" placeholder="Tambahkan catatan untuk SPK ini">{{ old('catatan', $spk->catatan) }}</textarea>
        </div>
        <div class="p-3 border rounded bg-light">
          <div class="fw-semibold mb-2"><i class="fa fa-dollar-sign me-1"></i> Ringkasan Biaya</div>
          <div class="d-flex justify-content-between small mb-1"><span>Total Item:</span><span
              class="ringkasan-total-item">0</span></div>
          <div class="d-flex justify-content-between small mb-1"><span>Total Biaya:</span><span
              class="ringkasan-total-biaya">Rp 0</span></div>
          <hr class="my-2">
          <div class="d-flex justify-content-between fw-bold"><span>Total SPK:</span><span class="ringkasan-total-spk">Rp
              0</span></div>
        </div>
      </div>

      <!-- Kanan: Item Pekerjaan -->
      <div class="col-md-9">
        <div class="card border-0 shadow-none">
          <div class="card-body p-0">
            <div class="card border-0 shadow-sm">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="fa fa-list me-2"></i>Daftar Item Pekerjaan</h6>
                <div class="d-flex gap-2 align-items-center">
                  <button type="button" class="btn btn-outline-info mb-3" id="btnGroupItemsMode">
                    Group Items
                  </button>
                  <button type="button" class="btn btn-outline-primary mb-3" id="btnTambahItem">
                    <i class="fa fa-plus"></i> Tambah Item
                  </button>
                </div>
              </div>
              <div class="card-body p-3">
                <div class="row g-2 border-bottom bg-light fw-semibold small">
                  <div class="col-3 p-3">Nama Item</div>
                  <div class="col-2 p-3">Jumlah</div>
                  <div class="col-2 p-3">Satuan</div>
                  <div class="col-2 p-3">Ukuran</div>
                  <div class="col-2 p-3">Keterangan</div>
                  <div class="col-1 p-3 text-center">Aksi</div>
                </div>
                <div id="itemCardsContainer">
                  <div class="text-center text-muted py-4" id="noItemsMessage">
                    <i class="fa fa-list-alt fa-2x mb-2"></i>
                    <p class="mb-0">Belum ada item yang ditambahkan</p>
                  </div>
                </div>

                <div class="accordion mt-3" id="rekapAccordion">
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="rekapHeading">
                      <button class="accordion-button collapsed" type="button"
                              data-bs-toggle="collapse" data-bs-target="#rekapCollapse"
                              aria-expanded="false" aria-controls="rekapCollapse">
                        Rekap Produk
                      </button>
                    </h2>
                    <div id="rekapCollapse" class="accordion-collapse collapse"
                        aria-labelledby="rekapHeading" data-bs-parent="#rekapAccordion">
                      <div class="accordion-body">
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                              <tr>
                                <th>Produk</th>
                                <th>Kode</th>
                                <th class="text-end">Total Qty</th>
                                <th>Satuan</th>
                                <th class="text-end">Total Metric</th>
                                <th>Unit Metric</th>
                              </tr>
                            </thead>
                            <tbody id="rekapItemsBody">
                              <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data rekap</td>
                              </tr>
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
          </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
          <button type="button" class="btn btn-light" id="btnBatalSPK">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSimpanSPK">
            <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerSimpanSPK" role="status"
              aria-hidden="true"></span>
            <span class="label-simpan">Update SPK</span>
          </button>
        </div>
      </div>

    </div>
  </form>

  <!-- Modal Tambah/Edit Tugas Produksi -->
  <div class="modal fade" id="modalTugasProduksi" tabindex="-1" aria-labelledby="modalTugasProduksiLabel"
    aria-hidden="true">
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
              <input type="text" class="form-control" id="inputNamaTugas" placeholder="Cetak, Finishing, Desain, dll"
                required>
            </div>
            <div class="mb-3">
              <label class="form-label">Ditugaskan Kepada <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="text" class="form-control" id="inputDitugaskan" placeholder="Pilih karyawan..." readonly
                  required>
                <input type="hidden" id="inputDitugaskanId">
                <button type="button" class="btn btn-outline-secondary" id="btnCariKaryawan"><i
                    class="fa fa-search"></i></button>
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
              <textarea class="form-control" id="inputDeskripsi" rows="2"
                placeholder="Masukkan deskripsi tugas..."></textarea>
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
  <div class="modal fade" id="modalTambahItemPesanan" tabindex="-1" data-bs-backdrop="static"
    aria-labelledby="modalTambahItemPesananLabel" aria-hidden="true">
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
                          <input type="text" class="form-control" id="modalProdukSelect" placeholder="Pilih produk..."
                            readonly style="cursor: pointer; background-color: #fff;">
                          <input type="hidden" id="modalProdukId">
                          <button type="button" class="btn btn-outline-secondary" id="modalBtnCariProduk"
                            title="Cari Produk">
                            <i class="fa fa-search"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" id="modalKeteranganInput" rows="2"
                          placeholder="Masukkan keterangan pesanan..."></textarea>
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
                      <div class="col-md-4">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <div class="input-group">
                          <input type="number" class="form-control text-end" id="modalJumlahInput" min="0" step="0.01"
                            placeholder="0">
                          <span class="input-group-text" id="modalSatuanDisplay">pcs</span>
                        </div>
                      </div>
                      <div id="sectionUkuran" style="display: none;">
                        <div class="col-12">
                          <label class="form-label">Ukuran</label>
                          <div class="row g-2">
                            <div class="col-4">
                              <div class="input-group">
                                <span class="input-group-text">L</span>
                                <input type="number" class="form-control" id="modalLebarInput" min="0" step="0.1"
                                  value="0" placeholder="Lebar">
                                <span class="input-group-text" id="modalSatuanLebar">-</span>
                              </div>
                              <small class="text-muted mt-1" id="lebarStatus" style="display:none;"><i
                                  class="fa fa-lock"></i> Lebar terkunci sesuai produk</small>
                            </div>
                            <div class="col-4">
                              <div class="input-group">
                                <span class="input-group-text">P</span>
                                <input type="number" class="form-control" id="modalPanjangInput" min="0" step="0.1"
                                  value="0" placeholder="Panjang">
                                <span class="input-group-text" id="modalSatuanPanjang">-</span>
                              </div>
                              <small class="text-muted mt-1" id="panjangStatus" style="display:none;"><i
                                  class="fa fa-lock"></i> Panjang terkunci sesuai produk</small>
                            </div>
                            <div class="col-4" id="luasColumn">
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
                              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseBahanBaku" aria-expanded="false"
                                aria-controls="collapseBahanBaku">
                                Bahan Baku Terkait
                              </button>
                            </h2>
                            <div id="collapseBahanBaku" class="accordion-collapse collapse"
                              aria-labelledby="headingBahanBaku" data-bs-parent="#accordionRelasiProduk">
                              <div class="accordion-body" id="relasiBahanBakuBody">
                                <p class="text-muted mb-0 small">Belum ada informasi bahan baku untuk produk ini.</p>
                              </div>
                            </div>
                          </div>

                          <!-- Panel: Produk Komponen (Rakitan) -->
                          <div class="accordion-item">
                            <h2 class="accordion-header" id="headingKomponen">
                              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseKomponen" aria-expanded="false" aria-controls="collapseKomponen">
                                Produk Komponen (Rakitan)
                              </button>
                            </h2>
                            <div id="collapseKomponen" class="accordion-collapse collapse"
                              aria-labelledby="headingKomponen" data-bs-parent="#accordionRelasiProduk">
                              <div class="accordion-body" id="relasiKomponenBody">
                                <p class="text-muted mb-0 small">Belum ada informasi komponen rakitan untuk produk ini.
                                </p>
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
                    <span class="section-number me-2">3</span>
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

                <div class="section-item mb-4" id="sectionFiles">
                  <div class="d-flex align-items-center mb-3">
                    <span class="section-number me-2"></span>
                    <h6 class="mb-0 fw-bold">Files Path</h6>
                  </div>
                  <div class="ps-4">
                    <div class="mb-3">
                      <p class="text-muted small mb-3">Tentukan path file desain atau file pendukung lainnya.</p>
                      <button type="button" class="btn btn-outline-primary w-100 py-3 border-2 border-dashed"
                        id="btnOpenExplorerModalItem">
                        <i class="fa fa-folder-open fa-2x mb-2 d-block"></i>
                        <span>Pilih File dari Drive</span>
                      </button>
                    </div>
                    <!-- <ul class="nav nav-tabs nav-tabs-sm mb-3" id="fileSourceTabs">
                                                                              <li class="nav-item">
                                                                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabFileLokal">
                                                                                  <i class="fa fa-folder me-1"></i> File Lokal
                                                                                </button>
                                                                              </li>
                                                                            </ul>
                                                                            <div class="tab-content">
                                                                              <div class="tab-pane fade show active" id="tabFileLokal">
                                                                                <div class="dropzone-area border-2 border-dashed rounded p-4 text-center mb-3" id="modalDropZone">
                                                                                  <i class="fa fa-cloud-upload fa-2x text-primary mb-2"></i>
                                                                                  <p class="mb-1">Drag & drop file di sini atau <span class="text-primary fw-semibold" style="cursor:pointer;" id="modalBtnBrowseFile">klik untuk pilih file</span></p>
                                                                                  <small class="text-muted">Maksimal 10MB per file. Format: PDF, JPG, PNG, AI, PSD, CDR</small>
                                                                                  <input type="file" id="modalInputFiles" multiple accept=".pdf,.jpg,.jpeg,.png,.ai,.psd,.cdr" style="display:none;">
                                                                                </div>
                                                                              </div>
                                                                            </div> -->
                    <!-- Daftar File yang Diupload -->
                    <div class="uploaded-files-list mt-3" id="modalUploadedFilesList">
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
                  <span class="section-number me-2">2</span>
                  <h6 class="mb-0 fw-bold">Detail Orderan</h6>
                </div>

                {{-- Tab navigation --}}
                <ul class="nav nav-tabs nav-tabs-line mb-2" id="detailOrderanTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link bg-light active" id="tab-preview-orderan" data-bs-toggle="tab"
                      data-bs-target="#tab-preview-orderan-pane" type="button" role="tab">Preview File</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link bg-light" id="tab-summary-orderan" data-bs-toggle="tab"
                      data-bs-target="#tab-summary-orderan-pane" type="button" role="tab">Summary</button>
                  </li>
                </ul>

                <div class="tab-content" id="detailOrderanTabContent">
                  {{-- Tab 1: Summary Produk Orderan --}}
                  <div class="tab-pane fade" id="tab-summary-orderan-pane" role="tabpanel">
                    <div class="order-summary">
                      <!-- Produk Info -->
                      <div class="summary-item mb-3 pb-3 border-bottom">
                        <small class="text-muted text-uppercase">Produk</small>
                        <p class="mb-0 fw-semibold" id="summaryProduk">-</p>
                      </div>

                      <!-- Jumlah & Ukuran -->
                      <div class="summary-item mb-3 pb-3 border-bottom">
                        <div class="row">
                          <div class="col-6" id="summaryUkuranContainer" style="display: none;">
                            <small class="text-muted text-uppercase">Ukuran</small>
                            <p class="mb-0 fw-semibold" id="summaryUkuran">0 x 0 cm</p>
                          </div>
                          <div class="col-6">
                            <small class="text-muted text-uppercase">Jumlah</small>
                            <p class="mb-0 fw-semibold" id="summaryJumlah">0 pcs</p>
                          </div>
                          <div class="col-6">
                            <small class="text-muted text-uppercase d-block mt-1">Harga @</small>
                            <p class="mb-0 fw-semibold" id="summaryHargaBase">Rp 0</p>
                          </div>
                          <div class="col-6" id="summaryHargaPerSatuanContainer" style="display:none;">
                            <small class="text-muted text-uppercase d-block mt-1">Harga per <span
                                id="summaryHargaPerSatuanLabel">satuan</span></small>
                            <p class="mb-0 fw-semibold" id="summaryHargaPerSatuan">Rp 0</p>
                          </div>
                        </div>
                      </div>

                      <!-- Finishing -->
                      <div class="summary-item mb-3 pb-3 border-bottom">
                        <small class="text-muted text-uppercase">Finishing</small>
                        <div id="summaryFinishingList" class="mt-1">
                          <span class="text-muted">-</span>
                        </div>
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

                      <!-- Deadline -->
                      <div class="summary-item mt-3 mb-3 pb-3 border-bottom">
                        <small class="text-muted text-uppercase">Deadline</small>
                        <p class="mb-0 fw-semibold" id="summaryDeadline">-</p>
                      </div>

                      <!-- Files -->
                      <div class="summary-item mb-3 pb-3 border-bottom">
                        <small class="text-muted text-uppercase">Files</small>
                        <p class="mb-0 fw-semibold" id="summaryFiles">0 file</p>
                      </div>
                    </div>
                  </div>

                  {{-- Tab 2: Preview File Default --}}
                  <div class="tab-pane fade show active" id="tab-preview-orderan-pane" role="tabpanel">
                    <div class="mb-3" id="explorerOpenStandalone">
                      <button type="button" class="btn btn-primary btn-sm" data-action="open-explorer-item"
                        title="Pilih File Default" aria-label="Pilih File Default">
                        <i class="fa fa-folder-open"></i>
                      </button>
                    </div>
                    {{-- Controls untuk File Gambar --}}
                    <div id="imageFileControls" class="mb-3" style="display: none;">
                      <div class="d-flex align-items-center gap-2 mb-2">
                        <button type="button" class="btn btn-primary btn-sm" data-action="open-explorer-item"
                          title="Pilih File Default" aria-label="Pilih File Default">
                          <i class="fa fa-folder-open"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" id="btnRotateImage"
                          title="Rotate & Tukar Panjang/Lebar">
                          <i class="fa fa-undo"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" id="btnSyncImageDimensions"
                          title="Sinkronkan Dimensi ke Input Produk">
                          <i class="fa fa-exchange-alt"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-dark" id="btnImageTools" title="Image Tools">
                          <i class="fa fa-wrench"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="btnOpenFolderLocation"
                            title="Buka Lokasi File di Windows Explorer">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                      </div>
                      <div class="row mb-3">
                          <div class="col-4">
                            <label class="form-label small mb-1">Lebar (<span id="fileImageWidthUnit">cm</span>)</label>
                            <input type="text" class="form-control form-control-sm" id="fileImageWidth" readonly>
                          </div>
                          <div class="col-4">
                            <label class="form-label small mb-1">Panjang (<span id="fileImageHeightUnit">cm</span>)</label>
                            <input type="text" class="form-control form-control-sm" id="fileImageHeight" readonly>
                          </div>
                          <div class="col-4">
                            <label class="form-label small mb-1">Luas (<span id="fileImageAreaUnit">cm</span>²)</label>
                            <input type="text" class="form-control form-control-sm" id="fileImageArea" readonly>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <div class="col-12">
                              <div class="d-flex align-items-center gap-2">
                                  <select class="form-select form-select-sm" id="quickTemplateSelect" style="max-width: 250px;">
                                      <option value="">-- Pilih template finishing --</option>
                                  </select>
                                  <button type="button"
                                      class="btn btn-sm btn-primary"
                                      id="btnQuickApplyTemplate">
                                      <i class="fa fa-magic me-1"></i> Apply Template
                                  </button>
                              </div>
                          </div>
                      </div>
                    </div>

                    {{-- Controls untuk File PDF --}}
                    <div id="pdfFileControls" class="mb-3" style="display: none;">
                      <div class="row g-2">
                        <div class="col-4">
                          <label class="form-label small mb-1">Halaman</label>
                          <input type="text" class="form-control form-control-sm" id="filePdfPages" readonly>
                        </div>
                        <div class="col-4">
                          <label class="form-label small mb-1">Jumlah/Qty</label>
                          <input type="number" class="form-control form-control-sm" id="filePdfQty" min="1" step="1"
                            value="1">
                        </div>
                        <div class="col-4">
                          <label class="form-label small mb-1">Summary</label>
                          <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="filePdfSummary" readonly>
                            <span class="input-group-text" id="filePdfSummaryUnit">-</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="orderanPreviewContainer"
                      class="min-vh-50 d-flex align-items-center justify-content-center bg-dark bg-opacity-10 rounded p-3"
                      style="min-height: 280px;">
                      <p class="text-muted small mb-0 text-center">Belum ada file default. Upload file dan set sebagai
                        default untuk melihat preview.</p>
                    </div>
                    <div id="orderanPreviewFileInfo" class="mt-2 small text-muted" style="min-height: 0;"></div>
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
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .option-btn.active {
      background-color: #007bff !important;
      color: white !important;
      border-color: #007bff !important;
      box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
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
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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
      box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
    }

    .finishing-option .form-check-input:checked~.form-check-label .badge {
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

    .uploaded-files-list .file-item .file-path {
      font-size: 0.7rem;
      color: #adb5bd;
      max-width: 100%;
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

    .form-check-input:checked~.form-check-label .urgent-status {
      color: #dc3545;
    }

    .form-check-input:not(:checked)~.form-check-label .urgent-status {
      color: #198754;
    }
  </style>
  <!-- Modal File Explorer -->
  <div class="modal fade" id="modalFileExplorer" tabindex="-1" aria-labelledby="modalFileExplorerLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalFileExplorerLabel"><i class="fa fa-search me-2"></i>Pilih File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div class="explorer-container">
            <div class="explorer-address-bar p-2 bg-light border-bottom d-flex align-items-center">
              <button class="btn btn-sm btn-outline-secondary me-2" id="btnExplorerBack" title="Kembali">
                <i class="fa fa-arrow-left"></i>
              </button>
              <input type="text" class="form-control form-control-sm" id="inputExplorerPath" readonly>
            </div>
            <div class="explorer-content" id="explorerContent" style="height: 400px; overflow-y: auto;">
              <!-- Content loaded via AJAX -->
              <div class="p-4 text-center text-muted">
                <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                <p>Memuat direktori...</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <div class="text-muted small">
            <span id="selectedFileCount">0</span> file dipilih
          </div>
          <div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="btnPilihFileExplorer" disabled>
              <i class="fa fa-check me-1"></i> Pilih File
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Image Tools -->
  <div class="modal fade" id="modalImageTools" tabindex="-1" aria-labelledby="modalImageToolsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-info">
          <h5 class="modal-title" id="modalImageToolsLabel"><i class="fa fa-magic me-2"></i>Image Tools</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Kolom Kiri: Kontrol/Setting -->
            <div class="col-lg-9">
              <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                <select class="form-select form-select-sm" id="imageToolTemplateSelect" style="max-width: 200px;">
                  <option value="">-- Pilih template --</option>
                </select>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnUpdateImageToolsTemplate">
                  <i class="fa fa-save me-1"></i> Update Template
                </button>
              </div>
              <div class="d-flex flex-wrap gap-3 mb-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="imageToolAktifTeksPesan">
                  <label class="form-check-label small" for="imageToolAktifTeksPesan">Aktifkan Teks & Pesan</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="imageToolAktifDuplikasiLayout">
                  <label class="form-check-label small" for="imageToolAktifDuplikasiLayout">Aktifkan Duplikasi & Layout</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="imageToolAktifKanvasLatar">
                  <label class="form-check-label small" for="imageToolAktifKanvasLatar">Aktifkan Kanvas & Latar</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="imageToolAktifPlong">
                  <label class="form-check-label small" for="imageToolAktifPlong">Aktifkan Plong</label>
                </div>
              </div>
              <ul class="nav nav-tabs" id="imageToolsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="tab-teks" data-bs-toggle="tab" data-bs-target="#pane-teks"
                    type="button" role="tab">Teks & Pesan</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="tab-layout" data-bs-toggle="tab" data-bs-target="#pane-layout"
                    type="button" role="tab">Duplikasi & Layout</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="tab-kanvas" data-bs-toggle="tab" data-bs-target="#pane-kanvas"
                    type="button" role="tab">Kanvas & Latar</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="tab-plong" data-bs-toggle="tab" data-bs-target="#pane-plong" type="button"
                    role="tab">Plong</button>
                </li>
              </ul>
              <div class="tab-content border border-top-0 p-3" id="imageToolsTabContent">
                <!-- Tab 1: Teks & Pesan -->
                <div class="tab-pane fade show active" id="pane-teks" role="tabpanel">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="imageToolPesan" class="form-label">Pesan</label>
                        <input type="text" class="form-control" id="imageToolPesan" placeholder="Masukkan pesan" value="POLOS">

                        <div class="form-check mt-2">
                          <input class="form-check-input" type="checkbox" id="imageToolSingleLeftMessage">
                          <label class="form-check-label small" for="imageToolSingleLeftMessage">
                            1 pesan saja (kiri)
                          </label>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="imageToolUkuranPesan" class="form-label">Ukuran Pesan (pt)</label>
                        <input type="number" class="form-control" id="imageToolUkuranPesan" placeholder="Misal 1.5"
                          step="0.1" value="0.8">
                      </div>
                      <div class="mb-3">
                        <label for="imageToolWarnaPesan" class="form-label">Warna Pesan</label>
                        <input type="color" class="form-control form-control-color" id="imageToolWarnaPesan"
                          title="Pilih warna" value="#000000">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="imageToolPosX" class="form-label">Posisi X (cm)</label>
                        <input type="number" class="form-control" id="imageToolPosX" placeholder="Posisi X (cm)" step="0.05" min="0"
                          max="1" value="1">
                      </div>
                      <div class="mb-3">
                        <label for="imageToolPosY" class="form-label">Posisi Y (cm)</label>
                        <input type="number" class="form-control" id="imageToolPosY" placeholder="Posisi Y (cm)" step="0.05" min="0"
                          max="1" value="3">
                      </div>
                      <div class="mb-3">
                        <label for="imageToolRotasiPesan" class="form-label">Orientasi Pesan</label>
                        <select class="form-select" id="imageToolRotasiPesan">
                          <option value="0">Horizontal</option>
                          <option value="90">Vertical</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tab 2: Duplikasi & Layout -->
                <div class="tab-pane fade" id="pane-layout" role="tabpanel">
                  <div class="row">
                    <div class="col-md-6">
                      <h6 class="fw-semibold mb-2">Duplikasi (Copy)</h6>
                      <div class="mb-3">
                        <label for="imageToolCopyX" class="form-label">Jumlah Kolom (X)</label>
                        <input type="number" class="form-control" id="imageToolCopyX" placeholder="Masukkan jumlah kolom"
                          min="1" value="2">
                      </div>
                      <div class="mb-3">
                        <label for="imageToolCopyY" class="form-label">Jumlah Baris (Y)</label>
                        <input type="number" class="form-control" id="imageToolCopyY" placeholder="Masukkan jumlah baris"
                          min="1" value="2">
                      </div>
                      <div class="mb-3">
                        <label for="imageToolRotasiCopy" class="form-label">Orientasi Tiap Gambar</label>
                        <select class="form-select" id="imageToolRotasiCopy">
                          <option value="0">Original</option>
                          <option value="180">Flip</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <h6 class="fw-semibold mb-2">Jarak Antar Gambar</h6>
                      <div class="mb-3">
                        <label for="imageToolJarakX" class="form-label">Jarak Horizontal (cm)</label>
                        <input type="number" class="form-control" id="imageToolJarakX" placeholder="Masukkan jarak"
                          step="0.1" value="0">
                      </div>
                      <div class="mb-3">
                        <label for="imageToolJarakY" class="form-label">Jarak Vertikal (cm)</label>
                        <input type="number" class="form-control" id="imageToolJarakY" placeholder="Masukkan jarak"
                          step="0.1" value="0">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tab 3: Kanvas & Latar -->
                <div class="tab-pane fade" id="pane-kanvas" role="tabpanel">
                  <div class="row">
                    <div class="col-md-6">
                      <h6 class="fw-semibold mb-3 text-center">Lebihan</h6>
                      <div class="d-flex flex-column align-items-center">
                      <div class="input-group mb-3" style="max-width: 220px;">
                          <span class="input-group-text" style="width: 110px;">Lebihan Keliling</span>
                          <input type="number" class="form-control text-center" id="imageToolLebihanKeliling"
                            placeholder="cm" step="0.1" title="Nilai untuk seluruh sisi">
                          <button type="button" class="btn btn-outline-secondary" id="btnLebihanKelilingKunci"
                            title="Isi seluruh lebihan (atas, kiri, bawah, kanan) dengan nilai ini">
                            <i class="fa fa-lock"></i>
                          </button>
                        </div>
                        <div class="mb-2" style="width:120px;">
                          <input type="number" 
                                class="form-control text-center" 
                                id="imageToolLebihanAtas" 
                                placeholder="cm" 
                                step="0.1" value="2.5">
                        </div>
                        <div class="d-flex align-items-center justify-content-center gap-3">
                          <div style="width:120px;">
                            <input type="number" 
                                  class="form-control text-center" 
                                  id="imageToolLebihanKiri" 
                                  placeholder="cm" 
                                  step="0.1" value="2.5">
                          </div>

                          <!-- FOTO PREVIEW -->
                          <div id="imageToolsPreviewLebihan" style="width:80px; height:100px; border:1px dashed #ccc; display:flex; align-items-center; justify-content:center; background-color: #f8f9fa;">
                            <p class="text-muted small text-center mb-0" style="font-size: 0.7rem;">Preview</p>
                          </div>

                          <div style="width:120px;">
                            <input type="number" 
                                  class="form-control text-center" 
                                  id="imageToolLebihanKanan" 
                                  placeholder="cm" 
                                  step="0.1" value="2.5">
                          </div>
                        </div>

                        <div class="mt-2" style="width:120px;">
                          <input type="number" 
                                class="form-control text-center" 
                                id="imageToolLebihanBawah" 
                                placeholder="cm" 
                                step="0.1" value="2.5">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <h6 class="fw-semibold mb-2">Latar & Garis</h6>
                      <div class="row mb-3">
                        <div class="col-md-6">
                          <label for="imageToolWarnaLatar" class="form-label">
                            Warna Latar
                          </label>
                          <input type="color"
                                class="form-control form-control-color w-100"
                                id="imageToolWarnaLatar"
                                title="Pilih warna" value="#ffffff">
                        </div>

                        <div class="col-md-6">
                          <label for="imageToolWarnaGaris" class="form-label">
                            Warna Garis
                          </label>
                          <input type="color"
                                class="form-control form-control-color w-100"
                                id="imageToolWarnaGaris"
                                title="Pilih warna" value="#000000">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="imageToolUkuranGaris" class="form-label">Ukuran Garis (cm)</label>
                        <input type="number" class="form-control" id="imageToolUkuranGaris" placeholder="Masukkan ukuran"
                          step="0.01" value="0.1">
                      </div>
                      <div class="mb-3">
                        <label for="imageToolImageScale" class="form-label">Skala Gambar (%)</label>
                        <div class="input-group">
                          <input type="number" class="form-control" id="imageToolImageScale" placeholder="100" min="1" max="500" step="1" value="100">
                          <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Atur skala gambar terhadap ukuran asli (100% = ukuran asli).</small>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tab 4: Plong -->
                <div class="tab-pane fade" id="pane-plong" role="tabpanel">
                  <div class="row">
                    <!-- Kolom 1: Jenis, Bentuk, Warna -->
                    <div class="col-md-6">
                      <h6 class="fw-semibold mb-2">Jenis & Bentuk</h6>
                      <input type="hidden" id="imageToolJenisPlong" value="plong_per_jarak">
                      <div class="mb-3">
                        <label for="imageToolBentukPlong" class="form-label">Bentuk</label>
                        <select class="form-select" id="imageToolBentukPlong">
                          <option value="">Pilih bentuk</option>
                          <option value="circle">●</option>
                          <option value="square">■</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="imageToolWarnaPlong" class="form-label">Warna Plong</label>
                        <input type="color" class="form-control form-control-color" id="imageToolWarnaPlong" title="Pilih warna" value="#ffffff">
                      </div>
                    </div>

                    <!-- Kolom 2: Jarak dari tepi -->
                    <div class="col-md-6">
                      <div class="row g-2">
                        <h6 class="fw-semibold mb-2">Jarak Plong dari Tepi (cm)</h6>
                        <div class="input-group mb-2">
                          <span class="input-group-text" style="width: 80px;">Jarak Plong</span>
                          <input type="number" class="form-control" id="imageToolJarakPlong" placeholder="cm" step="0.1" value="2">
                          <span class="input-group-text">cm</span>
                        </div>

                        <h6 class="fw-semibold mb-2">Ukuran Plong</h6>
                        <div class="input-group mb-2">
                          <span class="input-group-text" style="width: 120px;">Diameter Lebar</span>
                          <input type="number" class="form-control" id="imageToolDiameterLebar" placeholder="cm" step="0.1" value="1">
                          <span class="input-group-text">cm</span>
                        </div>
                        <div class="input-group mb-3" id="imageToolDiameterPanjangContainer">
                          <span class="input-group-text" style="width: 120px;">Diameter Panjang</span>
                          <input type="number" class="form-control" id="imageToolDiameterPanjang" placeholder="cm" step="0.1">
                          <span class="input-group-text">cm</span>
                        </div>
                      </div>
                    </div>

                    <!-- Kolom 3: Ukuran & jumlah plong -->
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3 text-center">Plong</h6>
                        <div class="p-3">
                          <div class="d-flex flex-column align-items-center">
                            <div class="mb-2" style="width:100px;">
                              <input type="number" 
                                    class="form-control form-control-sm text-center" 
                                    id="imageToolPlongAtas" 
                                    min="0" 
                                    step="1" 
                                    placeholder="0">
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                              <div style="width:100px;">
                                <input type="number" 
                                      class="form-control form-control-sm text-center" 
                                      id="imageToolPlongKiri" 
                                      min="0" 
                                      step="1" 
                                      placeholder="0">
                              </div>

                              <!-- FOTO PREVIEW -->
                              <div id="imageToolsPreviewPlong" style="width:80px; height:100px; border:1px dashed #ccc; display:flex; align-items-center; justify-content:center; background-color: #f8f9fa;">
                                <p class="text-muted small text-center mb-0" style="font-size: 0.7rem;">Preview</p>
                              </div>
                              <div style="width:100px;">
                                <input type="number" 
                                      class="form-control form-control-sm text-center" 
                                      id="imageToolPlongKanan" 
                                      min="0" 
                                      step="1" 
                                      placeholder="0">
                              </div>
                            </div>
                            <div class="mt-2" style="width:100px;">
                              <input type="number" 
                                    class="form-control form-control-sm text-center" 
                                    id="imageToolPlongBawah" 
                                    min="0" 
                                    step="1" 
                                    placeholder="0">
                            </div>
                          </div>
                          <small class="text-muted d-block mt-3 text-center">
                            Jumlah lubang plong di setiap sisi.
                          </small>
                          <div class="mt-3">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" id="imageToolPlongLipat4">
                              <label class="form-check-label small" for="imageToolPlongLipat4">
                                Lipat Plong 4
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" id="imageToolPlongAtasPerM">
                              <label class="form-check-label small" for="imageToolPlongAtasPerM">
                                Atas per m
                              </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" id="imageToolPlongBawahPojok">
                              <label class="form-check-label small" for="imageToolPlongBawahPojok">
                                Bawah Pojok
                              </label>
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Kolom Kanan: Preview -->
            <div class="col-lg-3">
              <div class="sticky-top" style="top: 15px;">
                <h6 class="fw-semibold mb-2">Preview</h6>
                <div id="imageToolsPreviewContainer"
                  class="d-flex align-items-center justify-content-center bg-light rounded border"
                  style="min-height: 400px;">
                  <p class="text-muted small text-center">Preview akan ditampilkan di sini</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <input type="text"
              class="form-control form-control-sm"
              id="imageToolTemplateName"
              placeholder="Nama template finishing"
              style="max-width: 220px;">
            <button type="button"
              class="btn btn-sm btn-outline-secondary"
              id="btnSaveImageToolsTemplate">
              <i class="fa fa-save me-1"></i> Save Template
            </button>
          </div>

          <div>
            <button type="button" class="btn btn-danger" id="btnResetImageTools">Reset</button>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="btnApplyImageTools">Terapkan Perubahan</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalInvoiceGroup" tabindex="-1" aria-labelledby="modalInvoiceGroupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="formInvoiceGroup">
          <div class="modal-header">
            <h5 class="modal-title" id="modalInvoiceGroupLabel">
              <i class="fa fa-object-group me-1"></i> Buat Group Item Invoice
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info py-2 small mb-3" id="invoiceGroupItemInfo">
              <span class="fw-semibold" id="invoiceGroupItemCount">0</span> item akan dimasukkan ke group ini.
            </div>

            <div class="mb-3">
              <label class="form-label">Nama Group <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="invoiceGroupName" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Keterangan</label>
              <textarea class="form-control" id="invoiceGroupDescription" rows="2" placeholder="Keterangan tambahan untuk invoice (opsional)"></textarea>
            </div>

            <div class="row g-2">
              <div class="col-md-4">
                <label class="form-label">Qty Group <span class="text-danger">*</span></label>
                <input type="number" class="form-control text-end" id="invoiceGroupQty" min="0.01" step="0.01" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Harga Satuan Group <span class="text-danger">*</span></label>
                <input type="number" class="form-control text-end" id="invoiceGroupPrice" min="0" step="0.01" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Total Group</label>
                <input type="text" class="form-control text-end bg-light" id="invoiceGroupTotal" readonly>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save me-1"></i> Simpan Group
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <style>
    .explorer-item {
      display: flex;
      align-items: center;
      padding: 8px 15px;
      border-bottom: 1px solid #f1f1f1;
      cursor: pointer;
      transition: background 0.2s;
    }

    .explorer-item:hover {
      background-color: #e8f4ff;
    }

    .explorer-item .icon {
      width: 30px;
      font-size: 1.2rem;
      text-align: center;
      margin-right: 12px;
    }

    .explorer-item .name {
      flex: 1;
      font-size: 0.9rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .explorer-item .meta {
      font-size: 0.75rem;
      color: #999;
      width: 100px;
      text-align: right;
    }

    .file-icon {
      color: #6c757d;
    }

    .folder-icon {
      color: #ffca28;
    }

    /* Custom style for dashed button */
    .border-dashed {
      border-style: dashed !important;
    }

    .uploaded-files-list .file-item {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 10px;
      margin-bottom: 8px;
    }

    #btnSyncImageDimensions:disabled {
      background-color: #6c757d !important;
      border-color: #6c757d !important;
      color: #fff !important;
      opacity: 0.8;
      cursor: not-allowed;
    }
    
    .item-card:hover{
      background:#fafafa;
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

  @php
    $spkEditInitialItems = $spk->items->map(function ($item) {
        $produk = $item->produk;
        $rawProduk = null;
        if ($produk) {
            $rawProduk = [
                'id'                    => $produk->id,
                'nama_produk'           => $produk->nama_produk,
                'kode_produk'           => $produk->kode_produk ?? null,
                'is_metric'             => (bool) $produk->is_metric,
                'metric_unit'           => $produk->metric_unit ?? 'cm',
                'panjang_locked'        => (bool) ($produk->panjang_locked ?? false),
                'lebar_locked'          => (bool) ($produk->lebar_locked ?? false),
                'harga_bertingkat_json' => $produk->harga_bertingkat_json ?? [],
                'harga_reseller_json'   => $produk->harga_reseller_json ?? [],
                'satuan_nama'           => optional($produk->satuan)->nama_detail_parameter ?? $item->satuan ?? 'pcs',
            ];
        }

        return [
            'id'              => $item->id,
            'produk_id'       => $item->produk_id,
            'nama_produk'     => $item->nama_produk,
            'jumlah'          => (float) $item->jumlah,
            'satuan'          => $item->satuan,
            'deadline'        => optional($item->deadline)->format('Y-m-d\TH:i'),
            'is_urgent'       => (bool) $item->is_urgent,
            'keterangan'      => $item->keterangan,
            'lebar'           => $item->lebar !== null ? (float) $item->lebar : null,
            'panjang'         => $item->panjang !== null ? (float) $item->panjang : null,
            'biaya_produk'    => (float) $item->biaya_produk,
            'biaya_finishing' => (float) $item->biaya_finishing,
            'tugasProduksi'   => $item->tugas_produksi ?? [],
            'filePendukung'   => $item->file_pendukung ?? [],
            'files'           => $item->file_pendukung ?? [],
            'tipe_finishing'  => $item->tipe_finishing ?? [],
            'raw_produk'      => $rawProduk,
        ];
    });
  @endphp
  <script>
    window.SPK_EDIT_INITIAL = {
      customer: {
        id: {{ (int) $spk->pelanggan_id }},
        nama: @json(optional($spk->pelanggan)->nama),
        kode: @json(optional($spk->pelanggan)->kode),
        kategori_harga: @json(optional($spk->pelanggan)->kategori_harga ?? 'Umum'),
      },
      items: @json($spkEditInitialItems),
      invoiceGroups: @json($spk->invoice_groups ?? []),
    };
  </script>
  <script src="{{ asset('js/spk/spk-helper.js') }}"></script>
  <script src="{{ asset('js/spk/form-create.js') }}"></script>
@endpush
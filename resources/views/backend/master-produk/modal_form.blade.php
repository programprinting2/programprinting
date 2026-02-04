<div class="modal fade" id="tambahProduk" tabindex="-1" aria-labelledby="tambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahProdukLabel">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formProduk">
                @csrf
                <div class="modal-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="ProdukTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-umum" data-bs-toggle="tab" data-bs-target="#umum"
                                type="button" role="tab">
                                <i data-feather="user" class="me-1 icon-sm"></i> Detail Produk
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-spesifikasi-teknis" data-bs-toggle="tab"
                                data-bs-target="#spesifikasi-teknis" type="button" role="tab">
                                <i data-feather="tool" class="me-1 icon-sm"></i> Spesifikasi Teknis
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-finishing" data-bs-toggle="tab"
                                data-bs-target="#finishing" type="button" role="tab">
                                <i data-feather="layers" class="me-1 icon-sm"></i> Group Finishing
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-alamat" data-bs-toggle="tab" data-bs-target="#harga"
                                type="button" role="tab">
                                <i data-feather="map-pin" class="me-1 icon-sm"></i> Harga
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-alur-produksi" data-bs-toggle="tab"
                                data-bs-target="#alur-produksi" type="button" role="tab">
                                <i data-feather="shopping-cart" class="me-1 icon-sm"></i> Alur Produksi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-media-dokumen" data-bs-toggle="tab"
                                data-bs-target="#media-dokumen" type="button" role="tab">
                                <i data-feather="shopping-cart" class="me-1 icon-sm"></i> Media & Dokumen
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="ProdukTabContent">
                        <!-- Detail Produk -->
                        <div class="tab-pane fade show active" id="umum" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="nama_produk" class="form-label">Nama Produk<span
                                                    class="text-danger"> *</span></label>
                                            <input type="text" class="form-control" id="nama_produk" name="nama_produk"
                                                placeholder="Nama produk" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="jenis_produk" class="form-label">Jenis Produk <span
                                                    class="text-danger"> *</span></label>
                                            <select class="form-select" id="jenis_produk" name="jenis_produk" required>
                                                <option value="" selected disabled>Pilih Jenis Produk</option>
                                                <option value="produk">Produk</option>
                                                <option value="jasa">Jasa</option>
                                                <option value="rakitan">Rakitan</option>
                                            </select>
                                            <small id="jenis-produk-description" class="text-muted">Pilih jenis produk terlebih dahulu</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="satuan" class="form-label">Jenis Satuan <span class="text-danger">
                                                    *</span></label>
                                            <select class="form-select" id="satuanBarang" name="satuan_id" required>
                                                <option value="" selected disabled>Pilih satuan</option>
                                                @foreach($satuanList as $detail)
                                                    <option value="{{ $detail->id }}">{{ $detail->nama_detail_parameter }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="detail_satuan" class="form-label">Detail Satuan <span class="text-danger">*</span></label>
                                            <select class="form-select" id="detail_satuan" name="sub_satuan_id" required>
                                                <option value="" selected disabled>Pilih detail satuan</option>
                                                <!-- Options akan diisi dinamis oleh JS -->
                                            </select>
                                            <small class="text-muted">Detail lebih spesifik dari satuan yang dipilih</small>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="kategori_utama" class="form-label">Kategori Utama <span
                                                    class="text-danger"> *</span></label>
                                            <div class="input-group">
                                                <select class="form-select" id="kategori_utama" name="kategori_utama_id"
                                                    required>
                                                    <option value="" selected disabled>Pilih Kategori Utama</option>
                                                    @foreach($kategoriProdukList as $kategori)
                                                        <option value="{{ $kategori->id }}">
                                                            {{ $kategori->nama_detail_parameter }}</option>
                                                    @endforeach
                                                    {{-- @else
                                                    <option value="">Tidak ada data kategori utama</option>
                                                    @endif --}}
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="subKategori" class="form-label">Sub-Kategori <span
                                                    class="text-danger"> *</span></label>
                                            <select class="form-select" id="sub_kategori_id_produk"
                                                name="sub_kategori_id">
                                                <option value="" selected disabled>Pilih sub-kategori</option>
                                                <!-- Options akan diisi dinamis oleh JS, value=id -->
                                            </select>
                                            <small class="text-muted">Pengelompokan lebih detail dalam kategori yang
                                                sama</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="gunakan_dimensi" name="is_metric" value="1">
                                                <label class="form-check-label" for="gunakan_dimensi">Gunakan dimensi produk</label>
                                                <div class="form-text">Aktifkan untuk menambahkan informasi dimensi lebar dan panjang</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3" id="metric_unit_container" style="display: none;">
                                        <div class="col-md-6">
                                            <label for="metric_unit" class="form-label">Satuan Metric</label>
                                            <select class="form-select" id="metric_unit" name="metric_unit" disabled>
                                                <option value="cm">Centimeter (cm)</option>
                                                <option value="mm">Millimeter (mm)</option>
                                                <option value="m">Meter (m)</option>
                                            </select>
                                            <small class="text-muted">Satuan pengukuran dimensi produk</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Dimensi</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text">L</span>
                                                        <input type="number" class="form-control" id="lebar" name="lebar"
                                                            min="0" step="0.01" value="0" placeholder="Lebar" disabled>
                                                        <span class="input-group-text" id="label_metric_lebar">cm</span>
                                                    </div>
                                                    <small class="text-muted">Lebar produk</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text">P</span>
                                                        <input type="number" class="form-control" id="panjang"
                                                            name="panjang" min="0" step="0.01" value="0" placeholder="Panjang" disabled>
                                                        <span class="input-group-text" id="label_metric_panjang">cm</span>
                                                    </div>
                                                    <small class="text-muted">Panjang produk</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Luas</span>
                                                        <input type="number" class="form-control" id="luas" name="luas" 
                                                            readonly step="0.01" value="0" placeholder="0.00" disabled>
                                                        <span class="input-group-text" id="label_metric_luas">cm²</span>
                                                    </div>
                                                    <small class="text-muted">Lebar × Panjang</small>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-2">
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="lebar_locked" name="lebar_locked" value="1" disabled>
                                                        <label class="form-check-label small" for="lebar_locked">
                                                            <i class="fa fa-unlock lock-icon me-1" data-target="lebar_locked"></i>Lock lebar
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="panjang_locked" name="panjang_locked" value="1" disabled>
                                                        <label class="form-check-label small" for="panjang_locked">
                                                            <i class="fa fa-unlock lock-icon me-1" data-target="panjang_locked"></i>Lock panjang
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <!-- HIDDEN INPUT UNTUK JSON -->
                                        <!-- <input type="hidden" name="bahan_baku_json" id="bahan_baku_json"> -->
                                        <input type="hidden" name="harga_bertingkat_json" id="harga_bertingkat_json">
                                        <input type="hidden" name="harga_reseller_json" id="harga_reseller_json">
                                        <input type="hidden" name="foto_pendukung_json" id="foto_pendukung_json">
                                        <input type="hidden" name="video_pendukung_json" id="video_pendukung_json">
                                        <input type="hidden" name="dokumen_pendukung_json" id="dokumen_pendukung_json">
                                        <input type="hidden" name="alur_produksi_json" id="alur_produksi_json">
                                        <input type="hidden" name="biaya_tambahan_json" id="biaya_tambahan_json" value="">
                                        <input type="hidden" name="parameter_modal_json" id="parameter_modal_json" value="[]">
                                        <input type="hidden" name="finishing_json" id="finishing_json" value="[]">
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="keterangan" class="form-label">Keterangan</label>
                                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2" 
                                                placeholder="Tambahkan keterangan atau catatan untuk produk ini"></textarea>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="warna_id" class="form-label">Warna</label>
                                            <select class="form-select" id="warna_id" name="warna_id">
                                                <option value="">Pilih warna (opsional)</option>
                                                @foreach($modeWarnaOptions ?? [] as $warnaOption)
                                                    <option data-hex="{{ $warnaOption->keterangan }}" value="{{ $warnaOption->id }}">{{ $warnaOption->nama_detail_parameter }}</option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Pilih warna produk jika ada</small>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label">&nbsp;</label>
                                            <div id="warnaPreviewModal" class="warna-preview-modal flex-grow-1" 
                                                style="display: none; height: 38px; border: 1px solid #ced4da; border-radius: 0.375rem;" 
                                                title="Preview warna">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="tags" class="form-label">Tags</label>
                                            <div>
                                                <!-- <input name="tags" id="tags" /> -->
                                                <select id="tags" name="tags[]" multiple style="width: 100%"></select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="status_aktif" value="0">
                                                <input class="form-check-input" type="checkbox" id="status_aktif"
                                                    name="status_aktif" value="1" checked>
                                                <label class="form-check-label" for="status_aktif">Status Aktif</label>
                                                <div><small class="text-muted">Produk akan tampil di daftar produk
                                                        aktif</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Deskripsi & Media -->
                        <!-- <div class="tab-pane fade" id="kontak" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <!-- Deskripsi Produk -->
                        <!-- <div class="mb-3">
                                        <label for="deskripsi_produk" class="form-label fw-semibold">Deskripsi Produk</label>
                                        <textarea class="form-control" id="deskripsi_produk" name="deskripsi_produk" rows="3" placeholder="Deskripsi detail tentang produk"></textarea>
                                        <small class="text-muted">Jelaskan produk secara detail termasuk fitur dan manfaatnya</small>
                                    </div> -->
                        <!-- Media & Dokumen -->
                        <!-- <div class="border rounded p-3 mb-3">
                                        <div class="fw-semibold mb-2">Media & Dokumen</div>
                                        <div class="row g-3"> -->
                        <!-- Gambar Produk -->
                        <!-- <div class="col-md-6">
                                                <label class="form-label">Gambar Produk</label>
                                                <div class="border rounded d-flex flex-column align-items-center justify-content-center py-4 mb-2" style="min-height: 120px;">
                                                    <i data-feather="image" class="mb-2" style="width:32px;height:32px;"></i>
                                                    <input type="file" class="form-control mb-2" id="gambar_produk" name="gambar_produk[]" accept=".jpg,.jpeg,.png,.webp" multiple style="max-width: 220px;">
                                                    <small class="text-muted">Format: JPG, PNG, WebP (Maks. 5MB)</small>
                                                </div>
                                            </div> -->
                        <!-- Dokumen Pendukung -->
                        <!-- <div class="col-md-6">
                                                <label class="form-label">Dokumen Pendukung</label>
                                                <div class="border rounded d-flex flex-column align-items-center justify-content-center py-4 mb-2" style="min-height: 120px;">
                                                    <i data-feather="file-text" class="mb-2" style="width:32px;height:32px;"></i>
                                                    <input type="file" class="form-control mb-2" id="dokumen_produk" name="dokumen_produk[]" accept=".pdf,.doc,.docx,.xls,.xlsx" multiple style="max-width: 220px;">
                                                    <small class="text-muted">Format: PDF, DOC, XLS (Maks. 10MB)</small>
                                                </div>
                                            </div> -->
                        <!-- </div>
                                        <div class="row mt-3"> -->
                        <!-- URL Thumbnail -->
                        <!-- <div class="col-md-6">
                                                <label for="url_thumbnail" class="form-label">URL Thumbnail</label>
                                                <input type="text" class="form-control" id="url_thumbnail" name="url_thumbnail" placeholder="URL thumbnail produk">
                                            </div> -->
                        <!-- Dokumen Terunggah -->
                        <!-- <div class="col-md-6">
                                                <label class="form-label">Dokumen Terunggah</label>
                                                <div class="border rounded p-2" style="min-height: 48px;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i data-feather="file" class="me-2" style="width:16px;height:16px;"></i>
                                                        <span class="me-auto">spesifikasi-teknis.pdf</span>
                                                        <button type="button" class="btn btn-link text-danger p-0 ms-2" title="Hapus"><i data-feather="trash-2" style="width:16px;height:16px;"></i></button>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i data-feather="file" class="me-2" style="width:16px;height:16px;"></i>
                                                        <span class="me-auto">brosur-produk.pdf</span>
                                                        <button type="button" class="btn btn-link text-danger p-0 ms-2" title="Hapus"><i data-feather="trash-2" style="width:16px;height:16px;"></i></button>
                                                    </div>
                                                </div>
                                            </div> -->
                        <!-- </div>
                                    </div> -->
                        <!-- SEO & Pengaturan Online -->
                        <!-- <div class="border rounded p-3">
                                        <div class="fw-semibold mb-2">SEO & Pengaturan Online</div>
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Judul untuk SEO">
                                            <small class="text-muted">Judul yang akan ditampilkan di hasil pencarian mesin pencari</small>
                                        </div>
                                        <div>
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control" id="meta_description" name="meta_description" rows="2" placeholder="Deskripsi untuk SEO"></textarea>
                                            <small class="text-muted">Deskripsi singkat yang akan muncul di hasil pencarian</small>
                                        </div>
                                    </div> -->
                        <!-- </div>
                            </div>
                        </div>  -->

                        <!-- Harga -->
                        <div class="tab-pane fade" id="harga" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <!-- Tab Harga: Modal & Harga Jual -->
                                    <ul class="nav nav-tabs nav-tabs-line mb-3" id="lineTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="modal-tab" data-bs-toggle="tab"
                                                data-bs-target="#modal-tab-pane" type="button" role="tab">
                                                Modal
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="harga-jual-tab" data-bs-toggle="tab"
                                                data-bs-target="#harga-jual-tab-pane" type="button" role="tab">
                                                Harga Jual
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="hargaTabContent">
                                        <!-- Total Modal Keseluruhan -->
                                        <div class="alert alert-primary d-flex align-items-center mb-3" role="alert">
                                            <div>
                                                <div class="fw-semibold mb-1">
                                                    <i data-feather="info" class="me-2"></i>
                                                    Total Modal Keseluruhan
                                                    <span class="badge bg-light text-primary ms-2" id="totalItemModal">0
                                                        item</span>
                                                </div>
                                                <div class="fs-4 fw-bold" id="totalModalKeseluruhan">Rp 0</div>
                                                <div class="small text-muted mt-1">
                                                    Bahan Baku: <span id="totalBahanBakuText" class="fw-bold">Rp
                                                        0</span>
                                                    &nbsp;|&nbsp;
                                                    Parameter: <span id="totalParameterText" class="fw-bold">Rp 0</span>
                                                    &nbsp;|&nbsp;
                                                    Produk Rakitan: <span id="totalKomponenText" class="fw-bold">Rp 0</span>
                                                    &nbsp;|&nbsp;
                                                    Biaya Tambahan: <span id="totalBiayaTambahanText" class="fw-bold">Rp 0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tab Modal -->
                                        <div class="tab-pane fade show active" id="modal-tab-pane" role="tabpanel">
                                            <!-- Bahan Baku -->
                                            <div class="mb-4" id="bahanBakuSection">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="fw-semibold">Bahan Baku</div>
                                                    <!-- Tombol + Tambah Bahan -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="btnTambahBahan">
                                                        + Tambah Bahan
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle mb-0"
                                                        id="tabelBahanBaku">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Nama Bahan</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Satuan</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Harga</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Jumlah</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Total</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">Belum ada
                                                                    bahan baku ditambahkan</td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="4" class="text-end fw-bold">Total Modal
                                                                    Bahan:</td>
                                                                <td colspan="2" class="fw-bold" id="totalModalBahan">Rp
                                                                    0</td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- Parameter Modal -->
                                            <div class="mb-4" id="parameterModalSection">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="fw-semibold">Parameter Modal</div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="btnTambahParameter">
                                                        + Tambah Parameter
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle mb-0"
                                                        id="tabelParameterModal">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Nama Mesin</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Nama Parameter</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Harga</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Jumlah</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Total</th>
                                                                <th
                                                                    class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted">Pilih
                                                                    kategori parameter</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- Produk Rakitan -->
                                            <div class="mb-4" id="produkKomponenSection" style="display: none;">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="fw-semibold">Produk Rakitan</div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        id="btnTambahProdukKomponen">
                                                        + Tambah Produk
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle mb-0"
                                                        id="tabelProdukKomponen">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Kode Produk</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Nama Produk</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Modal Produk</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Jumlah</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Total</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">Belum ada produk komponen ditambahkan</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- Biaya Tambahan -->
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="fw-semibold">Biaya Tambahan</div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahBiayaTambahan">
                                                        + Tambah Biaya
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle mb-0" id="tabelBiayaTambahan">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Nama Biaya</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Nilai</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                                    Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="3" class="text-center text-muted">Belum ada biaya tambahan ditambahkan</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tab Harga Jual Dinamis -->
                                        <div class="tab-pane fade" id="harga-jual-tab-pane" role="tabpanel">
                                            <div class="card mb-3 border-2 border-primary-subtle"
                                                style="background: #f8f5ff;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <i data-feather="tag" class="me-2 text-primary"></i>
                                                            <span class="fw-semibold">Harga Umum (Bertingkat)</span>
                                                            <div class="small text-muted">Atur harga berdasarkan
                                                                quantity minimum</div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            id="btnTambahHargaBertingkat">+ Tambah Tingkat</button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered align-middle mb-0"
                                                            id="tabelHargaBertingkat">
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
                                            <div class="card mb-3 border-2 border-warning-subtle"
                                                style="background: #fffbe7;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <i data-feather="users" class="me-2 text-warning"></i>
                                                            <span class="fw-semibold">Harga Reseller (Bertingkat)</span>
                                                            <div class="small text-muted">Harga khusus untuk partner
                                                                reseller</div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            id="btnTambahHargaReseller">+ Tambah Tingkat</button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered align-middle mb-0"
                                                            id="tabelHargaReseller">
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
                        <div class="tab-pane fade" id="alur-produksi" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-semibold">Alur Produksi</span>
                                                <div class="small text-muted">Tentukan mesin yang digunakan untuk
                                                    memproduksi item ini</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                id="btnTambahMesin">
                                                + Tambah Mesin
                                            </button>
                                        </div>
                                    </div>
                                    <div id="daftarMesin">
                                        <!-- Mesin akan ditambahkan secara dinamis di sini -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media & Dokumen Tab -->
                        <div class="tab-pane fade" id="media-dokumen" role="tabpanel"
                            aria-labelledby="media-dokumen-tab">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-3">Media (Foto & Video)</h6>
                                    <div class="dropzone-area-media mb-3 text-center p-4 border-2 border-dashed rounded bg-light position-relative"
                                        id="mediaDropzoneArea" style="cursor:pointer;">
                                        <input type="file" class="d-none" id="mediaPendukungInput"
                                            name="media_pendukung_new[]" multiple accept="image/*,video/*">
                                        <div class="dz-message text-muted">
                                            <i data-feather="upload-cloud" class="icon-lg mb-2"></i><br>
                                            <span>Seret & lepas foto/video di sini atau klik untuk memilih file</span>
                                            <div style="font-size:0.85rem;">Maksimal 10 file, format gambar/video
                                                didukung</div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-12 mb-1 text-start"><strong>Foto</strong></div>
                                        <div class="col-12" id="fotoPendukungPreview">
                                            <div class="text-muted text-center" id="noFotoMessage">
                                                <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang
                                                ditambahkan.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-4">
                                        <div class="col-12 mb-1 text-start"><strong>Video</strong></div>
                                        <div class="col-12" id="videoPendukungPreview">
                                            <div class="text-muted text-center" id="noVideoMessage">
                                                <i data-feather="video" class="icon-lg mb-2"></i><br>Belum ada video
                                                yang ditambahkan.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Dokumen Pendukung</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="tambahDokumen"><i
                                                data-feather="plus" class="me-1 icon-sm"></i> Tambah Dokumen</button>
                                    </div>
                                    <p class="text-muted mb-3" style="font-size: 0.85rem;">Tambahkan dokumen pendukung
                                        seperti spesifikasi teknis, sertifikat, atau laporan uji.</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="dokumenPendukungTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 25%;">Nama Dokumen</th>
                                                    <th style="width: 20%;">Jenis</th>
                                                    <th style="width: 15%;">Ukuran</th>
                                                    <th style="width: 10%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="dokumenPendukungBody">
                                                <!-- Document rows will be added here -->
                                                <tr id="noDokumenMessage">
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum
                                                        ada dokumen yang ditambahkan.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Input file dokumen pendukung (hidden) -->
                                    <input type="file" class="d-none" id="dokumenPendukungInput"
                                        name="dokumen_pendukung_new[]" multiple
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.csv,.jpg,.jpeg,.png,.gif">
                                </div>
                            </div>
                        </div>

                        <!-- Spesifikasi Teknis -->
                        <div class="tab-pane fade" id="spesifikasi-teknis" role="tabpanel">
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
                                        <div
                                            class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Spesifikasi Teknis</h6>
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                id="tambah_spesifikasi_produk">
                                                <i data-feather="plus" class="icon-sm"></i> Tambah Spesifikasi
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div id="spesifikasi_produk_container">
                                                <div class="text-muted text-center py-3" id="no_spesifikasi_message">
                                                    Belum ada spesifikasi teknis. Klik tombol "Tambah Spesifikasi" untuk
                                                    menambahkan.
                                                </div>
                                            </div>
                                            <input type="hidden" name="spesifikasi_teknis_json"
                                                id="spesifikasi_teknis_json" value="[]">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Finishing Tab   -->
                        <div class="tab-pane fade" id="finishing" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-semibold">Daftar Finishing</span>
                                                <div class="small text-muted">Pilih group finishing yang tersedia untuk produk ini</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                id="btnTambahFinishing">
                                                <i data-feather="plus" class="me-1"></i> Tambah Group Finishing
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle mb-0"
                                            id="tabelFinishing">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                        Nama Group Finishing</th>
                                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                        Keterangan</th>
                                                    <!-- <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                        Harga Modal</th> -->
                                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                        Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">Belum ada finishing ditambahkan</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @push('custom-scripts')
                            <script>
                                let mesinIndex = 0;

                                function mesinTemplate(index = 0, data = {}) {
                                    return `
                                <div class="border rounded mb-3 p-3 position-relative mesin-item" data-index="${index}">
                                    <button type="button" class="btn btn-link text-danger position-absolute top-0 end-0 mt-2 me-2 btnHapusMesin" title="Hapus Mesin"><i data-feather="trash-2"></i></button>
                                    <div class="mb-2 fw-semibold">Mesin ${index + 1}</div>
                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Divisi Mesin</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control divisi-mesin-input" name="alur_produksi[${index}][divisi_mesin]" value="${data.divisi_mesin || ""}" placeholder="Pilih divisi mesin..." readonly style="cursor: pointer; background-color: #fff;">
                                                <input type="hidden" class="divisi-mesin-id-input" name="alur_produksi[${index}][divisi_mesin_id]" value="${data.divisi_mesin_id || ""}">
                                                <button type="button" class="btn btn-outline-secondary btn-cari-divisi-mesin" title="Cari Divisi Mesin"><i class="fa fa-search"></i></button>
                                            </div>
                                            <small class="text-muted">Keterangan: <span class="keterangan-divisi-span">${
                                                data.keterangan_divisi || "Tidak ada keterangan"
                                            }</span></small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Estimasi Waktu (menit)</label>
                                            <input type="number" class="form-control" name="alur_produksi[${index}][estimasi_waktu]" value="${data.estimasi_waktu || ''}" min="0" placeholder="Estimasi waktu" required>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Catatan</label>
                                        <textarea class="form-control" name="alur_produksi[${index}][catatan]" rows="2" placeholder="Catatan proses">${data.catatan || ''}</textarea>
                                    </div>
                                </div>
                                `;
                                }

                                function refreshFeather() {
                                    if (typeof feather !== 'undefined') {
                                        feather.replace();
                                    }
                                }

                                $(document).on('click', '#btnTambahMesin', function () {
                                    $('#daftarMesin').append(mesinTemplate(mesinIndex));
                                    mesinIndex++;
                                    refreshFeather();
                                });

                                $(document).on('click', '.btnHapusMesin', function () {
                                    $(this).closest('.mesin-item').remove();
                                    refreshFeather();
                                });

                                // Optional: Tambahkan mesin default jika diperlukan
                                // $('#btnTambahMesin').trigger('click');

                                $(document).on('click', '#btnTambahBahan', function () {
                                    var modalBahan = new bootstrap.Modal(document.getElementById('modalCariBahanBakuProduk'), {
                                        backdrop: 'static',
                                        keyboard: false,
                                        focus: true
                                    });
                                    modalBahan.show();
                                    setTimeout(function () {
                                        if ($('#tambahProduk').hasClass('show')) {
                                            $('body').addClass('modal-open');
                                        }
                                    }, 200);
                                });

                                window.addEventListener('bahanBakuDipilih', function (e) {
                                    if (!$('#modalCariBahanBakuProduk').hasClass('show')) return;
                                    const data = e.detail;
                                    // Cek duplikat
                                    if ($('#tabelBahanBaku tbody tr[data-id="' + data.id + '"]').length > 0) {
                                        Swal.fire('Info', 'Bahan baku sudah ditambahkan.', 'info');
                                        return;
                                    }
                                    // Tambahkan baris ke tabelBahanBaku
                                    let row = `
                                    <tr data-id="${data.id}">
                                        <td>${data.nama}<input type="hidden" name="bahan_baku_id[]" value="${data.id}"></td>
                                        <td>${data.satuan}</td>
                                        <td class="text-end">Rp ${data.harga.toLocaleString('id-ID')}<input type="hidden" name="harga_bahan[]" value="${data.harga}"></td>
                                        <td><input type="number" class="form-control form-control-sm jumlah-bahan" name="jumlah_bahan[]" value="1" min="1"></td>
                                        <td class="total-bahan text-success fw-semibold text-end">Rp ${data.harga}</td>
                                        <td><button type="button" class="btn btn-danger btn-xs btn-hapus-bahan"><i data-feather="trash-2" class="icon-sm"></i></button></td>
                                    </tr>
                                `;
                                    // Hapus pesan kosong jika ada
                                    $('#tabelBahanBaku tbody .text-muted').remove();
                                    $('#tabelBahanBaku tbody').append(row);
                                    if (typeof feather !== 'undefined') feather.replace();
                                    hitungTotalModalBahan();
                                });

                                // Hitung total saat jumlah berubah
                                $(document).on('input', '.jumlah-bahan', function () {
                                    const row = $(this).closest('tr');
                                    const harga = parseFloat(row.find('input[name="harga_bahan[]"]').val()) || 0;
                                    const jumlah = parseFloat(row.find('.jumlah-bahan').val()) || 0;
                                    const total = harga * jumlah;
                                    row.find('.total-bahan').html('<span class="text-success fw-semibold">Rp ' + total.toLocaleString('id-ID') + '</span>');
                                    hitungTotalModalBahan();
                                });

                                // Hapus baris bahan baku
                                $(document).on('click', '.btn-hapus-bahan', function () {
                                    $(this).closest('tr').remove();
                                    hitungTotalModalBahan();
                                });

                                // Hitung total semua bahan
                                function hitungTotalModalBahan() {
                                    let total = 0;
                                    $('#tabelBahanBaku tbody tr').each(function () {
                                        const harga = parseInt($(this).find('input[name="harga_bahan[]"]').val()) || 0;
                                        const jumlah = parseInt($(this).find('.jumlah-bahan').val()) || 0;
                                        total += harga * jumlah;
                                    });
                                    $('#totalModalBahan').text('Rp ' + total.toLocaleString('id-ID'));
                                }
                            </script>
                        @endpush
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

@push('plugin-styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endpush

@push('custom-styles')
<style>
    .warna-preview-modal {
    width: 100%;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.warna-preview-modal:hover {
    transform: scale(1.02);
}
.select2-container--open .select2-dropdown {
    top: 100% !important;
    bottom: auto !important;
}
.select2-container--default .select2-search--inline .select2-search__field {
    height: 28px;          
    line-height: 28px;
    padding: 0 2px;
    margin-top: 5px;       
}
.select2-container--default .select2-selection--multiple {
    height: 38px;
    min-height: 38px;      
    overflow-y: auto;      
}

.select2-container--default .select2-selection--multiple .select2-selection__rendered {
    padding: 2px 5px;
}

.select2-container--default .select2-search--inline .select2-search__field {
    min-height: 30px;
    line-height: 30px;
}
</style>
@endpush

@push('plugin-scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@include('backend.general-form.cari-bahanbaku', [
    'modalId' => 'modalCariBahanBakuProduk',
    'inputId' => 'searchBahanBakuProduk',
    'tableId' => 'tabelCariBahanBakuProduk',
    'paginationId' => 'paginationBahanBakuProduk',
    'clearBtnId' => 'clearSearchBahanBakuProduk',
])

@include('backend.general-form.cari-parameter', [
    'modalId' => 'modalCariParameterProduk',
    'inputId' => 'searchParameterProduk',
    'tableId' => 'tabelCariParameterProduk',
    'paginationId' => 'paginationParameterProduk',
    'clearBtnId' => 'clearSearchParameterProduk',
])

@include('backend.general-form.cari-mesin', [
    'modalId' => 'modalCariMesinProdukTambah',
    'inputId' => 'searchMesinProdukTambah',
    'tableId' => 'tabelCariMesinProdukTambah',
    'paginationId' => 'paginationMesinProdukTambah',
    'clearBtnId' => 'clearSearchMesinProdukTambah',
])

@include('backend.general-form.cari-produk', [
    'modalId' => 'modalCariProdukRakitanTambah',
    'inputId' => 'searchProdukRakitanTambah',
    'tableId' => 'tabelCariProdukRakitanTambah',
    'paginationId' => 'paginationProdukRakitanTambah',
    'clearBtnId' => 'clearSearchProdukRakitanTambah',
])

@include('backend.general-form.cari-divisi-mesin', [
    'modalId' => 'modalCariDivisiMesinProdukTambah',
    'inputId' => 'searchDivisiMesinProdukTambah',
    'tableId' => 'tabelCariDivisiMesinProdukTambah',
    'paginationId' => 'paginationDivisiMesinProdukTambah',
    'clearBtnId' => 'clearSearchDivisiMesinProdukTambah',
])

@include('backend.general-form.cari-finishing', [
    'modalId' => 'modalCariFinishing',
    'inputId' => 'searchFinishing',
    'tableId' => 'tabelCariFinishing',
    'paginationId' => 'paginationFinishing',
    'clearBtnId' => 'clearSearchFinishing',
])

<div class="modal fade" id="tambahProduk" tabindex="-1" aria-labelledby="tambahProdukLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"><!-- Tambahkan modal-dialog-centered di sini -->
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
                            <button class="nav-link" id="tab-deskripsidanmedia" data-bs-toggle="tab" data-bs-target="#kontak"
                                type="button" role="tab">
                                <i data-feather="phone" class="me-1 icon-sm"></i> Deskripsi & Media
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-alamat" data-bs-toggle="tab" data-bs-target="#alamat"
                                type="button" role="tab">
                                <i data-feather="map-pin" class="me-1 icon-sm"></i> Harga
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-penjualan" data-bs-toggle="tab" data-bs-target="#penjualan"
                                type="button" role="tab">
                                <i data-feather="shopping-cart" class="me-1 icon-sm"></i> Alur Produksi
                            </button>
                        </li>
                      
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-lain-lain" data-bs-toggle="tab" data-bs-target="#lain-lain"
                                type="button" role="tab">
                                <i data-feather="more-horizontal" class="me-1 icon-sm"></i> Lain-lain
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
              <button class="nav-link" id="media-dokumen-tab" data-bs-toggle="tab" data-bs-target="#media-dokumen" type="button" role="tab" aria-controls="media-dokumen" aria-selected="false"><i data-feather="file-text" class="me-1 icon-sm"></i> Media & Dokumen</button>
            </li>

                    </ul>

                    <div class="tab-content" id="ProdukTabContent">
                        <!-- Detail Produk -->
                        <div class="tab-pane fade show active" id="umum" role="tabpanel">
                            <div class="card mb-0">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nama_produk" class="form-label">Nama Produk *</label>
                                        <input type="text" class="form-control" id="nama_produk" name="nama_produk" placeholder="Nama produk" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kode_produk" class="form-label">Kode Produk *</label>
                                        <input type="text" class="form-control" id="kode_produk" name="kode_produk" placeholder="Kode produk" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="kategori_utama" class="form-label">Kategori Utama*</label>
                                        <div class="input-group">
                                            <select class="form-select" id="kategori_utama" name="kategori_utama" required>
                                                 <option value="" selected disabled>Pilih Kategori Utama</option>
                                                @foreach($kategoriProdukList as $kategori)
                                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_detail_parameter }}</option>
                                                @endforeach
                                                {{-- @else
                                                    <option value="">Tidak ada data kategori utama</option>
                                                @endif --}}
                                            </select>
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    <label for="subKategori" class="form-label">Sub-Kategori</label>
                                    <select class="form-select" id="sub_kategori_id_produk" name="sub_kategori_id">
                                        <option value="" selected disabled>Pilih sub-kategori</option>
                                        <!-- Options akan diisi dinamis oleh JS, value=id -->
                                    </select>
                                    <small class="text-muted">Pengelompokan lebih detail dalam kategori yang sama</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="satuan" class="form-label">Satuan *</label>
                                        <select class="form-select" id="satuanBarang" name="satuanBarang" required>
                                            <option value="" selected disabled>Pilih satuan</option>
                                            @foreach($satuanList as $detail)
                                                <option value="{{ $detail->id }}">{{ $detail->nama_detail_parameter }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Metode Penjualan</label>
                                        <div class="card p-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="metode_penjualan" id="jual_per_m2" value="m2" checked>
                                                <label class="form-check-label" for="jual_per_m2">
                                                    Dijual per m<sup>2</sup>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="metode_penjualan" id="jual_per_meter_lari" value="meter_lari">
                                                <label class="form-check-label" for="jual_per_meter_lari">
                                                    Dijual per meter lari
                                                </label>
                                            </div>
                                            <small class="text-muted">Produk dijual berdasarkan luas total (panjang × lebar)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="form-label">Dimensi</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" id="lebar" name="lebar" min="0" value="0" placeholder="Lebar (cm)">
                                        <small class="text-muted">Lebar (cm)</small>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" id="panjang" name="panjang" min="0" value="0" placeholder="Panjang (cm)">
                                        <small class="text-muted">Panjang (cm)</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="minimal_order" class="form-label">Minimal Order *</label>
                                        <input type="number" class="form-control" id="minimal_order" name="minimal_order" min="1" value="1" required>
                                        <small class="text-muted">Jumlah pemesanan minimal</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif" checked>
                                            <label class="form-check-label" for="status_aktif">Status Aktif</label>
                                            <div><small class="text-muted">Produk akan tampil di daftar produk aktif</small></div>
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                            </div>
                        </div>

                    
                        <!-- Deskripsi & Media -->
                        <div class="tab-pane fade" id="kontak" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <!-- Deskripsi Produk -->
                                    <div class="mb-3">
                                        <label for="deskripsi_produk" class="form-label fw-semibold">Deskripsi Produk</label>
                                        <textarea class="form-control" id="deskripsi_produk" name="deskripsi_produk" rows="3" placeholder="Deskripsi detail tentang produk"></textarea>
                                        <small class="text-muted">Jelaskan produk secara detail termasuk fitur dan manfaatnya</small>
                                    </div>
                                    <!-- Media & Dokumen -->
                                    <div class="border rounded p-3 mb-3">
                                        <div class="fw-semibold mb-2">Media & Dokumen</div>
                                        <div class="row g-3">
                                            <!-- Gambar Produk -->
                                            <div class="col-md-6">
                                                <label class="form-label">Gambar Produk</label>
                                                <div class="border rounded d-flex flex-column align-items-center justify-content-center py-4 mb-2" style="min-height: 120px;">
                                                    <i data-feather="image" class="mb-2" style="width:32px;height:32px;"></i>
                                                    <input type="file" class="form-control mb-2" id="gambar_produk" name="gambar_produk[]" accept=".jpg,.jpeg,.png,.webp" multiple style="max-width: 220px;">
                                                    <small class="text-muted">Format: JPG, PNG, WebP (Maks. 5MB)</small>
                                                </div>
                                            </div>
                                            <!-- Dokumen Pendukung -->
                                            <div class="col-md-6">
                                                <label class="form-label">Dokumen Pendukung</label>
                                                <div class="border rounded d-flex flex-column align-items-center justify-content-center py-4 mb-2" style="min-height: 120px;">
                                                    <i data-feather="file-text" class="mb-2" style="width:32px;height:32px;"></i>
                                                    <input type="file" class="form-control mb-2" id="dokumen_produk" name="dokumen_produk[]" accept=".pdf,.doc,.docx,.xls,.xlsx" multiple style="max-width: 220px;">
                                                    <small class="text-muted">Format: PDF, DOC, XLS (Maks. 10MB)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <!-- URL Thumbnail -->
                                            <div class="col-md-6">
                                                <label for="url_thumbnail" class="form-label">URL Thumbnail</label>
                                                <input type="text" class="form-control" id="url_thumbnail" name="url_thumbnail" placeholder="URL thumbnail produk">
                                            </div>
                                            <!-- Dokumen Terunggah -->
                                            <div class="col-md-6">
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
                                            </div>
                                        </div>
                                    </div>
                                    <!-- SEO & Pengaturan Online -->
                                    <div class="border rounded p-3">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Harga -->
                        <div class="tab-pane fade" id="alamat" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <!-- Tab Harga: Modal & Harga Jual -->
                                    <ul class="nav nav-tabs nav-tabs-line mb-3" id="lineTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="modal-tab" data-bs-toggle="tab" data-bs-target="#modal-tab-pane" type="button" role="tab">
                                                Modal
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="harga-jual-tab" data-bs-toggle="tab" data-bs-target="#harga-jual-tab-pane" type="button" role="tab">
                                                Harga Jual
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="hargaTabContent">
                                        <!-- Tab Modal -->
                                        <div class="tab-pane fade show active" id="modal-tab-pane" role="tabpanel">
                                            <!-- Bahan Baku -->
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="fw-semibold">Bahan Baku</div>
                                                    <!-- Tombol + Tambah Bahan -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahBahan" data-bs-toggle="modal" data-bs-target="#modalCariBahanBaku" data-bs-backdrop="static" data-bs-keyboard="false">
                                                        + Tambah Bahan
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle mb-0" id="tabelBahanBaku">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">NAMA</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">DESKRIPSI</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">STATUS</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">AKSI</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">Pilih kategori parameter</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- Parameter Modal -->
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <div class="fw-semibold">Parameter Modal</div>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahParameter" >
                                                        + Tambah Parameter
                                                    </button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered align-middle mb-0" id="tabelParameterModal">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">NAMA</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">DESKRIPSI</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">STATUS</th>
                                                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">AKSI</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">Pilih kategori parameter</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <!-- Total Modal Keseluruhan -->
                                            <div class="alert alert-primary d-flex align-items-center mb-0" role="alert">
                                                <div>
                                                    <div class="fw-semibold mb-1">
                                                        <i data-feather="info" class="me-2"></i>
                                                        Total Modal Keseluruhan
                                                        <span class="badge bg-light text-primary ms-2" id="totalItemModal">0 item</span>
                                                    </div>
                                                    <div class="fs-4 fw-bold" id="totalModalKeseluruhan">Rp 0</div>
                                                    <div class="small text-muted mt-1">
                                                        Bahan Baku: <span id="totalBahanBakuText" class="fw-bold">Rp 0</span>
                                                        &nbsp;|&nbsp;
                                                        Parameter: <span id="totalParameterText" class="fw-bold">Rp 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tab Harga Jual -->
                                        <div class="tab-pane fade" id="harga-jual-tab-pane" role="tabpanel">
                                            <!-- Modal Dasar Produksi -->
                                            <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                                                <div>
                                                    <div class="fw-semibold mb-1">
                                                        <i data-feather="dollar-sign" class="me-2"></i>
                                                        Modal Dasar Produksi
                                                    </div>
                                                    <div class="small text-muted">
                                                        Total Modal Keseluruhan
                                                        <span class="ms-2">4 komponen</span>
                                                    </div>
                                                    <div class="fs-4 fw-bold text-success mt-1">Rp 165.000 <span class="fs-6 fw-normal text-muted">Base cost</span></div>
                                                </div>
                                            </div>
                                            <!-- Harga Umum (Bertingkat) -->
                                            <div class="card mb-3 border-2 border-primary-subtle" style="background: #f8f5ff;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <i data-feather="tag" class="me-2 text-primary"></i>
                                                            <span class="fw-semibold">Harga Umum (Bertingkat)</span>
                                                            <div class="small text-muted">Atur harga berdasarkan quantity minimum</div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary">+ Tambah Tingkat</button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered align-middle mb-0">
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
                                                                <tr>
                                                                    <td><input type="number" class="form-control" value="1" min="1"></td>
                                                                    <td><input type="number" class="form-control" value="5" min="1"></td>
                                                                    <td><input type="text" class="form-control money-format" value="214500"></td>
                                                                    <td class="text-success fw-semibold">Rp 49.500</td>
                                                                    <td class="text-success fw-semibold">30%</td>
                                                                    <td><button type="button" class="btn btn-link text-danger p-0"><i data-feather="trash-2"></i></button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="number" class="form-control" value="6" min="1"></td>
                                                                    <td><input type="number" class="form-control" value="20" min="1"></td>
                                                                    <td><input type="text" class="form-control money-format" value="206250"></td>
                                                                    <td class="text-success fw-semibold">Rp 41.250</td>
                                                                    <td class="text-success fw-semibold">25%</td>
                                                                    <td><button type="button" class="btn btn-link text-danger p-0"><i data-feather="trash-2"></i></button></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Harga Reseller (Bertingkat) -->
                                            <div class="card mb-3 border-2 border-warning-subtle" style="background: #fffbe7;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <i data-feather="users" class="me-2 text-warning"></i>
                                                            <span class="fw-semibold">Harga Reseller (Bertingkat)</span>
                                                            <div class="small text-muted">Harga khusus untuk partner reseller</div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-warning">+ Tambah Tingkat</button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered align-middle mb-0">
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
                                                                <tr>
                                                                    <td><input type="number" class="form-control" value="1" min="1"></td>
                                                                    <td><input type="number" class="form-control" value="10" min="1"></td>
                                                                    <td><input type="text" class="form-control money-format" value="198000"></td>
                                                                    <td class="text-success fw-semibold">Rp 33.000</td>
                                                                    <td class="text-success fw-semibold">20%</td>
                                                                    <td><button type="button" class="btn btn-link text-danger p-0"><i data-feather="trash-2"></i></button></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="number" class="form-control" value="11" min="1"></td>
                                                                    <td><input type="number" class="form-control" value="50" min="1"></td>
                                                                    <td><input type="text" class="form-control money-format" value="189750"></td>
                                                                    <td class="text-success fw-semibold">Rp 24.750</td>
                                                                    <td class="text-success fw-semibold">15%</td>
                                                                    <td><button type="button" class="btn btn-link text-danger p-0"><i data-feather="trash-2"></i></button></td>
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
                       
                        <!-- alur produksi -->
                        <!-- Alur Produksi -->
                        <div class="tab-pane fade" id="penjualan" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-semibold">Alur Produksi</span>
                                                <div class="small text-muted">Tentukan mesin yang digunakan untuk memproduksi item ini</div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnTambahMesin">
                                                + Tambah Mesin
                                            </button>
                                        </div>
                                    </div>
                                    <div id="daftarMesin">
                                        <!-- Mesin akan ditambahkan secara dinamis di sini -->
                                    </div>
                                    <div class="alert alert-info d-flex align-items-center mt-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            Langkah-langkah produksi akan disimpan sebagai JSON dan digunakan untuk pelacakan produksi. Seleksi mesin akan memengaruhi biaya produksi dan perhitungan waktu.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <!-- Media & Dokumen Tab -->
                        <div class="tab-pane fade" id="media-dokumen" role="tabpanel" aria-labelledby="media-dokumen-tab">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-3">Media (Foto & Video)</h6>
                                    <div class="dropzone-area-media mb-3 text-center p-4 border-2 border-dashed rounded bg-light position-relative" id="mediaDropzoneArea" style="cursor:pointer;">
                                        <input type="file" class="d-none" id="mediaPendukungInput" name="media_pendukung_new[]" multiple accept="image/*,video/*">
                                        <div class="dz-message text-muted">
                                            <i data-feather="upload-cloud" class="icon-lg mb-2"></i><br>
                                            <span>Seret & lepas foto/video di sini atau klik untuk memilih file</span>
                                            <div style="font-size:0.85rem;">Maksimal 10 file, format gambar/video didukung</div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-12 mb-1 text-start"><strong>Foto</strong></div>
                                        <div class="col-12" id="fotoPendukungPreview">
                                            <div class="text-muted text-center" id="noFotoMessage">
                                            <i data-feather="image" class="icon-lg mb-2"></i><br>Belum ada foto yang ditambahkan.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-4">
                                        <div class="col-12 mb-1 text-start"><strong>Video</strong></div>
                                        <div class="col-12" id="videoPendukungPreview">
                                            <div class="text-muted text-center" id="noVideoMessage">
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
                                        <button type="button" class="btn btn-sm btn-primary" id="tambahDokumen"><i data-feather="plus" class="me-1 icon-sm"></i> Tambah Dokumen</button>
                                    </div>
                                    <p class="text-muted mb-3" style="font-size: 0.85rem;">Tambahkan dokumen pendukung seperti spesifikasi teknis, sertifikat, atau laporan uji.</p>
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
                                                        <i data-feather="file-text" class="icon-lg mb-2"></i><br>Belum ada dokumen yang ditambahkan.
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Input file dokumen pendukung (hidden) -->
                                    <input type="file" class="d-none" id="dokumenPendukungInput" name="dokumen_pendukung_new[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.txt,.csv,.jpg,.jpeg,.png,.gif">
                                </div>
                            </div>

                            <!-- Link Pendukung -->
                            <div class="card mt-3 mb-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Link Pendukung</h6>
                                    </div>
                                    <p class="text-muted mb-3" style="font-size: 0.85rem;">Tambahkan link pendukung seperti Google Drive, YouTube, atau website lain yang relevan.</p>
                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="url" class="form-control" id="inputLinkPendukung" placeholder="https://contoh.com/link-pendukung">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" id="inputKeteranganLinkPendukung" placeholder="Keterangan (opsional)">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-primary w-100" id="tambahLinkPendukung"><i data-feather="plus" class="icon-sm me-1"></i>Tambah Link</button>
                                        </div>
                                    </div>
                                    <ul class="list-group" id="daftarLinkPendukung">
                                        <!-- Daftar link akan muncul di sini -->
                                    </ul>
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
                                        <label class="form-label">Nama Mesin</label>
                                        <input type="text" class="form-control" name="alur_produksi[${index}][nama_mesin]" value="${data.nama_mesin || ''}" placeholder="Nama mesin" required>
                                        <small class="text-muted">Tipe: <span>${data.tipe_mesin || 'Tidak diketahui'}</span></small>
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

                        $(document).on('click', '#btnTambahMesin', function() {
                            $('#daftarMesin').append(mesinTemplate(mesinIndex));
                            mesinIndex++;
                            refreshFeather();
                        });

                        $(document).on('click', '.btnHapusMesin', function() {
                            $(this).closest('.mesin-item').remove();
                            refreshFeather();
                        });

                        // Optional: Tambahkan mesin default jika diperlukan
                        // $('#btnTambahMesin').trigger('click');
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
<!-- {{-- 
<script>
    // Script untuk menangani penambahan dan penghapusan baris piutang
    document.addEventListener('DOMContentLoaded', function () {
        // Inisialisasi Feather Icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        });
    });
</script> --}} -->

@push('custom-scripts')
  <script>
    // Event listener untuk perubahan kategori
    $(document).ready(function() {
  
      // Inisialisasi awal
       updateSubKategoriOptions($('#kategori_utama').val());
          
      $('#kategori_utama').on('change', function() {
        const selectedKategoriId = $(this).val();
        
        updateSubKategoriOptions(selectedKategoriId);
      });


    });
  </script>
  <!-- {{-- <script src="{{ asset('js/master-produk/add-modal.js') }}"></script> --}} -->
@endpush

<!-- Modal Cari Bahan Baku (pastikan ada di file ini atau include) -->
<div class="modal fade" id="modalCariBahanBaku" tabindex="-1" aria-labelledby="modalCariBahanBakuLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" data-bs-focus="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCariBahanBakuLabel">Cari Bahan Baku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Konten modal untuk mencari bahan baku -->
                <div class="mb-3">
                    <label for="searchBahanBaku" class="form-label">Cari Bahan Baku</label>
                    <input type="text" class="form-control" id="searchBahanBaku" placeholder="Nama atau kode bahan baku">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0" id="tabelCariBahanBaku">
                        <thead class="table-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Pilih</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Bahan</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Kode Bahan</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Satuan</th>
                                <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data bahan baku akan dimuat di sini melalui AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
<script>
/**
 * Agar modal utama tidak tertutup saat modal kedua dibuka (Bootstrap 5)
 * - Modal utama: #tambahProduk
 * - Modal kedua: #modalCariBahanBaku
 * 
 * Kunci: intercept event hide pada modal utama saat modal kedua dibuka.
 */
$(function() {
    // Cegah modal utama tertutup saat modal kedua dibuka
    var preventClose = false;

    $('#modalCariBahanBaku').on('show.bs.modal', function () {
        preventClose = true;
    });

    $('#tambahProduk').on('hide.bs.modal', function (e) {
        if (preventClose) {
            e.preventDefault();
        }
    });

    $('#modalCariBahanBaku').on('hidden.bs.modal', function () {
        preventClose = false;
        if ($('#tambahProduk').hasClass('show')) {
            $('body').addClass('modal-open');
        }
    });
});
</script>
@endpush
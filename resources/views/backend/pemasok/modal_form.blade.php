<div class="modal fade" id="tambahPemasok" tabindex="-1" aria-labelledby="tambahPemasokLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahPemasokLabel">Tambah Pemasok Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPemasok">
                @csrf
                <div class="modal-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="pemasokTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-umum" data-bs-toggle="tab" data-bs-target="#umum"
                                type="button" role="tab">
                                <i data-feather="user" class="me-1 icon-sm"></i> Info Umum
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-alamat" data-bs-toggle="tab" data-bs-target="#alamat"
                                type="button" role="tab">
                                <i data-feather="map-pin" class="me-1 icon-sm"></i> Alamat
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-pembelian" data-bs-toggle="tab" data-bs-target="#pembelian"
                                type="button" role="tab">
                                <i data-feather="shopping-cart" class="me-1 icon-sm"></i> Pembelian
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-pajak" data-bs-toggle="tab" data-bs-target="#pajak"
                                type="button" role="tab">
                                <i data-feather="file-text" class="me-1 icon-sm"></i> Pajak
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-rekening" data-bs-toggle="tab" data-bs-target="#rekening"
                                type="button" role="tab">
                                <i data-feather="credit-card" class="me-1 icon-sm"></i> Rekening Bank
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-utang-awal" data-bs-toggle="tab"
                                data-bs-target="#utang-awal" type="button" role="tab">
                                <i data-feather="credit-card" class="me-1 icon-sm"></i> Utang Awal
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pemasokTabContent">
                        <!-- Info Umum -->
                        <div class="tab-pane fade show active" id="umum" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Nama pemasok wajib diisi untuk identifikasi</li>
                                                <li>Nomor handphone dan whatsapp digunakan untuk komunikasi</li>
                                                <li>Email dan website untuk keperluan korespondensi resmi</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nama" name="nama" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Handphone</label>
                                            <input type="tel" class="form-control" id="handphone" name="handphone">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">No. Whatsapp</label>
                                            <input type="tel" class="form-control" id="no_telp" name="no_telp">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="1">Aktif</option>
                                                <option value="0">Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="pemasok_email" name="email">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Website</label>
                                            <input type="url" class="form-control" id="website" name="website">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="tab-pane fade" id="alamat" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Daftar Alamat</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="addAlamat">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Alamat
                                        </button>
                                    </div>
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Anda dapat menambahkan beberapa alamat (kantor pusat, gudang, dll)
                                                </li>
                                                <li>Pilih satu alamat sebagai alamat utama untuk pengiriman dokumen</li>
                                                <li>Pastikan alamat lengkap dan jelas untuk memudahkan pengiriman</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Daftar Alamat -->
                                    <div id="alamat-list">
                                        <!-- Akan diisi secara dinamis oleh JavaScript -->
                                    </div>

                                    <!-- Pilihan Alamat Utama -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Alamat Utama</label>
                                            <select class="form-select" id="alamat_utama" name="alamat_utama">
                                                <!-- Akan diisi secara dinamis oleh JavaScript -->
                                            </select>
                                            <small class="text-muted">Alamat ini akan digunakan untuk pengiriman dokumen
                                                resmi</small>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!-- Pembelian -->
                        <div class="tab-pane fade" id="pembelian" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Kategori menentukan jenis pemasok</li>
                                                <li>Syarat pembayaran menentukan kapan pembayaran harus dilakukan</li>
                                                <li>Default diskon akan otomatis diterapkan pada setiap transaksi</li>
                                                <li>Pilih akun utang dan uang muka sesuai kebijakan akuntansi</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Kategori <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="kategori" name="kategori" required>
                                                <!-- <option value="">Pilih Kategori</option> -->
                                                @foreach($kategori as $kat)
                                                    <option value="{{ $kat->nama_detail_parameter }}">{{ $kat->nama_detail_parameter }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Syarat Pembayaran</label>
                                            <select class="form-select" id="syarat_pembayaran" name="syarat_pembayaran">
                                                <option value="COD">COD</option>
                                                <option value="Cicilan">Cicilan</option>
                                                <option value="Net 15">Net 15</option>
                                                <option value="Net 30">Net 30</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Default Diskon (%)</label>
                                            <input type="number" class="form-control" id="default_diskon"
                                                name="default_diskon" min="0" max="100">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Deskripsi Pembelian</label>
                                            <input type="text" class="form-control" id="deskripsi-pembelian"
                                                name="deskripsi_pembelian">
                                        </div>
                                        
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Akun Utang</label>
                                            <select class="form-select" id="akun_utang" name="akun_utang">
                                                <option value="Utang Usaha">Utang Usaha</option>
                                                <option value="Utang Lain-lain">Utang Lain-lain</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Akun Uang Muka</label>
                                            <select class="form-select" id="akun_uang_muka" name="akun_uang_muka">
                                                <option value="Uang Muka Pembelian">Uang Muka Pembelian</option>
                                                <option value="Kas">Kas</option>
                                                <option value="Bank">Bank</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pajak -->
                        <div class="tab-pane fade" id="pajak" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>NPWP diperlukan untuk pemasok yang wajib pajak</li>
                                                <li>NIK/Paspor diperlukan untuk identitas pemasok</li>
                                                <li>Centang "Wajib Pajak" jika pemasok memiliki NPWP</li>
                                                <li>Data pajak digunakan untuk pelaporan perpajakan</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">NPWP</label>
                                            <input type="text" class="form-control" id="npwp" name="npwp">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">NIK/Paspor</label>
                                            <input type="text" class="form-control" id="nik" name="nik">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="wajib_pajak"
                                                    name="wajib_pajak">
                                                <label class="form-check-label">Wajib Pajak</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rekening Pemasok -->
                        <div class="tab-pane fade" id="rekening" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div id="rekening-list">
                                    <!-- Rekening items will be added here dynamically -->
                                    </div>
                            
                                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addRekening">
                                        <i data-feather="plus"></i> Tambah Rekening
                                    </button>

                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Tambahkan rekening bank yang digunakan untuk pembayaran</li>
                                                <li>Pilih satu rekening sebagai rekening utama</li>
                                                <li>Pastikan nomor rekening dan nama pemilik sesuai</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label">Rekening Utama <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="rekening_utama" name="rekening_utama" required>
                                        <option value="">Pilih rekening utama</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Utang Awal -->
                        <div class="tab-pane fade" id="utang-awal" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Utang awal adalah saldo utang yang sudah ada sebelum pemasok
                                                    didaftarkan</li>
                                                <li>Anda dapat menambahkan beberapa utang awal dengan tanggal dan jumlah
                                                    yang berbeda</li>
                                                <li>Pastikan untuk mengisi syarat pembayaran dan keterangan yang jelas
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Utang Awal</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="btnTambahUtang">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Utang Awal
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="tableUtangAwal">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Jumlah</th>
                                                    <th>Mata Uang</th>
                                                    <th>Syarat Pembayaran</th>
                                                    <th>Nomor #</th>
                                                    <th>Keterangan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="utang-row">
                                                    <td>
                                                        <input type="date" class="form-control form-control-sm"
                                                            name="utang_tanggal[]" value="{{ date('Y-m-d') }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm"
                                                            name="utang_jumlah[]" value="0">
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm"
                                                            name="utang_mata_uang[]">
                                                            <option value="IDR">IDR</option>
                                                            <option value="USD">USD</option>
                                                            <option value="EUR">EUR</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm"
                                                            name="utang_syarat_pembayaran[]">
                                                            <option value="COD">COD</option>
                                                            <option value="Net 7">Net 7</option>
                                                            <option value="Net 14">Net 14</option>
                                                            <option value="Net 30">Net 30</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="utang_nomor[]">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="utang_keterangan[]">
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-xs btn-danger btn-hapus-utang">
                                                            <i data-feather="x" class="icon-sm"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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

@push('custom-scripts')
    <script>
        const pemasokStoreUrl = "{{ route('backend.pemasok.store') }}";
    </script>
    <script src="{{ asset('js/pemasok/pemasok-helper.js') }}"></script>
    <script src="{{ asset('js/pemasok/pemasok-modal.js') }}"></script>
@endpush
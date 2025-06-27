<div class="modal fade" id="modalEditPelanggan" tabindex="-1" aria-labelledby="modalEditPelangganLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPelangganLabel">Edit Data Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPelanggan">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="editPelangganTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="edit-tab-umum" data-bs-toggle="tab"
                                data-bs-target="#edit-umum" type="button" role="tab">
                                <i data-feather="user" class="me-1 icon-sm"></i> Info Umum
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-kontak" data-bs-toggle="tab"
                                data-bs-target="#edit-kontak" type="button" role="tab">
                                <i data-feather="phone" class="me-1 icon-sm"></i> Kontak lain
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-alamat" data-bs-toggle="tab"
                                data-bs-target="#edit-alamat" type="button" role="tab">
                                <i data-feather="map-pin" class="me-1 icon-sm"></i> Pengiriman
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-penjualan" data-bs-toggle="tab"
                                data-bs-target="#edit-penjualan" type="button" role="tab">
                                <i data-feather="shopping-cart" class="me-1 icon-sm"></i> Penjualan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-pajak" data-bs-toggle="tab"
                                data-bs-target="#edit-pajak" type="button" role="tab">
                                <i data-feather="file-text" class="me-1 icon-sm"></i> Pajak
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-saldo-piutang" data-bs-toggle="tab"
                                data-bs-target="#edit-saldo-piutang" type="button" role="tab">
                                <i data-feather="credit-card" class="me-1 icon-sm"></i> Saldo Piutang
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-lain-lain" data-bs-toggle="tab"
                                data-bs-target="#edit-lain-lain" type="button" role="tab">
                                <i data-feather="more-horizontal" class="me-1 icon-sm"></i> Lain-lain
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="editPelangganTabContent">
                        <!-- Info Umum -->
                        <div class="tab-pane fade show active" id="edit-umum" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Handphone</label>
                                            <input type="tel" class="form-control" id="edit_handphone" name="handphone">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">No. Whatsapp</label>
                                            <input type="tel" class="form-control" id="edit_no_telp" name="no_telp">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" id="edit_status" name="status">
                                                <option value="1">Aktif</option>
                                                <option value="0">Tidak Aktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="edit_email" name="email">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Website</label>
                                            <input type="url" class="form-control" id="edit_website" name="website">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kontak lain -->
                        <div class="tab-pane fade" id="edit-kontak" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Daftar Kontak Lain</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="editAddKontakLain">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Kontak
                                        </button>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="editTableKontakLain">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama Lengkap</th>
                                                    <th>Posisi Jabatan</th>
                                                    <th>Email</th>
                                                    <th>Handphone</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Kontak items will be added here dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="tab-pane fade" id="edit-alamat" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Daftar Alamat</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="editAddAlamat">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Alamat
                                        </button>
                                    </div>
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Anda dapat menambahkan beberapa alamat (rumah, kost, dll)</li>
                                                <li>Pilih satu alamat sebagai alamat utama</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Daftar Alamat -->
                                    <div id="edit-alamat-list">
                                        <!-- Akan diisi secara dinamis oleh JavaScript -->
                                    </div>

                                    <!-- Pilihan Alamat Utama -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Alamat Utama</label>
                                            <select class="form-select" id="edit_alamat_utama" name="alamat_utama">
                                                <!-- Akan diisi secara dinamis oleh JavaScript -->
                                            </select>
                                            <small class="text-muted">Alamat ini akan digunakan untuk pengiriman dokumen
                                                resmi</small>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!-- Penjualan -->
                        <div class="tab-pane fade" id="edit-penjualan" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Kategori harga menentukan harga yang akan digunakan untuk pelanggan
                                                    ini</li>
                                                <li>Syarat pembayaran menentukan kapan pelanggan harus membayar</li>
                                                <li>Default diskon akan otomatis diterapkan pada setiap transaksi</li>
                                                <li>Batas piutang menentukan maksimal piutang yang diizinkan</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Kategori Harga</label>
                                            <select class="form-select" id="edit_kategori_harga" name="kategori_harga">
                                                <option value="Umum">Umum</option>
                                                <option value="Reseller">Reseller</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Syarat Pembayaran</label>
                                            <select class="form-select" id="edit_syarat_pembayaran"
                                                name="syarat_pembayaran">
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
                                            <input type="number" class="form-control" id="edit_default_diskon"
                                                name="default_diskon" min="0" max="100" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pajak -->
                        <div class="tab-pane fade" id="edit-pajak" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>NPWP diperlukan untuk pelanggan yang wajib pajak</li>
                                                <li>NIK/Paspor diperlukan untuk identitas pelanggan</li>
                                                <li>Centang "Wajib Pajak" jika pelanggan memiliki NPWP</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">NPWP</label>
                                            <input type="text" class="form-control" id="edit_npwp" name="npwp">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">NIK/Paspor</label>
                                            <input type="text" class="form-control" id="edit_nik" name="nik">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="edit_wajib_pajak"
                                                    name="wajib_pajak">
                                                <label class="form-check-label">Wajib Pajak</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Saldo Piutang -->
                        <div class="tab-pane fade" id="edit-saldo-piutang" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Piutang awal adalah saldo piutang yang sudah ada sebelum pelanggan
                                                    didaftarkan</li>
                                                <li>Anda dapat menambahkan beberapa piutang awal dengan tanggal dan
                                                    jumlah yang berbeda</li>
                                                <li>Pastikan untuk mengisi syarat pembayaran dan keterangan yang jelas
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Piutang Awal</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="editBtnTambahPiutang">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Piutang Awal
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="editTablePiutangAwal">
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
                                                <!-- Piutang items will be added here dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lain-lain -->
                        <div class="tab-pane fade" id="edit-lain-lain" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <h6 class="mb-3">Pembatasan Piutang Pelanggan</h6>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="ms-4 mb-3">
                                                <div class="form-check mb-3">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="edit_batas_umur_faktur_check"
                                                        name="batas_umur_faktur_check">
                                                    <label class="form-check-label"
                                                        for="edit_batas_umur_faktur_check">
                                                        Batasi transaksi jika ada faktur dengan umur lebih dari
                                                    </label>
                                                    <div class="mt-2 ms-4">
                                                        <div class="input-group" style="width: 200px;">
                                                            <input type="number"
                                                                class="form-control form-control-sm"
                                                                id="edit_batas_umur_faktur"
                                                                name="batas_umur_faktur" value="0" min="0" disabled>
                                                            <span class="input-group-text">Hari</span>
                                                        </div>
                                                        <small class="text-muted">Masukkan jumlah hari maksimal umur faktur</small>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="edit_batas_total_piutang_check"
                                                        name="batas_total_piutang_check">
                                                    <label class="form-check-label"
                                                        for="edit_batas_total_piutang_check">
                                                        Batasi transaksi jika total piutang & pesanan melebihi
                                                    </label>
                                                    <div class="mt-2 ms-4">
                                                        <div class="input-group" style="width: 250px;">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="edit_batas_total_piutang_nilai"
                                                                name="batas_total_piutang_nilai"
                                                                value="0" min="0" disabled
                                                                data-type="number"
                                                                maxlength="20">
                                                        </div>
                                                        <small class="text-muted">Masukkan batas maksimal total piutang</small>
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
        const pelangganUpdateUrl = "{{ route('backend.pelanggan.update', ':id') }}";
    </script>
    <script src="{{ asset('js/pelanggan/pelanggan-helper.js') }}"></script>
    <script src="{{ asset('js/pelanggan/pelanggan-edit-modal.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi format mata uang
            PelangganHelper.initMoneyFormat('edit_');
            PelangganHelper.prepareFormSubmit('#formEditPelanggan');
        });
    </script>
@endpush
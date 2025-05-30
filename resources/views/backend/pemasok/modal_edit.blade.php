<div class="modal fade" id="modalEditPemasok" tabindex="-1" aria-labelledby="modalEditPemasokLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditPemasokLabel">Edit Data Pemasok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPemasok">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="editPemasokTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="edit-tab-umum" data-bs-toggle="tab" data-bs-target="#edit-umum" type="button" role="tab">
                                <i data-feather="user" class="me-1 icon-sm"></i> Info Umum
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-alamat" data-bs-toggle="tab" data-bs-target="#edit-alamat" type="button" role="tab">
                                <i data-feather="map-pin" class="me-1 icon-sm"></i> Alamat
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-pembelian" data-bs-toggle="tab" data-bs-target="#edit-pembelian" type="button" role="tab">
                                <i data-feather="shopping-cart" class="me-1 icon-sm"></i> Pembelian
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-pajak" data-bs-toggle="tab" data-bs-target="#edit-pajak" type="button" role="tab">
                                <i data-feather="file-text" class="me-1 icon-sm"></i> Pajak
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-rekening" data-bs-toggle="tab" data-bs-target="#edit-rekening" type="button" role="tab">
                                <i data-feather="credit-card" class="me-1 icon-sm"></i> Rekening Bank
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab-utang-awal" data-bs-toggle="tab" data-bs-target="#edit-utang-awal" type="button" role="tab">
                                <i data-feather="dollar-sign" class="me-1 icon-sm"></i> Utang Awal
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="editPemasokTabContent">
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
                                            <label class="form-label">No. Telepon</label>
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
                                                <li>Anda dapat menambahkan beberapa alamat (kantor, gudang, dll)</li>
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
                                            <small class="text-muted">Alamat ini akan digunakan untuk pengiriman dokumen resmi</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pembelian -->
                        <div class="tab-pane fade" id="edit-pembelian" role="tabpanel">
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
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_kategori" name="kategori" required>
                                                <option value="">Pilih Kategori</option>
                                                @foreach($kategori as $kat)
                                                    <option value="{{ $kat->nama_detail_parameter }}">{{ $kat->nama_detail_parameter }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Syarat Pembayaran</label>
                                            <select class="form-select" id="edit_syarat_pembayaran" name="syarat_pembayaran">
                                                <option value="COD">COD</option>
                                                <option value="Net 15">Net 15</option>
                                                <option value="Net 30">Net 30</option>
                                                <option value="Net 60">Net 60</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Default Diskon (%)</label>
                                            <input type="number" class="form-control" id="edit_default_diskon" name="default_diskon" min="0" max="100" value="0">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Deskripsi Pembelian</label>
                                            <input type="text" class="form-control" id="edit_deskripsi_pembelian" name="deskripsi_pembelian" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Akun Utang <span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_akun_utang" name="akun_utang" required>
                                                <!-- <option value="">Pilih Akun Utang</option> -->
                                                <option value="Utang Usaha">Utang Usaha</option>
                                                <option value="Utang Lain-lain">Utang Lain-lain</option>
                                                {{-- @foreach($akun_utang as $akun)
                                                    <option value="{{ $akun->id }}">{{ $akun->kode }} - {{ $akun->nama }}</option>
                                                @endforeach --}}
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Akun Uang Muka <span class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_akun_uang_muka" name="akun_uang_muka" required>
                                                <!-- <option value="">Pilih Akun Uang Muka</option> -->
                                                <option value="Uang Muka Pembelian">Uang Muka Pembelian</option>
                                                <option value="Kas">Kas</option>
                                                <option value="Bank">Bank</option>
                                                {{-- @foreach($akun_uang_muka as $akun)
                                                    <option value="{{ $akun->id }}">{{ $akun->kode }} - {{ $akun->nama }}</option>
                                                @endforeach --}}
                                            </select>
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
                                                <li>NPWP diperlukan untuk pemasok yang wajib pajak</li>
                                                <li>NIK diperlukan untuk identitas pemasok</li>
                                                <li>Centang "Wajib Pajak" jika pemasok memiliki NPWP</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">NPWP</label>
                                            <input type="text" class="form-control" id="edit_npwp" name="npwp" placeholder="00.000.000.0-000.000">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">NIK</label>
                                            <input type="text" class="form-control" id="edit_nik" name="nik" placeholder="16 digit NIK">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="edit_wajib_pajak" name="wajib_pajak">
                                                <label class="form-check-label">Wajib Pajak</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rekening -->
                        <div class="tab-pane fade" id="edit-rekening" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Daftar Rekening</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="editAddRekening">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Rekening
                                        </button>
                                    </div>

                                    <!-- Daftar Rekening -->
                                    <div id="edit-rekening-list">
                                        <!-- Akan diisi secara dinamis oleh JavaScript -->
                                    </div>

                                    <!-- Pilihan Rekening Utama -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Rekening Utama</label>
                                            <select class="form-select" id="edit_rekening_utama" name="rekening_utama">
                                                <!-- Akan diisi secara dinamis oleh JavaScript -->
                                            </select>
                                            <small class="text-muted">Rekening ini akan digunakan untuk pembayaran</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Utang Awal -->
                        <div class="tab-pane fade" id="edit-utang-awal" role="tabpanel">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                        <i data-feather="info" class="me-2"></i>
                                        <div>
                                            <strong>Petunjuk:</strong>
                                            <ul class="mb-0">
                                                <li>Utang awal adalah saldo utang yang sudah ada sebelum pemasok didaftarkan</li>
                                                <li>Anda dapat menambahkan beberapa utang awal dengan tanggal dan jumlah yang berbeda</li>
                                                <li>Pastikan untuk mengisi syarat pembayaran dan keterangan yang jelas</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Utang Awal</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="editBtnTambahUtang">
                                            <i data-feather="plus" class="icon-sm"></i> Tambah Utang Awal
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="editTableUtangAwal">
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
                                                <!-- Utang items will be added here dynamically -->
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
        const pemasokUpdateUrl = "{{ route('backend.pemasok.update', ':id') }}";
    </script>
    <script src="{{ asset('js/pemasok/pemasok-helper.js') }}"></script>
    <script src="{{ asset('js/pemasok/pemasok-edit-modal.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi format mata uang
            PemasokHelper.initMoneyFormat('edit_');
            PemasokHelper.prepareFormSubmit('#formEditPemasok');
        });
    </script>
@endpush 
@extends('layout.master')



@section('content')
    <form id="formTambahSPK" action="#" method="POST">
        <div class="row g-4">
            <!-- Kiri: Info Pelanggan, Status, Prioritas, Catatan -->
            <div class="col-md-3">
                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="fw-semibold mb-2"><i class="fa fa-user me-1"></i> Informasi Pelanggan</div>
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Pilih pelanggan...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No SPK</label>
                        <input type="text" class="form-control" value="SPK0525-291-014" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal SPK</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="fw-semibold mb-2"><i class="fa fa-flag me-1"></i> Status & Prioritas</div>
                    <div class="mb-3">
                        <label class="form-label">Status SPK</label>
                        <select class="form-select">
                            <option>Draft</option>
                            <option>Aktif</option>
                            <option>Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioritas</label>
                        <select class="form-select">
                            <option>Normal</option>
                            <option>Tinggi</option>
                            <option>Rendah</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4 p-3 border rounded bg-light">
                    <div class="fw-semibold mb-2"><i class="fa fa-sticky-note me-1"></i> Catatan</div>
                    <textarea class="form-control" rows="3" placeholder="Tambahkan catatan untuk SPK ini"></textarea>
                </div>
            </div>
            <!-- Kanan: Item Pekerjaan -->
            <div class="col-md-9">
                <div class="card border-0 shadow-none bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-bold mb-1" style="font-size: 1.1rem;">SPK #SPK0525-291-014</div>
                                <div class="text-muted small">Kelola item pekerjaan dan tugas produksi untuk SPK ini</div>
                            </div>
                            <span class="badge bg-white text-dark px-3 py-2 fw-normal" style="font-size: 1rem;">Draft</span>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <h5 class="fw-bold mb-2">Item Pekerjaan</h5>
                                <button type="button" class="btn btn-primary"><i class="fa fa-plus me-1"></i> Tambah
                                    Cetakan</button>
                            </div>
                            <div class="table-responsive mb-2 bg-white border-2 border-dark">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nama Item</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Catatan</th>
                                            <th style="width: 90px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><b>Cetak Brosur A4</b></td>
                                            <td>1000</td>
                                            <td>lembar</td>
                                            <td>Full color, dua sisi</td>
                                            <td>
                                                <button class="btn btn-sm btn-light" title="Edit"><i
                                                        class="fa fa-edit"></i></button>
                                                <button class="btn btn-sm btn-light" title="Hapus"><i
                                                        class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Desain Grafis</b></td>
                                            <td>1</td>
                                            <td>desain</td>
                                            <td>Revisi maksimal 3x</td>
                                            <td>
                                                <button class="btn btn-sm btn-light" title="Edit"><i
                                                        class="fa fa-edit"></i></button>
                                                <button class="btn btn-sm btn-light" title="Hapus"><i
                                                        class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-muted small mb-2"><i class="fa fa-info-circle me-1"></i> Perubahan disimpan
                                otomatis</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@extends('layout.master')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('spk.index') }}">Transaksi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('spk.index') }}">SPK</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail SPK</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <!-- Tombol Kembali -->
            <div class="mb-3">
                <a href="{{ route('spk.index') }}" class="btn btn-secondary btn-sm">
                    <i class="link-icon icon-sm" data-feather="arrow-left"></i> Kembali ke List
                </a>
            </div>

            <!-- Info Dasar SPK -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="card-title mb-3">Informasi SPK</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%;">Nomor SPK</td>
                                    <td>: {{ $spk->nomor_spk }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tanggal SPK</td>
                                    <td>:
                                        {{ \Carbon\Carbon::parse($spk->tanggal_spk)->locale('id')->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Status</td>
                                    <td>:
                                        @if($spk->status == 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($spk->status == 'proses_bayar')
                                            <span class="badge bg-warning text-dark">Proses Pembayaran</span>
                                        @elseif($spk->status == 'proses_produksi')
                                            <span class="badge bg-primary">Proses Produksi</span>
                                        @elseif($spk->status == 'sudah_cetak')
                                            <span class="badge bg-info">Sudah Cetak</span>
                                        @elseif($spk->status == 'siap_antar')
                                            <span class="badge bg-success">Siap Antar</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $spk->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="card-title mb-3">Informasi Pelanggan</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%;">Nama Pelanggan</td>
                                    <td>: {{ $spk->pelanggan->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Email</td>
                                    <td>: {{ $spk->pelanggan->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">No. Telepon</td>
                                    <td>: {{ $spk->pelanggan->no_telp ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-4">Track Status SPK</h6>
                    @php
                        $getStatusDate = function ($spk, $activityCode) {
                            $log = $spk->activityLogs->where('aktivitas', $activityCode)->sortBy('created_at')->first();
                            return $log ? \Carbon\Carbon::parse($log->created_at)->locale('id')->translatedFormat('d M Y H:i') : 'Pending';
                        };
                    @endphp

                    <div id="statusTimeline">
                        <ul class="timeline">
                            <!-- Draft -->
                            <li class="event {{ in_array($spk->status, ['draft', 'proses_bayar', 'proses_produksi', 'sudah_cetak', 'siap_antar']) ? 'active' : '' }}"
                                data-date="{{ $spk->created_at ? \Carbon\Carbon::parse($spk->created_at)->locale('id')->translatedFormat('d M Y H:i') : 'Pending' }}">
                                <h3 class="title">Draft</h3>
                                <p>SPK dalam tahap draft dan belum disetujui.</p>
                            </li>

                            <!-- Proses Pembayaran -->
                            <li class="event {{ in_array($spk->status, ['proses_bayar', 'proses_produksi', 'sudah_cetak', 'siap_antar']) ? 'active' : '' }}"
                                data-date="{{ $getStatusDate($spk, 'spk_acc_proses_bayar') }}">
                                <h3 class="title">Proses Pembayaran</h3>
                                <p>SPK sudah disetujui dan menunggu proses pembayaran dari pelanggan.</p>
                            </li>

                            <!-- Proses Produksi -->
                            <li class="event {{ in_array($spk->status, ['proses_produksi', 'sudah_cetak', 'siap_antar']) ? 'active' : '' }}"
                                data-date="{{ $getStatusDate($spk, 'spk_proses_produksi') }}">
                                <h3 class="title">Proses Produksi</h3>
                                <p>Pembayaran sudah diterima, produksi dimulai sekarang.</p>
                            </li>

                            <!-- Sudah Cetak -->
                            <li class="event {{ in_array($spk->status, ['sudah_cetak', 'siap_antar']) ? 'active' : '' }}"
                                data-date="{{ $getStatusDate($spk, 'spk_sudah_cetak') }}">
                                <h3 class="title">Sudah Cetak</h3>
                                <p>Produksi selesai, hasil cetak sudah siap.</p>
                            </li>

                            <!-- Siap Antar -->
                            <li class="event {{ $spk->status == 'siap_antar' ? 'active' : '' }}"
                                data-date="{{ $getStatusDate($spk, 'spk_siap_antar') }}">
                                <h3 class="title">Siap Antar</h3>
                                <p>Pesanan siap untuk dikirim kepada pelanggan.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Detail Items Pekerjaan -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-4">Detail Item Pekerjaan</h6>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($spk->items as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->nama_produk }}</td>
                                        <td class="text-center">{{ $item->jumlah }}</td>
                                        <td>{{ $item->satuan }}</td>
                                        <td class="text-end">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->harga_satuan * $item->jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3 text-muted">Tidak ada item pekerjaan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Total Biaya -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-semibold" style="width: 60%;">Total Biaya</td>
                                    <td class="text-end">Rp {{ number_format($spk->total_biaya, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan/Deskripsi (jika ada field di database) -->
            @if($spk->deskripsi || $spk->catatan)
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Catatan & Deskripsi</h6>
                        <p class="text-muted">{{ $spk->deskripsi ?? $spk->catatan ?? '-' }}</p>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('spk.index') }}" class="btn btn-secondary">
                            <i class="link-icon icon-sm" data-feather="arrow-left"></i> Kembali
                        </a>

                        @if($spk->status === 'draft')
                            <a href="{{ route('spk.edit', $spk->id) }}" class="btn btn-warning">
                                <i class="link-icon icon-sm" data-feather="edit"></i> Edit
                            </a>

                            <form action="{{ route('spk.acc', $spk->id) }}" method="POST" class="d-inline-block form-acc-spk">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="btn btn-success btn-acc-spk">
                                    <i class="link-icon icon-sm" data-feather="check-circle"></i> ACC ke Proses Pembayaran
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <style>
        .timeline .event.active:after {
            background-color: #198754;
        }
    </style>

    <script>
        feather.replace();

        document.querySelectorAll('.btn-acc-spk').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'ACC SPK ini?',
                    text: 'Status akan diubah menjadi Proses Pembayaran dan Anda akan diarahkan ke halaman kasir.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Acc',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.closest('form').submit();
                    }
                });
            });
        });

        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        @endif
    </script>
@endpush
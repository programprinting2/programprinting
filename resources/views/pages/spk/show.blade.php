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
                                        @php
                                            $statusLabels = [
                                                'draft' => 'Draft',
                                                'proses_bayar' => 'Proses Pembayaran',
                                                'manager_approval_order' => 'Manager Approval Order',
                                                'operator_cetak' => 'Operator Cetak',
                                                'finishing_qc' => 'Finishing / QC',
                                                'siap_diambil' => 'Siap Diambil',
                                                'selesai' => 'Selesai',
                                            ];

                                            $statusBadges = [
                                                'draft' => 'badge bg-secondary',
                                                'proses_bayar' => 'badge bg-warning text-dark',
                                                'manager_approval_order' => 'badge bg-primary',
                                                'operator_cetak' => 'badge bg-info text-dark',
                                                'finishing_qc' => 'badge bg-info',
                                                'siap_diambil' => 'badge bg-success',
                                                'selesai' => 'badge bg-success',
                                            ];

                                            $currentStatus = $spk->status;
                                            $label = $statusLabels[$currentStatus] ?? ($currentStatus ?? '-');
                                            $badgeClass = $statusBadges[$currentStatus] ?? 'badge bg-light text-dark';
                                        @endphp

                                        <span class="{{ $badgeClass }}">{{ $label }}</span>
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
                        $statusFlow = [
                            'draft',
                            'proses_bayar',
                            'manager_approval_order',
                            'operator_cetak',
                            'finishing_qc',
                            'siap_diambil',
                            'selesai',
                        ];

                        $statusLabels = [
                            'draft' => 'Draft',
                            'proses_bayar' => 'Proses Pembayaran',
                            'manager_approval_order' => 'Manager Approval Order',
                            'operator_cetak' => 'Operator Cetak',
                            'finishing_qc' => 'Finishing / QC',
                            'siap_diambil' => 'Siap Diambil',
                            'selesai' => 'Selesai',
                        ];

                        $statusDescriptions = [
                            'draft' => 'SPK dalam tahap draft dan belum di-ACC.',
                            'proses_bayar' => 'SPK sudah di-ACC dan menunggu proses pembayaran.',
                            'manager_approval_order' => 'SPK telah disetujui oleh Manager Order.',
                            'operator_cetak' => 'SPK sedang/ sudah diproses oleh Operator Cetak.',
                            'finishing_qc' => 'SPK sedang dalam proses Finishing / QC.',
                            'siap_diambil' => 'Pesanan siap diambil oleh pelanggan.',
                            'selesai' => 'SPK/pekerjaan telah selesai sepenuhnya.',
                        ];

                        $statusActivityCodes = [
                            'proses_bayar' => 'spk_acc_proses_bayar',
                            'manager_approval_order' => 'spk_manager_approval_order',
                            'operator_cetak' => 'spk_operator_cetak',
                            'finishing_qc' => 'spk_finishing_qc',
                            'siap_diambil' => 'spk_siap_diambil',
                            'selesai' => 'spk_selesai',
                        ];

                        $getStatusDate = function ($spk, ?string $activityCode) {
                            if (!$activityCode) {
                                return 'Pending';
                            }

                            $log = $spk->activityLogs
                                ->where('aktivitas', $activityCode)
                                ->sortBy('created_at')
                                ->first();

                            return $log
                                ? \Carbon\Carbon::parse($log->created_at)->locale('id')->translatedFormat('d M Y H:i')
                                : 'Pending';
                        };

                        $currentIndex = array_search($spk->status, $statusFlow, true);
                    @endphp

                    <div id="statusTimeline">
                        <ul class="timeline">
                            @foreach($statusFlow as $index => $statusKey)
                                @php
                                    $isActive = ($currentIndex !== false && $index <= $currentIndex);
                                    $label = $statusLabels[$statusKey] ?? $statusKey;
                                    $desc = $statusDescriptions[$statusKey] ?? '';
                                    if ($isActive) {
                                        if ($statusKey === 'draft') {
                                            $date = $spk->created_at
                                                ? \Carbon\Carbon::parse($spk->created_at)
                                                    ->locale('id')
                                                    ->translatedFormat('d M Y H:i')
                                                : 'Pending';
                                        } else {
                                            $activityCode = $statusActivityCodes[$statusKey] ?? null;

                                            $log = $activityCode
                                                ? $spk->activityLogs
                                                    ->where('aktivitas', $activityCode)
                                                    ->sortBy('created_at')
                                                    ->first()
                                                : null;

                                            $date = $log
                                                ? \Carbon\Carbon::parse($log->created_at)
                                                    ->locale('id')
                                                    ->translatedFormat('d M Y H:i')
                                                : 'Pending';
                                        }
                                    } else {
                                        $date = 'Pending';
                                    }
                                @endphp

                                <li class="event {{ $isActive ? 'active' : '' }}" data-date="{{ $date }}">
                                    <h3 class="title">{{ $label }}</h3>
                                    <p>{{ $desc }}</p>
                                </li>
                            @endforeach
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
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Biaya Produk</th>
                                    <th class="text-end">Biaya Finishing</th>
                                    <th class="text-end">Total Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($spk->items as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->nama_produk }}</td>
                                        <td class="text-center">{{ $item->jumlah }}</td>
                                        <td>{{ $item->satuan }}</td>
                                        <td class="text-end">Rp {{ number_format($item->biaya_produk / $item->jumlah, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->biaya_produk, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($item->biaya_finishing, 0, ',', '.') ?? "-" }}</td>
                                        <td class="text-end fw-semibold">Rp
                                            {{ number_format($item->total_biaya, 0, ',', '.') }}
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
                            <a href="{{ route('spk.edit', $spk) }}" class="btn btn-warning">
                                <i class="link-icon icon-sm" data-feather="edit"></i> Edit
                            </a>

                            <form action="{{ route('spk.acc', $spk) }}" method="POST" class="d-inline-block form-acc-spk">
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
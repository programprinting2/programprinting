@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('pembukuan.index') }}">Pembukuan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Jurnal Umum</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
              <h6 class="card-title mb-1">Jurnal Umum</h6>
              <p class="text-muted mb-0">Daftar transaksi jurnal dummy untuk review tampilan.</p>
            </div>
            <button class="btn btn-primary btn-sm" disabled>
              <i data-feather="plus" class="icon-sm me-1"></i> Buat Jurnal
            </button>
          </div>

          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>No Jurnal</th>
                  <th>Keterangan</th>
                  <th class="text-end">Debit</th>
                  <th class="text-end">Kredit</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($journals as $journal)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($journal['tanggal'])->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $journal['nomor'] }}</td>
                    <td>{{ $journal['keterangan'] }}</td>
                    <td class="text-end">Rp {{ number_format($journal['debit'], 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($journal['kredit'], 0, ',', '.') }}</td>
                    <td>
                      @if($journal['status'] === 'Posted')
                        <span class="badge bg-success">Posted</span>
                      @else
                        <span class="badge bg-warning text-dark">Draft</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('custom-scripts')
<script>
  feather.replace();
</script>
@endpush

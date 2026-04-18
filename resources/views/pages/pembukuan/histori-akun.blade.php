@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('pembukuan.index') }}">Pembukuan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Histori Akun</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div>
              <h6 class="card-title mb-1">Histori Akun</h6>
              <p class="text-muted mb-0">Akun terpilih: {{ $akunTerpilih }} (dummy display).</p>
            </div>
            <div>
              <button class="btn btn-outline-secondary btn-sm" disabled>Filter Periode</button>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Ref</th>
                  <th>Keterangan</th>
                  <th class="text-end">Debit</th>
                  <th class="text-end">Kredit</th>
                  <th class="text-end">Saldo Berjalan</th>
                </tr>
              </thead>
              <tbody>
                @php($running = 0)
                @foreach($ledgerRows as $row)
                  @php($running += ($row['debit'] - $row['kredit']))
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d/m/Y') }}</td>
                    <td class="fw-semibold">{{ $row['ref'] }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                    <td class="text-end">Rp {{ number_format($row['debit'], 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($row['kredit'], 0, ',', '.') }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($running, 0, ',', '.') }}</td>
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

@extends('layout.master')

@section('content')
  <nav class="page-breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('pembukuan.index') }}">Pembukuan</a></li>
      <li class="breadcrumb-item active" aria-current="page">Log Aktivitas Jurnal</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h6 class="card-title mb-1">Log Aktivitas Jurnal</h6>
              <p class="text-muted mb-0">Audit trail dummy untuk validasi alur UI.</p>
            </div>
            <span class="badge bg-light text-dark border">Read-only</span>
          </div>

          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Waktu</th>
                  <th>User</th>
                  <th>Aksi</th>
                  <th>Target</th>
                  <th>Level</th>
                </tr>
              </thead>
              <tbody>
                @foreach($logs as $log)
                  <tr>
                    <td>{{ $log['waktu'] }}</td>
                    <td class="fw-semibold">{{ $log['user'] }}</td>
                    <td>{{ $log['aksi'] }}</td>
                    <td>{{ $log['target'] }}</td>
                    <td>
                      @if($log['level'] === 'Critical')
                        <span class="badge bg-danger">Critical</span>
                      @elseif($log['level'] === 'Warning')
                        <span class="badge bg-warning text-dark">Warning</span>
                      @else
                        <span class="badge bg-info text-dark">Info</span>
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

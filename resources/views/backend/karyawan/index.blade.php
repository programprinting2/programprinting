@extends('layout.master')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">Master</a></li>
    <li class="breadcrumb-item active" aria-current="page">Karyawan</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="card-title">
          <div class="row">
            <div class="col-md-6">
              <h6 class="card-title">Data Karyawan</h6>
            </div>
            <div class="col-md-6 text-right">
              <button type="button" class="btn btn-primary float-end d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalKaryawan">
                <i class="link-icon icon-sm me-1" data-feather="plus"></i> Tambah Karyawan
              </button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID Karyawan</th>
                <th>Nama Lengkap</th>
                <th>Posisi</th>
                <th>Departemen</th>
                <th>Tanggal Masuk</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($karyawan as $k)
              <tr>
                <td>{{ $k->id_karyawan }}</td>
                <td>{{ $k->nama_lengkap }}</td>
                <td>{{ $k->posisi }}</td>
                <td>{{ $k->departemen }}</td>
                <td>{{ $k->tanggal_masuk->format('d/m/Y') }}</td>
                <td>
                  <span class="badge {{ $k->status == 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                    {{ $k->status }}
                  </span>
                </td>
                <td>
                  <div class="btn-group gap-1" role="group">
                    <button type="button" class="btn btn-warning btn-xs rounded" onclick="loadKaryawanData({{ $k->id }})">
                      <i class="link-icon icon-sm" data-feather="edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs rounded btn-delete-karyawan" 
                            data-id="{{ $k->id }}" 
                            data-nama="{{ $k->nama_lengkap }}">
                      <i class="link-icon icon-sm" data-feather="trash"></i>
                    </button>
                    <form id="formDeleteKaryawan{{ $k->id }}" 
                          action="{{ route('backend.karyawan.destroy', $k->id) }}" 
                          method="POST" style="display: none;">
                      @csrf
                      @method('DELETE')
                    </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between mt-4">
          <p class="text-muted">Menampilkan {{ count($karyawan) }} karyawan.</p>
          {{ $karyawan->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>

@include('backend.karyawan.modal_form')
@include('backend.karyawan.modal_edit')

@endsection 
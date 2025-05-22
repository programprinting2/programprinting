<div id="tableView" class="d-none">
<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 70px">Gambar</th>
                <th style="min-width: 180px; width: 20%">Nama Mesin</th>
                <th style="width: 12%">Tipe</th>
                <th style="width: 12%">Merek</th>
                <th style="width: 12%">Model</th>
                <th style="width: 10%">Lebar Max</th>
                <th style="width: 12%">Resolusi</th>
                <th style="width: 10%">Tinta</th>
                <th style="width: 12%">Status</th>
                <th style="width: 130px; text-align: center">Aksi</th>
            </tr>
        </thead>
        <tbody>
                @forelse($mesin as $item)
                <tr class="mesin-item" 
                    data-type="{{ $item->tipe_mesin }}" 
                    data-status="{{ $item->status }}" 
                    data-name="{{ $item->nama_mesin }}" 
                    data-brand="{{ $item->merek }}" 
                    data-model="{{ $item->model }}">
                    <td>
                        @if($item->thumbnailUrl)
                            <img src="{{ $item->thumbnailUrl }}" alt="{{ $item->nama_mesin }}" 
                                 class="img-thumbnail cursor-pointer" style="width: 50px; height: 50px; object-fit: cover;"
                                 data-bs-toggle="modal" data-bs-target="#imageModal{{ $item->id }}">
                        @else
                            <div class="bg-light text-center" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="image" style="width: 20px; height: 20px; color: #aaa;"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-medium text-nowrap">{{ $item->nama_mesin }}</td>
                    <td><span class="badge bg-primary">{{ $item->tipe_mesin }}</span></td>
                    <td>{{ $item->merek ?: '-' }}</td>
                    <td>{{ $item->model ?: '-' }}</td>
                    <td>{{ $item->lebar_media_maksimum ? $item->lebar_media_maksimum . ' cm' : '-' }}</td>
                    <td>
                    @php
                        $resolusi = '-';
                        if(is_array($item->detail_mesin)) {
                            foreach($item->detail_mesin as $detail) {
                                if(isset($detail['nama']) && (strtolower($detail['nama']) == 'resolusi' || strtolower($detail['nama']) == 'resolusi default')) {
                                    $resolusi = $detail['nilai'] . ' ' . ($detail['satuan'] ?? '');
                                    break;
                                }
                            }
                        }
                    @endphp
                    {{ $resolusi }}
                    </td>
                    <td>
                    @php
                        $tinta = '-';
                        if(is_array($item->detail_mesin)) {
                            foreach($item->detail_mesin as $detail) {
                                if(isset($detail['nama']) && (strtolower($detail['nama']) == 'tinta' || strtolower($detail['nama']) == 'jenis tinta')) {
                                    $tinta = $detail['nilai'];
                                    break;
                                }
                            }
                        }
                    @endphp
                    {{ $tinta }}
                    </td>
                    <td><span class="badge bg-{{ 
                        $item->status == 'Aktif' ? 'success' : 
                        ($item->status == 'Maintenance' ? 'warning' : 
                         ($item->status == 'Rusak' ? 'danger' : 'secondary')) 
                    }}">
                        <i data-feather="{{ 
                            $item->status == 'Aktif' ? 'check-circle' : 
                            ($item->status == 'Maintenance' ? 'tool' : 
                             ($item->status == 'Rusak' ? 'alert-triangle' : 'slash')) 
                        }}" class="icon-status me-1"></i>
                        {{ $item->status }}
                    </span></td>
                <td>
                    <div class="d-flex justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-info text-white" 
                                data-bs-toggle="modal" data-bs-target="#detailMesinModal{{ $item->id }}" 
                                title="Detail">
                            <i data-feather="eye" class="icon-sm"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" 
                                data-bs-toggle="modal" data-bs-target="#editMesinModal{{ $item->id }}" 
                                title="Edit">
                            <i data-feather="edit-2" class="icon-sm"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete-mesin" 
                                data-id="{{ $item->id }}" 
                                data-nama="{{ $item->nama_mesin }}" 
                                title="Hapus">
                            <i data-feather="trash-2" class="icon-sm"></i>
                        </button>
                        <form id="formDeleteMesin{{ $item->id }}" 
                              action="{{ route('backend.master-mesin.destroy', $item->id) }}" 
                              method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </td>
            </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada data mesin.</td>
            </tr>
                @endforelse
        </tbody>
    </table>
    </div>
</div> 
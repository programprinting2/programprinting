<?php

namespace App\Repositories\Eloquent;

use App\Models\BahanBaku;
use App\Repositories\Interfaces\BahanBakuRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BahanBakuRepository implements BahanBakuRepositoryInterface
{
    protected BahanBaku $model;

    public function __construct(BahanBaku $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['pemasokUtama', 'subKategoriDetail', 'kategoriDetail'])->get();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['pemasokUtama', 'subKategoriDetail', 'kategoriDetail']);

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_bahan) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kode_bahan) LIKE ?', ['%' . $search . '%'])
                  ->orWhereHas('kategoriDetail', function($subQuery) use ($search) {
                      $subQuery->whereRaw('LOWER(nama_detail_parameter) LIKE ?', ['%' . $search . '%']);
                  })
                  ->orWhereHas('subKategoriDetail', function($subQuery) use ($search) {
                      $subQuery->whereRaw('LOWER(nama_sub_detail_parameter) LIKE ?', ['%' . $search . '%']);
                  });
            });
        }

        if (isset($filters['kategori'])) {
            $query->where('kategori_id', $filters['kategori']);
        }

        if (isset($filters['sub_kategori'])) {
            $query->where('sub_kategori_id', $filters['sub_kategori']);
        }

        if (isset($filters['status'])) {
            $query->where('status_aktif', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?BahanBaku
    {
        return $this->model->find($id);
    }

    public function findByKode(string $kode): ?BahanBaku
    {
        return $this->model->where('kode_bahan', $kode)->first();
    }

    public function findWithRelations(int $id): ?BahanBaku
    {
        return $this->model->with(['pemasokUtama', 'subKategoriDetail', 'kategoriDetail', 'satuanUtamaDetail'])->find($id);
    }

    public function search(string $search): Collection
    {
        $search = strtolower($search);
        return $this->model->where(function($q) use ($search) {
            $q->whereRaw('LOWER(nama_bahan) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(kode_bahan) LIKE ?', ['%' . $search . '%']);
        })->get();
    }

    public function getByKategori(int $kategoriId): Collection
    {
        return $this->model->where('kategori_id', $kategoriId)->get();
    }

    public function getBySubKategori(int $subKategoriId): Collection
    {
        return $this->model->where('sub_kategori_id', $subKategoriId)->get();
    }

    public function getByStatus(bool $status): Collection
    {
        return $this->model->where('status_aktif', $status)->get();
    }

    public function create(array $data): BahanBaku
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $bahanBaku = $this->find($id);
        return $bahanBaku ? $bahanBaku->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $bahanBaku = $this->find($id);
        return $bahanBaku ? $bahanBaku->delete() : false;
    }

    public function count(): int
    {
        return $this->model->count();
    }
}











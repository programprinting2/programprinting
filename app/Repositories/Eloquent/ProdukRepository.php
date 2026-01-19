<?php

namespace App\Repositories\Eloquent;

use App\Models\Produk;
use App\Repositories\Interfaces\ProdukRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProdukRepository implements ProdukRepositoryInterface
{
    protected Produk $model;

    public function __construct(Produk $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['kategoriUtama', 'subKategori', 'satuan', 'subSatuan'])->get();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['kategoriUtama', 'subKategori', 'satuan', 'subSatuan']);

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_produk) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kode_produk) LIKE ?', ['%' . $search . '%']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('status_aktif', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Produk
    {
        return $this->model->find($id);
    }

    public function findByKode(string $kode): ?Produk
    {
        return $this->model->where('kode_produk', $kode)->first();
    }

    public function findWithRelations(int $id): ?Produk
    {
        return $this->model->with(['kategoriUtama', 'subKategori', 'satuan', 'subSatuan', 'bahanBakus.satuanUtamaDetail'])->find($id);
    }

    public function search(string $search): Collection
    {
        $search = strtolower($search);
        return $this->model->where(function($q) use ($search) {
            $q->whereRaw('LOWER(nama_produk) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(kode_produk) LIKE ?', ['%' . $search . '%']);
        })->get();
    }

    public function getByStatus(bool $status): Collection
    {
        return $this->model->where('status_aktif', $status)->get();
    }

    public function create(array $data): Produk
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Produk
    {
        $produk = $this->find($id);
        if (!$produk) {
            return null;
        }
        
        $produk->update($data);
        return $produk->fresh();
    }

    public function delete(int $id): bool
    {
        $produk = $this->find($id);
        return $produk ? $produk->delete() : false;
    }

    public function count(): int
    {
        return $this->model->count();
    }
}











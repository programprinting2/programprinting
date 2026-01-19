<?php

namespace App\Repositories\Eloquent;

use App\Models\Gudang;
use App\Repositories\Interfaces\GudangRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GudangRepository implements GudangRepositoryInterface
{
    protected Gudang $model;

    public function __construct(Gudang $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->withCount('rak')->get();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query()->withCount('rak');

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(kode_gudang) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(nama_gudang) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(manager) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(alamat) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kota) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(provinsi) LIKE ?', ['%' . $search . '%']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Gudang
    {
        return $this->model->find($id);
    }

    public function findByKode(string $kode): ?Gudang
    {
        return $this->model->where('kode_gudang', $kode)->first();
    }

    public function findWithRak(int $id): ?Gudang
    {
        return $this->model->with('rak')->find($id);
    }

    public function search(string $search): Collection
    {
        $search = strtolower($search);
        return $this->model->where(function($q) use ($search) {
            $q->whereRaw('LOWER(nama_gudang) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(kode_gudang) LIKE ?', ['%' . $search . '%']);
        })->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function create(array $data): Gudang
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $gudang = $this->find($id);
        return $gudang ? $gudang->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $gudang = $this->find($id);
        return $gudang ? $gudang->delete() : false;
    }

    public function count(): int
    {
        return $this->model->count();
    }
}











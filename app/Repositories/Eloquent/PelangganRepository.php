<?php

namespace App\Repositories\Eloquent;

use App\Models\Pelanggan;
use App\Repositories\Interfaces\PelangganRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PelangganRepository implements PelangganRepositoryInterface
{
    protected Pelanggan $model;

    public function __construct(Pelanggan $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kode_pelanggan) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(no_telp) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(handphone) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
            });
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Pelanggan
    {
        return $this->model->find($id);
    }

    public function findByKode(string $kode): ?Pelanggan
    {
        return $this->model->where('kode_pelanggan', $kode)->first();
    }

    public function search(string $search): Collection
    {
        $search = strtolower($search);
        return $this->model->where(function($q) use ($search) {
            $q->whereRaw('LOWER(nama) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(kode_pelanggan) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
        })->get();
    }

    public function getByStatus(bool $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function create(array $data): Pelanggan
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $pelanggan = $this->find($id);
        return $pelanggan ? $pelanggan->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $pelanggan = $this->find($id);
        return $pelanggan ? $pelanggan->delete() : false;
    }

    public function count(): int
    {
        return $this->model->count();
    }
}











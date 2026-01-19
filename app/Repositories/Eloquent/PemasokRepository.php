<?php

namespace App\Repositories\Eloquent;

use App\Models\Pemasok;
use App\Repositories\Interfaces\PemasokRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PemasokRepository implements PemasokRepositoryInterface
{
    protected Pemasok $model;

    public function __construct(Pemasok $model)
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

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(kode_pemasok) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(no_telp) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(handphone) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Pemasok
    {
        return $this->model->find($id);
    }

    public function findByKode(string $kode): ?Pemasok
    {
        return $this->model->where('kode_pemasok', $kode)->first();
    }

    public function search(string $search): Collection
    {
        $search = strtolower($search);
        return $this->model->where(function($q) use ($search) {
            $q->whereRaw('LOWER(nama) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(kode_pemasok) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
        })->get();
    }

    public function getByStatus(bool $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function create(array $data): Pemasok
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $pemasok = $this->find($id);
        return $pemasok ? $pemasok->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $pemasok = $this->find($id);
        return $pemasok ? $pemasok->delete() : false;
    }

    public function count(): int
    {
        return $this->model->count();
    }
}











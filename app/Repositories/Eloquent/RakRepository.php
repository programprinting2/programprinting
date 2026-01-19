<?php

namespace App\Repositories\Eloquent;

use App\Models\Rak;
use App\Repositories\Interfaces\RakRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RakRepository implements RakRepositoryInterface
{
    protected Rak $model;

    public function __construct(Rak $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('gudang')->get();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with('gudang');

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('kode_rak', 'like', "%$search%")
                  ->orWhere('nama_rak', 'like', "%$search%")
                  ->orWhereHas('gudang', function($q2) use ($search) {
                      $q2->where('nama_gudang', 'like', "%$search%")
                         ->orWhere('kode_gudang', 'like', "%$search%");
                  });
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['gudang_id'])) {
            $query->where('gudang_id', $filters['gudang_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Rak
    {
        return $this->model->find($id);
    }

    public function findByKode(string $kode): ?Rak
    {
        return $this->model->where('kode_rak', $kode)->first();
    }

    public function findWithGudang(int $id): ?Rak
    {
        return $this->model->with('gudang')->find($id);
    }

    public function getByGudang(int $gudangId): Collection
    {
        return $this->model->where('gudang_id', $gudangId)
            ->with('gudang')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function create(array $data): Rak
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $rak = $this->find($id);
        return $rak ? $rak->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $rak = $this->find($id);
        return $rak ? $rak->delete() : false;
    }

    public function count(): int
    {
        return $this->model->count();
    }
}











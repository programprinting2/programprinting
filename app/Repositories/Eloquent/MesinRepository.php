<?php

namespace App\Repositories\Eloquent;

use App\Models\MasterMesin;
use App\Repositories\Interfaces\MesinRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MesinRepository implements MesinRepositoryInterface
{
    protected MasterMesin $model;

    public function __construct(MasterMesin $model)
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
            $search = strtolower(trim($filters['search']));
            $search = preg_replace('/[^a-z0-9\s]/', '', $search);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(nama_mesin) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(merek) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(model) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(nomor_seri) LIKE ?', ['%' . $search . '%']);
            });
        }

        if (isset($filters['type']) && $filters['type'] !== 'semua') {
            $query->where('tipe_mesin', $filters['type']);
        }

        if (isset($filters['status']) && $filters['status'] !== 'semua') {
            if (in_array($filters['status'], ['Aktif', 'Maintenance', 'Rusak', 'Tidak Aktif'])) {
                $query->where('status', $filters['status']);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): ?MasterMesin
    {
        return $this->model->find($id);
    }

    public function search(string $search): Collection
    {
        $search = strtolower(trim($search));
        $search = preg_replace('/[^a-z0-9\s]/', '', $search);
        return $this->model->where(function($q) use ($search) {
            $q->whereRaw('LOWER(nama_mesin) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(merek) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(model) LIKE ?', ['%' . $search . '%']);
        })->get();
    }

    public function getByTipe(string $tipe): Collection
    {
        return $this->model->where('tipe_mesin', $tipe)->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function create(array $data): MasterMesin
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $mesin = $this->find($id);
        return $mesin ? $mesin->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $mesin = $this->find($id);
        return $mesin ? $mesin->delete() : false;
    }

    public function count(): int
    {
        return $this->model->count();
    }
}











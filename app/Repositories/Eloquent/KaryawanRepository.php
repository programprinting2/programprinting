<?php

namespace App\Repositories\Eloquent;

use App\Models\Karyawan;
use App\Repositories\Interfaces\KaryawanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class KaryawanRepository implements KaryawanRepositoryInterface
{
    protected Karyawan $model;

    public function __construct(Karyawan $model)
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
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(posisi) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(departemen) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(id_karyawan) LIKE ?', ['%' . $search . '%']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Karyawan
    {
        return $this->model->find($id);
    }

    public function findByIdKaryawan(string $idKaryawan): ?Karyawan
    {
        return $this->model->where('id_karyawan', $idKaryawan)->first();
    }

    public function search(string $search): Collection
    {
        $search = strtolower($search);
        return $this->model->where(function($q) use ($search) {
            $q->whereRaw('LOWER(nama_lengkap) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(id_karyawan) LIKE ?', ['%' . $search . '%'])
              ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
        })->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function getAktif(): Collection
    {
        return $this->model->aktif()->get();
    }

    public function create(array $data): Karyawan
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $karyawan = $this->find($id);
        return $karyawan ? $karyawan->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $karyawan = $this->find($id);
        return $karyawan ? $karyawan->delete() : false;
    }

    public function count(): int
    {
        return $this->model->count();
    }
}









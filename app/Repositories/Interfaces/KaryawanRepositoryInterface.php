<?php

namespace App\Repositories\Interfaces;

use App\Models\Karyawan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface KaryawanRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?Karyawan;
    public function findByIdKaryawan(string $idKaryawan): ?Karyawan;
    public function search(string $search): Collection;
    public function getByStatus(string $status): Collection;
    public function getAktif(): Collection;
    public function create(array $data): Karyawan;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}









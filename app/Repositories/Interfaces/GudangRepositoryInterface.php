<?php

namespace App\Repositories\Interfaces;

use App\Models\Gudang;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface GudangRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?Gudang;
    public function findByKode(string $kode): ?Gudang;
    public function findWithRak(int $id): ?Gudang;
    public function search(string $search): Collection;
    public function getByStatus(string $status): Collection;
    public function create(array $data): Gudang;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}









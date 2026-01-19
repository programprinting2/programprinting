<?php

namespace App\Repositories\Interfaces;

use App\Models\Pemasok;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PemasokRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?Pemasok;
    public function findByKode(string $kode): ?Pemasok;
    public function search(string $search): Collection;
    public function getByStatus(bool $status): Collection;
    public function create(array $data): Pemasok;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}











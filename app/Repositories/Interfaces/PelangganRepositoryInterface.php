<?php

namespace App\Repositories\Interfaces;

use App\Models\Pelanggan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PelangganRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?Pelanggan;
    public function findByKode(string $kode): ?Pelanggan;
    public function search(string $search): Collection;
    public function getByStatus(bool $status): Collection;
    public function create(array $data): Pelanggan;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}











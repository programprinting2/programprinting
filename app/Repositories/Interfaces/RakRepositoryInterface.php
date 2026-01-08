<?php

namespace App\Repositories\Interfaces;

use App\Models\Rak;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RakRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?Rak;
    public function findByKode(string $kode): ?Rak;
    public function findWithGudang(int $id): ?Rak;
    public function getByGudang(int $gudangId): Collection;
    public function getByStatus(string $status): Collection;
    public function create(array $data): Rak;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}









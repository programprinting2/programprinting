<?php

namespace App\Repositories\Interfaces;

use App\Models\SPK;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SpkRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10): LengthAwarePaginator;
    public function find(int $id): ?SPK;
    public function findWithRelations(int $id): ?SPK;
    public function findByNomor(string $nomor): ?SPK;
    public function create(array $data): SPK;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByCustomer(int $customerId): Collection;
    public function getByStatus(string $status): Collection;
    public function getByPrioritas(string $prioritas): Collection;
    public function countByStatus(): array;
}









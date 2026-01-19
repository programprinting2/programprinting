<?php

namespace App\Repositories\Interfaces;

use App\Models\Produk;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProdukRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?Produk;
    public function findByKode(string $kode): ?Produk;
    public function findWithRelations(int $id): ?Produk;
    public function search(string $search): Collection;
    public function getByStatus(bool $status): Collection;
    public function create(array $data): Produk;
    public function update(int $id, array $data): Produk;
    public function delete(int $id): bool;
    public function count(): int;
}











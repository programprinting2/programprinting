<?php

namespace App\Repositories\Interfaces;

use App\Models\BahanBaku;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BahanBakuRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?BahanBaku;
    public function findByKode(string $kode): ?BahanBaku;
    public function findWithRelations(int $id): ?BahanBaku;
    public function search(string $search): Collection;
    public function getByKategori(int $kategoriId): Collection;
    public function getBySubKategori(int $subKategoriId): Collection;
    public function getByStatus(bool $status): Collection;
    public function create(array $data): BahanBaku;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}









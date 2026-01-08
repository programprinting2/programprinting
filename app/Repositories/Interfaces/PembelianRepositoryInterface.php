<?php

namespace App\Repositories\Interfaces;

use App\Models\Pembelian;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PembelianRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?Pembelian;
    public function findByKode(string $kode): ?Pembelian;
    public function findWithRelations(int $id): ?Pembelian;
    public function getByPemasok(int $pemasokId): Collection;
    public function getByDateRange(string $startDate, string $endDate): Collection;
    public function create(array $data): Pembelian;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getTotalByMonth(int $year, int $month): float;
}









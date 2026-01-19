<?php

namespace App\Repositories\Interfaces;

use App\Models\PembelianItem;
use Illuminate\Database\Eloquent\Collection;

interface PembelianItemRepositoryInterface
{
    public function createForPembelian(int $pembelianId, array $data): PembelianItem;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByPembelian(int $pembelianId): Collection;
    public function deleteByPembelian(int $pembelianId): bool;
}











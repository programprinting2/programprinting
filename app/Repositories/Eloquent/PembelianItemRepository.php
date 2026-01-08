<?php

namespace App\Repositories\Eloquent;

use App\Models\PembelianItem;
use App\Repositories\Interfaces\PembelianItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PembelianItemRepository implements PembelianItemRepositoryInterface
{
    protected PembelianItem $model;

    public function __construct(PembelianItem $model)
    {
        $this->model = $model;
    }

    public function createForPembelian(int $pembelianId, array $data): PembelianItem
    {
        $data['pembelian_id'] = $pembelianId;
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $item = $this->model->find($id);
        return $item ? $item->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $item = $this->model->find($id);
        return $item ? $item->delete() : false;
    }

    public function getByPembelian(int $pembelianId): Collection
    {
        return $this->model->where('pembelian_id', $pembelianId)
            ->with('bahanBaku')
            ->get();
    }

    public function deleteByPembelian(int $pembelianId): bool
    {
        return $this->model->where('pembelian_id', $pembelianId)->delete();
    }
}









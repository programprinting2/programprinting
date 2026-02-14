<?php

namespace App\Repositories\Eloquent;

use App\Models\SPKItem;
use App\Repositories\Interfaces\SpkItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SpkItemRepository implements SpkItemRepositoryInterface
{
    protected SPKItem $model;

    public function __construct(SPKItem $model)
    {
        $this->model = $model;
    }

    public function createForSpk(int $spkId, array $data): SPKItem
    {
        $data['spk_id'] = $spkId;
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

    public function getBySpk(int $spkId): Collection
    {
        return $this->model->where('spk_id', $spkId)
            ->with(['produk'])
            ->get();
    }

    public function findWithRelations(int $id): ?SPKItem
    {
        return $this->model->with(['spk.pelanggan', 'produk'])->find($id);
    }
}











<?php

namespace App\Repositories\Eloquent;

use App\Models\SPK;
use App\Repositories\Interfaces\SpkRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SpkRepository implements SpkRepositoryInterface
{
    protected SPK $model;

    public function __construct(SPK $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['customer', 'items.bahan'])->get();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['customer', 'items.bahan'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function find(int $id): ?SPK
    {
        return $this->model->find($id);
    }

    public function findWithRelations(int $id): ?SPK
    {
        return $this->model->with(['customer', 'items.bahan', 'createdBy', 'updatedBy'])->find($id);
    }

    public function findByNomor(string $nomor): ?SPK
    {
        return $this->model->where('nomor_spk', $nomor)->first();
    }

    public function create(array $data): SPK
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $spk = $this->find($id);
        return $spk ? $spk->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $spk = $this->find($id);
        return $spk ? $spk->delete() : false;
    }

    public function getByCustomer(int $customerId): Collection
    {
        return $this->model->where('customer_id', $customerId)
            ->with(['customer', 'items.bahan'])
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->byStatus($status)
            ->with(['customer', 'items.bahan'])
            ->get();
    }

    public function getByPrioritas(string $prioritas): Collection
    {
        return $this->model->byPrioritas($prioritas)
            ->with(['customer', 'items.bahan'])
            ->get();
    }

    public function countByStatus(): array
    {
        return $this->model->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}











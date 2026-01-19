<?php

namespace App\Repositories\Eloquent;

use App\Models\Pembelian;
use App\Repositories\Interfaces\PembelianRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PembelianRepository implements PembelianRepositoryInterface
{
    protected Pembelian $model;

    public function __construct(Pembelian $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['pemasok'])->get();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['pemasok']);

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(kode_pembelian) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(nomor_form) LIKE ?', ['%' . $search . '%'])
                  ->orWhereHas('pemasok', function($pemasokQuery) use ($search) {
                      $pemasokQuery->whereRaw('LOWER(nama) LIKE ?', ['%' . $search . '%'])
                                   ->orWhereRaw('LOWER(kode_pemasok) LIKE ?', ['%' . $search . '%']);
                  });
            });
        }

        if (isset($filters['pemasok_id'])) {
            $query->where('pemasok_id', $filters['pemasok_id']);
        }

        if (isset($filters['tanggal_dari'])) {
            $query->where('tanggal_pembelian', '>=', $filters['tanggal_dari']);
        }

        if (isset($filters['tanggal_sampai'])) {
            $query->where('tanggal_pembelian', '<=', $filters['tanggal_sampai']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function find(int $id): ?Pembelian
    {
        return $this->model->find($id);
    }

    public function findByKode(string $kode): ?Pembelian
    {
        return $this->model->where('kode_pembelian', $kode)->first();
    }

    public function findWithRelations(int $id): ?Pembelian
    {
        return $this->model->with(['pemasok', 'items.bahanBaku'])->find($id);
    }

    public function getByPemasok(int $pemasokId): Collection
    {
        return $this->model->where('pemasok_id', $pemasokId)
            ->with(['pemasok', 'items'])
            ->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->with(['pemasok', 'items'])
            ->get();
    }

    public function create(array $data): Pembelian
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $pembelian = $this->find($id);
        return $pembelian ? $pembelian->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $pembelian = $this->find($id);
        return $pembelian ? $pembelian->delete() : false;
    }

    public function getTotalByMonth(int $year, int $month): float
    {
        return $this->model->whereYear('tanggal_pembelian', $year)
            ->whereMonth('tanggal_pembelian', $month)
            ->sum('total');
    }
}











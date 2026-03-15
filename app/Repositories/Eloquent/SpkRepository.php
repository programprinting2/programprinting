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

    // public function all(): Collection
    // {
    //     return $this->model->with(['pelanggan', 'items.produk'])->get();
    // }

    public function all(array $filters = [], array $with = []): Collection
    {
        // $query = $this->model->with(['pelanggan', 'items.produk']);
        
        // $query = $this->model
        // ->select('id','nomor_spk','pelanggan_id','tanggal_spk','status')
        // ->with([
        //     'pelanggan:id,nama',
        //     'items:id,spk_id,produk_id,jumlah,nama_produk,panjang,lebar,satuan,file_pendukung',
        //     'items.produk:id,nama_produk,kode_produk,alur_produksi_json,is_metric,metric_unit',
        //     'items.produk.bahanBakus:id,nama_bahan,kode_bahan'
        // ]);
        $query = $this->model
            ->select('id','nomor_spk','pelanggan_id','tanggal_spk','status','created_at')
            ->with($with);


        // Terapkan filter status jika ada
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['exclude_status'])) {
            $query->where('status', '!=', $filters['exclude_status']);
        }

        // Terapkan filter customer_id jika ada
        if (!empty($filters['customer_id'])) {
            $query->where('pelanggan_id', $filters['customer_id']);
        }

        if (!empty($filters['sort_status'])) {
            $query
                ->orderByRaw("CASE WHEN status = ? THEN 0 ELSE 1 END", [$filters['sort_status']])
                ->orderByDesc('created_at');
        } else {
            $query->orderByDesc('created_at');
        }

        // Terapkan filter search (nomor SPK, status, pelanggan) jika ada
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_spk', 'ILIKE', "{$search}%")
                    ->orWhere('status', 'ILIKE', "{$search}%")
                    ->orWhereHas('pelanggan', function ($sub) use ($search) {
                        $sub->where('nama', 'ILIKE', "{$search}%");
                    });
            });
        }

        return $query->get();
    }

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->with(['pelanggan', 'items.produk']);

        // FILTER STATUS
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // FILTER EXCLUDE STATUS
        if (!empty($filters['exclude_status'])) {
            $query->where('status', '!=', $filters['exclude_status']);
        }

        // FILTER STATUS PEMBAYARAN
        if (!empty($filters['status_pembayaran'])) {
            $query->where('status_pembayaran', $filters['status_pembayaran']);
        }

        // FILTER EXCLUDE STATUS PEMBAYARAN
        if (!empty($filters['exclude_status_pembayaran'])) {
            $query->where('status_pembayaran', '!=', $filters['exclude_status_pembayaran']);
        }

        // FILTER CUSTOMER
        if (!empty($filters['customer_id'])) {
            $query->where('pelanggan_id', $filters['customer_id']);
        }

        // FILTER USER PEMBUAT SPK
        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        // FILTER SEARCH (nomor SPK, status, pelanggan)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_spk', 'ILIKE', "{$search}%")
                ->orWhere('status', 'ILIKE', "{$search}%")
                ->orWhereHas('pelanggan', function ($sub) use ($search) {
                    $sub->where('nama', 'ILIKE', "{$search}%");
                });
            });
        }

        if (!empty($filters['sort_status'])) {
            $query
                ->orderByRaw("CASE WHEN status = ? THEN 0 ELSE 1 END", [$filters['sort_status']])
                ->orderByDesc('created_at');
        } else {
            $query->orderByDesc('created_at');
        }
    

        return $query->paginate($perPage);
    }

    public function find(int $id): ?SPK
    {
        return $this->model->find($id);
    }

    public function findWithRelations(int $id): ?SPK
    {
        return $this->model->with(['pelanggan', 'items.produk', 'items.produk.satuan', 'createdBy', 'updatedBy', 'activityLogs'])->find($id);
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
        return $this->model->where('pelanggan_id', $customerId)
            ->with(['pelanggan', 'items.produk'])
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->byStatus($status)
            ->with(['pelanggan', 'items.produk'])
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











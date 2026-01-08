<?php

namespace App\Repositories\Interfaces;

use App\Models\MasterMesin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MesinRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator;
    public function find(int $id): ?MasterMesin;
    public function search(string $search): Collection;
    public function getByTipe(string $tipe): Collection;
    public function getByStatus(string $status): Collection;
    public function create(array $data): MasterMesin;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function count(): int;
}









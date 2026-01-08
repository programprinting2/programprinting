<?php

namespace App\Repositories\Interfaces;

use App\Models\SPKItem;
use Illuminate\Database\Eloquent\Collection;

interface SpkItemRepositoryInterface
{
    public function createForSpk(int $spkId, array $data): SPKItem;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getBySpk(int $spkId): Collection;
    public function findWithRelations(int $id): ?SPKItem;
}









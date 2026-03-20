<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    /**
     *
     * @param Model $model Model yang punya relasi activityLogs (misal: SPK)
     * @param string $aktivitas Kode aktivitas, misal: spk_dibuat, status_diubah
     * @param string|null $keterangan Deskripsi untuk user
     * @param string|null $level Opsional: info, warning, dll
     */
    public static function log(Model $model, string $aktivitas, ?string $keterangan = null, ?string $level = null): void
    {
        ActivityLog::create([
            'loggable_type' => $model->getMorphClass(),
            'loggable_id'   => $model->getKey(),
            'aktivitas'     => $aktivitas,
            'keterangan'    => $keterangan,
            'user_id'      =>  (int) auth()->id(),
            'level'        => $level,
        ]);
    }
}
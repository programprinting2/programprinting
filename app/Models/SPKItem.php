<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SPKItem extends Model
{
    use HasFactory;

    protected $table = 'spk_items';
    
    protected $fillable = [
        'spk_id',
        'nama_produk',
        'jumlah',
        'satuan',
        'keterangan',
        'bahan_id',
        'lebar',
        'panjang',
        'biaya_desain',
        'biaya_finishing',
        'preview_image_path',
        'tipe_finishing',
        'tugas_produksi',
        'file_pendukung',
        'total_biaya'
    ];

    protected $casts = [
        'lebar' => 'decimal:2',
        'panjang' => 'decimal:2',
        'biaya_desain' => 'integer',
        'biaya_finishing' => 'integer',
        'total_biaya' => 'integer',
        'tipe_finishing' => 'array',
        'tugas_produksi' => 'array',
        'file_pendukung' => 'array'
    ];

    // Relasi ke SPK
    public function spk(): BelongsTo
    {
        return $this->belongsTo(SPK::class);
    }

    // Relasi ke Bahan Baku
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }

    // Method untuk menambah tugas produksi
    public function addTugasProduksi(array $taskData): void
    {
        $tasks = $this->tugas_produksi ?? [];
        $taskData['id'] = uniqid('task_');
        $taskData['created_at'] = now()->toISOString();
        $tasks[] = $taskData;
        
        $this->update(['tugas_produksi' => $tasks]);
        $this->updateTotalBiaya();
    }

    // Method untuk update tugas produksi
    public function updateTugasProduksi(string $taskId, array $taskData): void
    {
        $tasks = $this->tugas_produksi ?? [];
        
        foreach ($tasks as &$task) {
            if ($task['id'] === $taskId) {
                $task = array_merge($task, $taskData);
                $task['updated_at'] = now()->toISOString();
                break;
            }
        }
        
        $this->update(['tugas_produksi' => $tasks]);
        $this->updateTotalBiaya();
    }

    // Method untuk hapus tugas produksi
    public function removeTugasProduksi(string $taskId): void
    {
        $tasks = $this->tugas_produksi ?? [];
        $tasks = array_filter($tasks, fn($task) => $task['id'] !== $taskId);
        
        $this->update(['tugas_produksi' => array_values($tasks)]);
        $this->updateTotalBiaya();
    }

    // Method untuk menambah file pendukung
    public function addFilePendukung(array $fileData): void
    {
        $files = $this->file_pendukung ?? [];
        $fileData['id'] = uniqid('file_');
        $fileData['uploaded_at'] = now()->toISOString();
        $files[] = $fileData;
        
        $this->update(['file_pendukung' => $files]);
    }

    // Method untuk hapus file pendukung
    public function removeFilePendukung(string $fileId): void
    {
        $files = $this->file_pendukung ?? [];
        $files = array_filter($files, fn($file) => $file['id'] !== $fileId);
        
        $this->update(['file_pendukung' => array_values($files)]);
    }

    // Method untuk update tipe finishing
    public function updateTipeFinishing(array $finishingOptions): void
    {
        $this->update(['tipe_finishing' => $finishingOptions]);
    }

    // Method untuk update total biaya
    public function updateTotalBiaya(): void
    {
        $biayaDesain = $this->biaya_desain ?? 0;
        $biayaFinishing = $this->biaya_finishing ?? 0;
        
        // Hitung biaya dari tugas produksi
        $biayaTugas = 0;
        if ($this->tugas_produksi) {
            foreach ($this->tugas_produksi as $task) {
                $biayaTugas += $task['biaya'] ?? 0;
            }
        }
        
        $total = $biayaDesain + $biayaFinishing + $biayaTugas;
        $this->update(['total_biaya' => $total]);
        
        // Update total biaya SPK
        $this->spk->updateTotalBiaya();
    }

    // Method untuk mendapatkan total biaya tugas
    public function getTotalBiayaTugas(): int
    {
        if (!$this->tugas_produksi) return 0;
        
        return collect($this->tugas_produksi)->sum('biaya');
    }

    // Method untuk mendapatkan jumlah tugas
    public function getJumlahTugas(): int
    {
        return count($this->tugas_produksi ?? []);
    }

    // Method untuk mendapatkan jumlah file
    public function getJumlahFile(): int
    {
        return count($this->file_pendukung ?? []);
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

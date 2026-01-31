<?php

namespace App\Services;

use App\Models\DetailParameter;
use App\Models\MasterParameter;
use Illuminate\Support\Facades\DB;

class TagService
{
    /**
     * Get or create master parameter untuk tags
     */
    private static function getTagsMasterParameter()
    {
        return MasterParameter::firstOrCreate(
            ['nama_parameter' => 'TAGS PRODUK'],
            [
                'keterangan' => 'Master tags untuk produk',
                'aktif' => 1,
            ]
        );
    }

    /**
     * Convert array of tag names ke array of IDs (auto-create jika belum ada)
     */
    public static function processTagNames(array $tagNames): array
    {
        if (empty($tagNames)) return [];
        
        $masterParam = self::getTagsMasterParameter(); 
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            $tagName = trim(strtolower($tagName));
            if (empty($tagName)) continue;

            // Cek apakah tag sudah ada
            $existingTag = DetailParameter::where('master_parameter_id', $masterParam->id) 
                ->where('nama_detail_parameter', $tagName)
                ->first();

            if ($existingTag) {
                $tagIds[] = $existingTag->id;
            } else {
                // Auto-create tag baru
                $newTag = DetailParameter::create([
                    'master_parameter_id' => $masterParam->id, 
                    'nama_detail_parameter' => $tagName,
                    'keterangan' => 'Auto-created from product input',
                    'aktif' => 1,
                ]);
                $tagIds[] = $newTag->id;
            }
        }

        return array_unique($tagIds);
    }

    /**
     * Get all tags untuk autocomplete
     */
    public static function getAllTags()
    {
        $masterParam = self::getTagsMasterParameter();
        return DetailParameter::where('master_parameter_id', $masterParam->id) 
            ->where('aktif', 1)
            ->orderBy('nama_detail_parameter')
            ->get(['id', 'nama_detail_parameter', 'keterangan']);
    }

    public static function getTagNamesFromIds(array $tagIds): array
    {
        if (empty($tagIds)) return [];
        
        $masterParam = self::getTagsMasterParameter();
        return DetailParameter::where('master_parameter_id', $masterParam->id)
            ->whereIn('id', $tagIds)
            ->pluck('nama_detail_parameter')
            ->toArray();
    }

    /**
     * Search tags untuk autocomplete
     */
    public static function searchTags(string $query)
    {
        $cleanQuery = trim($query, '"\'');
        $masterParam = self::getTagsMasterParameter();

        return DetailParameter::where('master_parameter_id', $masterParam->id) 
            ->where('aktif', 1)
            ->where('nama_detail_parameter', 'ILIKE', "%{$cleanQuery}%")
            ->orderBy('nama_detail_parameter')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->nama_detail_parameter, 
                    'value' => $item->nama_detail_parameter 
                ];
            });
    }
}
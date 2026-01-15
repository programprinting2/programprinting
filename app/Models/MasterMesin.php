<?php

namespace App\Models;

use App\Services\SupabaseStorageService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class MasterMesin extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'mesin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_mesin',
        'tipe_mesin',
        'merek',
        'model',
        'nomor_seri',
        'status',
        'tanggal_pembelian',
        'harga_pembelian',
        'deskripsi',
        'lebar_media_maksimum',
        'detail_mesin',
        'catatan_tambahan',
        // 'cloudinary_public_id',
        'supabase_path',
        'harga_tinta_per_liter',
        'konsumsi_tinta_per_m2',
        'biaya_perhitungan_profil',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pembelian' => 'date',
        'harga_pembelian' => 'decimal:2',
        'lebar_media_maksimum' => 'decimal:2',
        'detail_mesin' => 'json',
        'harga_tinta_per_liter' => 'decimal:2',
        'konsumsi_tinta_per_m2' => 'decimal:2',
        'biaya_perhitungan_profil' => 'json',
    ];

    /**
     * Set the harga pembelian attribute.
     *
     * @param  string|int|float  $value
     * @return void
     */
    public function setHargaPembelianAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['harga_pembelian'] = str_replace('.', '', $value);
        } else {
            $this->attributes['harga_pembelian'] = null;
        }
    }

    /**
     * Get the formatted price with currency symbol.
     *
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->harga_pembelian, 0, ',', '.');
    }

    /**
     * Get the image URL dari Supabase Storage
     * 
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        // Prioritize supabase_path 
        if ($this->supabase_path) {
            return App::make(SupabaseStorageService::class)->getUrl($this->supabase_path);
        }
        
        return null;
    }
    
    /**
     * Get the thumbnail image URL
     * 
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->supabase_path) {
            return App::make(SupabaseStorageService::class)->getUrl($this->supabase_path, 'thumbnail');
        }
        
        return null;
    }
    
    /**
     * Get the medium-sized image URL
     * 
     * @return string|null
     */
    public function getMediumUrlAttribute()
    {
        if ($this->supabase_path) {
            return App::make(SupabaseStorageService::class)->getUrl($this->supabase_path, 'medium');
        }
        
        return null;
    }

    /**
     * Get calculated ink cost per square meter (m²)
     * 
     * @return float|null
     */
    public function getBiayaTintaPerM2Attribute()
    {
        if (!$this->harga_tinta_per_liter || !$this->konsumsi_tinta_per_m2) {
            return null;
        }
        
        // Konversi mL ke L (1L = 1000mL)
        $konsumsi_liter = $this->konsumsi_tinta_per_m2 / 1000; 
        
        // Hitung biaya tinta per m²
        return $this->harga_tinta_per_liter * $konsumsi_liter;
    }
    
    /**
     * Get total additional cost per square meter (m²)
     * 
     * @return float
     */
    public function getBiayaTambahanPerM2Attribute()
    {
        if (!$this->biaya_tambahan) {
            return 0;
        }
        
        $biaya = $this->biaya_tambahan;
        $total = 0;
        
        if (is_array($biaya)) {
            foreach ($biaya as $item) {
                if (isset($item['nilai']) && is_numeric($item['nilai'])) {
                    $total += (float)$item['nilai'];
                }
            }
        }
        
        return $total;
    }
    
    /**
     * Get total production cost per square meter (m²)
     * 
     * @return float
     */
    public function getTotalBiayaPerM2Attribute()
    {
        $biayaTinta = $this->biaya_tinta_per_m2 ?: 0;
        $biayaTambahan = $this->biaya_tambahan_per_m2;
        
        return $biayaTinta + $biayaTambahan;
    }
    
    /**
     * Get total production cost per square centimeter (cm²)
     * 
     * @return float
     */
    public function getTotalBiayaPerCm2Attribute()
    {
        // 1m² = 10000cm²
        return $this->total_biaya_per_m2 / 10000;
    }

    /**
     * Scope a query to only include mesin of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('tipe_mesin', $type);
    }

    /**
     * Scope a query to only include active machines.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }
}

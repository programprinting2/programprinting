<?php

namespace App\Providers;

use App\Models\SPK;
use App\Models\SPKItem;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use App\Models\Karyawan;
use App\Models\BahanBaku;
use App\Models\MasterMesin;
use App\Models\Produk;
use App\Models\Gudang;
use App\Models\Rak;
use App\Repositories\Eloquent\SpkRepository;
use App\Repositories\Eloquent\SpkItemRepository;
use App\Repositories\Eloquent\PelangganRepository;
use App\Repositories\Eloquent\PemasokRepository;
use App\Repositories\Eloquent\PembelianRepository;
use App\Repositories\Eloquent\PembelianItemRepository;
use App\Repositories\Eloquent\KaryawanRepository;
use App\Repositories\Eloquent\BahanBakuRepository;
use App\Repositories\Eloquent\MesinRepository;
use App\Repositories\Eloquent\ProdukRepository;
use App\Repositories\Eloquent\GudangRepository;
use App\Repositories\Eloquent\RakRepository;
use App\Repositories\Interfaces\SpkRepositoryInterface;
use App\Repositories\Interfaces\SpkItemRepositoryInterface;
use App\Repositories\Interfaces\PelangganRepositoryInterface;
use App\Repositories\Interfaces\PemasokRepositoryInterface;
use App\Repositories\Interfaces\PembelianRepositoryInterface;
use App\Repositories\Interfaces\PembelianItemRepositoryInterface;
use App\Repositories\Interfaces\KaryawanRepositoryInterface;
use App\Repositories\Interfaces\BahanBakuRepositoryInterface;
use App\Repositories\Interfaces\MesinRepositoryInterface;
use App\Repositories\Interfaces\ProdukRepositoryInterface;
use App\Repositories\Interfaces\GudangRepositoryInterface;
use App\Repositories\Interfaces\RakRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // SPK Repositories
        $this->app->bind(SpkRepositoryInterface::class, function ($app) {
            return new SpkRepository(new SPK());
        });

        $this->app->bind(SpkItemRepositoryInterface::class, function ($app) {
            return new SpkItemRepository(new SPKItem());
        });

        // Pelanggan Repository
        $this->app->bind(PelangganRepositoryInterface::class, function ($app) {
            return new PelangganRepository(new Pelanggan());
        });

        // Pemasok Repository
        $this->app->bind(PemasokRepositoryInterface::class, function ($app) {
            return new PemasokRepository(new Pemasok());
        });

        // Pembelian Repositories
        $this->app->bind(PembelianRepositoryInterface::class, function ($app) {
            return new PembelianRepository(new Pembelian());
        });

        $this->app->bind(PembelianItemRepositoryInterface::class, function ($app) {
            return new PembelianItemRepository(new PembelianItem());
        });

        // Karyawan Repository
        $this->app->bind(KaryawanRepositoryInterface::class, function ($app) {
            return new KaryawanRepository(new Karyawan());
        });

        // Bahan Baku Repository
        $this->app->bind(BahanBakuRepositoryInterface::class, function ($app) {
            return new BahanBakuRepository(new BahanBaku());
        });

        // Mesin Repository
        $this->app->bind(MesinRepositoryInterface::class, function ($app) {
            return new MesinRepository(new MasterMesin());
        });

        // Produk Repository
        $this->app->bind(ProdukRepositoryInterface::class, function ($app) {
            return new ProdukRepository(new Produk());
        });

        // Gudang Repository
        $this->app->bind(GudangRepositoryInterface::class, function ($app) {
            return new GudangRepository(new Gudang());
        });

        // Rak Repository
        $this->app->bind(RakRepositoryInterface::class, function ($app) {
            return new RakRepository(new Rak());
        });

        // Add other repositories here as needed
        // Example:
        // $this->app->bind(PelangganRepositoryInterface::class, function ($app) {
        //     return new PelangganRepository(new Pelanggan());
        // });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}


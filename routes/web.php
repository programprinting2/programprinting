<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// use App\Http\Controllers\MasterMesinController;
use App\Http\Controllers\MesinController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterParameterController;
use App\Http\Controllers\MasterBahanbakuController;
use App\Http\Controllers\PembelianController;
// use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SPKController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\MasterProdukController;
use App\Http\Controllers\CariController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\HutangController; 
use App\Http\Controllers\PekerjaanController;


Route::get('/', function () {
    return view('dashboard');
});



Route::group(['prefix' => 'backend'], function () {
    // Route::get('master-mesin', function () {
    //     return view('pages.backend.master-mesin');
    // });
    // Route::get('master-mesin', [MasterMesinController::class, 'index'])->name('backend.master-mesin');
    // Route::post('master-mesin/create', [MasterMesinController::class, 'store'])->name('backend.master-mesin.create');
    // Route::put('master-mesin/{id}/update', [MasterMesinController::class, 'update'])->name('backend.master-mesin.update');
    // Route::put('master-mesin/{id}/clear-image', [MasterMesinController::class, 'clearImage'])->name('backend.master-mesin.clear-image');
    // // Route::delete('master-mesin/{id}', [MasterMesinController::class, 'delete'])->name('backend.master-mesin.delete');
    // Route::get('/master-mesin/filter', [App\Http\Controllers\MasterMesinController::class, 'filter'])->name('backend.master-mesin.filter');
    // Route::get('/backend/master-mesin/grid', [App\Http\Controllers\MasterMesinController::class, 'grid'])->name('backend.master-mesin.grid');

    // Master Mesin New
    // Route::get('master-mesin-new', [MasterMesinController::class, 'index'])->name('backend.master-mesin-new');
    // Route::post('master-mesin-new/create', [MasterMesinController::class, 'store'])->name('backend.master-mesin-new.create');

    // Master Mesin Resource Routes
    Route::resource('master-mesin', MesinController::class)->names([
        'index' => 'backend.master-mesin.index',
        'create' => 'backend.master-mesin.create',
        'store' => 'backend.master-mesin.store',
        'show' => 'backend.master-mesin.show',
        'edit' => 'backend.master-mesin.edit',
        'update' => 'backend.master-mesin.update',
        'destroy' => 'backend.master-mesin.destroy',
    ]);
    Route::post('master-mesin/{id}/remove-biaya-tambahan', [MesinController::class, 'removeBiayaTambahan'])->name('backend.master-mesin.remove-biaya-tambahan');

    // Pelanggan Resource Routes
    Route::resource('pelanggan', PelangganController::class)->names([
        'index' => 'backend.pelanggan.index',
        'create' => 'backend.pelanggan.create',
        'store' => 'backend.pelanggan.store',
        'show' => 'backend.pelanggan.show',
        'edit' => 'backend.pelanggan.edit',
        'update' => 'backend.pelanggan.update',
        'destroy' => 'backend.pelanggan.destroy',
    ]);

    // Pemasok Resource Routes
    Route::resource('pemasok', PemasokController::class)->names([
        'index' => 'backend.pemasok.index',
        'create' => 'backend.pemasok.create',
        'store' => 'backend.pemasok.store',
        'show' => 'backend.pemasok.show',
        'edit' => 'backend.pemasok.edit',
        'update' => 'backend.pemasok.update',
        'destroy' => 'backend.pemasok.destroy',
    ]);

    Route::get('master-parameter', [MasterParameterController::class, 'index'])->name('backend.master-parameter');
    Route::post('master-parameter', [MasterParameterController::class, 'store'])->name('backend.master-parameter.store');
    Route::put('master-parameter/{id}', [MasterParameterController::class, 'update'])->name('backend.master-parameter.update');
    Route::delete('master-parameter/{id}', [MasterParameterController::class, 'destroy'])->name('backend.master-parameter.destroy');
    Route::get('master-parameter/{id}/detail', [MasterParameterController::class, 'detail'])->name('backend.master-parameter.detail');
    Route::post('master-parameter/{id}/detail', [MasterParameterController::class, 'storeDetail'])->name('backend.master-parameter.detail.store');
    Route::put('master-parameter/{id}/detail/{detailId}', [MasterParameterController::class, 'updateDetail'])->name('backend.master-parameter.detail.update');
    Route::delete('master-parameter/{id}/detail/{detailId}', [MasterParameterController::class, 'destroyDetail'])->name('backend.master-parameter.detail.destroy');
    
    // Sub Detail Parameter Resource Routes
    Route::resource('master-parameter.detail.sub-detail', SubDetailParameterController::class)->names([
        'index' => 'backend.master-parameter.detail.sub-detail.index',
        'create' => 'backend.master-parameter.detail.sub-detail.create',
        'store' => 'backend.master-parameter.detail.sub-detail.store',
        'show' => 'backend.master-parameter.detail.sub-detail.show',
        'edit' => 'backend.master-parameter.detail.sub-detail.edit',
        'update' => 'backend.master-parameter.detail.sub-detail.update',
        'destroy' => 'backend.master-parameter.detail.sub-detail.destroy',
    ]);

     // Bahan Baku Resource Routes
     Route::resource('master-bahanbaku', MasterBahanBakuController::class)->names([
        'index' => 'backend.master-bahanbaku.index',
        'create' => 'backend.master-bahanbaku.create',
        'store' => 'backend.master-bahanbaku.store',
        'show' => 'backend.master-bahanbaku.show',
        'edit' => 'backend.master-bahanbaku.edit',
        'update' => 'backend.master-bahanbaku.update',
        'destroy' => 'backend.master-bahanbaku.destroy',
    ]);
    Route::get('master-bahanbaku/{id}/produk', [MasterBahanbakuController::class, 'getProdukByBahanBaku'])
    ->name('backend.master-bahanbaku.produk');

    // Karyawan Resource Routes
    Route::resource('karyawan', KaryawanController::class)->names([
        'index' => 'backend.karyawan.index',
        'create' => 'backend.karyawan.create',
        'store' => 'backend.karyawan.store',
        'show' => 'backend.karyawan.show',
        'edit' => 'backend.karyawan.edit',
        'update' => 'backend.karyawan.update',
        'destroy' => 'backend.karyawan.destroy',
    ]);

    // Produk Resource Routes
    Route::resource('master-produk', MasterProdukController::class)->names([
        'index' => 'backend.master-produk.index',
        'create' => 'backend.master-produk.create',
        'store' => 'backend.master-produk.store',
        'show' => 'backend.master-produk.show',
        'edit' => 'backend.master-produk.edit',
        'update' => 'backend.master-produk.update',
        'destroy' => 'backend.master-produk.destroy',
    ]);
    
    // 'CariBahanBaku' => 'backend.master-produk.CariBahanBaku',
    Route::get('/cari-bahanbaku/', [CariController::class, 'cariBahanBaku'])->name('backend.cari-bahanbaku');
    Route::get('/cari-pemasok', [CariController::class, 'cariPemasok'])->name('backend.cari-pemasok');
    Route::get('/cari-mesin', [CariController::class, 'cariMesin'])->name('backend.cari-mesin');
    Route::get('/cari-divisi-mesin', [CariController::class, 'cariDivisiMesin'])->name('cari-divisi-mesin');
    Route::get('/cari-pelanggan', [CariController::class, 'cariPelanggan'])->name('backend.cari-pelanggan');
    Route::get('/cari-karyawan', [CariController::class, 'cariKaryawan'])->name('backend.cari-karyawan');
    Route::get('/cari-parameter', [CariController::class, 'cariParameter'])->name('backend.cari-parameter');
    Route::get('/cari-produk', [CariController::class, 'cariProdukKomponen'])->name('backend.cari-produk');
    Route::get('/cari-finishing', [CariController::class, 'cariFinishing'])->name('backend.cari-finishing');
    Route::get('/cari-semua-produk', [CariController::class, 'cariSemuaProduk'])->name('backend.cari-semua-produk');
    Route::get('/cari-produk-finishing', [CariController::class, 'cariProdukFinishing'])->name('backend.cari-produk-finishing');
    Route::get('/cari-relasi-produk/{produk}', [CariController::class, 'cariRelasiProduk'])->name('backend.cari-relasi-produk');
    
    // Master Gudang Resource Routes
    Route::resource('master-gudang', GudangController::class)->names([
        'index' => 'backend.master-gudang.index',
        'create' => 'backend.master-gudang.create',
        'store' => 'backend.master-gudang.store',
        'show' => 'backend.master-gudang.show',
        'edit' => 'backend.master-gudang.edit',
        'update' => 'backend.master-gudang.update',
        'destroy' => 'backend.master-gudang.destroy',
    ]);

    // Master Rak Resource Routes
    Route::resource('master-rak', RakController::class)->names([
        'index' => 'backend.master-rak.index',
        'create' => 'backend.master-rak.create',
        'store' => 'backend.master-rak.store',
        'show' => 'backend.master-rak.show',
        'edit' => 'backend.master-rak.edit',
        'update' => 'backend.master-rak.update',
        'destroy' => 'backend.master-rak.destroy',
    ]);

    // File Explorer helper
    Route::get('/file-explorer', [App\Http\Controllers\FileExplorerController::class, 'index'])->name('backend.file-explorer');
    Route::get('/open-folder-location', [App\Http\Controllers\FileExplorerController::class, 'openFolderLocation'])->name('backend.open-folder-location');
    Route::get('/preview-file', [App\Http\Controllers\FileExplorerController::class, 'previewFile'])->name('backend.preview-file');
    Route::get('/file-exists', [App\Http\Controllers\FileExplorerController::class, 'fileExists'])->name('backend.file-exists');
    Route::get('/file-image-info', [App\Http\Controllers\FileExplorerController::class, 'getImageInfo'])->name('backend.file-image-info');
    Route::get('/file-pdf-info', [App\Http\Controllers\FileExplorerController::class, 'getPdfInfo'])->name('backend.file-pdf-info');
    Route::post('/image-processing', [App\Http\Controllers\FileExplorerController::class, 'processImageTools'])->name('backend.image-processing');
    Route::post('/finishing-templates', [\App\Http\Controllers\FinishingTemplateController::class, 'store'])->name('backend.finishing-templates.store');
    Route::get('/finishing-templates', [\App\Http\Controllers\FinishingTemplateController::class, 'index'])->name('backend.finishing-templates.index');
    Route::get('/finishing-templates/{id}', [\App\Http\Controllers\FinishingTemplateController::class, 'show'])->name('backend.finishing-templates.show');
    Route::put('/finishing-templates/{id}', [\App\Http\Controllers\FinishingTemplateController::class, 'update'])->name('backend.finishing-templates.update');

});

Route::group(['prefix' => 'pembelian'], function () {
    // Pembelian Resource Routes
    Route::resource('/', PembelianController::class)->names([
        'index' => 'pembelian.index',
        'create' => 'pembelian.create',
        'store' => 'pembelian.store',
        'show' => 'pembelian.show',
        'edit' => 'pembelian.edit',
        'update' => 'pembelian.update',
        'destroy' => 'pembelian.destroy',
    ])->parameters(['' => 'kode_pembelian']);
});

Route::group(['prefix' => 'spk'], function () {
    Route::resource('/', SPKController::class)->names([
        'index' => 'spk.index',
        'create' => 'spk.create',
        'store' => 'spk.store',
        'show' => 'spk.show',
        'edit' => 'spk.edit',
        'update' => 'spk.update',
        'destroy' => 'spk.destroy',
    ])->parameters(['' => 'spk']);

    Route::patch('/{spk}/acc', [SPKController::class, 'accToPayment'])->name('spk.acc');
    Route::patch('/{spk}/status', [SPKController::class, 'updateStatus'])->name('spk.update-status');
});

Route::group(['prefix' => 'pekerjaan'], function () {
    Route::get('/manager-order', [PekerjaanController::class, 'managerOrder'])
        ->name('pekerjaan.manager-order');

    Route::get('/manager-produksi', [PekerjaanController::class, 'managerProduksi'])
        ->name('pekerjaan.manager-produksi');

    Route::get('/operator-cetak', [PekerjaanController::class, 'operatorCetak'])
        ->name('pekerjaan.operator-cetak');

    Route::get('/finishing-qc', [PekerjaanController::class, 'finishingQc'])
        ->name('pekerjaan.finishing-qc');

    Route::get('/siap-ambil', [PekerjaanController::class, 'siapAmbil'])
        ->name('pekerjaan.siap-ambil');

    Route::get('/tandai-selesai', [PekerjaanController::class, 'tandaiSelesai'])
        ->name('pekerjaan.tandai-selesai');
});

//Hutang Route
Route::get('/hutang', [HutangController::class, 'index'])->name('hutang.index');

Route::group(['prefix' => 'kasir'], function () {
    Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
    Route::get('/invoice/{no}', [KasirController::class, 'show'])->name('kasir.invoice.show');
    Route::get('/invoice/{no}/cetak', [KasirController::class, 'print'])->name('kasir.invoice.print');
    Route::get('/invoice/{no}/payment', [KasirController::class, 'payment'])->name('kasir.invoice.payment');
    Route::post('/invoice/{no}/payment', [KasirController::class, 'storePayment'])->name('kasir.invoice.payment.store');
});



Route::group(['prefix' => 'email'], function () {
    Route::get('inbox', function () {
        return view('pages.email.inbox');
    });
    Route::get('read', function () {
        return view('pages.email.read');
    });
    Route::get('compose', function () {
        return view('pages.email.compose');
    });
});

Route::group(['prefix' => 'apps'], function () {
    Route::get('chat', function () {
        return view('pages.apps.chat');
    });
    Route::get('calendar', function () {
        return view('pages.apps.calendar');
    });
});

Route::group(['prefix' => 'ui-components'], function () {
    Route::get('accordion', function () {
        return view('pages.ui-components.accordion');
    });
    Route::get('alerts', function () {
        return view('pages.ui-components.alerts');
    });
    Route::get('badges', function () {
        return view('pages.ui-components.badges');
    });
    Route::get('breadcrumbs', function () {
        return view('pages.ui-components.breadcrumbs');
    });
    Route::get('buttons', function () {
        return view('pages.ui-components.buttons');
    });
    Route::get('button-group', function () {
        return view('pages.ui-components.button-group');
    });
    Route::get('cards', function () {
        return view('pages.ui-components.cards');
    });
    Route::get('carousel', function () {
        return view('pages.ui-components.carousel');
    });
    Route::get('collapse', function () {
        return view('pages.ui-components.collapse');
    });
    Route::get('dropdowns', function () {
        return view('pages.ui-components.dropdowns');
    });
    Route::get('list-group', function () {
        return view('pages.ui-components.list-group');
    });
    Route::get('media-object', function () {
        return view('pages.ui-components.media-object');
    });
    Route::get('modal', function () {
        return view('pages.ui-components.modal');
    });
    Route::get('navs', function () {
        return view('pages.ui-components.navs');
    });
    Route::get('navbar', function () {
        return view('pages.ui-components.navbar');
    });
    Route::get('pagination', function () {
        return view('pages.ui-components.pagination');
    });
    Route::get('popovers', function () {
        return view('pages.ui-components.popovers');
    });
    Route::get('progress', function () {
        return view('pages.ui-components.progress');
    });
    Route::get('scrollbar', function () {
        return view('pages.ui-components.scrollbar');
    });
    Route::get('scrollspy', function () {
        return view('pages.ui-components.scrollspy');
    });
    Route::get('spinners', function () {
        return view('pages.ui-components.spinners');
    });
    Route::get('tabs', function () {
        return view('pages.ui-components.tabs');
    });
    Route::get('tooltips', function () {
        return view('pages.ui-components.tooltips');
    });
});

Route::group(['prefix' => 'advanced-ui'], function () {
    Route::get('cropper', function () {
        return view('pages.advanced-ui.cropper');
    });
    Route::get('owl-carousel', function () {
        return view('pages.advanced-ui.owl-carousel');
    });
    Route::get('sortablejs', function () {
        return view('pages.advanced-ui.sortablejs');
    });
    Route::get('sweet-alert', function () {
        return view('pages.advanced-ui.sweet-alert');
    });
});

Route::group(['prefix' => 'forms'], function () {
    Route::get('basic-elements', function () {
        return view('pages.forms.basic-elements');
    });
    Route::get('advanced-elements', function () {
        return view('pages.forms.advanced-elements');
    });
    Route::get('editors', function () {
        return view('pages.forms.editors');
    });
    Route::get('wizard', function () {
        return view('pages.forms.wizard');
    });
});

Route::group(['prefix' => 'charts'], function () {
    Route::get('apex', function () {
        return view('pages.charts.apex');
    });
    Route::get('chartjs', function () {
        return view('pages.charts.chartjs');
    });
    Route::get('flot', function () {
        return view('pages.charts.flot');
    });
    Route::get('peity', function () {
        return view('pages.charts.peity');
    });
    Route::get('sparkline', function () {
        return view('pages.charts.sparkline');
    });
});

Route::group(['prefix' => 'tables'], function () {
    Route::get('basic-tables', function () {
        return view('pages.tables.basic-tables');
    });
    Route::get('data-table', function () {
        return view('pages.tables.data-table');
    });
});

Route::group(['prefix' => 'icons'], function () {
    Route::get('feather-icons', function () {
        return view('pages.icons.feather-icons');
    });
    Route::get('mdi-icons', function () {
        return view('pages.icons.mdi-icons');
    });
});

Route::group(['prefix' => 'general'], function () {
    Route::get('blank-page', function () {
        return view('pages.general.blank-page');
    });
    Route::get('faq', function () {
        return view('pages.general.faq');
    });
    Route::get('invoice', function () {
        return view('pages.general.invoice');
    });
    Route::get('profile', function () {
        return view('pages.general.profile');
    });
    Route::get('pricing', function () {
        return view('pages.general.pricing');
    });
    Route::get('timeline', function () {
        return view('pages.general.timeline');
    });
});

Route::group(['prefix' => 'auth'], function () {
    Route::get('login', function () {
        return view('pages.auth.login');
    });
    Route::get('register', function () {
        return view('pages.auth.register');
    });
});

Route::group(['prefix' => 'error'], function () {
    Route::get('404', function () {
        return view('pages.error.404');
    });
    Route::get('500', function () {
        return view('pages.error.500');
    });
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});


// 404 for undefined routes
Route::any('/{page?}', function () {
    return View::make('pages.error.404');
})->where('page', '.*');

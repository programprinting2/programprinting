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
use App\Http\Controllers\MasterMesinController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterKontakController;
use App\Http\Controllers\MasterParameterController;
use App\Http\Controllers\MasterBahanbakuController;
use App\Http\Controllers\PembelianController;

Route::get('/', function () {
    return view('dashboard');
});



Route::group(['prefix' => 'backend'], function () {
    // Route::get('master-mesin', function () {
    //     return view('pages.backend.master-mesin');
    // });
    Route::get('master-mesin', [MasterMesinController::class, 'index'])->name('backend.master-mesin');
    Route::post('master-mesin/create', [MasterMesinController::class, 'store'])->name('backend.master-mesin.create');
    Route::put('master-mesin/{id}/update', [MasterMesinController::class, 'update'])->name('backend.master-mesin.update');
    Route::put('master-mesin/{id}/clear-image', [MasterMesinController::class, 'clearImage'])->name('backend.master-mesin.clear-image');
    Route::delete('master-mesin/{id}', [MasterMesinController::class, 'delete'])->name('backend.master-mesin.delete');
    Route::get('/master-mesin/filter', [App\Http\Controllers\MasterMesinController::class, 'filter'])->name('backend.master-mesin.filter');
    Route::get('/backend/master-mesin/grid', [App\Http\Controllers\MasterMesinController::class, 'grid'])->name('backend.master-mesin.grid');

    Route::get('master-kontak', [MasterKontakController::class, 'index'])->name('backend.master-kontak');
    Route::post('master-kontak/create', [MasterKontakController::class, 'store'])->name('backend.master-kontak.create');
    Route::put('master-kontak/{id}/update', [MasterKontakController::class, 'update'])->name('backend.master-kontak.update');
    Route::delete('master-kontak/{id}', [MasterKontakController::class, 'delete'])->name('backend.master-kontak.delete');
    Route::get('/master-kontak/filter', [App\Http\Controllers\MasterKontakController::class, 'filter'])->name('backend.master-kontak.filter');

    Route::get('master-parameter', [MasterParameterController::class, 'index'])->name('backend.master-parameter');
    Route::post('master-parameter', [MasterParameterController::class, 'store'])->name('backend.master-parameter.store');
    Route::put('master-parameter/{id}', [MasterParameterController::class, 'update'])->name('backend.master-parameter.update');
    Route::delete('master-parameter/{id}', [MasterParameterController::class, 'destroy'])->name('backend.master-parameter.destroy');
    Route::get('master-parameter/{id}/detail', [MasterParameterController::class, 'detail'])->name('backend.master-parameter.detail');
    Route::post('master-parameter/{id}/detail', [MasterParameterController::class, 'storeDetail'])->name('backend.master-parameter.detail.store');
    Route::put('master-parameter/{id}/detail/{detailId}', [MasterParameterController::class, 'updateDetail'])->name('backend.master-parameter.detail.update');
    Route::delete('master-parameter/{id}/detail/{detailId}', [MasterParameterController::class, 'destroyDetail'])->name('backend.master-parameter.detail.destroy');

    Route::get('master-bahanbaku', [MasterBahanbakuController::class, 'index'])->name('backend.master-bahanbaku');
});

Route::group(['prefix' => 'pembelian'], function(){
    Route::get('/', [PembelianController::class, 'index'])->name('pembelian.index');
    Route::post('/create', [PembelianController::class, 'store'])->name('pembelian.create');
});

// Route::group(['prefix' => 'spk'], function(){
    
// });



Route::group(['prefix' => 'email'], function(){
    Route::get('inbox', function () { return view('pages.email.inbox'); });
    Route::get('read', function () { return view('pages.email.read'); });
    Route::get('compose', function () { return view('pages.email.compose'); });
});

Route::group(['prefix' => 'apps'], function(){
    Route::get('chat', function () { return view('pages.apps.chat'); });
    Route::get('calendar', function () { return view('pages.apps.calendar'); });
});

Route::group(['prefix' => 'ui-components'], function(){
    Route::get('accordion', function () { return view('pages.ui-components.accordion'); });
    Route::get('alerts', function () { return view('pages.ui-components.alerts'); });
    Route::get('badges', function () { return view('pages.ui-components.badges'); });
    Route::get('breadcrumbs', function () { return view('pages.ui-components.breadcrumbs'); });
    Route::get('buttons', function () { return view('pages.ui-components.buttons'); });
    Route::get('button-group', function () { return view('pages.ui-components.button-group'); });
    Route::get('cards', function () { return view('pages.ui-components.cards'); });
    Route::get('carousel', function () { return view('pages.ui-components.carousel'); });
    Route::get('collapse', function () { return view('pages.ui-components.collapse'); });
    Route::get('dropdowns', function () { return view('pages.ui-components.dropdowns'); });
    Route::get('list-group', function () { return view('pages.ui-components.list-group'); });
    Route::get('media-object', function () { return view('pages.ui-components.media-object'); });
    Route::get('modal', function () { return view('pages.ui-components.modal'); });
    Route::get('navs', function () { return view('pages.ui-components.navs'); });
    Route::get('navbar', function () { return view('pages.ui-components.navbar'); });
    Route::get('pagination', function () { return view('pages.ui-components.pagination'); });
    Route::get('popovers', function () { return view('pages.ui-components.popovers'); });
    Route::get('progress', function () { return view('pages.ui-components.progress'); });
    Route::get('scrollbar', function () { return view('pages.ui-components.scrollbar'); });
    Route::get('scrollspy', function () { return view('pages.ui-components.scrollspy'); });
    Route::get('spinners', function () { return view('pages.ui-components.spinners'); });
    Route::get('tabs', function () { return view('pages.ui-components.tabs'); });
    Route::get('tooltips', function () { return view('pages.ui-components.tooltips'); });
});

Route::group(['prefix' => 'advanced-ui'], function(){
    Route::get('cropper', function () { return view('pages.advanced-ui.cropper'); });
    Route::get('owl-carousel', function () { return view('pages.advanced-ui.owl-carousel'); });
    Route::get('sortablejs', function () { return view('pages.advanced-ui.sortablejs'); });
    Route::get('sweet-alert', function () { return view('pages.advanced-ui.sweet-alert'); });
});

Route::group(['prefix' => 'forms'], function(){
    Route::get('basic-elements', function () { return view('pages.forms.basic-elements'); });
    Route::get('advanced-elements', function () { return view('pages.forms.advanced-elements'); });
    Route::get('editors', function () { return view('pages.forms.editors'); });
    Route::get('wizard', function () { return view('pages.forms.wizard'); });
});

Route::group(['prefix' => 'charts'], function(){
    Route::get('apex', function () { return view('pages.charts.apex'); });
    Route::get('chartjs', function () { return view('pages.charts.chartjs'); });
    Route::get('flot', function () { return view('pages.charts.flot'); });
    Route::get('peity', function () { return view('pages.charts.peity'); });
    Route::get('sparkline', function () { return view('pages.charts.sparkline'); });
});

Route::group(['prefix' => 'tables'], function(){
    Route::get('basic-tables', function () { return view('pages.tables.basic-tables'); });
    Route::get('data-table', function () { return view('pages.tables.data-table'); });
});

Route::group(['prefix' => 'icons'], function(){
    Route::get('feather-icons', function () { return view('pages.icons.feather-icons'); });
    Route::get('mdi-icons', function () { return view('pages.icons.mdi-icons'); });
});

Route::group(['prefix' => 'general'], function(){
    Route::get('blank-page', function () { return view('pages.general.blank-page'); });
    Route::get('faq', function () { return view('pages.general.faq'); });
    Route::get('invoice', function () { return view('pages.general.invoice'); });
    Route::get('profile', function () { return view('pages.general.profile'); });
    Route::get('pricing', function () { return view('pages.general.pricing'); });
    Route::get('timeline', function () { return view('pages.general.timeline'); });
});

Route::group(['prefix' => 'auth'], function(){
    Route::get('login', function () { return view('pages.auth.login'); });
    Route::get('register', function () { return view('pages.auth.register'); });
});

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('pages.error.404'); });
    Route::get('500', function () { return view('pages.error.500'); });
});

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});


// 404 for undefined routes
Route::any('/{page?}',function(){
    return View::make('pages.error.404');
})->where('page','.*');

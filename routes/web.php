<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('summary/{tahun?}', 'DashboardController@summary')->name('dashboard.index');
        Route::get('perbulan/{tahun?}', 'DashboardController@perbulan')->name('dashboard.perbulan');
        Route::get('perkecamatan/{tahun?}', 'DashboardController@perkecamatan')->name('dashboard.perkecamatan');
        Route::get('perchannel/{tahun?}', 'DashboardController@perchannel')->name('dashboard.perchannel');
        Route::get('perkelurahan/{id_kecamatan}/{tahun?}', 'DashboardController@perkelurahan')->name('dashboard.perkelurahan');
        Route::get('total/{tahun?}', 'TotalController@index')->name('dashboard.total');
        Route::get('hasil-integrasi/{tahun?}', 'TotalController@hasilIntegrasi')->name('dashboard.hasil-integrasi');
    });

    Route::prefix('bphtb')->group(function () {
        Route::get('dashboard/{tahun?}', 'DashboardController@dashboard_bphtb')->name('dashboard.bphtb');
    });

    Route::prefix('retribusi')->group(function () {
        Route::get('dashboard/{tahun?}', 'DashboardController@dashboard_retribusi')->name('dashboard.retribusi');
    });

    Route::group(['prefix' => 'master'], function () {
        Route::resource('kecamatan', 'KecamatanController');
        Route::resource('kelurahan', 'KelurahanController')->except('index', 'show', 'store', 'destroy');
    
        Route::get('kelurahan/{id}', 'KelurahanController@index')->name('kelurahan.index');
    });

    Route::group(['prefix' => 'pbb'], function () {
        Route::get('perkelurahan/{id_kecamatan}', 'PBBController@perkelurahan')->name('pbb.perkelurahan');
        Route::get('detail_perkelurahan/{id_kecamatan}/{id_kelurahan}', 'PBBController@detail_perkelurahan')->name('pbb.detail_perkelurahan');
        Route::get('perchannel/{id_channel}/{id_kecamatan?}', 'PBBController@perchannel')->name('pbb.perchannel');
    });


    Route::group(['prefix' => 'print'], function () {
        Route::group(['prefix' => 'all'], function () {
            Route::get('by-kecamatan', 'PrintController@all_by_kecamatan')->name('print.all-by-kecamatan');
            Route::get('by-channel', 'PrintController@all_by_channel')->name('print.all-by-channel');
        });
        Route::group(['prefix' => 'kecamatan'], function () {
            Route::get('by-kelurahan/{id_kecamatan}', 'PrintController@kecamatan_by_kelurahan')->name('print.kecamatan-by-kelurahan');
            Route::get('by-channel/{id_kecamatan}', 'PrintController@kecamatan_by_channel')->name('print.kecamatan-by-channel');
            Route::get('transaction/by-channel/{id_channel}/{id_kecamatan}', 'PrintController@transaction_by_channel')->name('print.transaction-by-channel');
        });
        Route::group(['prefix' => 'kelurahan'], function () {
            Route::get('all-channel/{id_kecamatan}/{id_kelurahan}', 'PrintController@kelurahan_all_channel')->name('print.kelurahan_all_channel');
        });

    });

    Route::get('get-kelurahan-by-kecamatan/{id_kecamatan}', 'KelurahanController@getByKecamatan');

    Route::group(['prefix' => 'laporan'], function () {
        Route::get('/form', 'LaporanController@form')->name('laporan.form');
        Route::post('/store', 'LaporanController@store')->name('laporan.store');
    });

    Route::group(['prefix' => 'retribusi-pajak'], function () {
        Route::get('kategori/{id_jenis_pajak}', 'RetribusiController@kategori')->name('ret-pajak.kategori');
        Route::get('detail-transaksi/{id_kategori_pajak}', 'RetribusiController@detail')->name('ret-pajak.detail');
    });

    Route::group(['prefix' => 'retribusi-pajak/bulanan'], function () {
        Route::get('jenis-pajak/{tahun}', 'ViewPajakBulananController@jenis')->name('view-bulanan.jenis');
        Route::get('kategori-pajak/{id_jenis_pajak}', 'ViewPajakBulananController@kategori')->name('view-bulanan.kategori');
    });

    Route::group(['prefix' => 'piutang-pajak'], function () {
        Route::get('kategori/{id_jenis_pajak}', 'RetribusiController@piutang_kategori')->name('piutang-pajak.kategori');
        Route::get('detail-transaksi/{id_kategori_pajak}', 'RetribusiController@piutang_detail')->name('piutang-pajak.detail');
    });

    Route::group(['prefix' => 'piutang-pajak/bulanan'], function () {
        Route::get('jenis-piutang-pajak/{tahun}', 'ViewPiutangPajakBulananController@jenis')->name('view-piutang-bulanan.jenis');
        Route::get('kategori-piutang-pajak/{id_jenis_pajak}', 'ViewPiutangPajakBulananController@kategori')->name('view-piutang-bulanan.kategori');
    });

});
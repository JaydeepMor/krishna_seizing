<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::group(['middleware' => ['auth']], function() {
    Route::get('/', 'DashboardController@index')->name('dashboard');

    Route::resource('finance/ho', 'FinanceHoController');

    Route::resource('finance/company', 'FinanceCompanyController');

    Route::resource('group', 'GroupController');

    Route::get('subseizer/activity', 'UserController@getActivity')->name('activity.index');

    Route::resource('subseizer', 'UserController');

    Route::post('subseizer/subscribe/{id}', 'UserController@subscription')->name('subseizer.subscription');

    Route::post('subseizer/report/export', 'UserController@export')->name('subseizer.report.export');

    Route::resource('constant', 'ConstantController');

    Route::resource('vehicle', 'VehicleController');

    Route::post('vehicle/import', 'VehicleController@importExcel')->name('vehicle.import');

    Route::post('vehicle/confirm/{id}', 'VehicleController@confirmVehicle')->name('vehicle.confirm');

    Route::post('vehicle/cancel/{id}', 'VehicleController@cancelVehicle')->name('vehicle.cancel');

    Route::get('vehicle/excel/sample/export', 'VehicleController@downloadSampleExcel')->name('vehicle.sample.export');

    Route::get('vehicle/sync/redis', 'VehicleController@syncToRedis')->name('vehicle.sync.redis');

    Route::delete('vehicle/finance/delete/{financeCompanyId}', 'VehicleController@removeFinanceVehicles')->name('vehicle.finance.delete');

    Route::resource('report', 'ReportController');

    Route::post('report/export', 'ReportController@export')->name('vehicles.report.export');

    Route::get('report/download/{filePath}', 'ReportController@download')->name('vehicles.report.download');

    Route::get('application/download', 'BaseController@downloadApplication')->name('download.application');

    // Ajax routes.
    Route::get('theme/set/cookie', 'BaseController@setThemeCookie')->name('set-theme-cookie');
    Route::post('admin/password/update', 'BaseController@updateAdminPassword')->name('admin.password.update');
});

<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\Organization\FileCaseController;
use App\Http\Controllers\Organization\HomeController;
use App\Http\Controllers\Organization\ProfileController;
use App\Http\Controllers\Organization\StaffRolesController;
use App\Http\Controllers\Organization\StaffsController;
use App\Routes\Profile;
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


Route::name('organization.')->middleware(['ensure.organization.session'])->prefix('organization')->group(function () {

    Route::controller(HomeController::class)->group(function () {
        Profile::routes();
        Route::get('dashboard', 'index')->name('dashboard');
       
    });
    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'index')->name('profile');
        Route::put('profile/update', 'update')->name('profile.update');
    });

    // ----------------------- Role Routes ----------------------------------------------------
    Route::controller(StaffRolesController::class)->name('staffroles')->group(function () {
        Route::get('staffroles', 'index');
        Route::post('staffroles', 'save');
        Route::put('staffroles', 'update');
        Route::delete('staffroles', 'delete');
        Route::get('staffroles/permission/{id}', 'permission')->name('.permission.view');
        Route::put('staffroles/permission', 'permission_update')->name('.permission.update');
    });

    Route::controller(StaffsController::class)->group(function () {
        Route::get('staffs', 'index')->name('staffs');
        Route::get('staffs/add', 'add')->name('staffs.add');
        Route::post('staffs/add', 'save')->name('staffs.add');
        Route::get('staffs/{slug}', 'edit')->name('staffs.edit');
        Route::post('staffs/{slug}', 'update')->name('staffs.edit');
        Route::delete('staffs', 'delete')->name('staffs');
        Route::get('staffs/permission/{id}', 'permission')->name('staffs.permission.view');
        Route::put('staffs/permission', 'permission_update')->name('staffs.permission.update');
    });

    Route::controller(FileCaseController::class)->group(function () {
        Route::get('filecaseview', 'index')->name('cases.filecaseview');
        Route::get('filecaseview/{id}', 'edit')->name('cases.filecaseview.edit');
        Route::post('filecaseview/{id}', 'update')->name('cases.filecaseview.edit');
        Route::delete('filecaseview', 'delete')->name('cases.filecaseview.delete');

        Route::get('filecase', 'filecase')->name('cases.filecase');
        Route::post('/import-filecases', 'importExcel')->name('cases.filecases.import');
        Route::get('/download-sample', 'downloadSample')->name('cases.filecase.sample');

    });


    Route::post('get-cities', [CityController::class, 'get_cities'])->name('cities.list');
});

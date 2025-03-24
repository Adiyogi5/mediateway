<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\Individual\FileCaseController;
use App\Http\Controllers\Individual\HomeController;
use App\Http\Controllers\Individual\ProfileController;
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


Route::name('individual.')->middleware(['ensure.individual.session'])->prefix('individual')->group(function () {

    Route::controller(HomeController::class)->group(function () {
        Profile::routes();
        Route::get('dashboard', 'index')->name('dashboard');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'index')->name('profile');
        Route::put('profile/update', 'update')->name('profile.update');
    });

    Route::controller(FileCaseController::class)->group(function () {
        Route::get('filecaseview', 'index')->name('case.filecaseview');
        Route::get('filecaseview/{id}', 'edit')->name('case.filecaseview.edit');
        Route::post('filecaseview/{id}', 'update')->name('case.filecaseview.edit');
        Route::delete('filecaseview', 'delete')->name('case.filecaseview.delete');

        Route::get('filecase', 'filecase')->name('case.filecase');
        Route::post('register-case', 'registerCase')->name('case.registercase');

        Route::get('/file-case-payment','filecasepayment')->name('case.filecasepayment');
        Route::post('/verify-payment','verify_payment')->name('case.verify_payment');
        Route::get('/file-case-payment-success/{id}','filecasepayment_success')->name('case.filecasepayment_success');
    });

    Route::post('get-cities', [CityController::class, 'get_cities'])->name('cities.list');
});

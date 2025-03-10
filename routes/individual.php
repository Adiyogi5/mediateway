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
        Route::get('file-case', 'index')->name('file-case');
        Route::post('register-case', 'registerCase')->name('register-case');
    });

    Route::post('get-cities', [CityController::class, 'get_cities'])->name('cities.list');
});

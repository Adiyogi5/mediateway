<?php

use App\Http\Controllers\BannerController;
use App\Routes\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\HomeCmsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TestimonialController;

/*
|--------------------------------------------------------------------------
| Web Routes For Admin
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Admin & Sub-Admin Routes
Route::middleware(['auth', 'permission', 'authCheck', 'verified'])->group(function () {
    Profile::routes();
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

    // ----------------------- Role Routes ----------------------------------------------------
    Route::controller(RolesController::class)->name('roles')->group(function () {
        Route::get('roles', 'index')->middleware('isAllow:102,can_view');
        Route::post('roles', 'save')->middleware('isAllow:102,can_add');
        Route::put('roles', 'update')->middleware('isAllow:102,can_edit');
        Route::delete('roles', 'delete')->middleware('isAllow:102,can_delete');
        Route::get('roles/permission/{id}', 'permission')->name('.permission.view')->middleware('isAllow:102,can_edit');
        Route::put('roles/permission', 'permission_update')->name('.permission.update')->middleware('isAllow:102,can_edit');
    });

    // ----------------------- Admin and Sub Admin Routes ----------------------------------------------------
    Route::controller(UsersController::class)->group(function () {
        Route::get('users', 'index')->name('users')->middleware('isAllow:103,can_view');
        Route::get('users/add', 'add')->name('users.add')->middleware('isAllow:103,can_add');
        Route::post('users/add', 'save')->name('users.add')->middleware('isAllow:103,can_add');
        Route::get('users/{slug}', 'edit')->name('users.edit')->middleware('isAllow:103,can_edit');
        Route::post('users/{slug}', 'update')->name('users.edit')->middleware('isAllow:103,can_edit');
        Route::delete('users', 'delete')->name('users')->middleware('isAllow:103,can_delete');
        Route::get('users/permission/{id}', 'permission')->name('users.permission.view')->middleware('isAllow:103,can_edit');
        Route::put('users/permission', 'permission_update')->name('users.permission.update')->middleware('isAllow:103,can_edit');
    });

    // ----------------------- States Routes ----------------------------------------------------
    Route::controller(StateController::class)->name('states')->group(function () {
        Route::get('states', 'index')->middleware('isAllow:105,can_view');
        Route::post('states', 'save')->middleware('isAllow:105,can_add');
        Route::put('states', 'update')->middleware('isAllow:105,can_edit');
        Route::delete('states', 'delete')->middleware('isAllow:105,can_delete');
    });

    // ----------------------- City Routes ----------------------------------------------------
    Route::controller(CityController::class)->name('cities')->group(function () {
        Route::get('cities', 'index')->middleware('isAllow:106,can_view');
        Route::post('cities', 'save')->middleware('isAllow:106,can_add');
        Route::put('cities', 'update')->middleware('isAllow:106,can_edit');
        Route::delete('cities', 'delete')->middleware('isAllow:106,can_delete');
    });

    // ----------------------- Home CMS Routes ----------------------------------------------------
    Route::controller(HomeCmsController::class)->group(function () {
        Route::get('homecms', 'index')->name('homecms')->middleware('isAllow:104,can_view');
        Route::get('homecms/add', 'add')->name('homecms.add')->middleware('isAllow:104,can_add');
        Route::post('homecms/add', 'save')->name('homecms.add')->middleware('isAllow:104,can_add');
        Route::get('homecms/{id}', 'edit')->name('homecms.edit')->middleware('isAllow:104,can_edit');
        Route::post('homecms', 'slug')->name('homecms.slug')->middleware('isAllow:104,can_edit');
        Route::post('homecms/{id}', 'update')->name('homecms.edit')->middleware('isAllow:104,can_edit');
        Route::delete('homecms', 'delete')->name('homecms')->middleware('isAllow:104,can_delete');
    });

    // ----------------------- CMS Routes ----------------------------------------------------
    Route::controller(CmsController::class)->group(function () {
        Route::get('cms', 'index')->name('cms')->middleware('isAllow:104,can_view');
        Route::get('cms/add', 'add')->name('cms.add')->middleware('isAllow:104,can_add');
        Route::post('cms/add', 'save')->name('cms.add')->middleware('isAllow:104,can_add');
        Route::get('cms/{id}', 'edit')->name('cms.edit')->middleware('isAllow:104,can_edit');
        Route::post('cms', 'slug')->name('cms.slug')->middleware('isAllow:104,can_edit');
        Route::post('cms/{id}', 'update')->name('cms.edit')->middleware('isAllow:104,can_edit');
        Route::delete('cms', 'delete')->name('cms')->middleware('isAllow:104,can_delete');
    });
    
    // ----------------------- Banner Routes ----------------------------------------------------
    Route::controller(BannerController::class)->group(function () {
        Route::get('banners', 'index')->name('banners')->middleware('isAllow:112,can_view');
        Route::post('banners', 'save')->name('banners')->middleware('isAllow:112,can_add');
        Route::put('banners', 'update')->name('banners')->middleware('isAllow:112,can_edit');
        Route::delete('banners', 'delete')->name('banners')->middleware('isAllow:112,can_delete');
    });

    // ----------------------- FAQs Routes ----------------------------------------------------
    Route::controller(FaqController::class)->name('faqs')->group(function () {
        Route::get('faqs-list', 'index')->middleware('isAllow:110,can_view');
        Route::post('faqs-list', 'save')->middleware('isAllow:110,can_add');
        Route::put('faqs-list', 'update')->middleware('isAllow:110,can_edit');
        Route::delete('faqs-list', 'delete')->middleware('isAllow:110,can_delete');
    });

    // ----------------------- Features Routes ----------------------------------------------------
    Route::controller(FeatureController::class)->group(function () {
        Route::get('features', 'index')->name('features')->middleware('isAllow:111,can_view');
        Route::get('features/add', 'add')->name('features.add')->middleware('isAllow:111,can_add');
        Route::post('features/add', 'save')->name('features.add')->middleware('isAllow:111,can_add');
        Route::get('features/{id}', 'edit')->name('features.edit')->middleware('isAllow:111,can_edit');
        Route::post('features/{id}', 'update')->name('features.edit')->middleware('isAllow:111,can_edit');
        Route::delete('features', 'delete')->name('features.delete')->middleware('isAllow:111,can_delete');
    });

    // ----------------------- Testimonial Routes ----------------------------------------------------
    Route::controller(TestimonialController::class)->group(function () {
        Route::get('testimonials', 'index')->name('testimonials')->middleware('isAllow:111,can_view');
        Route::get('testimonials/add', 'add')->name('testimonials.add')->middleware('isAllow:111,can_add');
        Route::post('testimonials/add', 'save')->name('testimonials.add')->middleware('isAllow:111,can_add');
        Route::get('testimonials/{id}', 'edit')->name('testimonials.edit')->middleware('isAllow:111,can_edit');
        Route::post('testimonials/{id}', 'update')->name('testimonials.edit')->middleware('isAllow:111,can_edit');
        Route::delete('testimonials', 'delete')->name('testimonials.delete')->middleware('isAllow:111,can_delete');
    });

     // ----------------------- Client Routes ----------------------------------------------------
     Route::controller(ClientController::class)->group(function () {
        Route::get('clients', 'index')->name('clients')->middleware('isAllow:111,can_view');
        Route::get('clients/add', 'add')->name('clients.add')->middleware('isAllow:111,can_add');
        Route::post('clients/add', 'save')->name('clients.add')->middleware('isAllow:111,can_add');
        Route::get('clients/{id}', 'edit')->name('clients.edit')->middleware('isAllow:111,can_edit');
        Route::post('clients/{id}', 'update')->name('clients.edit')->middleware('isAllow:111,can_edit');
        Route::delete('clients', 'delete')->name('clients.delete')->middleware('isAllow:111,can_delete');
    });

    Route::any('setting/{id}', [SettingController::class, 'setting'])->name('setting')->middleware('isAllow:101,can_view');
    Route::get('database-backup', [SettingController::class, 'database_backup'])->name('database_backup')->middleware('isAllow:101,can_view');
    Route::get('server-control', [SettingController::class, 'serverControl'])->name('server-control')->middleware('isAllow:101,can_view');
    Route::post('server-control', [SettingController::class, 'serverControlSave'])->name('server-control')->middleware('isAllow:101,can_view');
});

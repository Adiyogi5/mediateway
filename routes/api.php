<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json([
        'message'   => "Adiyogi eTally :: Api Working Fine."
    ]);
});


// Send All Types of Notices 
Route::get('/bulk_send_2b_notice', function () {
    Artisan::call('bulk:send-2b-notice');   
    return '<h1>update bulk send 2b notice</h1>';
});
Route::get('/bulk_send_3a_notice', function () {
    Artisan::call('bulk:send-3a-notice');   
    return '<h1>update bulk send 3a notice</h1>';
});
Route::get('/bulk_send_3b_notice', function () {
    Artisan::call('bulk:send-3b-notice');   
    return '<h1>update bulk send 3b notice</h1>';
});
Route::get('/bulk_send_3c_notice', function () {
    Artisan::call('bulk:send-3c-notice');   
    return '<h1>update bulk send 3c notice</h1>';
});


Route::get('clear-all', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('storage:link');
    return '<h1>Clear All</h1>';
});


Route::any('{path}', function () {
    return response()->json([
        'status'    => false,
        'message'   => 'Api not found..!!'
    ], 404);
})->where('path', '.*');

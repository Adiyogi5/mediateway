<?php

use App\Http\Controllers\Api\CommonController;
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

//Create Live Court Rooms - Arbitrator
Route::get('/create_live_court_room', function () {
    Artisan::call('bulk:create-live-court-room');   
    return '<h1>create live court room</h1>';
});
//Status Live Court Rooms - Arbitrator
Route::get('/status_live_court_room', function () {
    Artisan::call('bulk:status-live-court-room');   
    return '<h1>status live court room</h1>';
});


//Create Live Meeting Rooms - Conciliator
Route::get('/create_live_conciliator_meeting_room', function () {
    Artisan::call('bulk:create-live-conciliator-meeting-room');   
    return '<h1>create live conciliator meeting room</h1>';
});
//Status Live Meeting Rooms - Conciliator
Route::get('/status_live_conciliator_meeting_room', function () {
    Artisan::call('bulk:status-live-conciliator-meeting-room');   
    return '<h1>status live conciliator meeting room</h1>';
});


//Create Live Meeting Rooms - Mediator
Route::get('/create_live_mediator_meeting_room', function () {
    Artisan::call('bulk:create-live-mediator-meeting-room');   
    return '<h1>create live mediator meeting room</h1>';
});
//Status Live Meeting Rooms - Mediator
Route::get('/status_live_mediator_meeting_room', function () {
    Artisan::call('bulk:status-live-mediator-meeting-room');   
    return '<h1>status live mediator meeting room</h1>';
});


//Create Claim Petition
Route::get('/create_claim_petition', function () {
    Artisan::call('bulk:create-claim-petition');   
    return '<h1>create claim petition</h1>';
});


// Send All Types of Notices 
// Route::get('/bulk_send_1_notice', function () {
//     Artisan::call('bulk:send-1-notice');   
//     return '<h1>update bulk send 1 notice</h1>';
// });
// Route::get('/bulk_send_1b_notice', function () {
//     Artisan::call('bulk:send-1b-notice');   
//     return '<h1>update bulk send 1b notice</h1>';
// });
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
Route::get('/bulk_send_3d_notice', function () {
    Artisan::call('bulk:send-3d-notice');   
    return '<h1>update bulk send 3d notice</h1>';
});
Route::get('/bulk_send_4a_notice', function () {
    Artisan::call('bulk:send-4a-notice');   
    return '<h1>update bulk send 4a notice</h1>';
});
Route::get('/bulk_send_5a_notice', function () {
    Artisan::call('bulk:send-5a-notice');   
    return '<h1>update bulk send 5a notice</h1>';
});


Route::get('/bulk_send_email_preconciliation_notice', function () {
    Artisan::call('bulk:preconciliation-notice-email-send');   
    return '<h1>update bulk send email preconciliation notice</h1>';
});
Route::get('/bulk_send_whatsapp_preconciliation_notice', function () {
    Artisan::call('bulk:preconciliation-notice-whatsapp-send');   
    return '<h1>update bulk send whatsapp preconciliation notice</h1>';
});
Route::get('/bulk_send_sms_preconciliation_notice', function () {
    Artisan::call('bulk:preconciliation-notice-sms-send');   
    return '<h1>update bulk send sms preconciliation notice</h1>';
});


Route::get('clear-all', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('storage:link');
    return '<h1>Clear All</h1>';
});

Route::controller(CommonController::class)->group(function () {
    Route::post('send-otp', 'sendOtp');
});

Route::any('{path}', function () {
    return response()->json([
        'status'    => false,
        'message'   => 'Api not found..!!'
    ], 404);
})->where('path', '.*');

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


Route::controller(CommonController::class)->group(function () {
    Route::post('send-otp', 'sendOtp');
});


//Create Live Court Rooms - Arbitrator
Route::get('/create_live_court_room', function () {
    Artisan::call('bulk:create-live-court-room');   
    return '<h1>create live court room</h1>';
});
Route::get('/create_email_live_court_room', function () {
    Artisan::call('bulk:create-email-live-court-room');   
    return '<h1>create email live court room</h1>';
});
Route::get('/create_whatsapp_live_court_room', function () {
    Artisan::call('bulk:create-whatsapp-live-court-room');   
    return '<h1>create whatsapp live court room</h1>';
});
//Status Live Court Rooms - Arbitrator
Route::get('/status_live_court_room', function () {
    Artisan::call('bulk:status-live-court-room');   
    return '<h1>status live court room</h1>';
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

// ########################################################
// ############### Stage 2B Notice Crone ##################
Route::get('/bulk_send_email_2b_notice', function () {
    Artisan::call('bulk:send-email-2b-notice');   
    return '<h1>update bulk send email 2b notice</h1>';
});
Route::get('/bulk_send_whatsapp_2b_notice', function () {
    Artisan::call('bulk:send-whatsapp-2b-notice');   
    return '<h1>update bulk send whatsapp 2b notice</h1>';
});
Route::get('/bulk_send_sms_2b_notice', function () {
    Artisan::call('bulk:send-sms-2b-notice');   
    return '<h1>update bulk send sms 2b notice</h1>';
});

// ########################################################
// ############### Stage 3A Notice Crone ##################
Route::get('/bulk_send_email_3a_notice', function () {
    Artisan::call('bulk:send-email-3a-notice');   
    return '<h1>update bulk send email 3a notice</h1>';
});
Route::get('/bulk_send_whatsapp_3a_notice', function () {
    Artisan::call('bulk:send-whatsapp-3a-notice');   
    return '<h1>update bulk send whatsapp 3a notice</h1>';
});
Route::get('/bulk_send_sms_3a_notice', function () {
    Artisan::call('bulk:send-sms-3a-notice');   
    return '<h1>update bulk send sms 3a notice</h1>';
});

// ########################################################
// ############### Stage 3B Notice Crone ##################
Route::get('/bulk_send_email_3b_notice', function () {
    Artisan::call('bulk:send-email-3b-notice');   
    return '<h1>update bulk send email 3b notice</h1>';
});
Route::get('/bulk_send_whatsapp_3b_notice', function () {
    Artisan::call('bulk:send-whatsapp-3b-notice');   
    return '<h1>update bulk send whatsapp 3b notice</h1>';
});
Route::get('/bulk_send_sms_3b_notice', function () {
    Artisan::call('bulk:send-sms-3b-notice');   
    return '<h1>update bulk send sms 3b notice</h1>';
});

// ########################################################
// ############### Stage 3C Notice Crone ##################
Route::get('/bulk_send_email_3c_notice', function () {
    Artisan::call('bulk:send-email-3c-notice');   
    return '<h1>update bulk send email 3c notice</h1>';
});
Route::get('/bulk_send_whatsapp_3c_notice', function () {
    Artisan::call('bulk:send-whatsapp-3c-notice');   
    return '<h1>update bulk send whatsapp 3c notice</h1>';
});
Route::get('/bulk_send_sms_3c_notice', function () {
    Artisan::call('bulk:send-sms-3c-notice');   
    return '<h1>update bulk send sms 3c notice</h1>';
});

// ########################################################
// ############### Stage 3D Notice Crone ##################
Route::get('/bulk_send_3d_notice', function () {
    Artisan::call('bulk:send-3d-notice');   
    return '<h1>update bulk send 3d notice</h1>';
});
//Create Claim Petition
Route::get('/create_claim_petition', function () {
    Artisan::call('bulk:create-claim-petition');   
    return '<h1>create claim petition</h1>';
});

// ########################################################
// ############### Stage 4A Notice Crone ##################
Route::get('/bulk_send_email_4a_notice', function () {
    Artisan::call('bulk:send-email-4a-notice');   
    return '<h1>update bulk send email 4a notice</h1>';
});
Route::get('/bulk_send_whatsapp_4a_notice', function () {
    Artisan::call('bulk:send-whatsapp-4a-notice');   
    return '<h1>update bulk send whatsapp 4a notice</h1>';
});
Route::get('/bulk_send_sms_4a_notice', function () {
    Artisan::call('bulk:send-sms-4a-notice');   
    return '<h1>update bulk send sms 4a notice</h1>';
});

// ########################################################
// ############### Stage 5A Notice Crone ##################
Route::get('/bulk_send_email_5a_notice', function () {
    Artisan::call('bulk:send-email-5a-notice');   
    return '<h1>update bulk send email 5a notice</h1>';
});
Route::get('/bulk_send_whatsapp_5a_notice', function () {
    Artisan::call('bulk:send-whatsapp-5a-notice');   
    return '<h1>update bulk send whatsapp 5a notice</h1>';
});
Route::get('/bulk_send_sms_5a_notice', function () {
    Artisan::call('bulk:send-sms-5a-notice');   
    return '<h1>update bulk send sms 5a notice</h1>';
});



// ################################################################
// ############### Pre-Conciliation Notice Crone ##################
Route::get('/bulk_save_pdf_preconciliation_notice', function () {
    Artisan::call('bulk:preconciliation-notice-pdf-save');   
    return '<h1>update bulk save pdf preconciliation notice</h1>';
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
//############### Conciliation Notice Crone ##################
Route::get('/bulk_send_email_conciliation_notice', function () {
    Artisan::call('bulk:conciliation-notice-email-send');   
    return '<h1>update bulk send email conciliation notice</h1>';
});
Route::get('/bulk_send_whatsapp_conciliation_notice', function () {
    Artisan::call('bulk:conciliation-notice-whatsapp-send');   
    return '<h1>update bulk send whatsapp conciliation notice</h1>';
});
Route::get('/bulk_send_sms_conciliation_notice', function () {
    Artisan::call('bulk:conciliation-notice-sms-send');   
    return '<h1>update bulk send sms conciliation notice</h1>';
});
//########### Create Live Meeting Rooms - Conciliator ###########
Route::get('/create_live_email_conciliator_meeting_room', function () {
    Artisan::call('bulk:create-live-email-conciliator-meeting-room');   
    return '<h1>create live email conciliator meeting room</h1>';
});
Route::get('/create_live_whatsapp_conciliator_meeting_room', function () {
    Artisan::call('bulk:create-live-whatsapp-conciliator-meeting-room');   
    return '<h1>create live whatsapp conciliator meeting room</h1>';
});
//########### Status Live Meeting Rooms - Conciliator ###########
Route::get('/status_live_conciliator_meeting_room', function () {
    Artisan::call('bulk:status-live-conciliator-meeting-room');   
    return '<h1>status live conciliator meeting room</h1>';
});



// #############################################################
// ################ Pre-Mediation Notice Crone #################
Route::get('/bulk_save_pdf_premediation_notice', function () {
    Artisan::call('bulk:premediation-notice-pdf-save');   
    return '<h1>update bulk save pdf premediation notice</h1>';
});
Route::get('/bulk_send_email_premediation_notice', function () {
    Artisan::call('bulk:premediation-notice-email-send');   
    return '<h1>update bulk send email premediation notice</h1>';
});
Route::get('/bulk_send_whatsapp_premediation_notice', function () {
    Artisan::call('bulk:premediation-notice-whatsapp-send');   
    return '<h1>update bulk send whatsapp premediation notice</h1>';
});
Route::get('/bulk_send_sms_premediation_notice', function () {
    Artisan::call('bulk:premediation-notice-sms-send');   
    return '<h1>update bulk send sms premediation notice</h1>';
});
//################ Mediation Notice Crone #################
Route::get('/bulk_send_email_mediation_notice', function () {
    Artisan::call('bulk:mediation-notice-email-send');   
    return '<h1>update bulk send email mediation notice</h1>';
});
Route::get('/bulk_send_whatsapp_mediation_notice', function () {
    Artisan::call('bulk:mediation-notice-whatsapp-send');   
    return '<h1>update bulk send whatsapp mediation notice</h1>';
});
Route::get('/bulk_send_sms_mediation_notice', function () {
    Artisan::call('bulk:mediation-notice-sms-send');   
    return '<h1>update bulk send sms mediation notice</h1>';
});
//########### Create Live Meeting Rooms - Mediator ###########
Route::get('/create_live_email_mediator_meeting_room', function () {
    Artisan::call('bulk:create-live-email-mediator-meeting-room');   
    return '<h1>create live email mediator meeting room</h1>';
});
Route::get('/create_live_whatsapp_mediator_meeting_room', function () {
    Artisan::call('bulk:create-live-whatsapp-mediator-meeting-room');   
    return '<h1>create live whatsapp mediator meeting room</h1>';
});
//########### Status Live Meeting Rooms - Mediator ###########
Route::get('/status_live_mediator_meeting_room', function () {
    Artisan::call('bulk:status-live-mediator-meeting-room');   
    return '<h1>status live mediator meeting room</h1>';
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

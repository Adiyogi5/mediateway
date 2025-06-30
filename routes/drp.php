<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\Drp\AdvocateCourtRoomController;
use App\Http\Controllers\Drp\AwardController;
use App\Http\Controllers\Drp\CaseAssignController;
use App\Http\Controllers\Drp\CaseBulkUpdateController;
use App\Http\Controllers\Drp\CaseListController;
use App\Http\Controllers\Drp\CaseManagerCourtRoomController;
use App\Http\Controllers\Drp\CaseNoticeListController;
use App\Http\Controllers\Drp\CasesAllNoticeListController;
use App\Http\Controllers\Drp\ConciliatorMeetingRoomController;
use App\Http\Controllers\Drp\CourtRoomController;
use App\Http\Controllers\Drp\ProfileController;
use App\Http\Controllers\Drp\HomeController;
use App\Http\Controllers\Drp\MediatorMeetingRoomController;
use App\Http\Controllers\Drp\OrderSheetController;
use App\Http\Controllers\Drp\SendNoticeController;
use App\Http\Controllers\Drp\SettlementLetterController;
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


Route::name('drp.')->middleware(['ensure.drp.session'])->prefix('drp')->group(function () {

    Route::controller(HomeController::class)->group(function () {
        Profile::routes();
        Route::get('dashboard', 'index')->name('dashboard');
       
    });

    // ----------------------- Profile Routes ---------------------------------------
    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'index')->name('profile');
        Route::put('profile/update', 'update')->name('profile.update');
    });

    // ----------------------- Order Sheet Routes ---------------------------------------
    Route::controller(AwardController::class)->group(function () {
        Route::get('/award', 'index')->name('award');
        Route::get('award/add', 'add')->name('award.add');
        Route::post('award/add', 'save')->name('award.add');
        Route::get('award/{id}', 'edit')->name('award.edit');
        Route::post('award/{id}', 'update')->name('award.edit');
        Route::delete('award', 'delete')->name('award.delete');
        Route::get('getawardVariables', 'getawardVariables')->name('getawardVariables');
    });

    // ----------------------- Order Sheet Routes ---------------------------------------
    Route::controller(OrderSheetController::class)->group(function () {
        Route::get('/ordersheet', 'index')->name('ordersheet');
        Route::get('ordersheet/add', 'add')->name('ordersheet.add');
        Route::post('ordersheet/add', 'save')->name('ordersheet.add');
        Route::get('ordersheet/{id}', 'edit')->name('ordersheet.edit');
        Route::post('ordersheet/{id}', 'update')->name('ordersheet.edit');
        Route::delete('ordersheet', 'delete')->name('ordersheet.delete');
        Route::get('getordersheetVariables', 'getordersheetVariables')->name('getordersheetVariables');
    });

    // ----------------------- Settlement Order Routes ---------------------------------------
    Route::controller(SettlementLetterController::class)->group(function () {
        Route::get('/settlementletter', 'index')->name('settlementletter');
        Route::get('settlementletter/add', 'add')->name('settlementletter.add');
        Route::post('settlementletter/add', 'save')->name('settlementletter.add');
        Route::get('settlementletter/{id}', 'edit')->name('settlementletter.edit');
        Route::post('settlementletter/{id}', 'update')->name('settlementletter.edit');
        Route::delete('settlementletter', 'delete')->name('settlementletter.delete');
        Route::get('getsettlementletterVariables', 'getsettlementletterVariables')->name('getsettlementletterVariables');
    });

    // ----------------------- Case Manager - Send Notices Routes ---------------------------------
    Route::controller(SendNoticeController::class)->group(function () {
        Route::get('conciliationnoticelist', 'conciliationnoticelist')->name('conciliationprocess.conciliationnoticelist');
        Route::get('/conciliation-process/case-list', 'caseList')->name('conciliationprocess.caseList');
        Route::post('/conciliation-process/send-notices', 'sendNotices')->name('conciliationprocess.sendNotices');
        Route::get('/conciliation-notice/{id}', 'getConciliationNotice')->name('conciliationprocess.viewdetail');
    });

     // ----------------------- CaseAssign Routes ----------------------------------------------------
    Route::controller(CaseAssignController::class)->group(function () {
        Route::get('caseassign', 'index')->name('caseassign');
        Route::get('caseassign/{id}', 'assign')->name('caseassign.assign');
        Route::put('caseassign/assigndetail/{id}', 'updateassigndetail')->name('caseassign.updateassigndetail');
        Route::delete('caseassign', 'delete')->name('caseassign.delete');
        Route::get('caseassign/{id}/edit', 'edit')->name('caseassign.edit');
        Route::put('caseassign/casedetail/{id}', 'updateCaseDetail')->name('caseassign.updatecasedetail');
    });

    // ----------------------- Advocate - Court Room Routes ----------------------------------------
    Route::controller(AdvocateCourtRoomController::class)->group(function () {
        Route::get('advocatecourtroom', 'index')->name('advocatecourtroom.courtroomlist');
        Route::get('liveadvocatecourtroom/{room_id}', 'livecourtroom')->name('advocatecourtroom.livecourtroom');
        Route::get('/get-flattened-advocate-case-data/{caseId}', 'getFlattenedAdvocateCaseData')->name('advocatecourtroom.getFlattenedAdvocateCaseData');
        Route::post('/fetch-advocate-notices', 'fetchNoticesByCaseId')->name('advocatecourtroom.fetch.notices');
        Route::post('/fetch-advocate-awards', 'fetchAwardsByCaseId')->name('advocatecourtroom.fetch.awards');
        Route::post('/advocatecourtroom/save-notice', 'saveNotice')->name('advocatecourtroom.savenotice');
        Route::get('/advocatecourtroom/datatable/upcoming-rooms', 'upcomingRoomsData')->name('advocatecourtroom.datatable.upcoming');
        Route::get('/advocatecourtroom/datatable/closed-rooms', 'closedRoomsData')->name('advocatecourtroom.datatable.closed');
    });

    // ----------------------- Case Manager - Case Bulk Update Routes ---------------------------------
    Route::controller(CaseBulkUpdateController::class)->group(function () {
        Route::get('casebulkupdate', 'casebulkupdate')->name('cases.casebulkupdate');
        Route::post('/import-casebulkupdate', 'importBulkUpdateExcel')->name('cases.casebulkupdate.import');
        Route::get('/download-sample', 'downloadBulkUpdateSample')->name('cases.casebulkupdate.sample');
    });

    // ----------------------- Case Manager - All Case Notices Routes ----------------------------------
    Route::controller(CasesAllNoticeListController::class)->group(function () {
        Route::get('cashmanagercasenoticelist', 'index')->name('allnotices.cashmanagercasenoticelist');
    });

    // ----------------------- Case Manager - Court Room Routes ----------------------------------------
    Route::controller(CaseManagerCourtRoomController::class)->group(function () {
        Route::get('casemanagercourtroom', 'index')->name('casemanagercourtroom.courtroomlist');
        Route::get('livecasemanagercourtroom/{room_id}', 'livecourtroom')->name('casemanagercourtroom.livecourtroom');
        Route::get('/get-flattened-casemanager-case-data/{caseId}', 'getFlattenedCasemanagerCaseData')->name('casemanagercourtroom.getFlattenedCasemanagerCaseData');
        Route::post('/fetch-casemanager-notices', 'fetchNoticesByCaseId')->name('casemanagercourtroom.fetch.notices');
        Route::post('/fetch-casemanager-awards', 'fetchAwardsByCaseId')->name('casemanagercourtroom.fetch.awards');
        Route::post('/casemanagercourtroom/save-notice', 'saveNotice')->name('casemanagercourtroom.savenotice');
        Route::get('/casemanagercourtroom/datatable/upcoming-rooms', 'upcomingRoomsData')->name('casemanagercourtroom.datatable.upcoming');
        Route::get('/casemanagercourtroom/datatable/closed-rooms', 'closedRoomsData')->name('casemanagercourtroom.datatable.closed');
    });

    // ----------------------- Arbitrator - Court Room Routes ---------------------------------------
    Route::controller(CourtRoomController::class)->group(function () {
        Route::get('courtroomlist', 'index')->name('courtroom.courtroomlist');
        Route::get('livecourtroom/{room_id}', 'livecourtroom')->name('courtroom.livecourtroom');
        Route::post('/courtroom/save-recording', 'saveRecording')->name('courtroom.saveRecording');
        Route::get('/courtroom/get-flattened-case-data/{caseId}', 'getFlattenedCaseData')->name('courtroom.getFlattenedCaseData');
        Route::post('/courtroom/fetch-notices', 'fetchNoticesByCaseId')->name('courtroom.fetch.notices');
        Route::post('/courtroom/fetch-awards', 'fetchAwardsByCaseId')->name('courtroom.fetch.awards');
        Route::post('/courtroom/save-notice', 'saveNotice')->name('courtroom.savenotice');
        Route::post('/courtroom/close-court-room','closeCourtRoom')->name('courtroom.close');
        Route::get('/courtroom/datatable/upcoming-rooms', 'upcomingRoomsData')->name('courtroom.datatable.upcoming');
        Route::get('/courtroom/datatable/closed-rooms', 'closedRoomsData')->name('courtroom.datatable.closed');
    });

    // ----------------------- Arbitrator - Case List Routes ---------------------------------------
    Route::controller(CaseListController::class)->group(function () {
        Route::get('caselist', 'index')->name('allcases.caselist');
        Route::post('approve', 'approveCase')->name('allcases.approve');
        Route::get('viewcasedetail/{id}', 'viewcasedetail')->name('allcases.viewcasedetail');
    });

    // ----------------------- Arbitrator - Case Notice List Routes ---------------------------------------
    Route::controller(CaseNoticeListController::class)->group(function () {
        Route::get('arbitratorcasenoticelist', 'index')->name('allnotices.arbitratorcasenoticelist');
    });

    // ----------------------- Conciliator - Meeting Room Routes ---------------------------------------
    Route::controller(ConciliatorMeetingRoomController::class)->group(function () {
        Route::get('conciliator-meetingroom-list', 'index')->name('conciliatormeetingroom.conciliatormeetingroomlist');
        Route::get('live-conciliator-meetingroom/{room_id}', 'liveconciliatormeetingroom')->name('conciliatormeetingroom.liveconciliatormeetingroom');
        Route::get('/conciliator-meetingroom/case-list', 'caseList')->name('conciliatormeetingroom.caseList');
        Route::post('conciliator-meetingroom/save', 'store')->name('conciliatormeetingroom.store');
        Route::post('/conciliator-meetingroom/save-recording', 'saveRecording')->name('conciliatormeetingroom.saveRecording');
        Route::get('/conciliator-meetingroom/get-flattened-case-data/{caseId}', 'getFlattenedCaseData')->name('conciliatormeetingroom.getFlattenedCaseData');
        Route::post('/conciliator-meetingroom/fetch-notices', 'fetchNoticesByCaseId')->name('conciliatormeetingroom.fetch.notices');
        Route::post('/conciliator-meetingroom/fetch-awards', 'fetchAwardsByCaseId')->name('conciliatormeetingroom.fetch.awards');
        Route::post('/conciliator-meetingroom/fetch-settlementagreements', 'fetchSettlementAgreementsByCaseId')->name('conciliatormeetingroom.fetch.settlementagreements');
        Route::post('/conciliator-meetingroom/save-notice', 'saveNotice')->name('conciliatormeetingroom.savenotice');
        Route::post('/conciliator-meetingroom/close-meeting-room','closeconciliatormeetingroom')->name('conciliatormeetingroom.close');
        Route::get('/conciliator-meetingroom/datatable/upcoming-rooms', 'upcomingRoomsData')->name('conciliatormeetingroom.datatable.upcoming');
        Route::get('/conciliator-meetingroom/datatable/closed-rooms', 'closedRoomsData')->name('conciliatormeetingroom.datatable.closed');

    });


    // ----------------------- Mediator - Meeting Room Routes ---------------------------------------
    Route::controller(MediatorMeetingRoomController::class)->group(function () {
        Route::get('mediator-meetingroom-list', 'index')->name('mediatormeetingroom.mediatormeetingroomlist');
        Route::get('live-mediator-meetingroom/{room_id}', 'livemediatormeetingroom')->name('mediatormeetingroom.livemediatormeetingroom');
        Route::get('/mediator-meetingroom/case-list', 'caseList')->name('mediatormeetingroom.caseList');
        Route::post('mediator-meetingroom/save', 'store')->name('mediatormeetingroom.store');
        Route::post('/mediator-meetingroom/save-recording', 'saveRecording')->name('mediatormeetingroom.saveRecording');
        Route::get('/mediator-meetingroom/get-flattened-case-data/{caseId}', 'getFlattenedCaseData')->name('mediatormeetingroom.getFlattenedCaseData');
        Route::post('/mediator-meetingroom/fetch-notices', 'fetchNoticesByCaseId')->name('mediatormeetingroom.fetch.notices');
        Route::post('/mediator-meetingroom/fetch-awards', 'fetchAwardsByCaseId')->name('mediatormeetingroom.fetch.awards');
        Route::post('/mediator-meetingroom/fetch-settlementagreements', 'fetchSettlementAgreementsByCaseId')->name('mediatormeetingroom.fetch.settlementagreements');
        Route::post('/mediator-meetingroom/save-notice', 'saveNotice')->name('mediatormeetingroom.savenotice');
        Route::post('/mediator-meetingroom/close-meeting-room','closemediatormeetingroom')->name('mediatormeetingroom.close');
        Route::get('/mediator-meetingroom/datatable/upcoming-rooms', 'upcomingRoomsData')->name('mediatormeetingroom.datatable.upcoming');
        Route::get('/mediator-meetingroom/datatable/closed-rooms', 'closedRoomsData')->name('mediatormeetingroom.datatable.closed');

    });

    Route::post('get-cities', [CityController::class, 'get_cities'])->name('cities.list');
});

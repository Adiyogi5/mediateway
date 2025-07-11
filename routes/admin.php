<?php

use App\Http\Controllers\AwardController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookAppointmentController;
use App\Http\Controllers\CallBackController;
use App\Http\Controllers\CaseAssignController;
use App\Http\Controllers\CaseBulkUpdateController;
use App\Http\Controllers\CasesAllNoticeListController;
use App\Routes\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClaimPetitionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConciliationCaseAssignController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\DrpListController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\HomeCmsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NoticeTemplateController;
use App\Http\Controllers\OrderSheetController;
use App\Http\Controllers\OrganizationListController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ServiceFeeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SettlementLetterController;
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
    Route::get('all-sms-count', [HomeController::class,  'all_sms_count'])->name('all_sms_count');

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

    // ----------------------- Notices Routes ---------------------------------------
    Route::controller(NoticeTemplateController::class)->group(function () {
        Route::get('/noticetemplate', 'index')->name('noticetemplate');
        Route::get('noticetemplate/add', 'add')->name('noticetemplate.add');
        Route::post('noticetemplate/add', 'save')->name('noticetemplate.add');
        Route::get('noticetemplate/{id}', 'edit')->name('noticetemplate.edit');
        Route::post('noticetemplate/{id}', 'update')->name('noticetemplate.edit');
        Route::delete('noticetemplate', 'delete')->name('noticetemplate.delete');
        Route::get('getnoticetemplateVariables', 'getnoticetemplateVariables')->name('getnoticetemplateVariables');
    });

    // ----------------------- Award Routes ---------------------------------------
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

     // ----------------------- Order Sheet Routes ---------------------------------------
     Route::controller(ClaimPetitionController::class)->group(function () {
        Route::get('/claimpetition', 'index')->name('claimpetition');
        Route::get('claimpetition/add', 'add')->name('claimpetition.add');
        Route::post('claimpetition/add', 'save')->name('claimpetition.add');
        Route::get('claimpetition/{id}', 'edit')->name('claimpetition.edit');
        Route::post('claimpetition/{id}', 'update')->name('claimpetition.edit');
        Route::delete('claimpetition', 'delete')->name('claimpetition.delete');
        Route::get('getclaimpetitionVariables', 'getclaimpetitionVariables')->name('getclaimpetitionVariables');
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

    // ----------------------- Case Bulk Update Routes -----------------------------------------------
    Route::controller(CaseBulkUpdateController::class)->group(function () {
        Route::get('casebulkupdate', 'casebulkupdate')->name('cases.casebulkupdate');
        Route::post('/import-casebulkupdate', 'importBulkUpdateExcel')->name('cases.casebulkupdate.import');
        Route::get('/download-sample', 'downloadBulkUpdateSample')->name('cases.casebulkupdate.sample');

    });

    // ----------------------- CaseAssign Routes -------------------------------------------------
    Route::controller(CaseAssignController::class)->group(function () {
        Route::get('caseassign', 'index')->name('caseassign')->middleware('isAllow:111,can_view');
        Route::get('caseassign/{id}', 'assign')->name('caseassign.assign')->middleware('isAllow:111,can_edit');
        Route::put('caseassign/assigndetail/{id}', 'updateassigndetail')->name('caseassign.updateassigndetail')->middleware('isAllow:111,can_edit');
        Route::delete('caseassign', 'delete')->name('caseassign.delete')->middleware('isAllow:111,can_delete');
        Route::get('caseassign/{id}/edit', 'edit')->name('caseassign.edit')->middleware('isAllow:111,can_edit');
        Route::put('caseassign/casedetail/{id}', 'updateCaseDetail')->name('caseassign.updatecasedetail')->middleware('isAllow:111,can_edit');
    });

    // ----------------------- CaseAssign Routes -------------------------------------------------
    Route::controller(ConciliationCaseAssignController::class)->group(function () {
        Route::get('conciliation-caseassign', 'index')->name('conciliation_caseassign')->middleware('isAllow:111,can_view');
        Route::post('conciliation-caseassign/bulkassign', 'bulkAssign')->name('conciliation_caseassign.bulkassign');
        Route::get('conciliation-caseassign/unassigned-data', 'unassignedCases')->name('conciliation_caseassign.unassigneddata');
        Route::delete('conciliation-caseassign', 'delete')->name('conciliation_caseassign.delete')->middleware('isAllow:111,can_delete');
    });

    // ----------------------- All Case Notices Routes -------------------------------------------
    Route::controller(CasesAllNoticeListController::class)->group(function () {
        Route::get('casenoticelist', 'index')->name('allcasenotices.casenoticelist');
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

    // ----------------------- Organization List Routes ----------------------------------------------------
    Route::controller(OrganizationListController::class)->group(function () {
        Route::get('organizationlist', 'index')->name('organizationlist.index')->middleware('isAllow:112,can_view');
        Route::post('organizationlist', 'save')->name('organizationlist.store')->middleware('isAllow:112,can_add');
        Route::put('organizationlist', 'update')->name('organizationlist.update')->middleware('isAllow:112,can_edit');
        Route::delete('organizationlist', 'delete')->name('organizationlist.destroy')->middleware('isAllow:112,can_delete');
    });

    // ----------------------- DRP List Routes ----------------------------------------------------
    Route::controller(DrpListController::class)->group(function () {
        Route::get('drplist', 'index')->name('drplist.index')->middleware('isAllow:112,can_view');
        Route::get('drplist/drp-detail/{id}', 'show')->name('drplist.drpdetail')->middleware('isAllow:112,can_view');
        Route::post('drplist/approve/{id}', 'approve')->name('drplist.approve')->middleware('isAllow:112,can_add');;
        Route::post('drplist/reject/{id}', 'reject')->name('drplist.reject')->middleware('isAllow:112,can_add');;
    });

    // ----------------------- BLOG Routes ----------------------------------------------------
    Route::controller(BlogController::class)->group(function () {
        Route::get('blogs', 'index')->name('blogs')->middleware('isAllow:104,can_view');
        Route::get('blogs/add', 'add')->name('blogs.add')->middleware('isAllow:104,can_add');
        Route::post('blogs/add', 'save')->name('blogs.add')->middleware('isAllow:104,can_add');
        Route::get('blogs/{id}', 'edit')->name('blogs.edit')->middleware('isAllow:104,can_edit');
        Route::post('blogs', 'slug')->name('blogs.slug')->middleware('isAllow:104,can_edit');
        Route::post('blogs/{id}', 'update')->name('blogs.edit')->middleware('isAllow:104,can_edit');
        Route::delete('blogs', 'delete')->name('blogs')->middleware('isAllow:104,can_delete');
    });

    // ----------------------- NEWS Routes ----------------------------------------------------
    Route::controller(NewsController::class)->group(function () {
        Route::get('news', 'index')->name('news')->middleware('isAllow:104,can_view');
        Route::get('news/add', 'add')->name('news.add')->middleware('isAllow:104,can_add');
        Route::post('news/add', 'save')->name('news.add')->middleware('isAllow:104,can_add');
        Route::get('news/{id}', 'edit')->name('news.edit')->middleware('isAllow:104,can_edit');
        Route::post('news', 'slug')->name('news.slug')->middleware('isAllow:104,can_edit');
        Route::post('news/{id}', 'update')->name('news.edit')->middleware('isAllow:104,can_edit');
        Route::delete('news', 'delete')->name('news')->middleware('isAllow:104,can_delete');
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

    // ----------------------- Service Fee Routes ----------------------------------------------------
    Route::controller(ServiceFeeController::class)->group(function () {
        Route::get('servicefee', 'index')->name('servicefee')->middleware('isAllow:111,can_view');
        Route::get('servicefee/add', 'add')->name('servicefee.add')->middleware('isAllow:111,can_add');
        Route::post('servicefee/add', 'save')->name('servicefee.add')->middleware('isAllow:111,can_add');
        Route::get('servicefee/{id}', 'edit')->name('servicefee.edit')->middleware('isAllow:111,can_edit');
        Route::post('servicefee/{id}', 'update')->name('servicefee.edit')->middleware('isAllow:111,can_edit');
        Route::delete('servicefee', 'delete')->name('servicefee.delete')->middleware('isAllow:111,can_delete');
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

    // ----------------------- Contact Inquiries Routes ---------------------------------------------
    Route::controller(ContactUsController::class)->group(function () {
        Route::get('inquiries', 'index')->name('inquiries')->middleware('isAllow:111,can_view');
        Route::get('inquiries/{id}', 'show')->name('inquiries.show')->middleware('isAllow:111,can_view');
        Route::delete('inquiries/{id}', 'destroy')->name('inquiries.delete')->middleware('isAllow:111,can_delete');
    });

    // ----------------------- Book Appointment Routes ---------------------------------------------
    Route::controller(BookAppointmentController::class)->group(function () {
        Route::get('bookappointments', 'index')->name('bookappointments')->middleware('isAllow:111,can_view');
        Route::get('bookappointments/{id}', 'show')->name('bookappointments.show')->middleware('isAllow:111,can_view');
        Route::delete('bookappointments/{id}', 'destroy')->name('bookappointments.delete')->middleware('isAllow:111,can_delete');
    });

    // ----------------------- Call Back Routes ---------------------------------------------
    Route::controller(CallBackController::class)->group(function () {
        Route::get('callbacks', 'index')->name('callbacks')->middleware('isAllow:111,can_view');
        Route::get('callbacks/{id}', 'show')->name('callbacks.show')->middleware('isAllow:111,can_view');
        Route::delete('callbacks/{id}', 'destroy')->name('callbacks.delete')->middleware('isAllow:111,can_delete');
    });

    Route::any('setting/{id}', [SettingController::class, 'setting'])->name('setting')->middleware('isAllow:101,can_view');
    Route::get('database-backup', [SettingController::class, 'database_backup'])->name('database_backup')->middleware('isAllow:101,can_view');
    Route::get('server-control', [SettingController::class, 'serverControl'])->name('server-control')->middleware('isAllow:101,can_view');
    Route::post('server-control', [SettingController::class, 'serverControlSave'])->name('server-control')->middleware('isAllow:101,can_view');
});

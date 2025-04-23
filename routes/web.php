<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Route;

// ================== Frontend Routes ==================
Route::controller(FrontController::class)->name('front.')->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/home', 'index')->name('home');
    Route::get('{cms}', 'showCms')->name('show-cms')->whereIn('cms', ['about-us', 'terms-conditions', 'privacy-policy', 'rules','why-choose','return-cancel','shipping-delivery']);

    // ============= call-back Routes ======
    Route::get('/call-back', 'callback')->name('callback');
    Route::post('/request-call-back', 'requestcallback')->name('requestcallback');

    // ============= book-appointment Routes ======
    Route::get('/book-appointment', 'bookappointment')->name('bookappointment');
    Route::post('/request-book-appointment', 'requestbookappointment')->name('requestbookappointment');

    // ============= contact-us Routes ======
    Route::get('/contact-us', 'contactus')->name('contactus');
    Route::post('/submit-contact-us', 'submitcontactus')->name('submitcontactus');

    // ============= Blogs Routes ======
    Route::get('/blogs', 'blogs')->name('blogs');

    // ============= News Routes ======
    Route::get('/news', 'news')->name('news');

    // ============= Faqs Routes ======
    Route::get('/faqs', 'faqs')->name('faqs');
});

// ================== Push Notification Routes ==================
Route::get('/push-notificaiton', [FireController::class, 'index'])->name('push-notificaiton');
Route::post('/store-token', [FireController::class, 'storeToken'])->name('store.token');
Route::post('/send-web-notification', [FireController::class, 'sendWebNotification'])->name('send.web-notification');

// ================== Common Routes ==================
Route::get('test', [CommonController::class, 'test'])->name('test');
Route::post('get-cities', [CityController::class, 'get_cities'])->name('cities.list');
Route::post('upload-image', [CommonController::class, 'upload_image'])->name('upload_image');
Route::get('get-user-list-filter', [CommonController::class, 'get_user_list_filter'])->name('get_user_list_filter');

// Redirects based on guard type
Route::get('{guard}', function ($guard) {
    if (in_array($guard, ['admin', 'individual', 'organization', 'drp'])) {
        return redirect(url("/$guard/login"));
    }
    abort(404);
})->whereIn('guard', ['admin', 'individual', 'organization', 'drp']);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ================== Dashboard Route ==================
// Route::redirect('admin/dashboard', '/dashboard');

// ================== Protected Routes (Authenticated Users Only) ==================
// Route::middleware(['authCheck'])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('home');
//     })->name('dashboard');
// });

// Handle any undefined routes
Route::fallback(function () {
    abort(404);
});

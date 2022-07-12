<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// GetYourGuide Routes

Route::get('/1/get-availabilities/', 'Api\ApiController@getAvailabilities');
Route::post('/1/reserve/', 'Api\ApiController@reserve');
Route::post('/1/cancel-reservation/', 'Api\ApiController@cancelReservation');
Route::post('/1/book/', 'Api\ApiController@book');
Route::post('/1/cancel-booking/', 'Api\ApiController@cancelBooking');

// Bokun Routes

Route::get('/plugin/definition/', 'Api\BokunController@getDefinition');
Route::post('/product/search', 'Api\BokunController@searchProducts');
Route::post('/product/getById', 'Api\BokunController@getProductById');
Route::post('/product/getAvailable', 'Api\BokunController@getAvailable');
Route::post('/product/getAvailability', 'Api\BokunController@getAvailability');
Route::post('/booking/reserve', 'Api\BokunController@reservation');
Route::post('/booking/confirm', 'Api\BokunController@confirmation');
Route::post('/booking/cancel', 'Api\BokunController@cancellation');

// Viator Routes

Route::post('/viator/get-availabilities', 'Api\ViatorController@getAvailabilities');
Route::post('/viator/get-tour-list', 'Api\ViatorController@getTourList');
Route::post('/viator/book', 'Api\ViatorController@book');
Route::post('/viator/cancel-booking', 'Api\ViatorController@cancelBooking');
Route::post('/viator/amend-booking', 'Api\ViatorController@amendBooking');


// Mobile Api Routes
Route::post('/mobile/login', 'Api\GuideController@postLogin');
Route::post('/mobile/getCustomerLogin', 'Api\CustomerController@getCustomerLogin');


Route::middleware('customer')->group(function () {
    Route::post('/mobile/getCancelPolicyByOption', 'Api\CustomerController@getCancelPolicyByOption');
    Route::post('/mobile/getBarcodeForCustomer', 'Api\CustomerController@getBarcodeForCustomer');
    Route::post('/mobile/getMeetingPointsForCustomer', 'Api\CustomerController@getMeetingPointsForCustomer');
    Route::post('/mobile/getGuideInformationForCustomer', 'Api\CustomerController@getGuideInformationForCustomer');
    Route::post('/mobile/cancelBookingByCustomer', 'Api\CustomerController@cancelBookingByCustomer');
    Route::post('/mobile/setCustomerLog', 'Api\CustomerController@setCustomerLog');
});

Route::middleware('guide')->group(function () {

    Route::post('/mobile/checkin', 'Api\GuideController@checkIn');
    Route::post('/mobile/deleteCheck', 'Api\GuideController@deleteCheck');
    Route::post('/mobile/getDateById', 'Api\GuideController@getDateById');
    Route::post('/mobile/getMeetingTimesByDate', 'Api\GuideController@getMeetingTimesByDate');
    Route::post('/mobile/getOptions', 'Api\GuideController@getOptionsByTime');
    Route::post('/mobile/getTargetMeetings', 'Api\GuideController@getTargetMeetings');
    Route::post('/mobile/getTargetMeetingsForKrep', 'Api\GuideController@getTargetMeetingsForKrep');
    Route::post('/mobile/getTargetMeetingsForSupplier', 'Api\GuideController@getTargetMeetingsForSupplier');

    Route::post('/mobile/setCustomersByGuide', 'Api\GuideController@setCustomersByGuide');
    Route::post('/mobile/setBill', 'Api\GuideController@setBill');
    Route::post('/mobile/setGuideImage', 'Api\GuideController@setGuideImage');
    Route::post('/mobile/getBillsByUserId', 'Api\GuideController@getBillsByUserId');
    Route::post('/mobile/updateuser', 'Api\GuideController@updateUser');
    Route::post('/mobile/setMeetingStartEndTime', 'Api\GuideController@setMeetingStartEndTime');


// Mobile Api Statistic Routes
    Route::post('/mobile/othersstatistic', 'Api\StatisticController@OthersStc');
    Route::post('/mobile/guidestatistic', 'Api\StatisticController@GuideStc');
    Route::post('/mobile/supplierstatistic', 'Api\StatisticController@SupplierStc');


// Mobile Api GuideManagement Input Output
    Route::post('/mobile/setShiftInputOutput', 'Api\GuideManagementController@setShiftInputOutput');
    Route::post('/mobile/getShifts', 'Api\GuideManagementController@getShifts');

    Route::post('/mobile/getGuideCalendar', 'Api\GuideController@getGuideCalendar');

    Route::get('/mobile/ticket-types', 'Api\BarcodeController@index');
    Route::post('/mobile/read-barcodes', 'Api\BarcodeController@readBarcodes');



});

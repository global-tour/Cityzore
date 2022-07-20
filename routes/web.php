<?php

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


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



//Admin Route Groups
Route::group(['domain' => 'admin.'. env('ROUTE_VARIABLE', 'cityzore.com')], function () {

    Route::get('/try', 'TryController@index');
    //Admin Login Routes
    Route::get('/login', 'Admin\LoginController@showLoginForm')->name('admin.login')->middleware('guest:admin');
    Route::post('/login', 'Admin\LoginController@login');
    Route::post('validateLogin', 'Auth\LoginController@validateLogin');

    // Password Reset Routes
    Route::get('password/reset', 'Auth\LoginController@resetPassword');
    Route::post('sendResetPasswordEmail', 'Auth\LoginController@sendResetPasswordEmail');
    Route::get('reset-password-link/{token}', 'Auth\LoginController@resetPasswordLinkForPanel');
    Route::post('sendNewPassword', 'Auth\LoginController@sendNewPassword');
});

Route::get('get-options', 'Admin\ProductController@getOptionsAjax');



Route::group(['domain' => 'admin.'. env('ROUTE_VARIABLE', 'cityzore.com'), 'middleware' => 'auth:admin'], function () {
    Route::get('booking-sync', 'MesutTestController@index');
    Route::group(['namespace' => 'Supplier'], function () {
        Route::middleware('permission:Supplier Create/Edit')->group(function () {
            Route::get('supplier/create', 'SupplierController@create')->name('supplier.create');
            Route::post('supplier/create', 'SupplierController@store')->name('supplier.store');
            Route::get('supplier/{id}/edit', 'SupplierController@edit')->name('supplier.edit');
            Route::post('/supplier/{id}/update', 'SupplierController@update');
            Route::get('changeSupplierStatus', 'SupplierController@changeStatus')->name('setStatus');
            Route::post('/supplier/sendRCode', 'SupplierController@selectOptionsForRCode');
            Route::post('/supplier/removeOption', 'SupplierController@removeOption');
            Route::post('/supplier/supplierid', 'SupplierController@getOptions');
            Route::post('/supplier/changeLicenseStatus', 'Supplier\SupplierController@changeLicenseStatus');
            Route::post('/supplier/deleteLicense', 'Supplier\SupplierController@deleteLicense');
            Route::post('/supplier/editSuggestFile', 'Supplier\SupplierController@editSuggestFile');
        });

        Route::middleware('permission:Show All Suppliers')->group(function () {
            Route::get('supplier', 'SupplierController@index')->name('supplier.index');
            Route::get('/supplier/{id}/details', 'SupplierController@detailsForAdmin');
        });

        Route::any('/supplier/{id}/destroy', 'SupplierController@destroy')->name('supplier.destroy')->middleware('permission:Supplier Delete');
    });

    Route::group(['namespace' => 'User'], function () {
        Route::middleware('permission:User Create/Edit')->group(function () {
            Route::get('user', 'UserController@index');
            Route::get('user/{id}/edit', 'UserController@edit');
            Route::post('user/{id}/update', 'UserController@update');
            Route::get('user/create', 'UserController@create');
            Route::post('user/store', 'UserController@store');
            Route::get('/commissioners', 'CommissionerController@commissioners');
            Route::get('/commissioner/setStatus', 'CommissionerController@setStatus');
            Route::post('/commission/saveCommission', 'CommissionerController@saveCommission');
            Route::get('/commissioner/{id}/editCommissions', 'CommissionerController@editCommissions');
            Route::post('/commissioner/optionSelect', 'CommissionerController@optionSelect');
            Route::post('/commissioner/saveOptionCommission', 'User\CommissionerController@saveOptionCommission');
            Route::get('/commissioner/{id}/details', 'CommissionerController@commissionerDetails');
            Route::any('/commissioner/{id}/turnToStandardUser', 'CommissionerController@turnToStandardUser');
            Route::post('/deleteLicenses', 'CommissionerController@deleteLicenses');
            Route::post('/editSuggestFile', 'CommissionerController@editSuggestFile');
            Route::post('/changeLicenseStatus', 'CommissionerController@changeLicenseStatus');
            Route::post('/commission/saveCommissionType', 'CommissionerController@saveCommissionType');
        });

        Route::post('user/{id}/destroy', 'UserController@destroy')->middleware('permission:User Delete');
    });

    Route::group(['namespace' => 'UserManagement'], function () {
        Route::middleware('permission:User Management')->group(function () {
            Route::get('/permission/create', 'PermissionController@create');
            Route::get('/permission/{id}/edit', 'PermissionController@edit');
            Route::post('/permission/{id}/update', 'PermissionController@update');
            Route::post('/permission/store', 'PermissionController@store');
            Route::get('/permissions', 'PermissionController@index');
            Route::any('/permission/{id}/delete', 'PermissionController@destroy');
            Route::post('/permissions/admin-or-all-users', 'PermissionController@adminOrAllUsers');
            Route::get('/roles', 'RoleController@index');
            Route::get('/role/create', 'RoleController@create');
            Route::post('/role/store', 'RoleController@store');
            Route::get('/role/{id}/edit', 'RoleController@edit');
            Route::post('/role/{id}/update', 'RoleController@update');
            Route::any('/role/{id}/delete', 'RoleController@destroy');
        });
    });

    Route::group(['namespace' => 'Ticket', 'prefix' => 'ticket'], function() {
        Route::get('/','TicketController@index')->name('ticket.index');
        Route::post('/payment','TicketController@takeTicket')->name('post.payment');
    });

    Route::group(['namespace' => 'Admin'], function () {

        Route::post('/barcodes-excel-import-ajax', 'BarcodeController@importExcelAjax');

        Route::get('/', 'PanelController@adminPanel')->name('adminPanel');
        Route::get('/availability', 'PanelController@availabilityDate')->name('availabilityDate');
        Route::get('/statistic', 'StatisticController@index')->name('statistic.index');
        Route::get('/statistic/barcode-analysis', 'StatisticController@barcodeAnalysis')->name('statistic.barcodeAnalysis');
        Route::post('/statistic/barcode-analysis/update', 'StatisticController@barcodeAnalysisUpdate')->name('statistic.barcodeAnalysisUpdate');
        Route::post('/statistic/upload/{which}', 'StatisticController@uploadStatistic')->name('statistic.upload');
        Route::post('/statistic/ready', 'StatisticController@readyStatistic');
        Route::post('/logout','LoginController@logout')->name('logout');
        Route::get('/close-window', 'PanelController@closeWindow');


        Route::name('Admin')->middleware('permission:Admin Create/Edit/Delete')->group(function () {

            Route::get('category', "CategoryController@index");
            Route::get('category/create', 'CategoryController@create');
            Route::post('category/store', 'CategoryController@store');
            Route::get('category/{id}/edit', 'CategoryController@edit');
            Route::post('category/{id}/update', 'CategoryController@update');
            Route::post('category/{id}/destroy', 'CategoryController@destroy');

            Route::get('platform', "PlatformController@index");
            Route::get('platform/create', 'PlatformController@create');
            Route::post('platform/store', 'PlatformController@store');
            Route::get('platform/{id}/edit', 'PlatformController@edit');
            Route::post('platform/{id}/update', 'PlatformController@update');
            Route::post('platform/{id}/destroy', 'PlatformController@destroy');

            Route::get('/adminchat', "ChatController@getChatPage");

            Route::get('/admin', 'AdminController@index')->name('admin.index');
            Route::get('/admin/{id}/edit', 'AdminController@edit')->name('admin.edit');
            Route::post('/admin/{id}/update', 'AdminController@update')->name('admin.update');
            Route::get('/admin/create', 'AdminController@create')->name('admin.create');
            Route::post('/admin/store', 'AdminController@store')->name('admin.store');
            Route::post('/admin/{id}/destroy', 'AdminController@destroy')->name('admin.destroy');
            Route::post('/admin/ajax', 'AdminController@ajax')->name('admin.ajax');
            Route::get('/userLogs', 'AdminController@getUserLogs');
            Route::get('/apiLogs', 'AdminController@getApiLogs');
            Route::get('/paymentLogs', 'PaymentController@paymentLogs');
            Route::get('/errorLogs', 'AdminController@getErrorLogs');
            Route::post('/adminLogs', 'AdminController@createAdminLogs');
            Route::post('/mailLogs', 'AdminController@createMailLogs');
            Route::get('/meetingLogs', 'MeetingController@getMeetingLogs');
            Route::get('/customerLogs', 'CustomerController@getMobileCustomerLogs');
            Route::post('/customerlog/ajax', 'CustomerController@ajax');
            Route::get('/fetchCustomerLogs', 'CustomerController@fetchCustomerLogs');
        });


        Route::name('Guide')->group(function () {

            Route::get('guide/index', 'GuideManagementController@index');
            Route::get('guide/detail/{id}', 'GuideManagementController@detail');
            Route::post('guide/ajax', 'GuideManagementController@ajax');

            Route::get('guide/planning', 'GuideManagementController@planning');
            Route::get('guide/planning/cal_events', 'GuideManagementController@cal_events');

            Route::post('guide/planning/cal_quicksave', 'GuideManagementController@cal_quicksave');
            Route::post('guide/planning/cal_description', 'GuideManagementController@cal_description');
            Route::post('guide/planning/cal_check_rep_events', 'GuideManagementController@cal_check_rep_events');
            Route::post('guide/planning/cal_edit_update', 'GuideManagementController@cal_edit_update');
            Route::post('guide/planning/cal_delete', 'GuideManagementController@cal_delete');
            Route::post('guide/planning/cal_update', 'GuideManagementController@cal_update');

            Route::get('guide/planning/exporter', 'GuideManagementController@exporter');
            Route::get('guide/planning/cal_export', 'GuideManagementController@cal_export');

            Route::post('guide/planning/importer', 'GuideManagementController@importer');
            Route::post('guide/planning/loader', 'GuideManagementController@loader');
        });




        Route::name('Product')->group(function () {
            Route::middleware('permission:Product Create')->group(function () {
                Route::get('/product/create', 'ProductController@create');
                Route::post('/product/store', 'ProductController@store');
                Route::get('/product/{id}/copy', 'ProductController@copyProduct');
                Route::post('/confirmSupplierProductEdit', 'ProductController@confirmSupplierProductEdit');
            });

            Route::middleware('permission:Product Edit')->group(function () {
                Route::get('/product/{id}/edit', 'ProductController@edit');
                Route::post('/product/{id}/edit', 'ProductController@update');
                Route::get('/productPCT/{id}/edit', 'ProductController@editPCT');
                Route::post('/productPCT/{id}/edit', 'ProductController@updatePCT');
                Route::get('/productPCTcom/{id}/edit', 'ProductController@editPCTcom');
                Route::post('/productPCTcom/{id}/edit', 'ProductController@updatePCTcom');
                Route::get('/productCTP/{id}/edit', 'ProductController@editCTP');
                Route::post('/productCTP/{id}/edit', 'ProductController@updateCTP');
                Route::get('/changeProductDraftStatus', 'ProductController@changeDraftStatus')->name('setProductDraftStatus');
                Route::get('/changeProductPublishedStatus', 'ProductController@changePublishedStatus')->name('setProductPublishedStatus');
                Route::post('/product/orderImages', 'ProductController@orderImages');
            });

            Route::get('/product', 'ProductController@index')->name('product.index')->middleware('permission:Show All Products|Product Create|Product Edit|Product Delete');
            Route::get('/productPCT', 'ProductController@indexPCT')->middleware('permission:Show All Products|Product Create|Product Edit|Product Delete');
            Route::get('/productPCTcom', 'ProductController@indexPCTcom')->middleware('permission:Show All Products|Product Create|Product Edit|Product Delete');
            Route::get('/productCTP', 'ProductController@indexCTP')->middleware('permission:Show All Products|Product Create|Product Edit|Product Delete');

            Route::post('/product/{id}/delete', 'ProductController@destroy')->name('admin.product.destroy')->middleware('permission:Product Delete');

            Route::get('/product/{id}/deleteDraft', 'Admin\ProductController@deleteDraft')->middleware('permission:Product Delete');
            Route::post('product/deletePhoto', 'ProductController@deletePhoto')->middleware('permission:Product Create|Product Edit');
            Route::post('product/setAsCoverPhoto', 'ProductController@setAsCoverPhoto')->middleware('permission:Product Create|Product Edit');
            Route::post('product/getImageGallery', 'ProductController@getImageGallery')->middleware('permission:Product Create|Product Edit');
            Route::post('product/setImagesForProduct', 'ProductController@setImagesForProduct')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/titleValidation', 'ProductController@titleValidation')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/isFilledAll', 'ProductController@isFilledAll')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/getOptions', 'ProductController@getOptions')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/create/getCities', 'ProductController@getCities')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/getProductImages', 'ProductController@getProductImages')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/deleteProductFile', 'ProductController@deleteProductFile')->middleware('permission:Product Create|Product Edit');
            Route::post('updateProductStepByStep', 'ProductController@updateProductStepByStep')->middleware('permission:Product Create|Product Edit');
            Route::post('productDraft', 'ProductController@productDraft')->middleware('permission:Product Create|Product Edit');
            Route::get('/product-preview/{id}', 'ProductController@productPreview')->middleware('permission:Show All Products|Product Create|Product Edit|Product Delete');
            Route::post('/metaTagSave', 'MetaTagController@store')->middleware('permission:Product Create|Product Edit');
            Route::post('/getProductGalleryData', 'ProductController@getProductGalleryData')->middleware('permission:Product Create|Product Edit');;
        });

        Route::name('Option')->group(function () {
            Route::get('/option', 'OptionController@index')->middleware('permission:Show All Options');
            Route::get('/option/{id}/edit', 'OptionController@edit')->name('option.edit')->middleware('permission:Option Edit');
            Route::post('/option/{id}/update', 'OptionController@update')->name('option.update')->middleware('permission:Option Edit');
            Route::any('/option/{id}/delete', 'OptionController@destroy')->middleware('permission:Option Delete');
            Route::get('/option/create', 'OptionController@create')->middleware('permission:Option Create');
            Route::post('/option/optionStore', 'OptionController@optionStore')->middleware('permission:Product Create');
            Route::post('/option/optionUpdate', 'OptionController@optionUpdate')->middleware('permission:Product Edit');
            Route::post('/option/store', 'OptionController@store')->middleware('permission:Option Create');
            Route::post('/option/preview', 'OptionController@preview')->middleware('permission:Product Create|Product Edit');
            Route::post('/option/{id}/detach', 'OptionController@optionDetach')->middleware('permission:Product Edit');
            Route::post('/option/show-supplier', 'OptionController@optionSupp')->middleware('permission:Show All Options');
            Route::post('/option/saveOptionCommission', 'OptionController@saveOptionCommission')->middleware('permission:User Create/Edit');
            Route::get('/option/changeOptionPublishedStatus', 'OptionController@changeOptionPublishedStatus')->middleware('permission:Option Edit');
            Route::post('/option/setApiConnection', 'OptionController@setApiConnection')->middleware('permission:Show All Options');
            Route::post('/option/getOptionPart', 'OptionController@getOptionPart')->middleware('permission:Product Create|Product Edit');
            Route::post('/option/saveOptionPart', 'OptionController@saveOptionPart')->middleware('permission:Product Create|Product Edit');
            Route::post('/option/ajax', 'OptionController@ajax')->middleware('permission:Product Create|Product Edit');
            Route::get('/option/{id}/copy', 'OptionController@copyOption');
            Route::post('/save-option-files', 'OptionController@saveOptionFiles');
            Route::post('/delete-option-file', 'OptionController@deleteOptionFile');
            Route::post('/get-bigbus-products', 'OptionController@getBigbusProducts')->middleware('permission:Product Create|Product Edit')->name('bigbus.products');
            Route::post('/api-connect-form', 'OptionController@apiConnect')->middleware('permission:Product Create|Product Edit');
            Route::post('/api-disconnect-form', 'OptionController@apiDisconnect')->middleware('permission:Product Create|Product Edit');
        });

        Route::name('Voucher')->group(function () {
            Route::middleware('permission:Voucher Template Access')->group(function () {
                Route::resource('voucher-template', 'VoucherTemplateController');
            });

            Route::middleware('permission:Voucher Create')->group(function () {
                Route::get('/voucher/create', 'VoucherController@create');
                Route::post('/voucher/store', 'VoucherController@store');
            });

            Route::middleware('permission:Voucher Edit')->group(function () {
                Route::get('/voucher/{id}/edit', 'VoucherController@edit');
                Route::post('/voucher/{id}/update', 'VoucherController@update');
            });

            Route::get('/vouchers', 'VoucherController@index')->middleware('permission:Show All Vouchers');
            Route::post('/voucher/{id}/delete', 'VoucherController@destroy')->middleware('permission:Voucher Delete');
            Route::post('/voucher/getOptions', 'VoucherController@getOptions')->middleware('permission:Voucher Create|Voucher Edit');
        });

        Route::name('Create Ticket')->group(function () {
            Route::get('/single-pdf/{id}', 'BarcodeController@createTicket')->name('single.pdf')->middleware('permission:Create Ticket');
        });

        Route::name('Gallery')->group(function () {
            Route::middleware('permission:Photo Upload')->group(function () {
                Route::post('/gallery/uploadPhoto/{which}', 'GalleryController@uploadPhoto');
                Route::get('/gallery/create', 'GalleryController@create');
                Route::get('/gallery/cityPhotos', 'GalleryController@cityPhotos');
                Route::get('/gallery/addCityPhoto', 'GalleryController@addCityPhoto');
                Route::post('gallery/uploadPhotoForCity', 'GalleryController@uploadPhotoForCity');
                Route::get('/gallery/editCityPhoto/{id}', 'GalleryController@editCityPhoto');
                Route::post('/gallery/uploadOnlyPhotoForCity', 'GalleryController@uploadOnlyPhotoForCity');
            });

            Route::get('/gallery', 'GalleryController@index')->middleware('permission:Show All Photos|Photo Upload|Photo Edit|Photo Delete');
            Route::get('/gallery/{id}/delete', 'GalleryController@delete')->middleware('permission:Photo Delete');
            Route::post('/gallery/updateImage', 'GalleryController@update')->middleware('permission:Photo Edit');
        });

        Route::name('Pricing')->group(function() {
            Route::middleware('permission:Pricing Create')->group(function () {
                Route::get('pricing/create', 'PricingController@create');
                Route::post('/pricing/store', 'PricingController@store');
            });

            Route::get('pricing/{id}/edit', 'PricingController@edit')->middleware('permission:Pricing Edit');
            Route::post('pricing/update', 'PricingController@update')->middleware('permission:Pricing Edit');
            Route::get('pricings', 'PricingController@index')->name('pricing.index')->middleware('permission:Show All Pricings');
            Route::any('pricing/{id}/delete', 'PricingController@destroy')->middleware('permission:Pricing Delete');
        });

        Route::name('Attraction')->group(function () {
            Route::middleware('permission:Attraction Create/Edit/Delete')->group(function () {
                Route::get('/attraction/create', 'AttractionController@create');
                Route::post('/attraction/store', 'AttractionController@store');


                Route::get('/attraction/{id}/edit', 'AttractionController@edit');
                Route::get('/attractionpct/{id}/edit', 'AttractionController@editpct');
                Route::get('/attractionpctcom/{id}/edit', 'AttractionController@editpctcom');
                Route::get('/attractionctp/{id}/edit', 'AttractionController@editctp');


                Route::post('/attraction/{id}/update', 'AttractionController@update');
                Route::post('/attraction/bindCity', 'AttractionController@bindCity');
                Route::post('/attraction/deleteCity', 'AttractionController@deleteCity');
                Route::post('/attraction/setStatus', 'AttractionController@setStatus');
            });

            Route::get('/attraction', 'AttractionController@index')->middleware('permission:Show All Attractions');
            Route::get('/attractionPCT', 'AttractionController@indexPCT')->middleware('permission:Show All Attractions');
            Route::get('/attractionPCTcom', 'AttractionController@indexPCTcom')->middleware('permission:Show All Attractions');
            Route::get('/attractionCTP', 'AttractionController@indexCTP')->middleware('permission:Show All Attractions');
        });

        Route::name('Booking')->group(function () {
            Route::get('/bookings', 'BookingController@index')->name('bookings.index')->middleware('permission:Show All Bookings');
            Route::get('/bookingsv2', 'BookingController@indexV2')->middleware('permission:Show All Bookings');
            Route::post('/bookings-excel-import', 'BookingController@importExcel')->name('bookings.importExcel');
            Route::post('/bookings-extra-file-import', 'BookingController@boookings_extra_file_imports')->name('bookings.extra');
            Route::get('/mail-information', 'BookingController@mailInformation');
            Route::get('/mails', 'BookingController@mails');
            Route::post('/get-booking-detail', 'BookingController@bookingDetail')->middleware('permission:Show All Bookings');
            Route::post('update-booking-detail', 'BookingController@updateBookingDetail')->middleware('permission:Show All Bookings');
            Route::post('/send-mail-to-customer', 'BookingController@sendMailToCustomer')->middleware('permission:Show All Bookings');
            Route::post('import-invoice-number', 'BookingController@importInvoiceNumber')->middleware('permission:Show All Bookings');
            Route::post('delete-invoice-number', 'BookingController@deleteImportedInvoice')->middleware('permission:Show All Bookings');

            Route::middleware('permission:Booking Create/Edit')->group(function () {
                Route::get('/booking/{id}/edit', 'BookingController@edit')->name('bookings.edit');
                Route::post('/booking/{id}/update', 'BookingController@update')->name('bookings.update');
                Route::get('/booking/create', 'BookingController@create')->name('bookings.create');
                Route::post('/booking/store', 'BookingController@store')->name('booking.store');
                Route::post('/booking/productSelect', 'BookingController@productSelect');
                Route::post('/booking/optionSelect', 'BookingController@optionSelect');
                Route::post('/booking/bookingTime', 'BookingController@bookingTime');
                Route::get('/booking/changeStatus/{id}', 'BookingController@changeStatus');
                Route::get('/booking/addComment/{id}', 'BookingController@addComment');
                Route::post('/booking/storeComment', 'BookingController@storeComment');
                Route::get('/booking/specialRefCode/{id}', 'BookingController@specialRefCode');
                Route::post('/booking/storeSpecialRefCode', 'BookingController@storeSpecialRefCode');
                Route::get('/booking/exportToExcel', 'BookingController@exportToExcel');
                Route::post('/saveRCode', 'BookingController@saveRCode');
                Route::post('booking/ajax', 'BookingController@ajax');
                Route::post('/booking/optionSelectMultiple', 'BookingController@optionSelectMultiple');
                Route::get('/downloadExtraFile/{id}', 'BookingController@downloadExtraFile');
                Route::post('/checkMailForCustomer', 'BookingController@checkMailForCustomer');
                Route::post('/checkCustomerMail', 'BookingController@checkCustomerMail');
                Route::post('/add-comment-to-booking', 'BookingController@addCommentToBooking');
                Route::post('/add-special-ref-code', 'BookingController@specialRefCodev2');


                Route::get('/meeting/index', 'MeetingController@index')->name('bookings.meeting.index');
                Route::get('/meeting/excel', 'MeetingController@get_meetings_excel')->name('bookings.meeting.excel');
                Route::get('/meeting/pdf', 'MeetingController@get_meetings_pdf')->name('bookings.meeting.pdf');
                Route::post('/meeting/ajax', 'MeetingController@ajax')->name('bookings.meeting.ajax');

                Route::get('/on-goings', 'BookingController@onGoings');
            });
        });

        Route::name('External Payment')->group(function () {
            Route::get('/external-payment', 'PaymentController@externalPayment')->name('externalPayment')->middleware('permission:Show All External Payments');
            Route::post('/external-payment/edit', 'PaymentController@externalPaymentEdit')->middleware('permission:Show All External Payments');

            Route::middleware('permission:External Payment Create')->group(function () {
                Route::get('/external-payment/create', 'PaymentController@externalPaymentCreate');
                Route::post('/storePaymentLink', 'PaymentController@storePaymentLink');
                Route::post('resendEmail', 'PaymentController@resendEmail');
                Route::get('/external-payments-pdf/{id}', 'PaymentController@externalPaymentInvoice')->middleware('permission:External Payment Create');
            });
        });

        Route::name('Blog')->group(function () {
            Route::get('/blog', 'BlogController@index')->middleware('permission:Show All Blog Post');
            Route::get('/blogPCT', 'BlogController@indexPCT')->middleware('permission:Show All Blog Post');
            Route::get('/blogPCTcom', 'BlogController@indexPCTcom')->middleware('permission:Show All Blog Post');
            Route::get('/blogCTP', 'BlogController@indexCTP')->middleware('permission:Show All Blog Post');
            Route::get('/blog/{id}/delete', 'BlogController@destroy')->middleware('permission:Blog Delete');
            Route::get('/blog/{id}/deletePCT', 'BlogController@destroyPCT')->middleware('permission:Blog Delete');
            Route::get('/blog/{id}/deletePCTcom', 'BlogController@destroyPCTcom')->middleware('permission:Blog Delete');
            Route::get('/blog/{id}/deleteCTP', 'BlogController@destroyCTP')->middleware('permission:Blog Delete');

            Route::middleware('permission:Blog Create/Edit')->group(function () {
                Route::get('/blog/create', 'BlogController@create');
                Route::get('/blog/createPCT', 'BlogController@createPCT');
                Route::get('/blog/createPCTcom', 'BlogController@createPCTcom');
                Route::get('/blog/createCTP', 'BlogController@createCTP');
                Route::post('/blog/createNewBlogPost', 'BlogController@createNewBlogPost');
                Route::post('/blog/create/uploadImageForBlogPost', 'BlogController@uploadImageForBlogPost');
                Route::get('/blog/{id}/edit', 'BlogController@edit');
                Route::get('/blog/{id}/editPCT', 'BlogController@editPCT');
                Route::get('/blog/{id}/editPCTcom', 'BlogController@editPCTcom');
                Route::get('/blog/{id}/editCTP', 'BlogController@editCTP');
                Route::post('/blog/{id}/update', 'BlogController@update');
                Route::post('/blog/ajax', 'BlogController@ajax');
            });
        });

        Route::name('Special Offers')->group(function () {
            Route::middleware('permission:Special Offer Create/Edit')->group(function () {
                Route::get('/special-offers/create', 'SpecialOffersController@create');
                Route::post('/special-offers/getDateTimes', 'SpecialOffersController@getDateTimes');
                Route::post('/special-offers/saveChanges', 'SpecialOffersController@saveChanges');
                Route::post('/special-offers/getAvailabilityType', 'SpecialOffersController@getAvailabilityType');
                Route::post('/special-offers/changeSpecialOfferStatus', 'SpecialOffersController@changeSpecialOfferStatus');
                Route::post('/special-offers/deleteOldSpecialOffer', 'SpecialOffersController@deleteOldSpecialOffer');
                Route::post('/special-offers/edit', 'SpecialOffersController@edit');
            });

            Route::get('/special-offers', 'SpecialOffersController@index')->middleware('permission:Show All Special Offers');
        });

        Route::name('Finance')->group(function () {
            Route::middleware('permission:Finance Show/Download')->group(function () {
                Route::get('/finance', 'FinanceController@financeAdmin');
                Route::get('/bills', 'FinanceController@financeBills');
                Route::get('/finance/exportToExcel', 'FinanceController@exportToExcel');
                Route::post('/finance/get-bookings', 'FinanceController@getBookings');
                Route::get('/finance/download-bill-image/{id}', 'FinanceController@downloadBillImage');
                Route::post('/finance/ajax', 'FinanceController@ajax');
            });
        });

        Route::name('Barcode')->group(function () {
            Route::middleware('permission:Barcode Create')->group(function () {
                Route::get('/barcodes/create', 'BarcodeController@create');
                Route::post('/barcodes/store', 'BarcodeController@store');
                Route::post('/barcodes-excel-import', 'BarcodeController@importExcel');
                Route::post('/barcodes-excel-import-for-opera', 'BarcodeController@importOperaExcel');
                Route::post('/barcodes-excel-import-for-picasso', 'BarcodeController@importPicassoExcel');
                Route::post('/barcodes-excel-import-for-grevin', 'BarcodeController@importGrevinExcel');
                Route::post('/barcodes-pdf-import-for-triomphe', 'BarcodeController@importPdfForTriomphe');
                Route::post('/barcodes-pdf-import-for-sainte', 'BarcodeController@importPdfForSainte');
                Route::post('/barcodes-excel-import-for-rodin', 'BarcodeController@importRodinExcel');
                Route::post('/barcodes-excel-import-for-montparnasse-adult', 'BarcodeController@importMontparnasseAdultExcel');
                Route::post('/barcodes-excel-import-for-montparnasse-infant', 'BarcodeController@importMontparnasseInfantExcel');
                Route::post('/barcodes-excel-import-for-orsay-or-orangerie', 'BarcodeController@importOrsayOrOrangerieExcel');
                Route::post('/barcodes-excel-import-for-pompidou', 'BarcodeController@importPompidouExcel');
            });

            Route::middleware('permission:Create Ticket')->group(function () {
                Route::post('/barcode/createTicket/{id}', 'BarcodeController@createTicket')->name('createTicket');
                Route::get('/barcode/multiple-ticket', 'BarcodeController@multipleTicket');
                Route::post('/barcode/multiple-ticket', 'BarcodeController@createMultipleTicket')->name('multipleTicket');
                Route::get('/barcodes/changeIsUsedStatus', 'BarcodeController@changeIsUsedStatus');
            });

            Route::get('/barcodes', 'BarcodeController@index')->middleware('permission:Show All Barcodes');
            Route::post('/barcodes/remove', 'BarcodeController@destroy');
        });

        Route::name('Ticket Type')->group(function () {
            Route::middleware('permission:Ticket Type Create/Edit')->group(function () {
                Route::get('/ticket-type/create', 'TicketTypeController@create');
                Route::post('/ticket-type/create', 'TicketTypeController@store')->name('ticket-type.store');
                Route::get('/ticket-type/{id}/edit', 'TicketTypeController@edit');
                Route::post('/ticket-type/{id}/update', 'TicketTypeController@update');
                Route::post('/ticket-type/setUsableAsTicket', 'TicketTypeController@setUsableAsTicket');
            });

            Route::get('/ticket-type', 'TicketTypeController@index')->middleware('permission:Show All Ticket Types');
        });

        Route::name('Coupon')->group(function () {
            Route::middleware('permission:Coupon Create/Edit')->group(function () {
                Route::get('/coupon/create', 'CouponController@create');
                Route::post('/coupon/optionSelect', 'CouponController@optionSelect');
                Route::post('/coupon/couponSaved', 'CouponController@saveNewCoupon');
                Route::post('coupon/edit', 'CouponController@edit');
            });

            Route::get('/coupons', 'CouponController@index')->name('coupon.index')->middleware('permission:Show All Coupons');
            Route::any('coupon/{id}/delete', 'CouponController@destroy')->middleware('permission:Coupon Delete');
        });

        Route::name('Config')->group(function () {
            Route::get('/config', 'ConfigController@config')->middleware('permission:Show Config');
            Route::post('/config/{id}/update', 'ConfigController@saveConfig')->middleware('permission:Config Edit');
             Route::get('/cache-config', 'ConfigController@cacheConfig');
             Route::post('/cache-config', 'ConfigController@saveCacheConfig');

            Route::middleware('permission:Show General Config')->group(function () {
                Route::get('/general-config', 'ConfigController@generalConfig');
                Route::get('/general-config/seo-for-pages', 'ConfigController@seoForPages');
                Route::get('/general-config/seo-for-pages-pct', 'ConfigController@seoForPagesPCT');
                Route::get('/general-config/seo-for-pages-pctcom', 'ConfigController@seoForPagesPCTcom');
                Route::get('/general-config/seo-for-pages-ctp', 'ConfigController@seoForPagesCTP');


                Route::get('/general-config/changeMetaTags/{id}/{platform?}', 'ConfigController@changeMetaTags');
                Route::get('/general-config/product-translations', 'ConfigController@productTranslations');
                Route::get('/general-config/product-translations-pct', 'ConfigController@productTranslationsPCT');
                Route::get('/general-config/product-translations-pctcom', 'ConfigController@productTranslationsPCTcom');
                Route::get('/general-config/product-translations-ctp', 'ConfigController@productTranslationsCTP');
                Route::get('/general-config/option-translations', 'ConfigController@optionTranslations');
                Route::get('/general-config/translateProduct/{productID}/{languageID}', 'ConfigController@translateProduct');
                Route::get('/general-config/translateProduct/{productID}', 'ConfigController@translateProductForAll');
                Route::post('/general-config/getProductTranslation', 'ConfigController@getProductTranslation');
                Route::post('/general-config/saveProductTranslationForAll', 'ConfigController@saveProductTranslationForAll');
                Route::post('/general-config/saveOptionTranslationForAll', 'ConfigController@saveOptionTranslationForAll');
                Route::get('/general-config/translateOption/{optionID}/{languageID}', 'ConfigController@translateOption');
                Route::get('/general-config/product-sort', 'ConfigController@getProductSort');
                Route::post('/general-config/getProductSortForAPage', 'ConfigController@getProductSortForAPage');


                Route::get('/general-config/product-meta-tags-translations', 'ConfigController@productMetaTagsTranslations');
                Route::get('/general-config/product-meta-tags-translations-pct', 'ConfigController@productMetaTagsTranslationsPCT');
                Route::get('/general-config/product-meta-tags-translations-pctcom', 'ConfigController@productMetaTagsTranslationsPCTcom');
                Route::get('/general-config/product-meta-tags-translations-ctp', 'ConfigController@productMetaTagsTranslationsCTP');

                Route::get('/general-config/translateProductMetaTags/{productID}/{languageID}', 'ConfigController@translateProductMetaTags');



                Route::get('/general-config/page-meta-tags-translations', 'ConfigController@pageMetaTagsTranslations'); // *
                Route::get('/general-config/page-meta-tags-translations-pct', 'ConfigController@pageMetaTagsTranslationsPCT'); // *
                Route::get('/general-config/page-meta-tags-translations-pctcom', 'ConfigController@pageMetaTagsTranslationsPCTcom'); // *
                Route::get('/general-config/page-meta-tags-translations-ctp', 'ConfigController@pageMetaTagsTranslationsCTP'); // *




                Route::get('/general-config/category-translations', 'ConfigController@categoryTranslations');
                Route::get('/general-config/translateCategory/{categoryID}/{languageID}', 'ConfigController@translateCategory');

                Route::get('/general-config/translatePageMetaTags/{pageID}/{languageID}', 'ConfigController@translatePageMetaTags'); // *

                Route::get('/general-config/attraction-translations', 'ConfigController@attractionTranslations'); // *
                Route::get('/general-config/attraction-translations-pct', 'ConfigController@attractionTranslationsPCT'); // *
                Route::get('/general-config/attraction-translations-pctcom', 'ConfigController@attractionTranslationsPCTcom'); // *
                Route::get('/general-config/attraction-translations-ctp', 'ConfigController@attractionTranslationsCTP'); // *


                Route::get('/general-config/translateAttraction/{attractionID}/{languageID}', 'ConfigController@translateAttraction');
                Route::get('/general-config/route-translations', 'ConfigController@routeTranslations');
                Route::get('/general-config/translateRoute/{routeID}/{languageID}', 'ConfigController@translateRoute');
                Route::get('/general-config/blog-translations', 'ConfigController@blogTranslations');
                Route::get('/general-config/blog-translations-pct', 'ConfigController@blogTranslationsPCT');
                Route::get('/general-config/blog-translations-pctcom', 'ConfigController@blogTranslationsPCTcom');
                Route::get('/general-config/blog-translations-ctp', 'ConfigController@blogTranslationsCTP');
                Route::get('/general-config/translateBlog/{blogID}/{languageID}', 'ConfigController@translateBlog');


                Route::get('/general-config/blog-meta-tags-translations', 'ConfigController@blogMetaTagsTranslations');
                Route::get('/general-config/blog-meta-tags-translations-pct', 'ConfigController@blogMetaTagsTranslationsPCT');
                Route::get('/general-config/blog-meta-tags-translations-pctcom', 'ConfigController@blogMetaTagsTranslationsPCTcom');
                Route::get('/general-config/blog-meta-tags-translations-ctp', 'ConfigController@blogMetaTagsTranslationsCTP');
                Route::get('/general-config/translateBlogMetaTags/{blogID}/{languageID}', 'ConfigController@translateBlogMetaTags');
                Route::get('/general-config/country-translations', 'ConfigController@countryTranslations');
                Route::get('/general-config/city-translations', 'ConfigController@cityTranslations');
                Route::get('/general-config/faq-translations', 'ConfigController@faqTranslations');
                Route::get('/general-config/translateCountry/{countryID}/{languageID}', 'ConfigController@translateCountry');
                Route::get('/general-config/translateCity/{cityID}/{languageID}', 'ConfigController@translateCity');
                Route::get('/general-config/translateFAQ/{cityID}/{languageID}', 'ConfigController@translateFAQ');
                Route::get('/general-config/update-home-banner', 'ConfigController@updateHomeBanner');
                Route::get('/general-config/update-home-banner-pct', 'ConfigController@updateHomeBannerPCT');
                Route::get('/general-config/update-home-banner-pctcom', 'ConfigController@updateHomeBannerPCTcom');
                Route::get('/general-config/update-home-banner-ctp', 'ConfigController@updateHomeBannerCTP');
            });

            Route::middleware('permission:General Config Edit')->group(function () {
                Route::post('/general-config/saveMetaTags/{id}/{platform?}', 'ConfigController@saveMetaTags');
                Route::post('/general-config/saveProductTranslation/{productID}/{languageID}', 'ConfigController@saveProductTranslation');
                Route::post('/general-config/saveOptionTranslation/{optionID}/{languageID}', 'ConfigController@saveOptionTranslation');
                Route::post('/general-config/pageSelect', 'ConfigController@pageSelect');
                Route::post('/general-config/productSelect', 'ConfigController@productSelect');
                Route::post('/general-config/sendProductSort', 'ConfigController@sendProductSort');
                Route::post('/general-config/unsetSortedProduct', 'ConfigController@unsetSortedProduct');
                Route::post('/general-config/saveProductMetaTagsTranslation/{productID}/{languageID}', 'ConfigController@saveProductMetaTagsTranslation');
                Route::post('/general-config/savePageMetaTagsTranslation/{pageID}/{languageID}', 'ConfigController@savePageMetaTagsTranslation');
                Route::post('/general-config/saveCategoryTranslation/{categoryID}/{languageID}', 'ConfigController@saveCategoryTranslation');
                Route::post('/general-config/saveAttractionTranslation/{attractionID}/{languageID}', 'ConfigController@saveAttractionTranslation');
                Route::post('/general-config/saveRouteTranslation/{routeID}/{languageID}', 'ConfigController@saveRouteTranslation');
                Route::post('/general-config/saveBlogTranslation/{blogID}/{languageID}', 'ConfigController@saveBlogTranslation');
                Route::post('/general-config/saveBlogMetaTagsTranslation/{blogID}/{languageID}', 'ConfigController@saveBlogMetaTagsTranslation');
                Route::post('/general-config/saveCountryTranslation/{countryID}/{languageID}', 'ConfigController@saveCountryTranslation');
                Route::post('/general-config/saveCityTranslation/{cityID}/{languageID}', 'ConfigController@saveCityTranslation');
                Route::post('/general-config/saveFaqTranslation/{cityID}/{languageID}', 'ConfigController@saveFaqTranslation');
                Route::post('/general-config/postHomeBanner', 'ConfigController@postHomeBanner');
                Route::post('/general-config/postHomeBannerPCT', 'ConfigController@postHomeBannerPCT');
                Route::post('/general-config/postHomeBannerPCTcom', 'ConfigController@postHomeBannerPCTcom');
                Route::post('/general-config/postHomeBannerCTP', 'ConfigController@postHomeBannerCTP');
            });

            Route::name('Notification')->group(function () {
                Route::middleware('permission:Show Notifications')->group(function () {
                    Route::post('/registerNotification', 'NotificationController@registerNotification');
                    Route::post('/markNotificationAsRead', 'NotificationController@markNotificationAsRead');
                    Route::post('/markAllAsRead', 'NotificationController@markAllAsRead');
                    Route::post('/deleteAllNotifications', 'NottificationController@deleteAllNotifications');
                    Route::post('/deleteNotification', 'NotificationController@deleteNotification');
                    Route::get('notification/{id}/details', 'NotificationController@notificationDetails');
                });
            });
        });

        Route::name('Localization')->group(function () {
            Route::middleware('permission:Language Show/Create')->group(function () {
                Route::get('/languages', 'LocalizationController@languages');
                Route::get('/language/create', 'LocalizationController@createLang');
                Route::post('/language/store', 'LocalizationController@storeLang');
            });

            Route::middleware('permission:Language Edit')->group(function () {
                Route::get('/language/{id}/edit', 'LocalizationController@editLang');
                Route::post('/language/update', 'LocalizationController@updateLang');
                Route::get('/language/isActive', 'LocalizationController@setIsActive');
                Route::post('/language/nextPrevPage', 'LocalizationController@nextPrevPage');
            });

            Route::post('/language/search', 'LocalizationController@search')->middleware('permission:Language Show/Create|Language Edit');
        });

        Route::name('FAQ')->group(function () {
            Route::middleware('permission:Language Show/Create')->group(function () {
                Route::get('/faq', 'FaqController@index');
                Route::get('/faq/create', 'FaqController@create');
                Route::get('/faq/{faq}/edit', 'FaqController@edit');
                Route::post('/faq/store', 'FaqController@store');
                Route::post('/faq/update', 'FaqController@update');
                Route::any('/faq/{id}/delete', 'FaqController@destroy');
                Route::post('/addFaqCategory', 'FaqController@addFaqCategory');
                Route::post('/getOldFaqCategories', 'FaqController@getOldFaqCategories');
                Route::post('/saveFaqQuestionAnswer', 'FaqController@saveFaqQuestionAnswer');
            });
        });

    });

    Route::group(['namespace' => 'Pdfs'], function () {
        Route::get('/print-pdf/{id}', 'PdfController@voucherBackend')->name('print.pdf')->middleware('permission:Show All Bookings');
        Route::get('/print-pdf-v2/{id}', 'PdfController@voucherBackendv2')->name('print.pdf.v2')->middleware('permission:Show All Bookings');
        Route::get('/print-invoice/{id}', 'PdfController@invoiceBackend')->name('print.invoice')->middleware('permission:Show All Bookings');
        Route::post('/multiple-tickets', 'PdfController@multipleTickets')->name('print.multipleTickets')->middleware('permission:Create Ticket');
        Route::post('/getUsableBarcodeCount', 'PdfController@getUsableBarcodeCount')->middleware('permission:Create Ticket');
        Route::get('/finance-pdf/{month}/{year}/{totalRate}/{companyID}/{isPlatform}/{commissionerRequest}', 'PdfController@financeInvoice')->middleware('permission:Finance Show/Download');
        Route::get('/print-voucher/{id}', 'PdfController@panelVoucher')->middleware('permission:Voucher Create/Download');
        Route::post('/multiple-tickets-on-index', 'PdfController@multipleTicketsOnIndex')->name('print.multipleTickets')->middleware('permission:Create Ticket');
    });

    Route::group(['namespace' => 'Comment'], function () {
        Route::middleware('permission:Comment Edit')->group(function () {
            Route::get('/changeCommentStatus', 'CommentController@setStatus');
            Route::any('/comments/{id}/delete', 'CommentController@destroy');
        });

        Route::get('/comments', 'CommentController@index')->middleware('permission:Show All Comments');
    });

    Route::group(['namespace' => 'Helpers'], function () {
        Route::post('/getRowsForDataTable', 'DataTableFunctions@getRowsForDataTable');
        Route::post('/pageIDForDataTable', 'DataTableFunctions@pageIDForDataTable');
        Route::post('/getAttractionsByCity', 'CommonFunctions@getAttractionsByCity')->middleware('permission:Product Create|Product Edit');
        Route::get('/barcodes/fillSearchableTicketType', 'DataTableFunctions@fillSearchableTicketType');
    });


    /*
     * Datatables
     **/
    Route::group([
        'namespace' => 'Datatables'
    ], function () {

        Route::post('/get-rows-for-bookings', 'AllBookingsDatatable@getRows');
    });

    //Availability Routes
    Route::get('availabilities', 'Admin\AvailabilityController@index')->name('availability.index')->middleware('permission:Show All Availabilities');
    Route::any('/availability/{id}/delete', 'Admin\AvailabilityController@destroy')->middleware('permission:Availability Delete');
    Route::post('/availability/getAvailabilities','Admin\AvailabilityController@getAvailabilities')->middleware('permission:Product Create|Option Create|Product Edit|Option Edit');
    Route::post('/expiredAvailabilities', 'Admin\AvailabilityController@expiredAvailabilities');
    Route::get('/av/create', 'Admin\AvailabilityController@avCreate')->middleware('permission:Option Create|Option Edit|Product Create|Product Edit|Availability Create');
    Route::post('/av/store', 'Admin\AvailabilityController@avStore')->middleware('permission:Option Create|Option Edit|Product Create|Product Edit|Availability Create');
    Route::get('/av/{id}/edit', 'Admin\AvailabilityController@avEdit')->middleware('permission:Option Edit|Product Edit|Availability Edit');
    Route::post('/av/getAvdates', 'Admin\AvailabilityController@getAvdates')->middleware('permission:Availability Edit|Availability Create|Option Edit|Option Create|Product Edit|Product Create');
    Route::post('/av/applyChanges', 'Admin\AvailabilityController@applyChanges')->middleware('permission:Availability Edit|Availability Create|Option Edit|Option Create|Product Edit|Product Create');
    Route::post('/av/getAvdatesToEditWithoutHour', 'Admin\AvailabilityController@getAvdatesToEditWithoutHour');
    Route::get('/av/getAvBookings', 'Admin\AvailabilityController@getAvBookings')->middleware('permission:Option Edit|Product Edit|Availability Edit');
    Route::post('/av/getAvOnGoing', 'Admin\AvailabilityController@getAvOnGoing')->middleware('permission:Option Edit|Product Edit|Availability Edit');

    Route::group(['name' => 'Temporary Functions','namespace' => 'Helpers'], function () {
        $c = 'TemporaryFunctions@';

        Route::get('/renamedSuleymanTours', $c.'renamedSuleymanTours');

        // Adds -{productID} to product and product translation urls
        Route::get('/replaceProductUrls', $c.'replaceProductUrls');
        Route::get('/replaceProductTranslationUrls', $c.'replaceProductTranslationUrls');

        // Migrates old product and product translation urls to old_product and old_product_translations tables
        Route::get('/migrateProductUrls', $c.'migrateProductUrls');
        Route::get('/migrateProductTranslationUrls', $c.'migrateProductTranslationUrls');
        Route::get('/supplierEmailHash', $c.'supplierMailHash');

        // Translate urls of already translated products
        Route::get('translateUrlFields', $c.'translateUrlFields');
    });

    Route::name('Mailer')->namespace('Admin')->group(function(){
        Route::get('/mailer', 'BulkMailController@index');
        Route::post('/get-booking-for-mail', 'BulkMailController@getBookingForMail');
        Route::post('/get-platforms', 'BulkMailController@getPlatforms');
        Route::get('/get-platforms/{ids}', 'BulkMailController@getPlatformsById');
        Route::post('/get-options', 'BulkMailController@getOptions');
        Route::post('/get-products', 'BulkMailController@getProducts');
        Route::get('/get-options/{ref}', 'BulkMailController@getOptionsByRef');
        Route::post('/send-mail', 'BulkMailController@sendMails');
    });

    // Changes XS Image Size
    Route::get('changeXS', 'Admin\GalleryController@changeXS');

    Route::get('/sendMailToUserWhenCart30MinutesHavePassed', 'Admin\CartController@sendMailToUserWhenCart30MinutesHavePassed');

    // Routes for switching to pariscitytoursfr related routes
    Route::get('/switchToPCT', 'Admin\ProductController@switchAll');

});


//Supplier Route Groups
Route::group(['domain' => 'supplier.'. env('ROUTE_VARIABLE', 'cityzore.com')], function () {
    //Supplier Login Routes
    Route::get('/login', 'Supplier\LoginController@showLoginForm')->name('supplier.login')->middleware('guest:supplier');
    Route::post('/login', 'Supplier\LoginController@login');
    Route::post('validateLogin', 'Auth\LoginController@validateLogin');

    // Password Reset Routes
    Route::get('password/reset', 'Auth\LoginController@resetPassword');
    Route::post('sendResetPasswordEmail', 'Auth\LoginController@sendResetPasswordEmail');
    Route::get('reset-password-link/{token}', 'Auth\LoginController@resetPasswordLinkForPanel');
    Route::post('sendNewPassword', 'Auth\LoginController@sendNewPassword');

    // R-Code Related Routes
    Route::get('/bookings-restaurant/{refCode}/{hash}', 'Admin\BookingController@bookingsRestaurant');
    Route::post('/saveRCodeAsRestaurant', 'Admin\BookingController@saveRCodeAsRestaurant');
});

Route::group(['domain' => 'supplier.'. env('ROUTE_VARIABLE'), 'middleware' => ['auth:supplier,subUser']], function() {
    Route::group(['namespace' => 'UserManagement'], function () {
        Route::middleware('permission:User Management')->group(function () {
            Route::get('/roles', 'RoleController@index');
            Route::get('/role/create', 'RoleController@create');
            Route::post('/role/store', 'RoleController@store');
            Route::get('/role/{id}/edit', 'RoleController@edit');
            Route::post('/role/{id}/update', 'RoleController@update');
            Route::any('/role/{id}/delete', 'RoleController@destroy');
        });
    });
    Route::group(['namespace' => 'Supplier'] ,function () {
        Route::middleware('permission:Supplier Create/Edit')->group(function () {
            $c = 'SupplierController@';

            Route::get('/', 'PanelController@supplierPanel')->name('supplierPanel');
            Route::post('/logout','LoginController@logout')->name('logout');
            Route::get('supplier', $c.'index');
            Route::get('supplier/create', $c.'create')->name('supplier.create');
            Route::post('supplier/create', $c.'store')->name('supplier.store');
            Route::get('supplier/{id}/edit', $c.'edit');
            Route::post('/supplier/{id}/update', $c.'update');
            Route::get('changeSupplierStatus', $c.'changeStatus')->name('setStatus');
            Route::any('/supplier/{id}/destroy', $c.'destroy')->name('supplier.destroy');
            Route::post('/supplier/supplierid', $c.'getOptions');
            Route::post('/supplier/removeOption', $c.'removeOption');
            Route::post('/supplier/sendRCode', $c.'selectOptionsForRCode');
            Route::get('/supplier/{id}/details', $c.'details');
            Route::post('/supplier/{id}/updatePaymentDetails', $c.'updatePaymentDetails');
            Route::post('/supplier/sendVerificationEmail', $c.'sendVerificationEmail');
            Route::post('/supplier/submitVerificationEmail', $c.'submitVerificationEmail');
            Route::post('/{id}/licenseSave', $c.'storeLicenseFiles');
            Route::post('/supplier/deleteLicense', $c.'deleteLicense');
            Route::post('/supplier/sendRCode', $c.'selectOptionsForRCode');
        });
        Route::get('/close-window', 'PanelController@closeWindow');
    });

    Route::group(['namespace' => 'UserManagement'],function () {
        Route::middleware('permission:User Management')->group(function () {
            $c = 'SubUserController@';

            Route::get('/subuser/create', ''.$c.'create');
            Route::post('/subuser/create', ''.$c.'store');
            Route::get('/subusers', ''.$c.'index');
            Route::get('/subuser/{id}/edit', ''.$c.'edit');
            Route::post('/subuser/{id}/update', ''.$c.'update');
            Route::any('/subuser/{id}/delete', ''.$c.'destroy');
        });
    });

    Route::namespace('Admin')->group(function () {
        Route::name('Product')->group(function () {
            Route::middleware('permission:Product Create')->group(function () {
                Route::get('/product/create', 'ProductController@create')->name('product.create');
                Route::post('/product/store', 'ProductController@store')->name('product.store');
            });

            Route::middleware('permission:Product Edit')->group(function () {
                Route::get('/product/{id}/edit', 'ProductController@edit');
                Route::post('/product/{id}/edit', 'ProductController@update')->name('product.update');
                Route::get('/changeProductDraftStatus', 'ProductController@changeDraftStatus')->name('setProductDraftStatus');
                Route::get('/changeProductPublishedStatus', 'ProductController@changePublishedStatus')->name('setProductPublishedStatus');
                Route::post('/product/orderImages', 'ProductController@orderImages');
            });

            Route::get('/product', 'ProductController@index')->middleware('permission:Show All Products|Product Create|Product Edit|Product Delete');
            Route::post('/product/{id}/delete', 'ProductController@destroy')->middleware('permission:Product Delete');
            Route::post('product/deletePhoto', 'ProductController@deletePhoto')->middleware('permission:Product Create|Product Edit');
            Route::get('/product/{id}/deleteDraft', 'ProductController@deleteDraft')->middleware('permission:Product Delete');;
            Route::post('product/setAsCoverPhoto', 'ProductController@setAsCoverPhoto')->middleware('permission:Product Create|Product Edit');
            Route::post('product/getImageGallery', 'ProductController@getImageGallery')->middleware('permission:Product Create|Product Edit');
            Route::post('product/setImagesForProduct', 'ProductController@setImagesForProduct')->middleware('permission:Product Create|Product Edit');
            Route::post('productDraft', 'ProductController@productDraft')->middleware('permission:Product Create|Product Edit');
            Route::post('updateProductStepByStep', 'ProductController@updateProductStepByStep')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/titleValidation', 'ProductController@titleValidation')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/isFilledAll', 'ProductController@isFilledAll')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/getOptions', 'ProductController@getOptions')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/create/getCities', 'ProductController@getCities')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/getProductImages', 'ProductController@getProductImages')->middleware('permission:Product Create|Product Edit');
            Route::post('/product/deleteProductFile', 'ProductController@deleteProductFile')->middleware('permission:Product Create|Product Edit');
            Route::get('/supplierPublished', 'ProductController@supplierPublished')->name('supplierPublished')->middleware('permission:Product Create|Product Edit');
            Route::get('/product-preview/{id}', 'ProductController@productPreview')->middleware('permission:Show All Products|Product Create|Product Edit|Product Delete');
            Route::post('/getProductGalleryData', 'ProductController@getProductGalleryData')->middleware('permission:Product Create|Product Edit');
        });

        Route::name('Option')->group(function () {
            Route::get('/option', 'OptionController@index')->middleware('permission:Show All Options');
            Route::get('/option/{id}/edit', 'OptionController@edit')->name('option.edit')->middleware('permission:Option Edit');
            Route::post('/option/{id}/update', 'OptionController@update')->name('option.update')->middleware('permission:Option Edit');
            Route::any('/option/{id}/delete', 'OptionController@destroy')->middleware('permission:Option Delete');
            Route::get('/option/create', 'OptionController@create')->middleware('permission:Option Create');
            Route::post('/option/{id}/detach', 'OptionController@optionDetach')->middleware('permission:Product Edit');
            Route::post('/option/optionStore', 'OptionController@optionStore')->middleware('permission:Product Create');
            Route::post('/option/optionUpdate', 'OptionController@optionUpdate')->middleware('permission:Product Create');
            Route::post('/option/store', 'OptionController@store')->middleware('permission:Option Create');
            Route::post('/option/preview', 'OptionController@preview')->middleware('permission:Product Create|Product Edit');
            Route::get('/option/changeOptionPublishedStatus', 'OptionController@changeOptionPublishedStatus')->middleware('permission:Option Edit');
            Route::post('/option/getOptionPart', 'OptionController@getOptionPart')->middleware('permission:Product Create|Product Edit');
            Route::post('/option/saveOptionPart', 'OptionController@saveOptionPart')->middleware('permission:Product Create|Product Edit');
            Route::post('/option/ajax', 'OptionController@ajax')->middleware('permission:Product Create|Product Edit');
        });

        Route::name('Voucher')->group(function () {
            Route::middleware('permission:Voucher Template Access')->group(function () {
                Route::resource('voucher-template', 'VoucherTemplateController');
            });

            Route::middleware('permission:Voucher Create')->group(function () {
                Route::get('/voucher/create', 'VoucherController@create');
                Route::post('/voucher/store', 'VoucherController@store');
            });

            Route::middleware('permission:Voucher Edit')->group(function () {
                Route::get('/voucher/{id}/edit', 'VoucherController@edit');
                Route::post('/voucher/{id}/update', 'VoucherController@update');
            });

            Route::get('/vouchers', 'VoucherController@index')->middleware('permission:Show All Vouchers');
            Route::post('/voucher/{id}/delete', 'VoucherController@destroy')->middleware('permission:Voucher Delete');
            Route::post('/voucher/getOptions', 'VoucherController@getOptions')->middleware('permission:Voucher Create|Voucher Edit');
        });

        Route::name('Pricing')->group(function () {
            Route::middleware('permission:Pricing Create')->group(function () {
                Route::get('pricing/create', 'PricingController@create');
                Route::post('/pricing/store', 'PricingController@store');
            });

            Route::get('pricing/{id}/edit', 'PricingController@edit')->middleware('permission:Pricing Edit');
            Route::post('pricing/update', 'PricingController@update')->middleware('permission:Pricing Edit');
            Route::get('pricings', 'PricingController@index')->middleware('permission:Show All Pricings');
            Route::any('pricing/{id}/delete', 'PricingController@destroy')->middleware('permission:Pricing Delete');
        });

        Route::name('Gallery')->group(function () {
            Route::middleware('permission:Photo Upload')->group(function () {
                Route::post('/gallery/uploadPhoto/{which}', 'GalleryController@uploadPhoto');
                Route::get('/gallery/create', 'GalleryController@create');
            });

            Route::get('/gallery', 'GalleryController@index')->middleware('permission:Show All Photos|Photo Upload|Photo Edit|Photo Delete');
            Route::get('/gallery/{id}/delete', 'GalleryController@delete')->middleware('permission:Photo Delete');
            Route::post('/gallery/updateImage', 'GalleryController@update')->middleware('permission:Photo Edit');
        });

        Route::name('Finance')->group(function () {
            Route::middleware('permission:Finance Show/Download')->group(function () {
                Route::get('/finance', 'FinanceController@financeAdmin');
                Route::get('/finance/exportToExcel', 'FinanceController@exportToExcel');
                Route::post('/finance/get-bookings', 'FinanceController@getBookings');

            });
        });

        Route::name('Booking')->group(function () {
            Route::get('/bookings', 'BookingController@index')->name('bookings.index')->middleware('permission:Show All Bookings');

            Route::middleware('permission:Booking Create/Edit')->group(function () {
                Route::get('/booking/{id}/edit', 'BookingController@edit')->name('bookings.edit');
                Route::post('/booking/{id}/update', 'BookingController@update')->name('bookings.update');
                Route::get('/booking/create', 'BookingController@create')->name('bookings.create');
                Route::post('/booking/store', 'BookingController@store')->name('booking.store');
                Route::post('/booking/productSelect', 'BookingController@productSelect');
                Route::post('/booking/bookingTime', 'BookingController@bookingTime');
                Route::get('/booking/changeStatus/{id}', 'BookingController@changeStatus');
                Route::get('/booking/addComment/{id}', 'BookingController@addComment');
                Route::post('/booking/storeComment', 'BookingController@storeComment');
                Route::get('/booking/specialRefCode/{id}', 'BookingController@specialRefCode');
                Route::post('/booking/storeSpecialRefCode', 'BookingController@storeSpecialRefCode');
                Route::post('/saveRCode', 'BookingController@saveRCode');
                Route::post('/booking/ajax', 'BookingController@ajax');
                Route::get('/booking/exportToExcel', 'BookingController@exportToExcel');


            });
            Route::post('/booking/optionSelect', 'BookingController@optionSelect');
            Route::post('/booking/optionSelectMultiple', 'BookingController@optionSelectMultiple');

        });

        Route::name('Special Offers')->group(function () {
            Route::middleware('permission:Special Offer Create/Edit')->group(function () {
                Route::get('/special-offers/create', 'SpecialOffersController@create');
                Route::post('/special-offers/getDateTimes', 'SpecialOffersController@getDateTimes');
                Route::post('/special-offers/saveChanges', 'SpecialOffersController@saveChanges');
                Route::post('/special-offers/getAvailabilityType', 'SpecialOffersController@getAvailabilityType');
                Route::post('/special-offers/changeSpecialOfferStatus', 'SpecialOffersController@changeSpecialOfferStatus');
                Route::post('/special-offers/deleteOldSpecialOffer', 'SpecialOffersController@deleteOldSpecialOffer');
                Route::post('/special-offers/edit', 'SpecialOffersController@edit');
            });

            Route::get('/special-offers', 'SpecialOffersController@index')->middleware('permission:Show All Special Offers');
        });

        Route::name('Barcode')->group(function () {
            Route::middleware('permission:Barcode Create')->group(function () {
                Route::get('/barcodes/create', 'BarcodeController@create');
                Route::post('/barcodes/store', 'BarcodeController@store');
                Route::post('/barcodes-excel-import', 'BarcodeController@importExcel');
                Route::post('/barcodes-excel-import-for-opera', 'BarcodeController@importOperaExcel');
                Route::post('/barcodes-excel-import-for-picasso', 'BarcodeController@importPicassoExcel');
                Route::post('/barcodes-excel-import-for-grevin', 'BarcodeController@importGrevinExcel');
                Route::post('/barcodes-pdf-import-for-triomphe', 'BarcodeController@importPdfForTriomphe');
                Route::post('/barcodes-pdf-import-for-sainte', 'BarcodeController@importPdfForSainte');
                Route::post('/barcodes-excel-import-for-rodin', 'BarcodeController@importRodinExcel');
                Route::post('/barcodes-excel-import-for-montparnasse-adult', 'BarcodeController@importMontparnasseAdultExcel');
                Route::post('/barcodes-excel-import-for-montparnasse-infant', 'BarcodeController@importMontparnasseInfantExcel');
            });

            Route::get('/barcodes', 'BarcodeController@index')->middleware('permission:Show All Barcodes');
            Route::delete('/barcodes/remove', 'BarcodeController@destroy');
            Route::middleware('permission:Create Ticket')->group(function () {
                Route::post('/barcode/createTicket/{id}', 'BarcodeController@createTicket')->name('createTicket');
                Route::get('/barcode/multiple-ticket', 'BarcodeController@createMultipleTicket');
            });
        });

        Route::name('External Payment')->group(function () {
            Route::get('/external-payment', 'PaymentController@externalPayment')->name('externalPayment')->middleware('permission:Show All External Payments');
            Route::post('/external-payment/edit', 'PaymentController@externalPaymentEdit')->middleware('permission:Show All External Payments');

            Route::middleware('permission:External Payment Create')->group(function () {
                Route::get('/external-payment/create', 'PaymentController@externalPaymentCreate');
                Route::post('/storePaymentLink', 'PaymentController@storePaymentLink');
                Route::post('resendEmail', 'PaymentController@resendEmail');
            });
        });

        Route::name('Config')->group(function () {
            Route::get('/config', 'ConfigController@config')->middleware('permission:Show Config');
            Route::post('/config/{id}/update', 'ConfigController@saveConfig')->middleware('permission:Config Edit');
        });

        Route::get('/single-pdf/{id}', 'BarcodeController@createTicket')->name('single.pdf');
    });

    Route::namespace('Pdfs')->group(function () {
        Route::get('/print-pdf/{id}', 'PdfController@voucherBackend')->name('print.pdf');
        Route::get('/print-pdf-v2/{id}', 'PdfController@voucherBackendv2')->name('print.pdf.v2');
        Route::get('/print-invoice/{id}', 'PdfController@invoiceBackend')->name('print.invoice');
        Route::post('/multiple-tickets', 'PdfController@multipleTickets')->name('print.multipleTickets');
        Route::post('/getUsableBarcodeCount', 'PdfController@getUsableBarcodeCount');
        Route::get('/finance-pdf/{month}/{year}/{totalRate}/{companyID}/{commissionerRequest}', 'PdfController@financeInvoice');
        Route::get('/print-voucher/{id}', 'PdfController@panelVoucher');
        Route::post('/multiple-tickets-on-index', 'PdfController@multipleTicketsOnIndex')->name('print.multipleTickets');
    });

    Route::namespace('Comment')->group(function () {
        Route::get('/comments', 'CommentController@index')->middleware('permission:Show All Comments');

        Route::middleware('permission:Comment Edit')->group(function () {
            Route::any('/comments/{id}/delete', 'CommentController@destroy');
        });
    });

    Route::namespace('Helpers')->group(function () {
        Route::post('/getRowsForDataTable', 'DataTableFunctions@getRowsForDataTable');
        Route::post('/pageIDForDataTable', 'DataTableFunctions@pageIDForDataTable');
        Route::post('/getAttractionsByCity', 'CommonFunctions@getAttractionsByCity');
    });

    //Availability Routes
    Route::get('availabilities', 'Admin\AvailabilityController@index')->name('availability.index')->middleware('permission:Show All Availabilities');
    Route::any('/availability/{id}/delete', 'Admin\AvailabilityController@destroy')->middleware('permission:Availability Delete');
    Route::post('/availability/getAvailabilities','Admin\AvailabilityController@getAvailabilities')->middleware('permission:Product Create|Option Create|Product Edit|Option Edit');
    Route::get('/av/create', 'Admin\AvailabilityController@avCreate')->middleware('permission:Option Create|Option Edit|Product Create|Product Edit|Availability Create');
    Route::post('/av/store', 'Admin\AvailabilityController@avStore')->middleware('permission:Option Create|Option Edit|Product Create|Product Edit|Availability Create');
    Route::get('/av/{id}/edit', 'Admin\AvailabilityController@avEdit')->middleware('permission:Option Edit|Product Edit|Availability Edit');
    Route::post('/av/getAvdates', 'Admin\AvailabilityController@getAvdates')->middleware('permission:Availability Edit|Availability Create|Option Edit|Option Create|Product Edit|Product Create');
    Route::post('/av/applyChanges', 'Admin\AvailabilityController@applyChanges')->middleware('permission:Availability Edit|Availability Create|Option Edit|Option Create|Product Edit|Product Create');
    Route::post('/av/getAvdatesToEditWithoutHour', 'Admin\AvailabilityController@getAvdatesToEditWithoutHour')->middleware('permission:Availability Edit|Availability Create|Option Edit|Option Create|Product Edit|Product Create');
    Route::get('/av/getAvBookings', 'Admin\AvailabilityController@getAvBookings')->middleware('permission:Option Edit|Product Edit|Availability Edit');
    Route::post('/av/getAvOnGoing', 'Admin\AvailabilityController@getAvOnGoing')->middleware('permission:Option Edit|Product Edit|Availability Edit');

});


Route::redirect('/paris/eiffel-tower-2nd-floor-priority-access-entry-ticket', '/paris/paris-eiffel-tower-2nd-floor-direct-access-entry-ticket', 301);
Route::redirect('/es/paris/tour-en-bus-turistico-conciergerie-sainte-chapelle-crucero-por-el-sena', '/es/paris/big-bus-hop-on-hop-off-tour-conciergerie-sainte-chapelle-seine-river-cruise', 301);
Route::redirect('/paris/paris-eiffel-tower-visit-with-summit-louvre-and-seine-river-cruise', '/paris/paris-eiffel-tower-visit-with-summit-with-guide-louvre-and-seine-river-cruise', 301);
Route::redirect('/paris/skip-the-ticket-line-conciergerie-admission-tickets', '/paris/skip-the-ticket-line-conciergerie-admission-tickets-123', 301);
Route::redirect('/attraction/big-bus', '/attraction/big-bus-hop-on-hop-off-paris', 301);
Route::redirect('/tr/attraction/big-bus', '/tr/populer-yerler/cift-katli-otobus', 301);
Route::redirect('/es/attraction/big-bus', '/es/atraccion/Gran-autobús', 301);
Route::redirect('/de/attraction/big-bus', '/de/attraktion/Großer-Bus', 301);
Route::redirect('/tr/attraction/disneyland', '/tr/populer-yerler/disneyland', 301);
Route::redirect('/it/attraction/disneyland', '/it/attrazione/disneyland', 301);
Route::redirect('/ru/attraction/disneyland', '/ru/dostoprimechatelnost/диснейленд', 301);
Route::redirect('/es/attraction/disneyland', '/es/atraccion/disneylandia', 301);
Route::redirect('/de/attraction/disneyland', '/de/attraktion/disneyland', 301);
Route::redirect('/tr/attraction/cabaret-shows', '/tr/populer-yerler/Kabare-Gösterileri', 301);
Route::redirect('/ru/attraction/cabaret-shows', '/ru/dostoprimechatelnost/кабаре-шоу', 301);
Route::redirect('/es/attraction/cabaret-shows', '/es/atraccion/espectáculos-de-cabaret', 301);
Route::redirect('/de/attraction/cabaret-shows', '/de/attraktion/Kabarett-Shows', 301);
Route::redirect('/es/terms-and-conditions', '/es/terminos-y-condiciones', 301);
Route::redirect('/de/terms-and-conditions', '/de/geschaeftsbedingungen', 301);
Route::redirect('/tr/privacy-policy', '/tr/gizlilik-politikasi', 301);
Route::redirect('/pt/privacy-policy', '/pt/politica-de-privacidade', 301);
Route::redirect('/es/privacy-policy', '/es/politica-de-privacidad', 301);
Route::redirect('/fr/privacy-policy', '/fr/politique-de-confidentialite', 301);
Route::redirect('/ru/privacy-policy', '/ru/politika-konfidencialnosti', 301);
Route::redirect('/it/frequently-asked-questions', '/it/domande-frequenti', 301);
Route::redirect('/tr/frequently-asked-questions', '/tr/sikca-sorulan-sorular', 301);
Route::redirect('/es/frequently-asked-questions', '/es/preguntas-frecuentes', 301);
Route::redirect('/de/frequently-asked-questions', '/de/haeufig-gestellte-fragen', 301);
Route::redirect('/pt/attraction/eiffel-tower', '/pt/atracao/Torre-Eiffel', 301);
Route::redirect('/es/attraction/eiffel-tower', '/es/atraccion/Torre-Eiffel', 301);
Route::redirect('/attraction/eiffel-tower-tour', '/attraction/eiffel-tower-tickets', 301);
Route::redirect('/it/about-us', '/it/riguardo-a-noi', 301);
Route::redirect('/fr/about-us', '/fr/a-propos-de-nous', 301);
Route::redirect('/pt/about-us', '/pt/sobre-nos', 301);
Route::redirect('/tr/about-us', '/tr/hakkimizda', 301);
Route::redirect('/de/about-us', '/de/ueber-uns', 301);
Route::redirect('/ru/about-us', '/ru/o-nas', 301);
Route::redirect('/supplier-register', '/become-a-supplier', 301);
Route::redirect('/it/become-a-supplier', '/it/diventare-a-fornitore', 301);
Route::redirect('/es/attraction/seine-river-cruise', '/es/atraccion/Crucero-por-el-Sena', 301);
Route::redirect('/it/attraction/seine-river-cruise', '/it/attrazione/Crociera-sulla-Senna', 301);
Route::redirect('/tr/attraction/seine-river-cruise', '/tr/populer-yerler/sen-nehri-gezisi', 301);
Route::redirect('/de/attraction/seine-river-cruise', '/de/attraktion/Seine-Fluss-Kreuzfahrt', 301);
Route::redirect('/ru/attraction/seine-river-cruise', '/ru/dostoprimechatelnost/круиз-по-реке-сене', 301);
Route::redirect('/fr/attraction/seine-river-cruise', '/fr/attraction/Croisière-sur-la-Seine', 301);
Route::redirect('/pt/attraction/seine-river-cruise', '/pt/atracao/Cruzeiro-no-Rio-Sena', 301);
Route::redirect('/tr/attraction/museums-exhibitions', '/tr/populer-yerler/müzeler-sergiler', 301);
Route::redirect('/pt/attraction/museums-exhibitions', '/pt/atracao/museus-exposições', 301);
Route::redirect('/fr/attraction/museums-exhibitions', '/fr/attraction/musées-expositions', 301);
Route::redirect('/de/attraction/museums-exhibitions', '/de/attraktion/Museen-Ausstellungen', 301);
Route::redirect('/es/attraction/museums-exhibitions', '/es/atraccion/museos-exposiciones', 301);
Route::redirect('/ru/attraction/museums-exhibitions', '/ru/dostoprimechatelnost/музеи-выставки', 301);
Route::redirect('/it/attraction/versailles-palace', '/it/attrazione/versailles-palazzo', 301);
Route::redirect('/ru/attraction/versailles-palace', '/ru/dostoprimechatelnost/версальский-дворец', 301);
Route::redirect('/pt/attraction/versailles-palace', '/pt/atracao/Palácio-de-Versalhes', 301);
Route::redirect('/es/attraction/versailles-palace', '/es/atraccion/Palacio-de-Versalles', 301);
Route::redirect('/tr/attraction/versailles-palace', '/tr/populer-yerler/versay-sarayı', 301);
Route::redirect('/de/attraction/versailles-palace', '/de/attraktion/Schloss-Versailles', 301);
Route::redirect('/fr/attraction/versailles-palace', '/fr/attraction/Château-de-Versailles', 301);
Route::redirect('/es/special-offers', '/es/ofertas-especiales', 301);
Route::redirect('/de/special-offers', '/de/spezialangebot', 301);
Route::redirect('/pt/special-offers', '/pt/ofertas-especiais', 301);
Route::redirect('/ru/special-offers', '/ru/specialnye-predlozheniya', 301);
Route::redirect('/tr/special-offers', '/tr/ozel-teklifler', 301);
Route::redirect('/fr/special-offers', '/fr/offres-speciales', 301);
Route::redirect('/es/become-a-commissioner', '/es/convertirse-en-un-comisionado', 301);
Route::redirect('/es/become-a-commissioner', '/it/diventare-a-commissario', 301);
Route::redirect('/pt/become-a-commissioner', '/pt/tornar-se-um-comissario', 301);
Route::redirect('/fr/terms-and-conditions', '/fr/termes-et-conditions', 301);
Route::redirect('/ru/terms-and-conditions', '/ru/politika-konfidencialnosti', 301);
Route::redirect('/tr/terms-and-conditions', '/tr/sartlar-ve-kosullar', 301);
Route::redirect('/pt/terms-and-conditions', '/pt/termos-e-condicoes', 301);
Route::redirect('/it/terms-and-conditions', '/it/termini-e-condizioni', 301);
Route::redirect('/ru/attraction/eiffel-tower', '/ru/dostoprimechatelnost/эйфелева-башня', 301);
Route::redirect('/de/attraction/eiffel-tower', '/de/attraktion/Eiffel-Turm', 301);
Route::redirect('/fr/attraction/eiffel-tower', '/fr/attraction/tour-eiffel', 301);
Route::redirect('/it/attraction/eiffel-tower', '/it/attrazione/Torre-Eiffel', 301);
Route::redirect('/tr/attraction/eiffel-tower', '/tr/populer-yerler/eyfel-kulesi', 301);
Route::redirect('/tr/attraction/louvre-museum', '/tr/populer-yerler/Louvre-müzesi', 301);
Route::redirect('/it/attraction/louvre-museum', '/it/attrazione/Museo-del-Louvre', 301);
Route::redirect('/ru/attraction/louvre-museum', '/ru/dostoprimechatelnost/лувр-музей', 301);
Route::redirect('/ru/become-a-commissioner', '/ru/stat-komissionerom', 301);
Route::redirect('/de/contact', '/de/kontakt', 301);
Route::redirect('/es/contact', '/es/contacto', 301);
Route::redirect('/ru/contact', '/ru/kontakt', 301);
Route::redirect('/tr/paris/paris-seine-river-cruise-dinner-and-cabaret-show-at-lido-de-paris', '/tr/paris/paris-sen-nehri-tekne-turu-aksam-yemegi-ve-lido-kabare-sov-17', 301);
Route::redirect('/pt/paris/paris-moulin-rouge-show-e-cruzeiro-no-rio-sena', '/pt/paris/paris-moulin-rouge-show-e-cruzeiro-no-rio-sena-130', 301);
Route::redirect('/de/paris/eiffelturm-ueberspringen-sie-den-ticket-line-summit-eingang-champagner-erlebnis-optionale-flusskreuzfahrt', '/de/paris/eiffelturm-ueberspringen-sie-den-ticket-line-summit-eingang-champagner-erlebnis-optionale-flusskreuzfahrt-225', 301);
Route::redirect('/ru/paris/parizh-uzhin-kruiz', '/ru/paris/parizh-uzhin-kruiz-93', 301);
Route::redirect('/ru/paris/parizh-shou-kabare-mulen-ruzh', '/ru/paris/parizh-shou-kabare-mulen-ruzh-111', 301);
Route::redirect('/tr/paris/sanzelize-bulvari-nda-fransiz-aksam-yemegi-ve-sen-nehri-gezisi', '/tr/paris/sanzelize-bulvari-nda-fransiz-aksam-yemegi-ve-sen-nehri-gezisi-211', 301);
Route::redirect('/de/paris/eiffelturm-optionaler-zugang-zum-gipfelgeschoss-pantheon-flusskreuzfahrt-auf-der-seine', '/de/paris/eiffelturm-optionaler-zugang-zum-gipfelgeschoss-pantheon-flusskreuzfahrt-auf-der-seine-202', 301);
Route::redirect('/ru/paris/parizh-propustit-biletnuyu-liniyu-posetite-eyfelevu-bashnyu-po-zhelaniyu-dostup-na-sammit-kkruiz-po-sene-i-shou-paradis-latin-cabaret', '/ru/paris/parizh-propustit-liniyu-biletov-eyfeleva-bashnya-po-zhelaniyu-posetite-sammit-kruiz-po-reke-sena-i-latinskoe-kabare-shou-paradis-117', 301);




// This part for ajax routes only

Route::group(['domain' => env('ROUTE_VARIABLE_WWW', 'www.cityzore.com')], function () {
    Route::get('/mail/r/{code}', 'Admin\MailController@isRead');
    Route::get('getLocales', 'Helpers\CommonFunctions@getLocales');
    Route::get('setLocale', 'Helpers\CommonFunctions@setLocale');
    Route::get('getCurrencies', 'Helpers\CommonFunctions@getCurrencies');
    Route::post('setCurrencyCode', 'Helpers\CommonFunctions@setCurrencyCode');
    Route::get('/uniqueIDForCart', 'Helpers\CommonFunctions@uniqueIDForCart');
    Route::post('/getCartItems', 'Helpers\CommonFunctions@getCartItems');
    Route::post('validateLogin', 'Auth\LoginController@validateLogin');
    Route::post('searchVarious', 'HomeController@searchVarious');
    Route::match(['GET', 'POST'],'/all-products/checkAvailability', 'Product\ProductController@checkAvailability');
    Route::post('/useCoupon', 'Admin\CouponController@useCoupon');
    Route::post('/deleteUsedCoupon', 'Admin\CouponController@deleteUsedCoupon');
    Route::post('/checkout/newValuesForCart', 'Admin\CartController@newValuesForCart');
    Route::post('shareCart', 'Admin\CartController@shareCart');
    Route::post('sendResetPasswordEmail', 'Auth\LoginController@sendResetPasswordEmail');
    Route::post('sendNewPassword', 'Auth\LoginController@sendNewPassword');
    Route::post('/deleteLicense', 'User\CommissionerController@deleteLicenses');
    Route::get('/checkout', 'Booking\BookingController@checkOut');
    Route::post('/cartUpdate', 'Admin\CartController@update');
    Route::post('/addToCart', 'Admin\CartController@addToCart');
    Route::post('getPricingAndSpecialOffer', 'Product\ProductController@getPricingAndSpecialOffer');
    Route::post('getAvailableDatesNew', 'Product\ProductController@getAvailableDatesNew');
    Route::post('getAvailableTimesNew', 'Product\ProductController@getAvailableTimesNew');
    Route::post('/booking-successful', 'Booking\BookingController@bookingSuccessful');
    Route::post('/booking-successful-for-shared-cart', 'Booking\BookingController@bookingSuccessfulForSharedCart');
    Route::post('/booking-failed', 'Booking\BookingController@bookingFailed');
    Route::post('/external-payment-successful', 'Booking\BookingController@externalPaymentSuccessful');
    Route::post('/external-payment-failed', 'Booking\BookingController@externalPaymentFailed');
    Route::post('/addRemoveWishlist', 'Product\ProductController@addRemoveWishlist');
    Route::post('/getCities', 'Helpers\CommonFunctions@getCities');
    Route::post('/booking/record', 'Booking\BookingController@bookingRecord');
    Route::post('/cancel-by-user', 'CheckBookingController@cancelBookingByUser');

    Route::get('/check-booking', 'CheckBookingController@checkBookingIndex');
    Route::post('/check-booking', 'CheckBookingController@checkBooking');
    Route::post('/cancel-booking', 'CheckBookingController@cancelBooking');
    Route::post('/send-confirmation-code', 'CheckBookingController@sendConfirmationCode');
    Route::post('/reset-bkn-session', 'CheckBookingController@resetBKNSession');

    Route::get('/option/getOptionIncFore', 'Admin\OptionController@getOptionIncFore');
});

// Frontend Routes with Lang

Route::group(['prefix' => '{lang}', 'where' => ['lang' => 'tr|fr|ru|es|de|it|pt|nl'], 'domain' => env('ROUTE_VARIABLE_WWW', 'www.cityzore.com'), 'middleware' => ['redirectLocale']], function ($lang) {

    // Auth Routes
    Auth::routes();
    //

    // Routes that don't need localization
    Route::get('/', 'HomeController@index');
    Route::get('/home-attraction-tours', 'HomeController@homeAttractionTours');
    Route::get('/cities', 'HomeController@cities');
    Route::get('/mobile-apk', 'HomeController@mobileAPK');
    Route::get('/s', 'Product\ProductController@searchSpecific');
    Route::get('/blog', 'Blog\BlogController@getBlog');
    Route::get('testBigBusBooking', 'Helpers\BigBusRelated@testBigBusBooking');
    Route::get('testBigBusCancelBooking', 'Helpers\BigBusRelated@testBigBusCancelBooking');

    //

    // Sitemap routes
    Route::get('/sitemap', 'HomeController@siteMap')->name('siteMap');
    Route::get('/sitemap-en', 'HomeController@siteMapEn')->name('siteMap-en');
    Route::get('/sitemap-tr', 'HomeController@siteMapTr')->name('siteMap-tr');
    Route::get('/sitemap-fr', 'HomeController@siteMapFr')->name('siteMap-fr');
    Route::get('/sitemap-ru', 'HomeController@siteMapRu')->name('siteMap-ru');
    Route::get('/sitemap-es', 'HomeController@siteMapEs')->name('siteMap-es');
    Route::get('/sitemap-de', 'HomeController@siteMapDe')->name('siteMap-de');
    Route::get('/sitemap-it', 'HomeController@siteMapIt')->name('siteMap-it');
    Route::get('/sitemap-pt', 'HomeController@siteMapPt')->name('siteMap-pt');
    Route::get('/sitemap-nl', 'HomeController@siteMapNl')->name('siteMap-nl');
    //

    Route::post('/bookit', 'Booking\BookingController@bookIt');
    // One static parameter routes
    Route::any('/{param}', 'RouteController@routeLocalization');
    //

    // Two static parameter routes
    Route::post('supplier/create', 'Supplier\SupplierController@store');
    Route::get('password/reset', 'Auth\LoginController@resetPassword');
    Route::get('login/google', 'Auth\LoginController@redirectToProviderGoogle');
    Route::get('login/facebook', 'Auth\LoginController@redirectToProviderFacebook');
    //

    // Three static parameter routes
    Route::get('login/facebook/callback', 'Auth\LoginController@handleProviderCallbackFacebook');
    Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallbackGoogle');
    //

    // One static one dynamic routes
    Route::get('/deleteItemFromCart/{id}', 'Admin\CartController@deleteItemFromCart');
    Route::get('payment-link/{link}', 'Admin\PaymentController@externalPaymentDetails');
    Route::get('/print-pdf-frontend/{id}', 'Pdfs\PdfController@voucher');
    Route::get('/print-invoice-frontend/{id}', 'Pdfs\PdfController@invoice');
    Route::get('reset-password-link/{token}', 'Auth\LoginController@resetPasswordLink');
    Route::get('/voucher/{token}', 'Pdfs\PdfController@printVoucherByToken');
    Route::get('/verification/{id}', 'Auth\RegisterController@userVerification');
    Route::get('/downloadProductFile/{file}', 'Product\ProductController@downloadProductFile');
    //Route::get('/newattractionpage/{slug}', 'Product\ProductController@paginateAttractionPageOtherLanguage');
    //

    // Routes with dynamic parameters
    Route::get('/profile/{id}/edit', 'User\UserController@editFrontend');
    Route::post('/profile/{id}/update', 'User\UserController@updateFrontend');
    Route::get('payment-details/{id}/edit', 'User\CommissionerController@paymentDetails');
    Route::post('payment-details/{id}/update', 'User\CommissionerController@storePaymentDetails');
    Route::get('license-files/{id}/edit', 'User\CommissionerController@licenseFiles');
    Route::post('license-files/{id}/update', 'User\CommissionerController@storeLicenseFiles');
    Route::get('/print-invoice-commissioner/{id}/{totalCommission}', 'Pdfs\PdfController@commissionerInvoice');
    Route::get('/print-invoice-commissioner-bymonth/{dateParam}', 'Pdfs\PdfController@commissionerInvoiceByMonth');
    Route::get('/blog/{category}/{slug}', 'Blog\BlogController@getBlogPost');


    //

    // Routes with one static one dynamic parameters
    Route::get('/{static}/{dynamic}', 'RouteController@routeLocalizationForAttraction');

    // Please do not write any route below this.
    //
    //
    Route::get('{location}/{slug}', 'Product\ProductController@getProduct');
    //
    //
    //

});

// Frontend Routes w/out Lang (english)

Route::group(['domain' => env('ROUTE_VARIABLE_WWW', 'www.cityzore.com'), 'middleware' => ['redirectLocale']], function () {

    // Auth Routes
    Auth::routes();
    //

    // Routes that don't need localization
    Route::get('/', 'HomeController@index');
    Route::get('/s', 'Product\ProductController@searchSpecific');
    Route::get('/cities', 'HomeController@cities');
    Route::get('/mobile-apk', 'HomeController@mobileAPK');
    Route::get('/blog', 'Blog\BlogController@getBlog');
    Route::get('testBigBusBooking', 'Helpers\BigBusRelated@testBigBusBooking');
    Route::get('testBigBusCancelBooking', 'Helpers\BigBusRelated@testBigBusCancelBooking');
    Route::get('/successful-register', 'Auth\RegisterController@successfulRegister');
    Route::get('/404', 'Admin\MailController@error404')->name('mail.404');
    Route::post('/contact404', 'Admin\MailController@contact404')->name('mail.contact404');


    //

    // Sitemap Routes
    Route::get('/sitemap', 'HomeController@siteMap')->name('siteMap');
    Route::get('/sitemap-en', 'HomeController@siteMapEn')->name('siteMap-en');
    Route::get('/sitemap-tr', 'HomeController@siteMapTr')->name('siteMap-tr');
    Route::get('/sitemap-fr', 'HomeController@siteMapFr')->name('siteMap-fr');
    Route::get('/sitemap-ru', 'HomeController@siteMapRu')->name('siteMap-ru');
    Route::get('/sitemap-es', 'HomeController@siteMapEs')->name('siteMap-es');
    Route::get('/sitemap-de', 'HomeController@siteMapDe')->name('siteMap-de');
    Route::get('/sitemap-it', 'HomeController@siteMapIt')->name('siteMap-it');
    Route::get('/sitemap-pt', 'HomeController@siteMapPt')->name('siteMap-pt');
    Route::get('/sitemap-nl', 'HomeController@siteMapNl')->name('siteMap-nl');
    //


    //Route::get('/newattractionpage/{slug}', 'Product\ProductController@paginateAttractionPage');

    // One static parameter routes
    Route::any('/{param}', 'RouteController@routeLocalizationForEnglish');
    //

    // Two static parameter routes
    Route::post('supplier/create', 'Supplier\SupplierController@store');
    Route::get('password/reset', 'Auth\LoginController@resetPassword');
    Route::get('login/google', 'Auth\LoginController@redirectToProviderGoogle');
    Route::get('login/facebook', 'Auth\LoginController@redirectToProviderFacebook');
    //

    // Three static parameter routes
    Route::get('login/facebook/callback', 'Auth\LoginController@handleProviderCallbackFacebook');
    Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallbackGoogle');
    //

    // One static one dynamic routes
    Route::get('/attraction/{slug}', 'Helpers\UrlRelated@redirectAttraction');
    Route::get('/deleteItemFromCart/{id}', 'Helpers\UrlRelated@redirectDeleteItemFromCart');
    Route::get('payment-link/{link}', 'Helpers\UrlRelated@redirectExternalPaymentDetails');
    Route::get('/print-pdf-frontend/{id}', 'Helpers\UrlRelated@redirectVoucher');
    Route::get('/print-invoice-frontend/{id}', 'Helpers\UrlRelated@redirectInvoice');
    Route::get('/voucher/{token}', 'Helpers\UrlRelated@redirectPrintVoucherByToken');
    Route::get('/verification/{id}', 'Auth\RegisterController@userVerification');
    Route::get('reset-password-link/{token}', 'Helpers\UrlRelated@redirectResetPasswordLink');
    Route::get('/downloadProductFile/{file}', 'Product\ProductController@downloadProductFile');
    //

    // Routes with dynamic parameters
    Route::get('/profile/{id}/edit', 'Helpers\UrlRelated@redirectEditFrontend');
    Route::post('/profile/{id}/update', 'Helpers\UrlRelated@redirectUpdateFrontend');
    Route::get('payment-details/{id}/edit', 'Helpers\UrlRelated@redirectPaymentDetails');
    Route::post('payment-details/{id}/update', 'Helpers\UrlRelated@redirectStorePaymentDetails');
    Route::get('license-files/{id}/edit', 'Helpers\UrlRelated@redirectLicenseFiles');
    Route::post('license-files/{id}/update', 'Helpers\UrlRelated@redirectStoreLicenseFiles');
    Route::get('/print-invoice-commissioner/{id}/{totalCommission}', 'Helpers\UrlRelated@redirectCommissionerInvoice');
    Route::get('/print-invoice-commissioner-bymonth/{dateParam}', 'Pdfs\PdfController@commissionerInvoiceByMonth');
    Route::get('/blog/{category}/{slug}', 'Helpers\UrlRelated@redirectBlogPost');
    //

    // Please do not write any route below this.
    // This is for redirection for old product routes without lang code.
    //

    Route::get('{location}/{slug}', 'Helpers\UrlRelated@redirectProduct');
    //
    //
    //

});

// Routes for Authentication
Auth::routes();

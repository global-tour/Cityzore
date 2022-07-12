@include('frontend-partials.head', ['page' => 'check-booking'])

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$requestURL = Request::url();
$loginUrl = $langCodeForUrl . '/login';
$registerUrl = $langCodeForUrl . '/register';
if (strpos($requestURL, 'admin') !== false || strpos($requestURL, 'supplier') !== false) {
    $loginUrl = url('/login');
    $registerUrl = url('/register');
}
?>

<div id="cover-spin"></div>
<div style="margin-top: 50px" class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="rows tb-space pad-top-o pad-bot-redu" style="padding-top: 0px;padding-bottom: 0px;">
                <div class="container">
                    <a href="{{url('/')}}">
                        <img src="{{asset('img/paris-city-tours-logo.png')}}" style="width: 200px; height: 60px;">
                    </a>
                </div>
            </div>
            <div class="card bg-light">
                <div class="card-header"
                     style="background-image: linear-gradient(to right, rgba(255,255,255, 0.72) 0 100%), url({{asset('/img/eiffel.jpg')}}); background-size: cover; background-position-y: 65%; height: 125px;">
                    <div
                        style="height: inherit; display: flex; justify-content: center; align-items: center; font-size: 25px;">{{__('checkBooking')}}</div>
                </div>
                <div class="card-body mt-3">
                    <input type="hidden" id="checkLevel" value="1"/>
                    <div class="form-group row">
                        <div class="col-md-4 text-md-right">
                            <label for="bookingRefCode" class="col-form-label ">{{__('bookingReferenceCode')}}<br/><span
                                    class="text-secondary">{{__('example')}}: BKN12345</span></label>
                        </div>
                        <div class="col-md-6">
                            <input style="height: 25px" id="bookingRefCode" type="text" class="form-control"
                                   name="bookingRefCode" required autocomplete="name" autofocus>
                        </div>
                    </div>
                    <div id="conCodeBlock" style="display: none;">
                        <div class="form-group text-center m-0 p-0">
                            <span id="conCodeInformation"></span>
                        </div>
                        <div class="form-group row">
                            <label for="bookingRefCode"
                                   class="col-md-4 col-form-label text-md-right">{{__('confirmationCode')}}</label>
                            <div class="col-md-6">
                                <input style="height: 25px" id="confirmationCode" type="text" class="form-control"
                                       name="confirmationCode" required autocomplete="name" autofocus>
                            </div>
                        </div>
                    </div>
                    <div class="form-group d-md-flex justify-content-center">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <button id="checkBookingButton" class="btn btn-block btn-success">{{__('check')}}</button>
                        </div>
                        <div class="col-md-9 col-xl-4" style="display: none;">
                            <button id="tryAnotherCodeButton"
                                    class="btn btn-block btn-secondary">{{__('tryAnotherReferenceCode')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="bookingDetailsCard" class="card p-4 text-center" style="display: none;">
                <p class="text-success" style="font-size: 25px;">{{__('bookingDetails')}}</p>
                <p id="bookingTitle" class="m-0 p-0" style="font-size: 15px;"></p>
                <p class="m-0 p-0" style="font-size: 12px;"><b>{{__('date')}}:</b> <span id="bookingDateTime"></span>
                </p>
                <p class="m-0 p-0 mb-3" style="font-size: 12px;"><b>{{__('amount')}}:</b> <i
                        class="{{session()->get('currencyIcon')}}"></i> <span id="totalPrice"></span> x <span
                        id="bookingItems"></span></p>

                <p id="vouInvInformation" class="m-0 p-0" style="font-size: 12px;"></p>

                <div id="extra-files" style="display: none;flex-direction: column; width: 100%; margin-top: 20px">
                    <b>{{ __('downloadFiles') }}</b>
                </div>

                <a id="voucherUrl" href="" target="_blank">
                    <button type="submit" class="btn btn-xs btn-primary btn-block"
                            style="margin-bottom: 3px; color: black; background-color: transparent; border-color: #4CAF50; font-size: 10px; font-weight: bold;">
                        {{__('downloadYourVoucher')}}
                    </button>
                </a>
                <a id="invoiceUrl" href="" target="_blank">
                    <button type="submit" class="btn btn-xs btn-primary btn-block"
                            style="margin-bottom: 3px; color: black; background-color: transparent; border-color: #4CAF50; font-size: 10px; font-weight: bold;">
                        {{__('downloadYourInvoice')}}
                    </button>
                </a>
                <div class="d-flex justify-content-end mt-4">
                    <button id="cancelBookingButton" class="btn btn-outline-danger"
                            style="width: 150px;">{{__('cancelBooking')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('frontend-partials.general-scripts', ['page' => 'check-booking'])

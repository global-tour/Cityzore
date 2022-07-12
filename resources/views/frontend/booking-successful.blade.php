@include('frontend-partials.head', ['page' => 'booking-successful'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$apiRelated = new \App\Http\Controllers\Helpers\ApiRelated();
$cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
?>

<div class="container" style="margin-top: 5%;">
    <div class="alert" role="alert" style="text-align: center;text-align: center;border: 1px dotted #c62346;background: rgba(33, 255, 5, 0.05);">
        <span style="font-size: 50px;color: #c62346;padding-top: 20px;height: 65px;width: 65px;border: 1px solid #c62346;border-radius: 50%;display: inline-block;">&#10003;</span>
        <p style="font-size: 25px;padding: 3%;">{{__('bookingSuccessful')}}</p>
        @foreach($bookings as $b)
            <?php $product=\App\Product::where('referenceCode','=',$b->productRefCode)->first();
            $productTranslation = $product->translations;?>


            <a href="@if($productTranslation) {{url($langCodeForUrl.'/'.$productTranslation->url)}} @else {{url($langCodeForUrl.'/'.$product->url)}} @endif"><p style="font-size: 18px;">{{\App\Product::where('referenceCode','=',$b->productRefCode)->first()->title}}</p></a>

            <p style="font-size: 18px;">{{\App\Option::where('referenceCode','=',$b->optionRefCode)->first()->title}}</p>
            <p>{{__('date')}}: {{$b->date}} {{json_decode($b->hour,true)[0]['hour']}}</p>
            <p>{{__('amount')}}: <i class="{{session()->get('currencyIcon')}}"></i>{{$b->totalPrice}} x {{$apiRelated->getCategoryAndCountInfo($b->bookingItems)}}</p>
            <p>{{__('bookingSuccessful1')}}</p>

           @if($b->status == 4 && false)
            <a href="javascript:void(0)"><button type="submit" class="btn btn-xs btn-danger active btn-block" style="margin-left: 25%;width:50%;margin-bottom: 3px; color: #fff; border-color: #8852E4; font-size: 9px; font-weight: bold;">{{__('We will inform you after the booking is confirmed For Voucher')}}</button></a>
            @else
                @if($checkIfBookingHasTicketType == 0 || (auth()->check() && auth()->user()->ccEmail == "contact@parisviptrips.com"))
                    <a href="{{url($langCodeForUrl.'/print-pdf-frontend/'.$cryptRelated->encrypt($b->id))}}" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="margin-left: 25%;width:50%;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">{{__('downloadYourVoucher')}}</button></a>
                @endif
            @endif


            <a href="{{url($langCodeForUrl.'/print-invoice-frontend/'.$cryptRelated->encrypt($b->id))}}" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="margin-left: 25%;width:50%;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">{{__('downloadYourInvoice')}}</button></a>
            <a href="{{url('/')}}"><button class="btn btn-success" style="margin-top: 3%;background-color:#c62346;border-color: #c62346;">
                {{__('backToHome')}}</button></a>
        @endforeach
        <div class="container">
            <div class="col-lg-4 col-sm-12 col-xs-12">
                <img class="mobile-app-image" src="{{asset('img/global-tickets-mobile.png')}}" alt="Paris City Tours" style="width: 400px;">
            </div>
            <div class="col-lg-8 col-sm-12 col-xs-12" style="margin-top: 5%;">
                <h2>Download Our App and Track Your Reservation</h2>
                <ul style="list-style-type: none;">
                    <li><span style="color: green">&#10004;&nbsp</span>Download your tickets</li>
                    <li><span style="color: green">&#10004;&nbsp</span>See the meeting point and attraction address</li>
                    <li><span style="color: green">&#10004;&nbsp</span>Find the guide with live location</li>
                    <li><span style="color: green">&#10004;&nbsp</span>Live Help</li>
                </ul>
                <p>Scan the QR code for download our application:</p>
                <img src="{{asset('img/mobile-app-global-tickets.png')}}" alt="Paris City Tours" style="width: 100px;">
                <p style="margin-top:3%;">or click the download button:</p>
                <a href="https://play.google.com/store/apps/details?id=com.globaltickets" target="blank">Download Global Tickets for Android</a><br>
                <a href="https://apps.apple.com/tr/app/global-tickets/id1571098800?l" target="blank">Download Global Tickets for IOS</a>
            </div>
        </div>
    </div>
</div>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'booking-successful'])


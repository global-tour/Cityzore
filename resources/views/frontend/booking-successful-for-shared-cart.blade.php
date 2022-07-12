@include('frontend-partials.head', ['page' => 'booking-successful'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$cryptRelated = new \App\Http\Controllers\Helpers\CryptRelated();
?>

<div class="container" style="margin-top: 5%;">
    <div class="alert" role="alert" style="text-align: center;text-align: center;border: 1px dotted #c62346;background: rgba(33, 255, 5, 0.05);">
        <span style="font-size: 50px;color: #c62346;padding-top: 20px;height: 65px;width: 65px;border: 1px solid #c62346;border-radius: 50%;display: inline-block;">&#10003;</span>
        <p style="font-size: 25px;padding: 3%;">{{__('bookingSuccessful')}}</p>
        @foreach($bookings as $b)
            <p style="font-size: 18px;">{{\App\Option::where('referenceCode','=',$b->optionRefCode)->first()->title}}</p>
            <p>{{__('date')}}: {{$b->date}} {{json_decode($b->hour,true)[0]['hour']}}</p>
            <p>{{__('amount')}}: <i class="{{session()->get('currencyIcon')}}"></i>{{$b->totalPrice}} x {{(new App\Http\Controllers\Helpers\ApiRelated)->getCategoryAndCountInfo($b->bookingItems)}}</p>
            <p>{{__('bookingSuccessful1')}}</p>
            @if($checkIfBookingHasTicketType == 0 || (auth()->check() && auth()->user()->ccEmail == "contact@parisviptrips.com"))
                <a href="{{url($langCodeForUrl.'/print-pdf-frontend/'.$cryptRelated->encrypt($b->id))}}" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="margin-left: 25%;width:50%;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">{{__('downloadYourVoucher')}}</button></a>
            @endif
            <a href="{{url($langCodeForUrl.'/print-invoice-frontend/'.$cryptRelated->encrypt($b->id))}}" target="_blank"><button type="submit" class="btn btn-xs btn-primary btn-block" style="margin-left: 25%;width:50%;margin-bottom: 3px; color: black; background-color: transparent; border-color: #8852E4; font-size: 10px; font-weight: bold;">{{__('downloadYourInvoice')}}</button></a>
            <a href="{{url('/')}}"><button class="btn btn-success" style="margin-top: 3%;background-color:#c62346;border-color: #c62346;">{{__('backToHome')}}</button></a>
        @endforeach
    </div>
</div>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'booking-successful'])


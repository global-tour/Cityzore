@include('frontend-partials.head', ['page' => 'booking-confirmation'])
@include('frontend-partials.header')

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<section>
    <div class="spe-title col-md-12">
        <h2>{{__('bookingConfirmation')}}</h2>
        <div class="title-line">
            <div class="tl-1"></div>
            <div class="tl-2"></div>
            <div class="tl-3"></div>
        </div>
    </div>
    <div class="container py-5" style="margin-top: 50px; margin-bottom: 50px;">
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div id="nav-tab-card" class="">
                    <div class="col-md-12 col-sm-12 col-lg-12">
                     <form method="POST" action="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('bookingConfirmation'))}}">
                         @csrf
                         <input name="confirmationCode" placeholder="{{__('enterConfirmationCode')}}" id="confirmationCode" type="text" class="form-control">
                         <button id="confirmationButton" type="submit" class="btn btn-primary">{{__('send')}}</button>
                     </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'booking-confirmation'])

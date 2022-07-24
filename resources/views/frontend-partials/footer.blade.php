<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<section>
    <div class="rows">
        <div class="footer" style="background-color: #edf0f3;background-position: center;height: 340px;background-repeat: no-repeat;background-size: cover;padding-top: 3%;">
            <div class="container">
                <div class="foot-sec2">
                    <div class="row">
                        <div class="col-sm-3 foot-spec foot-com">
                            <h4 style="color: #253d52ad;">Tours & Tickets</h4>
                            <p><i class="icon-cz-payment"></i>{!! __('securePayment') !!}</p>
                            <p>Ways You Can Pay</p>
                            <img src="https://cdn.getyourguide.com/tf/assets/static/payment-methods/mastercard.svg" style="width: 50px;">
                            <img src="https://cdn.getyourguide.com/tf/assets/static/payment-methods/visa.svg" style="width: 50px;">

                        </div>
                        <div class="col-sm-3 foot-spec foot-com hidden-xs">
                            <p><a class="link-btn" style="width: 100%;text-align: center;" href="https://supplier.cityzore.com" target="_blank">{{__('supplierLogin')}}</a></p>
                            <p><a class="link-btn" style="width: 100%;text-align: center;" href="https://cityzore.com/{{$langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('become-a-supplier')}}" target="_blank">{{__('supplierRegister')}}</a></p>
                            <p><a class="link-btn" style="width: 100%;text-align: center;" href="https://cityzore.com/{{$langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('become-a-commissioner')}}" target="_blank">Commissioner Register</a></p>
                            <br>
                        </div>
                        <div class="col-sm-3 col-md-3  col-xs-12 foot-spec foot-com">
                            <h4 style="color: #253d52ad;padding-left: 3%;">{!! __('supportAndHelp') !!}</h4>
                            <ul class="two-columns" style="padding-left: 3%;">
                                <li> <a style="font-weight: 700; color: #253d52ad;" href="{{url('/')}}">{{__('home')}}</a> </li>
                                <li> <a style="font-weight: 700; color: #253d52ad;" href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('about-us'))}}" id="aboutUs">{{__('aboutUs')}}</a> </li>
                                <li> <a style="font-weight: 700; color: #253d52ad;" href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('terms-and-conditions'))}}">{{__('termsAndConditions')}}</a> </li>
                                <li> <a style="font-weight: 700; color: #253d52ad;" href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('privacy-policy'))}}">{{__('privacyPolicy')}}</a> </li>
                                <li> <a style="font-weight: 700; color: #253d52ad;" href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('frequently-asked-questions'))}}">{{__('frequentlyAskedQuestions')}}</a> </li>
                            </ul>
                        </div>
                        <div class="col-sm-3  col-xs-12 foot-social foot-spec foot-com">
                            <h4 style="color: #253d52ad;">{!! __('followUs') !!}</h4>
                            <ul>
                                <li><a href="https://www.facebook.com/pariscitytours.fr/" target="_blank"><i style="border: 1px solid;" class="icon-cz-facebook" aria-hidden="true"></i></a> </li>
                                <li><a href="https://www.instagram.com/pariscitytours.fr/" target="_blank"><i style="border: 1px solid;" class="icon-cz-instagram" aria-hidden="true" ></i></a> </li>
                                <li><a href="https://twitter.com/Parisviptrips" target="_blank"><i style="border: 1px solid;" class="icon-cz-twitter" aria-hidden="true"></i></a> </li>
                                <li><a href="https://www.linkedin.com/company/global-tours-and-tickets/about/" target="_blank"><i style="border: 1px solid;" class="icon-cz-linkedin" aria-hidden="true"></i></a> </li>
                            </ul>
                            <p style="margin-top: 20%;"> <i class="icon-cz-whatsapp" style="font-size: 22px;"></i> <span class="highlighted">+33184208801</span> </p>
                            <p> <a style="font-weight: 700; color: #253d52ad;" href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('contact'))}}"><span style="color: #253d52ad;font-size: 15px;">{{__('contact')}}</span></a> </p>
                        </div>
                    </div>
                    <br>
                    <span style="font-size:17px;color: #616161;">Partners:</span>
                    <img style="width:55px;margin-right: 15px;" src="https://images.squarespace-cdn.com/content/5bfd497d36099b8ca5df2039/1543328094903-FUI62X18106FS0I7F4AR/GYG_Expressive_Logo_01_Red_RGB_HR.png?content-type=image%2Fpng">
                    <img style="width: 95px;margin-right: 15px" src="https://static.tacdn.com/img2/brand_refresh/special/pride_month_Tripadvisor_lockup_horizontal_secondary.svg">
                    <img style="width: 55px;margin-right: 15px;" src="https://www.pngfind.com/pngs/m/260-2601938_viator-logo-viator-tripadvisor-hd-png-download.png">
                    <img style="width: 70px;margin-right: 15px;" src="https://www.expedia.com/_dms/header/logo.svg?locale=en_US&siteid=1&2">
                    <img style="width: 45px;margin-right: 15px;padding-top: 7px;" src="https://hohobassets.isango.com/phoenix/images/isango-cs.png">
                    <img style="width: 100px;margin-right: 15px;" src="https://staticv4.imgix.net/images/logo-musement-horizontal.png">
                    <img style="width: 50px;margin-right: 15px;" src="{{ asset('img/partners/kisspng-klook-travel-technology-limited-logo-discounts-and-5b09705fd039f2.8363984015273452478529.png') }}">
                    <br>
                    <p>Cityzore is administrated by Global Tours and Tickets Company</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="rows copy">
        <div class="container">
            <p style="font-weight: 700; color: #253d52ad;">Copyright © <?php echo date("Y"); ?> Cityzore. {{__('allRightsReserved')}}</p>
        </div>
    </div>
</section>
<section>
    <div class="icon-float">
        <ul>
            <li><a style="background-color: #1ebea5" href="https://wa.me/33184208801" class="sh"><i style="font-size: 16px" class="icon-cz-whatsapp"></i></a></li>
            <li><a href="https://www.facebook.com/pariscitytours.fr/" class="fb1" target="_blank"><i class="icon-cz-facebook" aria-hidden="true"></i></a> </li>
            <li><a href="https://twitter.com/Parisviptrips" class="tw1" target="_blank"><i class="icon-cz-twitter" aria-hidden="true"></i></a> </li>
            <li><a href="https://www.linkedin.com/company/global-tours-and-tickets/about/" class="li1" target="_blank"><i class="icon-cz-linkedin" aria-hidden="true"></i></a> </li>
            <li><a href="https://www.instagram.com/pariscitytours.fr/" class="inst" target="_blank"><i class="icon-cz-instagram" aria-hidden="true"></i></a> </li>
            <li><a href="mailto:contact@parisviptrips.com?subject=I want to make a reservation&body=I want to make a reservation to Eiffel Tower Summit Entrance!" class="sh1" target="_blank"><i class="icon-cz-mail" aria-hidden="true"></i></a> </li>
        </ul>
    </div>
    <script>
        window.addEventListener('load', function() {
            jQuery('#bookNow, .product-right-book').click(function() {
                gtag('event', 'conversion', {
                    'send_to': 'AW-1009135479/imJnCLrms_wCEPfemOED'
                });
            })
            if (document.location.href.indexOf('/booking-successfull') != -1) {
                gtag('event', 'conversion', {
                    'send_to': 'AW-1009135479/lt76CM-33PwCEPfemOED'
                });
            }
        });

    </script>
</section>
<div class="mobile-search-overlay">
    <div class="overlay-header">
        <h4>SEARCH</h4>
        <a class="overlay-close" href="javascript:;">X</a>
    </div>
    <div class="overlay-body">
        <div class="mobile-search-area">
            <form action="{{url($langCodeForUrl.'/s')}}" method="GET" id="searchSpecificForm" style="height: 100%">
                <input type="text" name="q" placeholder="{{ __('searchLocationsAttractions')}}" class="search-field">
                <a class="clear-input">X</a>
                <i class="glyphicon glyphicon-search" onclick="event.preventDefault(); document.getElementById('searchSpecificForm').submit();"></i>
                <div class="mobile-suggestions-container"></div>
            </form>
        </div>
    </div>
</div>
<div class="whatsapp-button-area">
    <a href="https://wa.me/+33184208801" target="_blank">
        <i class="icon-cz-whatsapp"></i>
    </a>
</div>

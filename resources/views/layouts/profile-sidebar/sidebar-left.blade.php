<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<div class="col-lg-3">
    <div class="db-l-1">
        <ul>
            <li><img src="{{('img/db-profile.jpg')}}" alt="" /></li>
            <li>{{auth()->user()->name}} {{auth()->user()->surname}}</li>
        </ul>
    </div>
    <div class="db-l-2">
        <ul>
            <li>
                <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('my-profile'))}}">
                    <img src="{{asset('/img/icon/dbl1.png')}}" alt="" /> {{__('allBookings')}}
                </a>
            </li>
            @if(!is_null(auth()->user()->commission))
                <li>
                    <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('commissions'))}}">
                        <img src="{{asset('/img/icon/dbl1.png')}}" alt="" /> {{__('commissions')}}
                    </a>
                </li>
            @endif
            <li>
                <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('profile-details'))}}">
                    <img src="{{asset('/img/icon/dbl6.png')}}" alt="" /> {{__('myProfile')}}
                </a>
            </li>
            @if(!is_null(auth()->user()->commission))
            <li>
                <a href="{{url($langCodeForUrl.'/payment-details/'.auth()->user()->id).'/edit'}}">
                    <img src="{{asset('/img/icon/dbl1.png')}}" alt="" /> {{__('paymentDetails')}}
                </a>
            </li>
            <li>
                <a href="{{url($langCodeForUrl.'/license-files/'.auth()->user()->id.'/edit')}}">
                    <img src="{{asset('/img/icon/dbl1.png')}}" alt="" /> {{__('licenseFiles')}}
                </a>
            </li>
            @endif
            <li>
                <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('my-coupons'))}}">
                    <img src="{{asset('/img/icon/dbl1.png')}}" alt="" /> {{__('myCoupons')}}
                </a>
            </li>
            <li>
                <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('my-wishlist'))}}">
                    <img src="{{asset('/img/icon/dbl1.png')}}" alt="" /> {{__('myWishlist')}}
                </a>
            </li>
        </ul>
    </div>
</div>

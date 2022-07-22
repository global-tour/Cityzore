<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$currentCurrency = !is_null(session()->get('currencyCode')) ? session()->get('currencyCode') : 2 ;
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$langs = \App\Language::where('isActive', 1)->get();
$currencies = \App\Currency::where('isActive', 1)->get();
$selectedCurrency = $currencies->where('id', $currentCurrency)->first();
?>


<section>
    <span id="expiredTime" style="display: none">{{__('cartExpired')}}</span>
    {{--    <div class="hidden-lg hidden-md col-xs-12 col-sm-12">--}}
    {{--        <a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('check-booking'))}}"--}}
    {{--           style="color: #337ab7;">{{__('checkYourBooking')}}</a>--}}
    {{--    </div>--}}
    <div class="top-logo" data-spy="affix" data-offset-top="250">
        <div class="container">
            <div class="header-row">

                <div class="wed-logo">
                    <a id="parisCityTours" href="{{url('/')}}"><img
                            src="{{asset('img/paris-city-tours-logo.webp')}}" alt="Paris City Tours"/>
                    </a>
                </div>

                @if(!request()->is('/', $langs->pluck('code')->toArray()))
                    <form method="GET" class="tourz-search-form tour-form-two"
                          action="{{url($langCodeForUrl.'/s')}}"
                          autocomplete="off">
                        <div class="home-banner-search">
                            <div class="search-bar header-search-area">
                                <input type="text" placeholder="{{__('searchLocationsAttractions')}}" name="q"
                                       class="search-field light-border" id="searchInput">
                                <button type="submit" class="search-button">SEARCH</button>
                                <div class="suggestions-container"></div>
                            </div>
                            <div class="mobile-search-bar">
                                <a href="javascript:;" class="mobile-search-field" id="mobile-search-button">
                                    <i class="glyphicon glyphicon-search"></i>
                                    Search
                                </a>
                            </div>
                        </div>
                    </form>
                @endif

                <div class="main-menu">
                    <ul>
                        <input type="hidden" id="sessionLocale" value="{{session()->get('userLanguage')}}">
                        <li class="languageLi2">
                            <div class="header-dropdownlang">
                                <button class="header-dropbtn fromCenter">{{ $langs->where('code', $langCode)->first()->name  }} ▼</button>
                                <div class="header-dropdown-content">
                                    @foreach($langs as $lang)
                                        @if($lang->code != $langCode)
                                            <span style="display: none;" class="languageCodes" data-lang-code="{{ $lang->code }}">
                                                <img alt="{{ $lang->displayName }}" style="width: 25px; height: 18px; margin-right: 5px;" src="/img/flag/{{ $lang->code }}.svg">
                                                {{ $lang->displayName }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </li>

                        @if(strpos(url()->current(), $commonFunctions->getRouteLocalization('credit-card-details-for-shared-cart')) == false)
                            <li class="currencyLi2">
                                <input type="hidden" id="sessionCurrency"
                                       value="{{ $currentCurrency }}">
                                <div class="header-dropdowncur">
                                    <button class="header-dropbtn fromCenter">{{ $selectedCurrency->currency }} <i class="{{ $selectedCurrency->iconClass }}"></i> ▼</button>
                                    <div class="header-dropdown-content">
                                        @foreach($currencies as $currency)
                                            @if($currency->id != $currentCurrency)
                                                <span style="display: none;" class="currencyCodes" data-cur-code="{{ $currency->id }}">
                                                    {{ $currency->currency }}<i class="{{ $currency->iconClass }}"></i>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </li>
                        @endif

                        <li class="about-menu">
                            <a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('cart'))}}">
                                <i class="icon-cz-cart" style="font-size: 17px"></i>
                                Cart
                                <div class="circle cartCount"  style="display: none"></div>
                            </a>
                            <div class="mm-pos">
                                <div class="about-mm m-menu">
                                    <div class="m-menu-inn">
                                        <div id="cartTableDiv" class="mm1-com-cart mm1-s1">
                                            @include('dynamic-components.header-cart-table')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        @if(strpos(url()->current(), $commonFunctions->getRouteLocalization('credit-card-details-for-shared-cart')) == false)
                            @if(!auth()->user())
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#myModal"  class="signin-button">{{__('signin')}}</a>
                                </li>
                                <li><a href="{{url($langCodeForUrl.'/register')}}"  class="signup-button">{{__('signup')}}</a>


                            @else
                                <li class="custom-dp">
                                    <a href="#" class="custom-btn">
                                        {{__('welcome')}}, {{auth()->user()->name}}
                                        &#9660;
                                    </a>


                                    <ul class="custom-dp-content">
                                        <li>
                                            <a href="{{url($langCodeForUrl.'/'. (new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('my-profile'))}}">
                                                {{__('profile')}}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{url($langCodeForUrl.'/'. (new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('check-booking'))}}">
                                                {{__('checkBooking')}}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{url($langCodeForUrl.'/'. (new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('my-wishlist'))}}">
                                                {{__('myWishlist')}}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ url($langCodeForUrl.'/logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                {{ __('logout') }}
                                            </a>
                                        </li>
                                        <form id="logout-form" action="{{ url($langCodeForUrl.'/logout') }}"
                                              method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </ul>

                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
                <div class="mobile-menu">
                    <div class="hamburger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="mobile-menu-content">
                        <ul>
                            <li class="hasSubmenu">

                                <button type="button" class="mst-dropdown-button">
                                    <span>{{ $langs->where('code', $langCode)->first()->name  }}</span>
                                    <em class="icon-cz-angle-up"></em>
                                </button>

                                <div class="mst-dropdown-menu">
                                    @foreach($langs as $lang)
                                        @if($lang->code != $langCode)
                                            <a class="languageCodes" data-lang-code="{{ $lang->code }}">
                                                <img alt="{{ $lang->displayName }}" style="width: 25px; height: 18px; margin-right: 5px;" src="/img/flag/{{ $lang->code }}.svg">
                                                {{ $lang->displayName }}
                                            </a>
                                        @endif()
                                    @endforeach
                                </div>

                            </li>

                            <li class="hasSubmenu">

                                <button type="button" class="mst-dropdown-button">
                                    <span>{{ $selectedCurrency->currency }}</span>
                                    <em class="icon-cz-angle-up"></em>
                                </button>

                                <div class="mst-dropdown-menu">
                                    @foreach($currencies as $currency)
                                        @if($currency->id != $currentCurrency)
                                            <a class="currencyCodes" data-cur-code="{{ $currency->id }}">
                                                    {{ $currency->currency }}<i class="{{ $currency->iconClass }}"></i>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </li>

                            <li>
                                <a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('cart'))}}">
                                    {{ __('cartLabel') }}
                                    <span class="cartCount" style="display: none"></span>
                                </a>
                            </li>
                            @if(strpos(url()->current(), $commonFunctions->getRouteLocalization('credit-card-details-for-shared-cart')) == false)
                                @if(!auth()->user())
                                    <li>
                                        <a href="#" data-toggle="modal" data-target="#myModal">
                                            {{__('signin')}}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{url($langCodeForUrl.'/register')}}">{{__('signup')}}</a>
                                    </li>
                                @else
                                    <li class="hasSubmenu">
                                        <button type="button" class="mst-dropdown-button">
                                            <span>{{__('welcome')}}, {{auth()->user()->name}}</span>
                                            <em class="icon-cz-angle-up"></em>
                                        </button>

                                        <div class="mst-dropdown-menu">
                                            <a href="{{url($langCodeForUrl.'/'. (new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('my-profile'))}}">
                                                {{__('profile')}}
                                            </a>
                                            <a href="{{url($langCodeForUrl.'/'. (new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('check-booking'))}}">
                                                {{__('checkBooking')}}
                                            </a>
                                            <a href="{{url($langCodeForUrl.'/'. (new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('my-wishlist'))}}">
                                                {{__('myWishlist')}}
                                            </a>
                                            <a href="{{ url($langCodeForUrl.'/logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                {{ __('logout') }}
                                            </a>
                                        </div>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="loginModalForm" method="POST" action="{{ url($langCodeForUrl.'/login') }}">
                    @csrf
                    <input hidden type="radio" checked name="guard" id="guard" value="web">

                    <div class="tr-regi-form">
                        <h4>{{__('signin')}}</h4>
                        <div style="display: none;" id="loginModalFormAlertDiv" class="alert alert-danger" role="alert">

                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input style="height: 5rem!important; border-radius: 0!important;" id="email" type="email"
                                       class="form-control" name="email" value="{{ old('email') }}" required
                                       autocomplete="email" autofocus placeholder="{{__('email')}}">
                                <label>{{__('email')}}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input style="height: 5rem!important; border-radius: 0!important;" id="password"
                                       type="password" class="form-control" name="password" required
                                       autocomplete="current-password" placeholder="{{__('password')}}">
                                <label>{{__('password')}}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button id="loginSubmitButton" type="submit" class="btn btn-primary btn-block">
                                    {{__('login')}}
                                </button>
                            </div>
                        </div>

                        <a class="btn btn-link"
                           href="{{ url($langCodeForUrl.'/password/reset') }}">
                            {{__('forgotYourPassword')}}
                        </a>
                        <br><a style="box-shadow: none;font-size: 11px;" href="{{url($langCodeForUrl.'/register')}}"
                               class="btn btn-link">{{__('register')}}</a>
                        <div class="soc-login">
                            <ul>
                                <li></li>
                                <li><a style="background: #DB4437!important;"
                                       href="{{url($langCodeForUrl.'/login/google')}}"><i class="fa fa-google-plus gp1"></i>
                                        Google</a></li>
                                <li></li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

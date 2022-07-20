<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$langs = \App\Language::where('isActive', 1)->get()->pluck('code')->toArray();
?>


<section>
    <span id="expiredTime" style="display: none">{{__('cartExpired')}}</span>
    <div class="ed-top">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="ed-com-t1-left">
                        <ul>
                            <li class="hidden-xs"><a href="#">contact@cityzore.com</a></li>
                            <li class="hidden-xs"><a href="#">{{__('phone')}}: +33184208801 </a></li>
                            <li><input type="hidden" id="sessionLocale" value="{{session()->get('userLanguage')}}"></li>
                            <li class="languageLi">

                            </li>
                            @if(strpos(url()->current(), $commonFunctions->getRouteLocalization('credit-card-details-for-shared-cart')) == false)
                                <li><input type="hidden" id="sessionCurrency" value="{{session()->get('currencyCode')}}"></li>
                                <li class="currencyLi">

                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="ed-com-t1-right">
                        <ul>
                            @if(strpos(url()->current(), $commonFunctions->getRouteLocalization('credit-card-details-for-shared-cart')) == false)
                                @if(!auth()->user())
                                    <li>
                                        <a href="#"  id="myBtn">{{__('signin')}}</a>
                                    </li>
                                    <li><a href="{{url($langCodeForUrl.'/register')}}">{{__('signup')}}</a>
                                    </li>
                                    <li class="hidden-xs hidden-sm">
                                        <a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('check-booking'))}}" style="color: #337ab7;">{{__('checkYourBooking')}}</a>
                                    </li>
                                    <li><form id="loginModalForm" method="POST" action="{{ url($langCodeForUrl.'/login') }}">
                                            @csrf
                                            <input hidden type="radio" checked name="guard" id="guard" value="web" >
                                            <div id="myModal" class="modal">
                                                <div class="modal-content">
                                                    <span class="close">&times;</span>
                                                    <section>
                                                        <div class="tr-regi-form">
                                                            <h4>{{__('signin')}}</h4>
                                                            <div style="display: none;" id="loginModalFormAlertDiv" class="alert alert-danger" role="alert">

                                                            </div>
                                                            <div class="row">
                                                                <div class="input-field col s12">
                                                                    <input style="height: 5rem!important; border-radius: 0!important;" id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{__('email')}}">
                                                                    <label>{{__('email')}}</label>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="input-field col s12">
                                                                    <input style="height: 5rem!important; border-radius: 0!important;" id="password" type="password" class="form-control" name="password" required autocomplete="current-password" placeholder="{{__('password')}}">
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
                                                            <a style="box-shadow: none;font-size: 11px;margin-top: 15px;" class="btn btn-link" href="{{ url($langCodeForUrl.'/password/reset') }}">
                                                                {{__('forgotYourPassword')}}
                                                            </a>
                                                            <br><a style="box-shadow: none;font-size: 11px;" href="{{url($langCodeForUrl.'/register')}}" class="btn btn-link">{{__('register')}}</a>
                                                            <div class="soc-login">
                                                                <ul>
                                                                    <li></li>
                                                                    <li><a style="background: #DB4437!important;" href="{{url($langCodeForUrl.'/login/google')}}"><i class="fa fa-google-plus gp1"></i> Google</a> </li>
                                                                    <li></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </div>
                                            </div>
                                        </form></li>
                                @else
                                    <li>
                                        <a class="waves-effect dropdown-button top-user-pro" href="#" data-activates="top-menu" style="height: 28px;margin-top: 1px;line-height: 17px;" id="myBtn">{{__('welcome')}}, {{auth()->user()->name}}<i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                        <ul id="top-menu" class="dropdown-content top-menu-sty" style="width: 169px; position: absolute; top: 0px; left: -37.6719px; opacity: 1; display: none;">
                                            <li><a href="{{url($langCodeForUrl.'/'. (new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('my-profile'))}}" style="color: #272525; background: transparent;"><i class="fa fa-user" aria-hidden="true"></i> {{__('profile')}}</a></li>
                                            <hr>
                                            <li><a href="{{url($langCodeForUrl.'/'. (new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('check-booking'))}}" style="color: #272525; background: transparent;"><i class="fa fa-user" aria-hidden="true"></i> {{__('checkBooking')}}</a></li>
                                            <hr>
                                            <li><a href="{{ url($langCodeForUrl.'/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="ho-dr-con-last" style="color: #272525; background: transparent !important;"><i class="fa fa-sign-in" aria-hidden="true"></i>{{ __('logout') }}</a>
                                            </li>
                                            <form id="logout-form" action="{{ url($langCodeForUrl.'/logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </ul>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                    <div class="ed-com-t1-social">
                        <ul>
                            <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            </li>
                            <li><a href="#"><i class="fa fa-google-plus" aria-hidden="true"></i></a>
                            </li>
                            <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hidden-lg hidden-md col-xs-12 col-sm-12">
        <a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('check-booking'))}}" style="color: #337ab7;">{{__('checkYourBooking')}}</a>
    </div>
    <div class="top-logo" data-spy="affix" data-offset-top="250">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-md-2">
                    <div class="wed-logo">
                        <a id="parisCityTours" href="{{url('/')}}"><img src="{{asset('img/paris-city-tours-logo.webp')}}" alt="Paris City Tours" />
                        </a>
                    </div>
                </div>
                <div class="col-md-5">
                    @if(!request()->is('/', $langs))
                        <form method="GET" class="tourz-search-form tour-form-two" action="{{url($langCodeForUrl.'/s')}}"
                              autocomplete="off">
                            <div class="home-banner-search">
                                <div class="search-bar header-search-area">
                                    <input type="text" placeholder="{{__('searchLocationsAttractions')}}" name="q" class="search-field light-border" id="searchInput">
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
                </div>
                <div class="col-md-5">
                    <div class="main-menu">
                        <ul>
                            <li class="hidden-xs"><a href="{{url('/')}}">{{__('home')}}</a>
                            </li>
                            <li class="special-offer-menu-class hidden-xs"><a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('special-offers'))}}">{{__('specialOffers')}}</a>
                            </li>
                            <li class="about-menu">
                                <a href="{{url($langCodeForUrl.'/'.(new App\Http\Controllers\Helpers\CommonFunctions)->getRouteLocalization('cart'))}}">
                                    <i style="font-size: 24px" class="icon-cz-cart"></i>
                                    <div class="circle" id="cartCount"></div>
                                </a>
                                <div class="mm-pos" style="margin-top: 50px;">
                                    <div class="about-mm m-menu" style="width:35%; right:0px;">
                                        <div class="m-menu-inn">
                                            <div id="cartTableDiv" class="mm1-com-cart mm1-s1">
                                                @include('dynamic-components.header-cart-table')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

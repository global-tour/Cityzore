@include('frontend-partials.head', ['page' => 'empty-cart'])
@include('frontend-partials.header')


<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>


<section>
    <div class="rows tb-space pad-top-o pad-bot-redu" style="padding-top: 4%;">
        <div class="container">
            <div class="col-md-6">
                <div class="spe-title">
                    <p style="font-size: 46px">{{__('cartIsEmpty')}}!</p>
                    <p style="margin-top:20px;font-weight: bold;color: #253d52;">{{__('addProductOnYourCart')}}</p>
                </div>
                <div class="col-md-8" style="text-align:center;margin-right: 15%;margin-left: 15%;">
                    <p style="font-size:26px;margin-bottom:25px;margin-top:20px;font-weight: bold;color: #253d52;">{{__('checkOurProducts')}}</p>
                    <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('all-products'))}}">
                        <div class="tour-mig-like-com">
                            <div class="tour-mig-lc-img">
                                <img src="{{asset('img/eiffel-tower-attraction.jpg')}}" alt="Eiffel Tower" style="height: 200px;">
                            </div>
                            <div class="tour-mig-lc-con">
                                <h5>{{__('eiffelTower')}}</h5>
                                <p>
                                    <span>12 {{__('packages')}}</span> {{__('startingFrom')}}
                                    <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor(39)}}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-md-12">
                    <img src="{{asset('img/empty-cart.jpg')}}" style="width:100%;">
                </div>
            </div>
        </div>
    </div>
</section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'empty-cart'])

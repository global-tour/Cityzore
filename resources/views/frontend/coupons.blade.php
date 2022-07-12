@include('frontend-partials.head', ['page' => 'coupons'])
@include('frontend-partials.header')

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
?>

<section>
    <div class="db">
    @include('layouts.profile-sidebar.sidebar-left')
        <div class="col-lg-9">
            <div class="db-2-com db-2-main" style="background-color: white;">
                <h4>{{__('myCoupons')}}</h4>
                <div class="db-2-main-com db-2-main-com-table">
                    @foreach($coupons as $coupon)
                        <?php $i=1; ?>
                        <div class="col-lg-4">
                            <div class="ticket">
                                <div class="ticket__inner">
                                    <div class="ticket__border"></div>
                                    <h2 class="ticket__title">
                                        @if ($coupon->discountType == 'percent')
                                            % {{intval($coupon->discount)}} {{__('off')}}
                                        @else
                                            <i class="{{session()->get('currencyIcon')}}"></i> {{$coupon->discount}} {{__('off')}}
                                        @endif
                                        <strong>{{__('yourCode')}}: {{$coupon->couponCode}}</strong></h2>
                                    <p class="ticket__text" style="font-size: 18px;">
                                        @if ($coupon->type == 1)
                                            <?php
                                            $product = \App\Product::findOrFail($coupon->productID);
                                            $option = \App\Option::findOrFail($coupon->lastSelect);
                                            ?>
                                            {{__('thisCouponFor')}} {{$option->title}} <a href="{{url($langCodeForUrl.'/'.$product->url)}}" >{{$product->title}}</a>
                                        @elseif ($coupon->type == 2)
                                            <?php
                                            $country = \App\Country::findOrFail($coupon->lastSelect);
                                            ?>
                                                {{__('thisCouponForAllTours')}} <a href="{{url($langCodeForUrl.'/s?q='.$country->countries_name.'&dateFrom=&dateTo=&_token=hKzsA0JyU0ZGKCPSG3Hwvrwag3dRXU1dYqSIgmhD&type=country')}}">{{$country->countries_name}}</a>!
                                        @elseif ($coupon->type == 3)
                                            <?php
                                            $attraction = \App\Attraction::findOrFail($coupon->lastSelect);
                                            $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
                                            $language = \App\Language::where('code', $langCode)->first();
                                            $langID = $language->id;
                                            ?>
                                                {{__('thisCouponForAllTours')}} <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language))}}">{{$attraction->name}}</a>!
                                        @elseif ($coupon->type == 4 || $coupon->type == 5)
                                            {{__('thisCouponForAllTours')}} <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('all-products'))}}">Cityzore</a>!
                                        @elseif ($coupon->type == 6)
                                            {{__('thisCouponIsJustForYou')}}! {{__('thisCouponForAllTours')}} <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('all-products'))}}">Cityzore</a>
                                        @endif
                                    </p>
                                    <p style="font-size: 20px;">{{__('couponEndDate')}}: {{date("d/m/Y", strtotime($coupon->endingDate))}}</p>
                                </div>
                            </div>
                        </div>
                        <?php $i++; ?>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'coupons'])

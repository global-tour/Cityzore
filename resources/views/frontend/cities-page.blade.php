@include('frontend-partials.head', ['page' => 'cities'])
@include('frontend-partials.header')

<?php
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;
$language = App\Language::where('code', $langCode)->first();
?>

<section>
    <div class="container" style="display: flex;">
        @foreach($cities as $city)
            <?php
            $cityImage = \App\CityImage::where('city', $city->city)->first();
            $cityObject = \App\City::where('name', $city->city)->first();
            $cityTranslation = \App\CityTranslation::where('cityID', $cityObject->id)->where('languageID', $language->id)->first();
            $cityName = $cityObject->name;
            if ($cityTranslation) {
                $cityName = $cityTranslation->name;
            }
            ?>
            <div class="thumbex">
                <div class="thumbnail">
                    <a href="{{url($langCodeForUrl.'/s?q='.$cityName.'&dateFrom=&dateTo=&type=city')}}">
                        @if(is_null($cityImage))
                            <img src="https://bit.ly/2vnI5ZM"/>
                        @else
                            <img src="{{Storage::disk('s3')->url('city-images/' . $cityImage->image)}}" alt="{{$cityImage->city}}">
                        @endif
                        <span>{{$cityName}}</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>



    <div class="row" style="margin-right: 5%;margin-left: 5%;">
        @foreach($products as $product)
            <div class="col-md-3 col-sm-6 col-xs-12 b_packages wow slideInUp" data-wow-duration="0.5s">
                @if(!((new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product) == 0))
                    <div class="band">
                        <div class="ribbon  ribbon--orange" style="margin-top: 0px">% {{(new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product)}}</div>
                    </div>
                @endif
                <a href="{{url($langCodeForUrl. '/'. $product->url)}}">
                    <div class="v_place_img">
                        <img class="mobile-image" style="height:160px;padding: 2%;"
                             @if ($product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first())
                             src="{{Storage::disk('s3')->url('product-images-xs/' . $product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first()->src)}}"
                             @else
                             src="{{Storage::disk('s3')->url('product-images-xs/default_product.jpg')}}"
                             @endif
                             alt="{{$product->title}}" title="Tour Booking" />
                    </div>
                </a>
                <div class="b_pack rows" style="min-height: 207px;max-height: 207px;">
                    <div class="row" style="height: 87px;">
                        <div class="col-md-12">
                            <?php
                            $language = App\Language::where('code', $langCode)->first();
                            $productTranslation = App\ProductTranslation::where('productID', $product->id)->where('languageID', $language->id)->first();
                            ?>
                            <h3>
                                <a href="{{url($langCodeForUrl. '/'. $product->url)}}" style="font-size: 15px;font-weight: 500;color: #1A2B50">
                                    @if ($productTranslation)
                                        {{$productTranslation->title}}
                                    @else
                                        {{$product->title}}
                                    @endif
                                </a>
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row" style="height: 30px;">
                                <div class="rating" style="margin-left: 3%;">
                                    @if(!($product->rate==null))
                                        @for($i=1;$i<=$product->rate;$i++)
                                            <i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>
                                        @endfor
                                        <label style="font-size: 13px;vertical-align: text-bottom; float:left;color: #1A2B50;">| ({{$product->rate}}/5) </label>
                                        <p><span></span></p>
                                    @else
                                        <div style="font-size: 13px;vertical-align: text-bottom;padding-top: 3%;">No reviews yet</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row container">
                            <div class="hidden-lg hidden-md hidden-sm col-xs-12 mobile_desc">
                                {!! html_entity_decode(substr($product->shortDesc, 0, 100)) !!} ...
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mobile_from">
                                <h4 style="font-size:13px;">{{__('from')}}
                                    <?php $specialOffer = (new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product) ?>
                                    @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                        @if($specialOffer != 0)
                                            <?php
                                            $specialOfferPrice = round(App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - (((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) * ((new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product)) / 100),2);
                                            ?>
                                            <span class="special-offer-price" style="font-size: 18px;"><i class="{{session()->get('currencyIcon')}}"></i>{{$specialOfferPrice}}</span>
                                            <i class="{{session()->get('currencyIcon')}}"></i><span class="strikeout">{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}</span>
                                            <?php
                                            $commissionerEarns = App\Currency::calculateCurrencyForVisitor(((new App\Http\Controllers\Helpers\CommonFunctions)->getCommissionMinPrice($product, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                            ?>
                                        @else
                                            <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}
                                            <?php
                                            $commissionerEarns = App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - App\Currency::calculateCurrencyForVisitor(((new App\Http\Controllers\Helpers\CommonFunctions)->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                            ?>
                                        @endif
                                        <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                                    @else
                                        @if($specialOffer != 0)
                                            <span class="special-offer-price" style="font-size: 18px;"><i class="{{session()->get('currencyIcon')}}"></i>{{round(App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - (((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) * ((new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product)) / 100),2)}}</span>
                                            <i class="{{session()->get('currencyIcon')}}"></i><span class="strikeout">{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}</span>
                                        @else
                                            <i class="{{session()->get('currencyIcon')}}" style="color: #f4364f;"></i><span style="color:#f4364f;font-size: 18px">{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}</span>
                                        @endif
                                    @endif
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
<!-- <section>
    <div class="rows tb-space">
        <h1 style="text-align: center;">Paris Top Tours</h1>
        <div class="wrapper2">
            <div class="sidebar2">
                <img src="{{asset('img/paris2.jpeg')}}" alt="Eiffel Tower" style="height: 100vh;width: 100%;">
                <div class="sidebar-text">
                    <h1>Paris</h1>
                </div>
            </div>
            <div class="main2">
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-lg-4" style="height: 300px;">
                            <div class="main-image">
                                <img style="height:160px;width: 100%;"
                                     @if ($product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first())
                                     src="{{Storage::disk('s3')->url('product-images-xs/' . $product->ProductGalleries()->where('product_id', '=', $product->id)->where('id', '=', $product->coverPhoto)->first()->src)}}"
                                     @else
                                     src="{{Storage::disk('s3')->url('product-images-xs/default_product.jpg')}}"
                                     @endif
                                     alt="{{$product->title}}" title="Tour Booking" />
                            </div>
                            <div class="main-title">
                                <a href="{{url($product->url)}}" style="font-size: 13px;font-weight: 500">{{$product->title}}</a>
                            </div>
                            <div class="main-rate">
                                <div class="col-md-8">
                                    <div class="rating">
                                        @for($i=1;$i<=$product->rate;$i++)
                                            <i class="icon-cz-star" style="color: #ffad0c; font-size: 15px;"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    @if(!($product->rate == null))
                                        <span style="margin-left: 3%; font-size:12px;vertical-align: text-bottom;">({{$product->rate}}/5)</span>
                                        <p><span></span></p>
                                    @endif
                                </div>
                            </div>
                            <div class="main-price">
                                <p style="margin-bottom: 0px;color: grey;">{{__('from')}}</p>
                                <h4 style="font-size:13px;margin-top: 0px;">
                                    <?php $specialOffer = (new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product) ?>
                                    @if(auth()->check() && auth()->guard('web')->user()->commission != null && auth()->guard('web')->user()->isActive == '1')
                                        @if($specialOffer != 0)
                                            <?php
                                            $specialOfferPrice = round(App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - (((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) * ((new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product)) / 100),2);
                                            ?>
                                            <span class="special-offer-price" style="font-size: 18px;"><i class="{{session()->get('currencyIcon')}}"></i>{{$specialOfferPrice}}</span>
                                            <i class="{{session()->get('currencyIcon')}}"></i><span class="strikeout">{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}</span>
                                            <?php
                                            $commissionerEarns = App\Currency::calculateCurrencyForVisitor(((new App\Http\Controllers\Helpers\CommonFunctions)->getCommissionMinPrice($product->id, auth()->guard('web')->user()->id, $specialOfferPrice)));
                                            ?>
                                        @else
                                            <i class="{{session()->get('currencyIcon')}}"></i>{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}
                                            <?php
                                            $commissionerEarns = App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - App\Currency::calculateCurrencyForVisitor(((new App\Http\Controllers\Helpers\CommonFunctions)->getCommissionMinPrice($product, auth()->guard('web')->user()->id)));
                                            ?>
                                        @endif
                                        <i class="{{session()->get('currencyIcon')}}"></i>{{$commissionerEarns}} COM
                                    @else
                                        @if($specialOffer != 0)
                                            <span class="special-offer-price" style="font-size: 18px;"><i class="{{session()->get('currencyIcon')}}"></i>{{round(App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) - (((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id)) * ((new App\Http\Controllers\Helpers\CommonFunctions)->getOfferPercentage($product)) / 100),2)}}</span>
                                            <i class="{{session()->get('currencyIcon')}}"></i><span class="strikeout">{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}</span>
                                        @else
                                            <i class="{{session()->get('currencyIcon')}}"></i><span style="font-size: 18px">{{App\Currency::calculateCurrencyForVisitor((new App\Http\Controllers\Helpers\CommonFunctions)->getMinPrice($product->id))}}</span>
                                        @endif
                                    @endif
                                </h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section> -->

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'frontend.home'])
